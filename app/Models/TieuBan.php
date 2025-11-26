<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TieuBan extends Model
{
    use HasFactory;

    protected $table = 'tieuban';
    protected $primaryKey = 'track_id';
    public $timestamps = false;

    protected $fillable = [
        'conference_id',
        'title',
        'committee_name',
        'description',
        'chair_id',
        'created_at',
    ];

    // Relationships
    public function hoiThao()
    {
        return $this->belongsTo(HoiThao::class, 'conference_id', 'conference_id');
    }

    public function chair()
    {
        return $this->belongsTo(NguoiDung::class, 'chair_id', 'user_id');
    }

    public function baiBaos()
    {
        return $this->hasMany(BaiBao::class, 'track_id', 'track_id');
    }

    public function chuyenMons()
    {
        return $this->hasMany(ChuyenMonReviewer::class, 'track_id', 'track_id');
    }
}
