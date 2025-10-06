<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Khoa extends Model
{
    use HasFactory;

    protected $table = 'Khoa';
    protected $primaryKey = 'faculty_id';
    public $timestamps = false;

    protected $fillable = [
        'faculty_code',
        'faculty_name',
    ];

    // Relationships
    public function nguoiDungs()
    {
        return $this->hasMany(NguoiDung::class, 'faculty_id', 'faculty_id');
    }

    public function hoiThaos()
    {
        return $this->hasMany(HoiThao::class, 'faculty_id', 'faculty_id');
    }
}
