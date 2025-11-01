<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewerBidding extends Model
{
    use HasFactory;

    protected $table = 'reviewer_bidding';

    protected $fillable = [
        'user_id',
        'paper_id',
        'conference_id',
        'bidding_value',
        'coi',
        'coi_reason',
        'note'
    ];

    protected $casts = [
        'coi' => 'boolean',
        'bidding_value' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Bidding value constants
    const BID_NO_BID = 0;
    const BID_WILLING = 1;
    const BID_ABLE = 2;
    const BID_EAGER = 3;

    public static function getBiddingLabels()
    {
        return [
            self::BID_NO_BID => 'Không muốn',
            self::BID_WILLING => 'Sẵn sàng',
            self::BID_ABLE => 'Có thể',
            self::BID_EAGER => 'Rất muốn'
        ];
    }

    public static function getBiddingColors()
    {
        return [
            self::BID_NO_BID => 'bg-gray-100 text-gray-800',
            self::BID_WILLING => 'bg-yellow-100 text-yellow-800',
            self::BID_ABLE => 'bg-blue-100 text-blue-800',
            self::BID_EAGER => 'bg-green-100 text-green-800'
        ];
    }

    public function getBiddingLabelAttribute()
    {
        return self::getBiddingLabels()[$this->bidding_value] ?? 'Không xác định';
    }

    public function getBiddingColorAttribute()
    {
        return self::getBiddingColors()[$this->bidding_value] ?? 'bg-gray-100 text-gray-800';
    }

    // Relationships
    public function reviewer()
    {
        return $this->belongsTo(NguoiDung::class, 'user_id', 'user_id');
    }

    public function paper()
    {
        return $this->belongsTo(BaiBao::class, 'paper_id', 'paper_id');
    }

    public function conference()
    {
        return $this->belongsTo(HoiThao::class, 'conference_id', 'conference_id');
    }

    // Scopes
    public function scopeForConference($query, $conferenceId)
    {
        return $query->where('conference_id', $conferenceId);
    }

    public function scopeForReviewer($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeWithCOI($query)
    {
        return $query->where('coi', true);
    }

    public function scopeWithoutCOI($query)
    {
        return $query->where('coi', false);
    }

    public function scopeByBidLevel($query, $minBid = 1)
    {
        return $query->where('bidding_value', '>=', $minBid);
    }

    // Helper methods
    public function hasCOI()
    {
        return $this->coi === true;
    }

    public function isEagerToBid()
    {
        return $this->bidding_value === self::BID_EAGER;
    }

    public function canBeAssigned()
    {
        return !$this->hasCOI() && $this->bidding_value > self::BID_NO_BID;
    }
}