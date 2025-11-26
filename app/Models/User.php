<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Specify the correct table name
    protected $table = 'nguoidung';

    // Specify the primary key
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'password',
        'phone',
        'affiliation',
        'bio',
        'expertise'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the user's roles
     */
    public function roles()
    {
        return $this->hasMany(VaiTroNguoiDung::class, 'user_id', 'user_id');
    }

    /**
     * Check if user has specific role
     */
    public function hasRole($roleCode)
    {
        return $this->roles()->where('role_code', $roleCode)->exists();
    }

    /**
     * Check if user has specific role for a conference
     */
    public function hasRoleForConference($roleCode, $conferenceId)
    {
        return $this->roles()
            ->where('role_code', $roleCode)
            ->where('conference_id', $conferenceId)
            ->exists();
    }

    /**
     * Get reviewer assignments
     */
    public function reviewerAssignments()
    {
        return $this->hasMany(ReviewerAssignment::class, 'user_id', 'user_id');
    }

    /**
     * Get active reviewer assignments (PENDING, ACCEPTED)
     */
    public function activeAssignments()
    {
        return $this->reviewerAssignments()
            ->whereIn('status', [ReviewerAssignment::STATUS_PENDING, ReviewerAssignment::STATUS_ACCEPTED]);
    }

    /**
     * Get reviewer biddings
     */
    public function reviewerBiddings()
    {
        return $this->hasMany(ReviewerBidding::class, 'user_id', 'user_id');
    }

    /**
     * Get reviewer preferences
     */
    public function reviewerPreferences()
    {
        return $this->hasMany(ReviewerPreference::class, 'user_id', 'user_id');
    }

    /**
     * Get reviewer preference for a conference
     */
    public function getPreferenceForConference($conferenceId)
    {
        return $this->reviewerPreferences()
            ->where('conference_id', $conferenceId)
            ->first();
    }

    /**
     * Get paper candidates (bài được mời bidding)
     */
    public function paperCandidates()
    {
        return $this->hasMany(ReviewerPaperCandidate::class, 'reviewer_id', 'user_id');
    }

    /**
     * Get papers where user is author
     */
    public function authoredPapers()
    {
        return $this->belongsToMany(BaiBao::class, 'TacGiaBaiBao', 'user_id', 'paper_id')
            ->withPivot('author_order', 'is_contact', 'organization');
    }

    /**
     * Get current reviewer workload (count of active assignments)
     */
    public function getReviewerWorkloadAttribute()
    {
        return $this->activeAssignments()->count();
    }

    /**
     * Check if reviewer can accept more papers
     * Dựa vào reviewer_preferences.max_papers_wanted
     */
    public function canAcceptMorePapers($conferenceId = null)
    {
        if ($conferenceId) {
            $preference = $this->getPreferenceForConference($conferenceId);
            $maxPapers = $preference ? $preference->max_papers_wanted : config('assignment.max_papers_per_reviewer', 5);
        } else {
            $maxPapers = config('assignment.max_papers_per_reviewer', 5);
        }

        return $this->reviewer_workload < $maxPapers;
    }

    /**
     * Get max papers wanted for a conference
     */
    public function getMaxPapersWanted($conferenceId)
    {
        $preference = $this->getPreferenceForConference($conferenceId);
        return $preference ? $preference->max_papers_wanted : config('assignment.max_papers_per_reviewer', 5);
    }

    /**
     * Check if user is author of a specific paper
     */
    public function isAuthorOfPaper($paperId)
    {
        return $this->authoredPapers()->where('paper_id', $paperId)->exists();
    }

    /**
     * Get the name attribute (alias for full_name)
     */
    public function getNameAttribute()
    {
        return $this->full_name;
    }

    /**
     * Override the getName method for authentication
     */
    public function getAuthIdentifierName()
    {
        return 'user_id';
    }
}
