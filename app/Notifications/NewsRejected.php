<?php

namespace App\Notifications;

use App\Models\News;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewsRejected extends Notification
{
    use Queueable;

    protected $news;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(News $news, string $reason)
    {
        $this->news = $news;
        $this->reason = $reason;
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
            'title' => 'Tin tức bị từ chối',
            'message' => sprintf(
                'Bài viết "%s" đã bị từ chối. Lý do: %s',
                $this->news->title,
                $this->reason
            ),
            'data' => json_encode([
                'url' => route('chair.news.edit', $this->news->news_id),
                'level' => 'error',
                'type' => 'news_rejected',
                'rejected_by' => auth()->user()->full_name ?? 'Admin',
                'rejection_reason' => $this->reason,
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
