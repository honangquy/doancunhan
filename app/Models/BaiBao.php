<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaiBao extends Model
{
    use HasFactory;

    protected $table = 'baibao';
    protected $primaryKey = 'paper_id';
    public $timestamps = false;

    protected $fillable = [
        'conference_id',
        'track_id',
        'submitter_id',
        'title',
        'abstract',
        'current_version_id',
        'status_code',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationships
    public function hoiThao()
    {
        return $this->belongsTo(HoiThao::class, 'conference_id', 'conference_id');
    }

    public function tieuBan()
    {
        return $this->belongsTo(TieuBan::class, 'track_id', 'track_id');
    }

    public function submitter()
    {
        return $this->belongsTo(NguoiDung::class, 'submitter_id', 'user_id');
    }

    public function trangThai()
    {
        return $this->belongsTo(TrangThaiBaiBao::class, 'status_code', 'status_code');
    }

    public function tacGias()
    {
        return $this->belongsToMany(NguoiDung::class, 'TacGiaBaiBao', 'paper_id', 'user_id')
            ->withPivot('author_order', 'is_contact', 'organization')
            ->orderBy('author_order');
    }

    public function phienBans()
    {
        return $this->hasMany(PhienBanBaiBao::class, 'paper_id', 'paper_id')
            ->orderBy('version_no', 'desc');
    }

    public function currentVersion()
    {
        return $this->belongsTo(PhienBanBaiBao::class, 'current_version_id', 'version_id');
    }

    public function phanCongs()
    {
        return $this->hasMany(PhanCongPhanBien::class, 'paper_id', 'paper_id');
    }

    public function biddings()
    {
        return $this->hasMany(Bidding::class, 'paper_id', 'paper_id');
    }

    public function reviewerBiddings()
    {
        return $this->hasMany(ReviewerBidding::class, 'paper_id', 'paper_id');
    }

    public function reviewerAssignments()
    {
        return $this->hasMany(ReviewerAssignment::class, 'paper_id', 'paper_id');
    }

    public function activeAssignments()
    {
        return $this->hasMany(ReviewerAssignment::class, 'paper_id', 'paper_id')
            ->whereIn('status', [ReviewerAssignment::STATUS_PENDING, ReviewerAssignment::STATUS_ACCEPTED]);
    }

    /**
     * Get paper candidates (các reviewer được mời bidding bài này)
     */
    public function paperCandidates()
    {
        return $this->hasMany(ReviewerPaperCandidate::class, 'paper_id', 'paper_id');
    }

    /**
     * Get locked biddings only (bidding đã được reviewer gửi)
     */
    public function lockedBiddings()
    {
        return $this->reviewerBiddings()->where('is_locked', true);
    }

    public function lichSuTrangThais()
    {
        return $this->hasMany(LichSuTrangThai::class, 'paper_id', 'paper_id')
            ->orderBy('changed_at', 'desc');
    }

    // Helper methods
    public function isSubmitted()
    {
        return $this->status_code === 'SUBMITTED';
    }

    public function isUnderReview()
    {
        return $this->status_code === 'UNDER_REVIEW';
    }

    public function isAccepted()
    {
        return $this->status_code === 'ACCEPTED';
    }

    public function isRejected()
    {
        return $this->status_code === 'REJECTED';
    }

    /**
     * Get count of assigned reviewers
     */
    public function getAssignedReviewerCountAttribute()
    {
        return $this->activeAssignments()->count();
    }

    /**
     * Get bidding statistics for this paper
     */
    public function getBiddingStatsAttribute()
    {
        $biddings = $this->reviewerBiddings()->where('coi', false)->get();

        return [
            'total_bidders' => $biddings->count(),
            'avg_bidding' => $biddings->avg('bidding_value'),
            'eager_count' => $biddings->where('bidding_value', ReviewerBidding::BID_EAGER)->count(),
            'able_count' => $biddings->where('bidding_value', ReviewerBidding::BID_ABLE)->count(),
            'willing_count' => $biddings->where('bidding_value', ReviewerBidding::BID_WILLING)->count(),
            'coi_count' => $this->reviewerBiddings()->where('coi', true)->count(),
        ];
    }

    /**
     * Check if paper needs more reviewers
     */
    public function needsMoreReviewers()
    {
        $maxReviewers = config('assignment.max_reviewers_per_paper', 3);
        return $this->activeAssignments()->count() < $maxReviewers;
    }

    /**
     * Get remaining reviewer slots
     */
    public function getRemainingReviewerSlotsAttribute()
    {
        $maxReviewers = config('assignment.max_reviewers_per_paper', 3);
        $current = $this->activeAssignments()->count();
        return max(0, $maxReviewers - $current);
    }
}
