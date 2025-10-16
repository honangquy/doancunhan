<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongBao extends Model
{
    use HasFactory;

    protected $table = 'thongbao';
    protected $primaryKey = 'notification_id';
    public $timestamps = false;

    protected $fillable = [
        'conference_id',
        'title',
        'content',
        'created_at',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
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