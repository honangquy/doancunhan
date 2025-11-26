<?php

namespace App\Notifications;

use App\Models\HoiThao;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConfigApprovalRequested extends Notification
{
    use Queueable;

    protected $conference;
    protected $configType;

    /**
     * Create a new notification instance.
     */
    public function __construct(HoiThao $conference, string $configType = 'general')
    {
        $this->conference = $conference;
        $this->configType = $configType;
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
            'title' => 'Yêu cầu duyệt cấu hình hội thảo',
            'message' => sprintf(
                'Hội thảo "%s" có cấu hình mới cần phê duyệt (%s)',
                $this->conference->title,
                $this->configType
            ),
            'data' => [
                'url' => route('admin.configured-conferences.index'),
                'level' => 'info',
                'created_by' => 'Hệ thống',
                'conference_id' => $this->conference->conference_id,
                'config_type' => $this->configType,
                'type' => 'config_approval'
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
