<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InviteMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You have been invited to Amo World'
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invite',
            with: ['url' => $this->url]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
