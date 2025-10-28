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
        'admin_notes',
        'invitation_token', // Track if this came from an invitation
        
        // Thông tin cá nhân chung
        'full_name',
        'email_contact',
        'country',
        'organization',
        'department', 
        'phone',
        'notes',
        
        // Dành cho tác giả
        'field_of_study',
        'academic_title',
        
        // Dành cho reviewer
        'expertise_keywords',
        'max_papers',
        
        // Cam kết
        'commitment_confirmed'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'commitment_confirmed' => 'boolean',
        'max_papers' => 'integer'
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

    /**
     * Get validation rules for author role.
     */
    public static function getAuthorValidationRules(): array
    {
        return [
            'role' => 'required|in:' . self::ROLE_AUTHOR,
            'full_name' => 'required|string|max:255',
            'email_contact' => 'required|email|max:255', 
            'country' => 'required|string|max:100',
            'organization' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'field_of_study' => 'required|string|max:255',
            'academic_title' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'commitment_confirmed' => 'required|accepted'
        ];
    }

    /**
     * Get validation rules for reviewer role.
     */
    public static function getReviewerValidationRules(): array
    {
        return [
            'role' => 'required|in:' . self::ROLE_REVIEWER,
            'email_contact' => 'required|email|max:255',
            'full_name' => 'required|string|max:255',
            'organization' => 'required|string|max:255',
            'expertise_keywords' => 'required|string|max:1000',
            'max_papers' => 'required|integer|min:1|max:50',
            'commitment_confirmed' => 'required|accepted'
        ];
    }

    /**
     * Check if this is an author request.
     */
    public function isAuthorRequest(): bool
    {
        return $this->role === self::ROLE_AUTHOR;
    }

    /**
     * Check if this is a reviewer request.
     */
    public function isReviewerRequest(): bool
    {
        return $this->role === self::ROLE_REVIEWER;
    }
}
