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
                'password'      => Crypt::decryptString($user->epassword),
                'protocol'      => 'imap'
            ]);

            try {
                $client->connect();
                $folder = $client->getFolder('INBOX');

                // Отримуємо останню дату імпортованого листа для цього користувача
                $lastImportedDate = Email::where('to', $user->email)
                    ->latest('received_at')
                    ->value('received_at');

                // Фільтруємо листи, які прийшли після останнього імпорту
                $query = $folder->messages()
                    ->unseen()
                    ->since($lastImportedDate ?? '1970-01-01')
                    ->limit(100);

                $messages = $query->get();

                $importedCount = 0;
                $skippedCount = 0;

                DB::transaction(function () use ($user, $messages, &$importedCount, &$skippedCount) {
                    foreach ($messages as $message) {
                        $messageId = $message->getHeaders()->get('message-id')->getValue();

                        if (Email::where('message_id', $messageId)->exists()) {
                            $skippedCount++;
                            continue;
                        }

                        Email::create([
                            'message_id'       => $messageId,
                            'virtual_user_id'  => $user->id,
                            'from'             => $message->getFrom()[0]->mail ?? '',
                            'to'               => $user->email,
                            'subject'          => $message->getSubject(),
                            'body'             => $message->getTextBody(),
                            'body_html'        => $message->getHtmlBody(),
                            'received_at'      => Carbon::parse($message->getDate()),
                            'headers'          => json_encode($message->getHeaders()->all()),
                        ]);

                        $importedCount++;
                    }
                });

                $client->disconnect();

                $this->info("Імпортовано: {$importedCount}, пропущено: {$skippedCount}");
                Log::info("Для {$user->email}: імпортовано {$importedCount}, пропущено {$skippedCount}");

            } catch (\Throwable $e) {
                Log::error("Помилка імпорту для {$user->email}: " . $e->getMessage());
                $this->error("Помилка імпорту для {$user->email}: " . $e->getMessage());

                try {
                    if ($client->isConnected()) {
                        $client->disconnect();
                    }
                } catch (\Throwable $disconnectError) {
                    Log::error("Помилка відключення: " . $disconnectError->getMessage());
                }

                continue;
            }
        }

        Log::info("Імпорт завершено");
        $this->info("Імпорт завершено");
    }
}
