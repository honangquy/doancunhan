<?php

namespace App\Notifications;

use App\Models\News;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewsApproved extends Notification
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
            'title' => 'Tin tức đã được duyệt',
            'message' => sprintf(
                'Bài viết "%s" đã được Admin phê duyệt và xuất bản.',
                $this->news->title
            ),
            'data' => json_encode([
                'url' => route('chair.news.show', $this->news->news_id),
                'level' => 'success',
                'type' => 'news_approved',
                'approved_by' => auth()->user()->full_name ?? 'Admin',
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
