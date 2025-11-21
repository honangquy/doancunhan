<?php

namespace App\Notifications;

use App\Models\JoinRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RoleApprovalRequested extends Notification
{
    use Queueable;

    protected $roleRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(JoinRequest $roleRequest)
    {
        $this->roleRequest = $roleRequest;
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
            'title' => 'Yêu cầu phân quyền người dùng',
            'message' => sprintf(
                'User %s xin quyền %s.',
                $this->roleRequest->user->full_name ?? 'N/A',
                $this->roleRequest->requested_role ?? 'N/A'
            ),
            'data' => [
                'url' => route('admin.join-requests.index'),
                'level' => 'warning',
                'created_by' => $this->roleRequest->user->full_name ?? 'Hệ thống',
                'join_request_id' => $this->roleRequest->request_id,
                'type' => 'role_approval'
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
