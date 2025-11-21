<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongBao extends Model
{
    use HasFactory;

    protected $table = 'thongbao';
    protected $primaryKey = 'announcement_id';
    public $timestamps = true; // Enable Laravel timestamps

    protected $fillable = [
        'conference_id',
        'title',
        'content',
        'audience',
        'scheduled_at',
        'sent_at',
        'status',
        'channels',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'channels' => 'array', // JSON array
    ];

    // Relationships
    public function hoiThao()
    {
        return $this->belongsTo(HoiThao::class, 'conference_id', 'conference_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(NguoiDung::class, 'created_by', 'user_id');
    }
}