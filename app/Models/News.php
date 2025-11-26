<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;

    protected $table = 'news';
    protected $primaryKey = 'news_id'; // Changed from 'id'

    protected $fillable = [
        'title',
        'slug',
        'category', // Changed from 'type'
        'conference_id',
        'summary',
        'content',
        'cover_image', // Changed from 'thumbnail_path'
        'attachment_path',
        'images',
        'is_featured',
        'status',
        'published_at',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'images' => 'array',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from title
        static::creating(function ($news) {
            if (empty($news->slug)) {
                $news->slug = Str::slug($news->title);

                // Ensure unique slug
                $originalSlug = $news->slug;
                $count = 1;
                while (static::where('slug', $news->slug)->exists()) {
                    $news->slug = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            // Set published_at if status is PUBLISHED and not set
            if ($news->status === 'PUBLISHED' && !$news->published_at) {
                $news->published_at = now();
            }
        });

        static::updating(function ($news) {
            // Update slug if title changed
            if ($news->isDirty('title') && empty($news->slug)) {
                $news->slug = Str::slug($news->title);

                $originalSlug = $news->slug;
                $count = 1;
                while (static::where('slug', $news->slug)->where($news->getKeyName(), '!=', $news->getKey())->exists()) {
                    $news->slug = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            // Set published_at when status changes to PUBLISHED
            if ($news->isDirty('status') && $news->status === 'PUBLISHED' && !$news->published_at) {
                $news->published_at = now();
            }
        });
    }

    /**
     * Relationship: News belongs to a Conference
     */
    public function conference()
    {
        return $this->belongsTo(HoiThao::class, 'conference_id', 'conference_id');
    }

    /**
     * Relationship: News belongs to a creator (User)
     */
    public function createdBy()
    {
        return $this->belongsTo(NguoiDung::class, 'created_by', 'user_id');
    }

    /**
     * Relationship: News belongs to an updater (User)
     */
    public function updatedBy()
    {
        return $this->belongsTo(NguoiDung::class, 'updated_by', 'user_id');
    }

    /**
     * Scope: Get published news only
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'PUBLISHED')
                     ->where(function($q) {
                         $q->whereNull('published_at')
                           ->orWhere('published_at', '<=', now());
                     });
    }

    /**
     * Scope: Filter by category
     */
    public function scopeCategory($query, $category)
    {
        if ($category) {
            return $query->where('category', $category);
        }
        return $query;
    }

    /**
     * Scope: Filter by conference
     */
    public function scopeConference($query, $conferenceId)
    {
        if ($conferenceId) {
            return $query->where('conference_id', $conferenceId);
        }
        return $query;
    }

    /**
     * Get cover image URL
     */
    public function getCoverImageUrlAttribute()
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        return asset('images/default-news.jpg');
    }

    /**
     * Get category display name
     */
    public function getCategoryNameAttribute()
    {
        $categories = [
            'NEWS' => 'Tin tức',
            'ANNOUNCEMENT' => 'Thông báo',
            'EVENT' => 'Sự kiện',
            'GUIDE' => 'Hướng dẫn'
        ];
        return $categories[$this->category] ?? $this->category;
    }

    /**
     * Get status display name
     */
    public function getStatusNameAttribute()
    {
        $statuses = [
            'DRAFT' => 'Bản nháp',
            'PENDING' => 'Chờ duyệt',
            'PUBLISHED' => 'Đã xuất bản',
            'ARCHIVED' => 'Lưu trữ'
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'DRAFT' => 'secondary',
            'PENDING' => 'warning',
            'PUBLISHED' => 'success',
            'ARCHIVED' => 'dark'
        ];
        return $colors[$this->status] ?? 'secondary';
    }
}
