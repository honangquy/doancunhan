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
        'acronym',
        'year',
        'description',
        'detailed_description',
        'submission_guidelines',
        'cfp_url',
        'field',
        'level_code',
        'location',
        'keywords',
        'start_date',
        'end_date',
        'submission_deadline',
        'review_deadline',
        'camera_ready_deadline',
        'result_announcement_deadline',
        'reviewers_per_paper',
        'enable_coi_check',
        'contact_email',
        'contact_phone',
        'expected_date', // kept for backward compatibility
        'objective', // kept for backward compatibility
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
        'start_date' => 'date',
        'end_date' => 'date',
        'submission_deadline' => 'datetime',
        'review_deadline' => 'datetime',
        'camera_ready_deadline' => 'datetime',
        'result_announcement_deadline' => 'datetime',
        'enable_coi_check' => 'boolean',
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

