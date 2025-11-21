<?php

namespace App\Notifications;

use App\Models\News;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewsApprovalRequested extends Notification
{
    use Queueable;

    protected $news;

    /**
     * Create a new notification instance.
     */
    public function __construct(News $news)
    {
        $this->news = $news;
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
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->user_id,
            'user_id' => $notifiable->user_id,
            'type' => 'news_approval',
            'title' => 'Yêu cầu duyệt tin tức mới',
            'message' => sprintf(
                'Bài: "%s" đang chờ phê duyệt. Người tạo: %s',
                $this->news->title,
                $this->news->createdBy->full_name ?? 'N/A'
            ),
            'data' => json_encode([
                'url' => route('admin.news.show', $this->news->news_id),
                'level' => 'warning',
                'created_by' => $this->news->createdBy->full_name ?? 'Hệ thống',
                'news_id' => $this->news->news_id
            ])
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
