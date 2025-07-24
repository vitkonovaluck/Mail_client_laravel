<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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
            $this->info("Імпорт для: {$user->email}");

            // Підключення до IMAP
            $client = Client::make([
                'host'          => 'mail.ce.vn.ua',
                'port'          => 993,
                'encryption'    => 'ssl',
                'validate_cert' => true,
                'username'      => $user->email,
                'password'      => 'informix', // потрібно знати пароль!
                'protocol'      => 'imap'
            ]);
            $this->info("Create client");
            try {
                $this->info("After Connect client");
                $client->connect();
                $this->info("Connect client");
//                $folder = $client->getFolder('INBOX');
//                $messages = $folder->messages()->unseen()->limit(10)->get();
//
//                foreach ($messages as $message) {
//                    Email::create([
//                        'virtual_user_id' => $user->id,
//                        'from' => $message->getFrom()[0]->mail ?? '',
//                        'to' => $user->email,
//                        'subject' => $message->getSubject(),
//                        'body' => $message->getTextBody(),
//                        'received_at' => Carbon::parse($message->getDate()),
//                    ]);
//                }

                $client->disconnect();
                $this->info("Disconnect client");
            } catch (\Throwable $e) {
                $this->error("Помилка імпорту: " . $e->getMessage());
                continue;
            }
        }

        $this->info("Імпорт завершено");
    }
}
