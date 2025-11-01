<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ReviewerAssignment extends Model
{
    use HasFactory;

    protected $table = 'reviewer_assignments';

    protected $fillable = [
        'user_id',
        'paper_id',
        'conference_id',
        'assigned_by',
        'assignment_method',
        'status',
        'assigned_at',
        'responded_at',
        'review_submitted_at',
        'decline_reason',
        'assignment_metadata'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'responded_at' => 'datetime', 
        'review_submitted_at' => 'datetime',
        'assignment_metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Status constants
    const STATUS_PENDING = 'PENDING';
    const STATUS_ACCEPTED = 'ACCEPTED';
    const STATUS_DECLINED = 'DECLINED';
    const STATUS_COMPLETED = 'COMPLETED';

    // Assignment method constants
    const METHOD_MANUAL = 'MANUAL';
    const METHOD_AUTO = 'AUTO';

    public static function getStatusLabels()
    {
        return [
            self::STATUS_PENDING => 'Chờ phản hồi',
            self::STATUS_ACCEPTED => 'Đã chấp nhận',
            self::STATUS_DECLINED => 'Đã từ chối',
            self::STATUS_COMPLETED => 'Hoàn thành'
        ];
    }

    public static function getStatusColors()
    {
        return [
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_ACCEPTED => 'bg-blue-100 text-blue-800',
            self::STATUS_DECLINED => 'bg-red-100 text-red-800',
            self::STATUS_COMPLETED => 'bg-green-100 text-green-800'
        ];
    }

    public static function getMethodLabels()
    {
        return [
            self::METHOD_MANUAL => 'Thủ công',
            self::METHOD_AUTO => 'Tự động'
        ];
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatusLabels()[$this->status] ?? 'Không xác định';
    }

    public function getStatusColorAttribute()
    {
        return self::getStatusColors()[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getMethodLabelAttribute()
    {
        return self::getMethodLabels()[$this->assignment_method] ?? 'Không xác định';
    }

    // Relationships
    public function reviewer()
    {
        return $this->belongsTo(NguoiDung::class, 'user_id', 'user_id');
    }

    public function paper()
    {
        return $this->belongsTo(BaiBao::class, 'paper_id', 'paper_id');
    }

    public function conference()
    {
        return $this->belongsTo(HoiThao::class, 'conference_id', 'conference_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(NguoiDung::class, 'assigned_by', 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(AssignmentNotification::class, 'assignment_id');
    }

    // Scopes
    public function scopeForConference($query, $conferenceId)
    {
        return $query->where('conference_id', $conferenceId);
    }

    public function scopeForReviewer($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('assignment_method', $method);
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isAccepted()
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function isDeclined()
    {
        return $this->status === self::STATUS_DECLINED;
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function canBeModified()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_ACCEPTED]);
    }

    public function getDaysAssigned()
    {
        return Carbon::now()->diffInDays($this->assigned_at);
    }

    public function isOverdue($maxDays = 14)
    {
        return $this->isPending() && $this->getDaysAssigned() > $maxDays;
    }

    // Metadata helpers
    public function getBidValueFromMetadata()
    {
        return $this->assignment_metadata['bid_value'] ?? null;
    }

    public function getCoiStatusFromMetadata()
    {
        return $this->assignment_metadata['coi_status'] ?? false;
    }

    public function setMetadata($bidValue = null, $coiStatus = false, $additionalData = [])
    {
        $this->assignment_metadata = array_merge([
            'bid_value' => $bidValue,
            'coi_status' => $coiStatus,
            'assigned_timestamp' => now()->toISOString()
        ], $additionalData);
        
        return $this;
    }
}