<?php

namespace App\Jobs;

use App\Mail\EmailMessage;
use App\Models\Email;
use App\Models\Folder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public array $data, // ['to', 'subject', 'body', 'attachments']
        public ?array $cc = null,
        public ?array $bcc = null
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Відправка листа
            $mail = Mail::to($this->data['to'])
                ->send(new EmailMessage(
                    $this->data['subject'],
                    $this->data['body'],
                    $this->data['attachments'] ?? []
                ));

            // Збереження копії у відправлені
            Email::create([
                'user_id' => $this->user->id,
                'folder_id' => Folder::where('slug', 'sent')->first()->id,
                'from' => $this->user->email,
                'to' => is_array($this->data['to']) ? implode(',', $this->data['to']) : $this->data['to'],
                'subject' => $this->data['subject'],
                'body' => $this->data['body'],
                'body_html' => $this->data['body'],
                'is_read' => true,
                'received_at' => now(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Email sending failed: '.$e->getMessage());
            throw $e;
        }
    }
}
