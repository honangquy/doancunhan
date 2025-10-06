<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XuLyCOI extends Model
{
    use HasFactory;

    protected $table = 'XuLyCOI';
    protected $primaryKey = 'decision_id';
    public $timestamps = false;

    protected $fillable = [
        'coi_id',
        'chair_id',
        'decision',
        'note',
        'decided_at',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    // Relationships
    public function coi()
    {
        return $this->belongsTo(COI::class, 'coi_id', 'coi_id');
    }

    public function chair()
    {
        return $this->belongsTo(\App\Models\NguoiDung::class, 'chair_id', 'user_id');
    }
}
