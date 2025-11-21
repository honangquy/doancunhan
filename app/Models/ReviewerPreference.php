<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewerPreference extends Model
{
    use HasFactory;

    protected $table = 'reviewer_preferences';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'conference_id',
        'max_papers_wanted',
        'expertise',
        'note',
    ];

    protected $casts = [
        'max_papers_wanted' => 'integer',
    ];

    /**
     * Relationship: Reviewer (User)
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Relationship: Conference
     */
    public function conference()
    {
        return $this->belongsTo(HoiThao::class, 'conference_id', 'conference_id');
    }

    /**
     * Scope: Get preferences for a conference
     */
    public function scopeForConference($query, $conferenceId)
    {
        return $query->where('conference_id', $conferenceId);
    }

    /**
     * Scope: Get preference for a reviewer in a conference
     */
    public function scopeForReviewer($query, $userId, $conferenceId)
    {
        return $query->where('user_id', $userId)
                     ->where('conference_id', $conferenceId);
    }

    /**
     * Helper: Check if reviewer can accept more papers
     */
    public function canAcceptMorePapers($currentWorkload = 0)
    {
        return $currentWorkload < $this->max_papers_wanted;
    }

    /**
     * Helper: Get remaining slots
     */
    public function getRemainingSlots($currentWorkload = 0)
    {
        return max(0, $this->max_papers_wanted - $currentWorkload);
    }
}
