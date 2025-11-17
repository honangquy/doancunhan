<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;

    protected $table = 'news';
    protected $primaryKey = 'news_id';

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'cover_image',
        'category',
        'conference_id',
        'is_featured',
        'status',
        'published_at',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
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
                while (static::where('slug', $news->slug)->where('news_id', '!=', $news->news_id)->exists()) {
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
     * Relationship: News belongs to a creator (NguoiDung)
     */
    public function creator()
    {
        return $this->belongsTo(NguoiDung::class, 'created_by', 'user_id');
    }

    /**
     * Relationship: News belongs to an updater (NguoiDung)
     */
    public function updater()
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
     * Scope: Get featured news
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
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
        return asset('images/default-news-cover.jpg');
    }

    /**
     * Get excerpt from content
     */
    public function getExcerptAttribute()
    {
        if ($this->summary) {
            return $this->summary;
        }
        return Str::limit(strip_tags($this->content), 200);
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
            'DRAFT' => 'gray',
            'PENDING' => 'yellow',
            'PUBLISHED' => 'green',
            'ARCHIVED' => 'red'
        ];
        return $colors[$this->status] ?? 'gray';
    }
}
