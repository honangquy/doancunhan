<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThemVienBoSung extends Model
{
    use HasFactory;

    protected $table = 'themvienbosungng';
    protected $primaryKey = 'co_chair_id';
    public $timestamps = false;

    protected $fillable = [
        'request_id',
        'fullname',
        'email',
        'affiliation',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationships
    public function conferenceRequest()
    {
        return $this->belongsTo(YeuCauHoiThao::class, 'request_id', 'request_id');
    }
}
