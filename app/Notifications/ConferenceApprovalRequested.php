<?php

namespace App\Notifications;

use App\Models\YeuCauHoiThao;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConferenceApprovalRequested extends Notification
{
    use Queueable;

    protected $conferenceRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(YeuCauHoiThao $conferenceRequest)
    {
        $this->conferenceRequest = $conferenceRequest;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Yêu cầu tổ chức hội thảo mới',
            'message' => sprintf(
                'Hội thảo "%s" đang chờ phê duyệt. Người yêu cầu: %s',
                $this->conferenceRequest->conference_name ?? 'N/A',
                $this->conferenceRequest->user->full_name ?? 'N/A'
            ),
            'data' => [
                'url' => route('admin.conference-requests.show', $this->conferenceRequest->id),
                'level' => 'warning',
                'created_by' => $this->conferenceRequest->user->full_name ?? 'Hệ thống',
                'conference_request_id' => $this->conferenceRequest->id,
                'type' => 'conference_approval'
            ]
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
