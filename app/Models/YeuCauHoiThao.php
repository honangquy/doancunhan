<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YeuCauHoiThao extends Model
{
    use HasFactory;

    protected $table = 'yeucauhoithao';
    protected $primaryKey = 'request_id';
    public $timestamps = false;

    protected $fillable = [
        'conference_id',
        'requester_id',
        'request_date',
        'admin_id',
        'approval_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'request_date' => 'datetime',
        'approval_date' => 'datetime',
    ];

    // Relationships
    public function hoiThao()
    {
        return $this->belongsTo(HoiThao::class, 'conference_id', 'conference_id');
    }

    public function requester()
    {
        return $this->belongsTo(NguoiDung::class, 'requester_id', 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(NguoiDung::class, 'admin_id', 'user_id');
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'PENDING';
    }

    public function isApproved()
    {
        return $this->status === 'APPROVED';
    }

    public function isRejected()
    {
        return $this->status === 'REJECTED';
    }
}

