<?php

namespace App\Mail;

use App\Models\YeuCauHoiThao;
use App\Models\NguoiDung;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConferenceRequestApproved extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $request;
    public $configUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(NguoiDung $user, YeuCauHoiThao $conferenceRequest, string $configUrl)
    {
        $this->user = $user;
        $this->request = $conferenceRequest;
        $this->configUrl = $configUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Yêu cầu Tạo Hội thảo Được Duyệt - ' . $this->request->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.conference-request-approved',
            with: [
                'user' => $this->user,
                'request' => $this->request,
                'configUrl' => $this->configUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
    {
        return [];
    }
}
