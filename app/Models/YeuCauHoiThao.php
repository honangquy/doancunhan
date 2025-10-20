<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YeuCauHoiThao extends Model
{
    use HasFactory;

    protected $table = 'yeucauhoithao';
    protected $primaryKey = 'request_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'field',
        'level_code',
        'expected_date',
        'objective',
        'proposal_file',
        'proposal_file_path',
        'status',
        'approver_id',
        'approval_note',
        'faculty_name',
        'affiliation',
        'chair_fullname',
        'chair_email',
        'chair_phone',
        'submitted_at',
        'created_at',
        'approved_at',
    ];

    protected $casts = [
        'expected_date' => 'date',
        'submitted_at' => 'datetime',
        'created_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(NguoiDung::class, 'user_id', 'user_id');
    }

    public function requester()
    {
        return $this->belongsTo(NguoiDung::class, 'user_id', 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(NguoiDung::class, 'approver_id', 'user_id');
    }

    public function hoiThao()
    {
        return $this->hasOne(HoiThao::class, 'conference_request_id', 'request_id');
    }

    public function conference()
    {
        return $this->hasOne(HoiThao::class, 'conference_request_id', 'request_id');
    }

    public function coChairs()
    {
        return $this->hasMany(ThemVienBoSung::class, 'request_id', 'request_id');
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'PENDING';
    }

    public function isApproved()
    {
        return $this->status === 'APPROVED';
    }

    public function isRejected()
    {
        return $this->status === 'REJECTED';
    }
}

