<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class COI extends Model
{
    use HasFactory;

    protected $table = 'COI';
    protected $primaryKey = 'coi_id';
    public $timestamps = false;

    protected $fillable = [
        'paper_id',
        'reviewer_id',
        'coi_code',
        'source_type',
        'evidence',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
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

    public function coiType()
    {
        return $this->belongsTo(LoaiCOI::class, 'coi_code', 'coi_code');
    }

    public function decision()
    {
        return $this->hasOne(XuLyCOI::class, 'coi_id', 'coi_id');
    }
}
