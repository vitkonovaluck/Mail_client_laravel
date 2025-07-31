<?php

namespace App\Console\Commands;

use App\Models\Folder;
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

                $this->getMessageUser($client, 'INBOX', 'inbox', $user);
//                $this->getMessageUser($client, 'Sent', 'sent', $user);
//                $this->getMessageUser($client, 'Trash', 'trash', $user);
                $client->disconnect();

            } catch (\Throwable $e) {
                Log::info("Імпорт для: {$user->email}");
                $this->error("Помилка імпорту: " . $e);
                continue;
            }

        }

        $this->info("Імпорт завершено");
    }

    public function getMessageUser($client, $imapFolder, $localFolder, $user)
    {
        try {
            $folder = $client->getFolder($imapFolder);

            // Отримуємо останню дату імпорту
            $lastImportedDate = Email::where('to', $user->email)
                ->latest('received_at')
                ->value('received_at');

            // Формуємо запит з критеріями
            $query = $folder->query()
                ->setFetchBody(true)
                ->fetchOrderDesc();

            if ($lastImportedDate) {
                $query->since($lastImportedDate);
            } else {
                $query->limit(100); // Обмеження для першого імпорту
            }

            $messages = $query->get();
            $folderId = Folder::where('slug', $localFolder)->first()->id;
            $importedCount = 0;

            foreach ($messages as $message) {
                try {
                    if ($this->importMessage($message, $user, $folderId)) {
                        $importedCount++;
                    }
                } catch (\Throwable $e) {
                    Log::error("Помилка імпорту листа: " . $e->getMessage());
                    continue;
                }
            }

            $this->info("Папка {$localFolder}: імпортовано {$importedCount} листів");

        } catch (\Throwable $e) {
            Log::error("Помилка обробки папки {$imapFolder}: " . $e->getMessage());
            $this->error("Помилка папки {$imapFolder}: " . $e->getMessage());
        }

    }


    protected function importMessage($message, VirtualUser $user, int $folderId): bool
    {
        try {
            $messageId = $this->generateMessageId($message);

            // Перевіряємо чи лист вже існує
            if (Email::where('message_id', $messageId)->exists()) {
                return false;
            }

            // Декодуємо тему листа
            $subject = $this->decodeSubject($message->getSubject());

            // Отримуємо відправника
            $from = $message->getFrom()[0]->mail ?? 'unknown';

            // Створюємо запис листа
            $email = Email::create([
                'message_id'       => $messageId,
                'virtual_user_id' => $user->id,
                'folder_id'       => $folderId,
                'from'            => $from,
                'to'              => $user->email,
                'subject'         => $subject,
                'body'            => $message->getTextBody() ?: '',
                'body_html'       => $message->getHtmlBody(),
                'received_at'     => Carbon::parse($message->getDate()),
                'is_read'         => $message->getFlags()->has('\\Seen'),
            ]);

            // Додаємо категорії
            $categories = $this->detectCategories($message);
            $email->categories()->sync($categories);

            return true;

        } catch (\Throwable $e) {
            Log::error("Помилка імпорту листа: " . $e->getMessage());
            return false;
        }
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
