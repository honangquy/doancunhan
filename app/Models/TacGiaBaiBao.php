<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TacGiaBaiBao extends Pivot
{
    protected $table = 'TacGiaBaiBao';

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'paper_id',
        'user_id',
        'author_order',
        'is_contact',
        'organization'
    ];

    protected $casts = [
        'is_contact' => 'boolean',
        'author_order' => 'integer',
    ];

    /**
     * Get the paper
     */
    public function paper()
    {
        return $this->belongsTo(BaiBao::class, 'paper_id', 'paper_id');
    }

    /**
     * Get the author (user)
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
