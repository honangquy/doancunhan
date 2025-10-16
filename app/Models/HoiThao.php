<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoiThao extends Model
{
    use HasFactory;

    protected $table = 'hoithao';
    protected $primaryKey = 'conference_id';
    public $timestamps = false;

    protected $fillable = [
        'parent_id',
        'level_code',
        'faculty_id',
        'title',
        'description',
        'year',
        'start_date',
        'end_date',
        'deadline_submission',
        'deadline_review',
        'deadline_camera_ready',
        'status',
        // New fields for conference detail page
        'cfp_url',
        'submission_guidelines',
        'detailed_description',
        'location',
        'contact_email', 
        'contact_phone',
        'chair_name',
        'chair_email',
        'keywords'
    ];

    protected $casts = [
        'year' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'deadline_submission' => 'date',
        'deadline_review' => 'date',
        'deadline_camera_ready' => 'date',
    ];

    // Relationships
    public function khoa()
    {
        return $this->belongsTo(Khoa::class, 'faculty_id', 'faculty_id');
    }

    public function parent()
    {
        return $this->belongsTo(HoiThao::class, 'parent_id', 'conference_id');
    }

    public function children()
    {
        return $this->hasMany(HoiThao::class, 'parent_id', 'conference_id');
    }

    public function tieuBans()
    {
        return $this->hasMany(TieuBan::class, 'conference_id', 'conference_id');
    }

    public function baiBaos()
    {
        return $this->hasMany(BaiBao::class, 'conference_id', 'conference_id');
    }

    public function thongBaos()
    {
        return $this->hasMany(ThongBao::class, 'conference_id', 'conference_id');
    }

    public function yeuCauHoiThao()
    {
        return $this->hasOne(YeuCauHoiThao::class, 'conference_id', 'conference_id');
    }

    /**
     * Get join requests for this conference.
     */
    public function joinRequests()
    {
        return $this->hasMany(JoinRequest::class, 'conference_id', 'conference_id');
    }

    /**
     * Get pending join requests.
     */
    public function pendingJoinRequests()
    {
        return $this->joinRequests()->where('status', JoinRequest::STATUS_PENDING);
    }

    /**
     * Get approved join requests by role.
     */
    public function approvedJoinRequests($role = null)
    {
        $query = $this->joinRequests()->where('status', JoinRequest::STATUS_APPROVED);
        
        if ($role) {
            $query->where('role', $role);
        }
        
        return $query;
    }

    // Helper methods
    public function isOpen()
    {
        return $this->status === 'open' || $this->status === 'OPEN';
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }

    public function isFinished()
    {
        return $this->status === 'finished';
    }

    public function isSubmissionOpen()
    {
        return $this->deadline_submission && $this->deadline_submission >= now()->startOfDay();
    }

    public function isReviewOpen()
    {
        return $this->deadline_review && $this->deadline_review >= now()->startOfDay();
    }

    /**
     * Get the number of days remaining until submission deadline.
     */
    public function getDaysUntilSubmission()
    {
        if (!$this->deadline_submission) {
            return null;
        }
        
        $now = now()->startOfDay();
        $deadline = \Carbon\Carbon::parse($this->deadline_submission)->startOfDay();
        
        if ($deadline < $now) {
            return 0;
        }
        
        return $now->diffInDays($deadline);
    }

    /**
     * Get conference statistics.
     */
    public function getStats()
    {
        return [
            'papers' => $this->baiBaos()->count(),
            'reviewers' => $this->approvedJoinRequests(JoinRequest::ROLE_REVIEWER)->count(),
            'authors' => $this->approvedJoinRequests(JoinRequest::ROLE_AUTHOR)->count()
        ];
    }
}
