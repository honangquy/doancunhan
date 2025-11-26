<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConferenceReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailSubject;
    public string $emailBody;
    public string $recipientName;

    public function __construct(string $subject, string $body, string $recipientName)
    {
        $this->emailSubject = $subject;
        $this->emailBody = $body;
        $this->recipientName = $recipientName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.conference_reminder',
            with: [
                'body' => $this->emailBody,
                'recipientName' => $this->recipientName
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
