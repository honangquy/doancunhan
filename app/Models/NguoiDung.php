<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class NguoiDung extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'NguoiDung';
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
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'is_student' => 'boolean',
        'locked' => 'boolean',
        'created_at' => 'datetime',
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
}
