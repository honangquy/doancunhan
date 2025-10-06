<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaiBao extends Model
{
    use HasFactory;

    protected $table = 'BaiBao';
    protected $primaryKey = 'paper_id';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'abstract',
        'keywords',
        'track_id',
        'submitter_id',
        'status',
        'current_version_id',
        'submission_date',
        'final_decision',
        'decision_notes',
        'decision_date',
        'camera_ready_deadline',
    ];

    protected $casts = [
        'submission_date' => 'datetime',
        'decision_date' => 'datetime',
        'camera_ready_deadline' => 'datetime',
    ];

    // Relationships
    public function tieuBan()
    {
        return $this->belongsTo(TieuBan::class, 'track_id', 'track_id');
    }

    public function submitter()
    {
        return $this->belongsTo(\App\Models\NguoiDung::class, 'submitter_id', 'user_id');
    }

    public function authors()
    {
        return $this->belongsToMany(
            \App\Models\NguoiDung::class,
            'TacGiaBaiBao',
            'paper_id',
            'user_id'
        )->withPivot('author_order', 'is_contact', 'affiliation');
    }

    public function currentVersion()
    {
        return $this->belongsTo(PhienBanBaiBao::class, 'current_version_id', 'version_id');
    }

    public function versions()
    {
        return $this->hasMany(PhienBanBaiBao::class, 'paper_id', 'paper_id')
            ->orderBy('version_no', 'desc');
    }

    public function biddings()
    {
        return $this->hasMany(Bidding::class, 'paper_id', 'paper_id');
    }

    public function assignments()
    {
        return $this->hasMany(PhanCongPhanBien::class, 'paper_id', 'paper_id');
    }

    public function reviews()
    {
        return $this->hasMany(PhanBien::class, 'paper_id', 'paper_id');
    }

    public function cois()
    {
        return $this->hasMany(COI::class, 'paper_id', 'paper_id');
    }
}
