<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoiThao extends Model
{
    use HasFactory;

    protected $table = 'HoiThao';
    protected $primaryKey = 'conference_id';
    public $timestamps = false;

    protected $fillable = [
        'parent_id',
        'level_code',
        'faculty_id',
        'title',
        'year',
        'start_date',
        'end_date',
        'deadline_submission',
        'deadline_review',
        'deadline_camera_ready',
        'status',
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

    // Helper methods
    public function isOpen()
    {
        return $this->status === 'OPEN';
    }

    public function isSubmissionOpen()
    {
        return $this->deadline_submission >= now();
    }

    public function isReviewOpen()
    {
        return $this->deadline_review >= now();
    }
}
