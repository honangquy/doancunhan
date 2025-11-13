<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $table = 'notification_templates';
    protected $primaryKey = 'template_id';

    // Schema cũ: code, title, body, default_channels, variables
    protected $fillable = [
        'code',
        'title',
        'body',
        'default_channels',
        'variables'
    ];

    protected $casts = [
        'default_channels' => 'array',
        'variables' => 'array'
    ];

    /**
     * Render template với biến thay thế
     */
    public function render(array $variables): array
    {
        $subject = $this->title; // title chính là subject trong schema cũ
        $body = $this->body;

        foreach ($variables as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $subject = str_replace($placeholder, $value ?? '', $subject);
            $body = str_replace($placeholder, $value ?? '', $body);
        }

        return [
            'subject' => $subject,
            'body' => $body
        ];
    }
}
