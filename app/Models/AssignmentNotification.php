<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentNotification extends Model
{
    use HasFactory;

    protected $table = 'assignment_notifications';

    protected $fillable = [
        'assignment_id',
        'notification_type',
        'status',
        'email_content',
        'sent_at',
        'error_message'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Notification type constants
    const TYPE_ASSIGNMENT = 'ASSIGNMENT';
    const TYPE_REMINDER = 'REMINDER';
    const TYPE_COI_ALERT = 'COI_ALERT';

    // Status constants
    const STATUS_PENDING = 'PENDING';
    const STATUS_SENT = 'SENT';
    const STATUS_FAILED = 'FAILED';

    public static function getTypeLabels()
    {
        return [
            self::TYPE_ASSIGNMENT => 'Thông báo phân công',
            self::TYPE_REMINDER => 'Nhắc nhở',
            self::TYPE_COI_ALERT => 'Cảnh báo COI'
        ];
    }

    public static function getStatusLabels()
    {
        return [
            self::STATUS_PENDING => 'Chờ gửi',
            self::STATUS_SENT => 'Đã gửi',
            self::STATUS_FAILED => 'Thất bại'
        ];
    }

    // Relationships
    public function assignment()
    {
        return $this->belongsTo(ReviewerAssignment::class, 'assignment_id');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('notification_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeSent($query)
    {
        return $query->where('status', self::STATUS_SENT);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isSent()
    {
        return $this->status === self::STATUS_SENT;
    }

    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function markAsSent()
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now()
        ]);
    }

    public function markAsFailed($errorMessage = null)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage
        ]);
    }
}