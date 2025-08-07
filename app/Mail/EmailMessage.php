<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailMessage extends Mailable
{

    use Queueable, SerializesModels;

    public function __construct(
        public string $subject,
        public string $body,
        public array $attachments = []
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.message',
            text: 'emails.message_plain',
            with: ['body' => $this->body]
        );
    }

    public function attachments(): array
    {
        return array_map(function ($attachment) {
            return Attachment::fromPath($attachment['path'])
                ->as($attachment['name'])
                ->withMime($attachment['mime']);
        }, $this->attachments);
    }
}
