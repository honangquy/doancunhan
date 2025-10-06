<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bidding extends Model
{
    use HasFactory;

    protected $table = 'Bidding';
    protected $primaryKey = ['user_id', 'paper_id'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'paper_id',
        'bidding_code',
        'note',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationships
    public function reviewer()
    {
        return $this->belongsTo(\App\Models\NguoiDung::class, 'user_id', 'user_id');
    }

    public function paper()
    {
        return $this->belongsTo(BaiBao::class, 'paper_id', 'paper_id');
    }

    public function biddingValue()
    {
        return $this->belongsTo(\App\Models\GiaTriBidding::class, 'bidding_code', 'bidding_code');
    }
}
