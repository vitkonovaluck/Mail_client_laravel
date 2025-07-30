<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
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
                'password'      => Crypt::decryptString($user->epassword), // потрібно знати пароль!
                'protocol'      => 'imap'
            ]);

            try {
                $client->connect();
                $folder = $client->getFolder('INBOX');
                $messages = $folder->messages()->unseen()->limit(10)->get();

                foreach ($messages as $message) {
                    $messageId = $this->generateMessageId($message);

                    Email::firstOrCreate(
                        ['message_id' => $messageId],
                        [
                            'virtual_user_id' => $user->id,
                            'from' => $message->getFrom()[0]->mail ?? 'unknown',
                            'to' => $user->email,
                            'subject' => $this->decodeSubject($message->getSubject()),
                            'body' => $message->getTextBody() ?: '',
                            'received_at' => Carbon::parse($message->getDate()),
                        ]
                    );                }

                $client->disconnect();

            } catch (\Throwable $e) {
                Log::info("Імпорт для: {$user->email}");
                $this->error("Помилка імпорту: " . $e);
                continue;
            }
        }

        $this->info("Імпорт завершено");
    }

    private function generateMessageId($message): string
    {
        // Спроба отримати Message-ID з заголовків
        if ($messageIdHeader = $message->getHeaders()->get('message-id')) {
            return $messageIdHeader->getValue();
        }

        // Генерація унікального ID на основі даних листа
        return md5(
            $message->getSubject() .
            $message->getDate()->toString() .
            $message->getFrom()[0]->mail ?? ''
        );
    }

    private function decodeSubject(?string $subject): string
    {
        if (!$subject) return 'No Subject';

        return mb_decode_mimeheader($subject) ?: $subject;
    }
}
