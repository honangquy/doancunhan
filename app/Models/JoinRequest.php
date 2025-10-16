<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JoinRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id',
        'user_id', 
        'role',
        'message',
        'status',
        'processed_by',
        'processed_at',
        'admin_notes'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Status constants
    const STATUS_PENDING = 'PENDING';
    const STATUS_APPROVED = 'APPROVED'; 
    const STATUS_REJECTED = 'REJECTED';

    // Role constants
    const ROLE_AUTHOR = 'AUTHOR';
    const ROLE_REVIEWER = 'REVIEWER';

    /**
     * Get the conference that this join request belongs to.
     */
    public function conference(): BelongsTo
    {
        return $this->belongsTo(HoiThao::class, 'conference_id', 'conference_id');
    }

    /**
     * Get the user who made this join request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'user_id', 'user_id');
    }

    /**
     * Get the admin who processed this request.
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'processed_by', 'user_id');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by conference.
     */
    public function scopeForConference($query, $conferenceId)
    {
        return $query->where('conference_id', $conferenceId);
    }

    /**
     * Scope to filter by user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if request is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if request is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if request is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }
}
