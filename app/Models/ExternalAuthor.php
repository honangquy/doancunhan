<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalAuthor extends Model
{
    use HasFactory;

    protected $fillable = [
        'paper_id',
        'name',
        'email', 
        'organization',
        'author_order',
        'is_contact'
    ];

    protected $casts = [
        'is_contact' => 'boolean'
    ];
}
