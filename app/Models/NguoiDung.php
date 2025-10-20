<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class NguoiDung extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $table = 'nguoidung';
    protected $primaryKey = 'user_id';
    public $timestamps = false;

    protected $fillable = [
        'email',
        'password_hash',
        'full_name',
        'is_student',
        'faculty_id',
        'organization',
        'avatar_url',
        'locked',
        'email_verified_at',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'is_student' => 'boolean',
        'locked' => 'boolean',
        'created_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    // JWT Methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // Password accessor/mutator
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function setPasswordHashAttribute($value)
    {
        $this->attributes['password_hash'] = bcrypt($value);
    }

    // Relationships
    public function khoa()
    {
        return $this->belongsTo(Khoa::class, 'faculty_id', 'faculty_id');
    }

    public function vaiTros()
    {
        return $this->hasMany(VaiTroNguoiDung::class, 'user_id', 'user_id');
    }

    // Alias for English API readability
    public function roles()
    {
        return $this->vaiTros();
    }

    // Accessor for Vietnamese field name
    public function getHotenAttribute()
    {
        return $this->full_name;
    }

    // Accessor for standard Laravel name attribute
    public function getNameAttribute()
    {
        return $this->full_name;
    }

    // Accessor for standard Laravel id attribute  
    public function getIdAttribute()
    {
        return $this->user_id;
    }

    public function baiBaosAsSubmitter()
    {
        return $this->hasMany(BaiBao::class, 'submitter_id', 'user_id');
    }

    public function baiBaosAsAuthor()
    {
        return $this->belongsToMany(BaiBao::class, 'TacGiaBaiBao', 'user_id', 'paper_id')
            ->withPivot('author_order', 'is_contact', 'organization');
    }

    public function phanCongs()
    {
        return $this->hasMany(PhanCongPhanBien::class, 'reviewer_id', 'user_id');
    }

    public function biddings()
    {
        return $this->hasMany(Bidding::class, 'user_id', 'user_id');
    }

    public function chuyenMons()
    {
        return $this->hasMany(ChuyenMonReviewer::class, 'user_id', 'user_id');
    }

    // Helper methods
    public function hasRole($roleCode, $conferenceId = null)
    {
        $query = $this->vaiTros()->where('role_code', $roleCode);
        
        if ($conferenceId !== null) {
            $query->where('conference_id', $conferenceId);
        }
        
        return $query->exists();
    }

    public function isAdmin()
    {
        return $this->hasRole('ADMIN');
    }

    public function isChair($conferenceId = null)
    {
        return $this->hasRole('CHAIR', $conferenceId);
    }

    public function isReviewer($conferenceId = null)
    {
        return $this->hasRole('REVIEWER', $conferenceId);
    }

    public function isAuthor()
    {
        return $this->hasRole('AUTHOR');
    }

    // Email Verification Methods
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmailNotification);
    }

    public function getEmailForVerification()
    {
        return $this->email;
    }

    /**
     * Assign a role to the user
     */
    public function assignRole($roleCode, $conferenceId = null)
    {
        // Check if user already has this role for this conference
        if ($this->hasRole($roleCode, $conferenceId)) {
            return true; // Role already exists
        }

        // Create new role assignment
        return VaiTroNguoiDung::create([
            'user_id' => $this->user_id,
            'role_code' => $roleCode,
            'conference_id' => $conferenceId,
        ]);
    }

    /**
     * Remove a role from the user
     */
    public function removeRole($roleCode, $conferenceId = null)
    {
        $query = $this->vaiTros()->where('role_code', $roleCode);
        
        if ($conferenceId !== null) {
            $query->where('conference_id', $conferenceId);
        }
        
        return $query->delete();
    }

    // Get primary role for display purposes
    public function getPrimaryRole()
    {
        // Priority order: ADMIN > CHAIR > REVIEWER > AUTHOR > USER
        if ($this->isAdmin()) {
            return 'ADMIN';
        }
        
        // Check if user is CHAIR in any conference
        if ($this->vaiTros()->where('role_code', 'CHAIR')->exists()) {
            return 'CHAIR';
        }
        
        // Check if user is REVIEWER in any conference
        if ($this->vaiTros()->where('role_code', 'REVIEWER')->exists()) {
            return 'REVIEWER';
        }
        
        // Check if user is AUTHOR
        if ($this->vaiTros()->where('role_code', 'AUTHOR')->exists()) {
            return 'AUTHOR';
        }
        
        // Default to USER (for users without any specific role)
        return 'USER';
    }

    // Get all roles as comma-separated string for display
    public function getAllRolesString()
    {
        $roles = $this->vaiTros()
            ->distinct('role_code')
            ->pluck('role_code')
            ->toArray();
        
        return empty($roles) ? 'USER' : implode(', ', $roles);
    }
}
