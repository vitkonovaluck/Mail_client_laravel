<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Webklex\IMAP\Facades\Client;
use App\Models\VirtualUser;
use App\Models\Email;
use Carbon\Carbon;

class ImportEmails extends Command
{
    protected $signature = 'mail:import';
    protected $description = 'Імпортувати листи для всіх користувачів';

    public function handle()
    {
        $users = VirtualUser::all();

        foreach ($users as $user) {
            Log::info("Імпорт для: {$user->email}");
            $this->info("Імпорт для: {$user->email}");

            // Підключення до IMAP
            $client = Client::make([
                'host'          => 'mail.ce.vn.ua',
                'port'          => 993,
                'encryption'    => 'ssl',
                'validate_cert' => false,
                'username'      => $user->email,
                'password'      => Crypt::decryptString($user->epassword),
                'protocol'      => 'imap'
            ]);

            try {
                $client->connect();
                $folder = $client->getFolder('INBOX');

                $messages = $folder->messages()
                    ->unseen()
                    ->limit(100)
                    ->get();

                DB::transaction(function () use ($user, $messages) {
                    foreach ($messages as $message) {
                        // Генерація унікального message_id
                        $messageId = $this->generateMessageId($message, $user->email);

                        Email::updateOrCreate(
                            ['message_id' => $messageId],
                            [
                                'virtual_user_id' => $user->id,
                                'from' => $message->getFrom()[0]->mail ?? '',
                                'to' => $user->email,
                                'subject' => $this->decodeSubject($message->getSubject()),
                                'body' => $message->getTextBody(),
                                'received_at' => Carbon::parse($message->getDate()),
                            ]
                        );
                    }
                });

                // ... код відключення ...

            } catch (\Throwable $e) {
                Log::error("Помилка імпорту для {$user->email}: " . $e->getMessage());
                continue;
            }
        }

        Log::info("Імпорт завершено");
        $this->info("Імпорт завершено");
    }

    protected function generateMessageId($message, $email): string
    {
        return optional($message->getHeaders()->get('message-id'))->getValue()
            ?? md5($email . $message->getSubject() . $message->getDate()->toString());
    }

    protected function decodeSubject(string $subject): string
    {
        return mb_decode_mimeheader($subject);
    }
}
