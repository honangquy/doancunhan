<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewerPaperCandidate extends Model
{
    use HasFactory;

    protected $table = 'reviewer_paper_candidate';
    protected $primaryKey = 'id';

    protected $fillable = [
        'paper_id',
        'reviewer_id',
        'conference_id',
        'sent_by',
        'round_no',
        'note',
    ];

    protected $casts = [
        'round_no' => 'integer',
    ];

    /**
     * Relationship: Paper (BaiBao)
     */
    public function paper()
    {
        return $this->belongsTo(BaiBao::class, 'paper_id', 'paper_id');
    }

    /**
     * Relationship: Reviewer (User)
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id', 'user_id');
    }

    /**
     * Relationship: Conference (HoiThao)
     */
    public function conference()
    {
        return $this->belongsTo(HoiThao::class, 'conference_id', 'conference_id');
    }

    /**
     * Relationship: Chair who sent this candidate (User)
     */
    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by', 'user_id');
    }

    /**
     * Relationship: Bidding for this paper-reviewer pair
     */
    public function bidding()
    {
        return $this->hasOne(ReviewerBidding::class, 'paper_id', 'paper_id')
                    ->where('user_id', $this->reviewer_id);
    }

    /**
     * Scope: Get candidates for a reviewer
     */
    public function scopeForReviewer($query, $reviewerId)
    {
        return $query->where('reviewer_id', $reviewerId);
    }

    /**
     * Scope: Get candidates for a conference
     */
    public function scopeForConference($query, $conferenceId)
    {
        return $query->where('conference_id', $conferenceId);
    }

    /**
     * Scope: Get candidates for a specific round
     */
    public function scopeForRound($query, $roundNo)
    {
        return $query->where('round_no', $roundNo);
    }

    /**
     * Scope: Get candidates with paper and bidding info
     */
    public function scopeWithDetails($query)
    {
        return $query->with(['paper', 'reviewer', 'bidding']);
    }
}
