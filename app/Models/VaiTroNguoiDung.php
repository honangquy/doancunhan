<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaiTroNguoiDung extends Model
{
    use HasFactory;

    protected $table = 'vaitronguoidung';
    protected $primaryKey = 'user_role_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'role_code',
        'conference_id',
    ];

    // Relationships
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'user_id', 'user_id');
    }

    public function hoiThao()
    {
        return $this->belongsTo(HoiThao::class, 'conference_id', 'conference_id');
    }

    public function loaiVaiTro()
    {
        return $this->belongsTo(LoaiVaiTro::class, 'role_code', 'role_code');
    }
}
