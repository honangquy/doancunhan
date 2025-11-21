<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $table = 'notification_templates';
    protected $primaryKey = 'template_id';

    // Schema mới: template_code, template_name, event_type, days_before, subject, body_html, body_text
    protected $fillable = [
        'template_code',
        'template_name',
        'event_type',
        'days_before',
        'subject',
        'body_html',
        'body_text',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'days_before' => 'integer'
    ];

    /**
     * Render template với biến thay thế
     */
    public function render(array $variables): array
    {
        $subject = $this->subject; // Dùng field 'subject' từ schema mới
        $body = $this->body_html;  // Dùng field 'body_html' từ schema mới

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
