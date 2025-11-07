<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConferenceBiddingSetting extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'conference_id',
        'enable_keyword_matching',
        'keyword_similarity_threshold',
        'allow_partial_keyword_match',
        'excluded_keywords'
    ];
    
    protected $casts = [
        'enable_keyword_matching' => 'boolean',
        'allow_partial_keyword_match' => 'boolean',
        'keyword_similarity_threshold' => 'decimal:2',
    ];
    
    // Relationship with conference
    public function conference()
    {
        return $this->belongsTo(Hoithao::class, 'conference_id', 'conference_id');
    }
    
    // Helper method to get excluded keywords as array
    public function getExcludedKeywordsArrayAttribute()
    {
        if (empty($this->excluded_keywords)) {
            return [];
        }
        return array_map('trim', explode(',', $this->excluded_keywords));
    }
    
    // Helper method to set excluded keywords from array
    public function setExcludedKeywordsFromArray($keywords)
    {
        if (empty($keywords) || !is_array($keywords)) {
            $this->excluded_keywords = null;
            return;
        }
        $this->excluded_keywords = implode(', ', array_filter($keywords));
    }
}
