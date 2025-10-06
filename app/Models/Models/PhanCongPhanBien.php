<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhanCongPhanBien extends Model
{
    use HasFactory;

    protected $table = 'PhanCongPhanBien';
    protected $primaryKey = 'assignment_id';
    public $timestamps = false;

    protected $fillable = [
        'paper_id',
        'reviewer_id',
        'assigned_by',
        'assigned_at',
        'deadline',
        'status_code',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'deadline' => 'datetime',
    ];

    // Relationships
    public function paper()
    {
        return $this->belongsTo(BaiBao::class, 'paper_id', 'paper_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(\App\Models\NguoiDung::class, 'reviewer_id', 'user_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(\App\Models\NguoiDung::class, 'assigned_by', 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(PhanBien::class, 'assignment_id', 'assignment_id');
    }
}
