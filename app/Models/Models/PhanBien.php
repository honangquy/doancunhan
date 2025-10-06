<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhanBien extends Model
{
    use HasFactory;

    protected $table = 'PhanBien';
    protected $primaryKey = 'review_id';
    public $timestamps = false;

    protected $fillable = [
        'assignment_id',
        'recommendation_code',
        'score',
        'comment_author',
        'comment_chair',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    // Relationships
    public function assignment()
    {
        return $this->belongsTo(PhanCongPhanBien::class, 'assignment_id', 'assignment_id');
    }

    public function recommendation()
    {
        return $this->belongsTo(LoaiKhuyenNghi::class, 'recommendation_code', 'recommendation_code');
    }
}
