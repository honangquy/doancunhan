<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'log_type',
        'user_id',
        'action',
        'description',
        'properties',
        'ip_address',
        'user_agent',
        'model_type',
        'model_id',
        'severity'
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constants for log types
    const TYPE_LOGIN = 'LOGIN';
    const TYPE_ACTION = 'ACTION';
    const TYPE_ERROR = 'ERROR';
    const TYPE_SYSTEM = 'SYSTEM';

    // Constants for severity
    const SEVERITY_LOW = 'low';
    const SEVERITY_MEDIUM = 'medium';
    const SEVERITY_HIGH = 'high';
    const SEVERITY_CRITICAL = 'critical';

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'user_id', 'user_id');
    }

    /**
     * Get the user who performed the action (Vietnamese method name)
     */
    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'user_id', 'user_id');
    }

    /**
     * Get the model that was acted upon
     */
    public function subject(): MorphTo
    {
        return $this->morphTo('model');
    }

    /**
     * Scope for filtering by log type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('log_type', $type);
    }

    /**
     * Scope for filtering by severity
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Scope for filtering by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get formatted severity with color
     */
    public function getSeverityColorAttribute()
    {
        return match($this->severity) {
            'low' => 'text-green-800 bg-green-100',
            'medium' => 'text-yellow-800 bg-yellow-100',
            'high' => 'text-orange-800 bg-orange-100',
            'critical' => 'text-red-800 bg-red-100',
            default => 'text-gray-800 bg-gray-100'
        };
    }

    /**
     * Get severity badge color with dot
     */
    public function getSeverityBadgeColor()
    {
        return match($this->severity) {
            'low' => ['bg' => 'bg-green-100 text-green-800', 'dot' => 'bg-green-400'],
            'medium' => ['bg' => 'bg-yellow-100 text-yellow-800', 'dot' => 'bg-yellow-400'],
            'high' => ['bg' => 'bg-orange-100 text-orange-800', 'dot' => 'bg-orange-400'],
            'critical' => ['bg' => 'bg-red-100 text-red-800', 'dot' => 'bg-red-400'],
            default => ['bg' => 'bg-gray-100 text-gray-800', 'dot' => 'bg-gray-400']
        };
    }

    /**
     * Get Vietnamese severity name
     */
    public function getSeverityNameAttribute()
    {
        return match($this->severity) {
            'low' => 'Thấp',
            'medium' => 'Trung bình',
            'high' => 'Cao',
            'critical' => 'Nghiêm trọng',
            default => $this->severity
        };
    }

    /**
     * Get formatted log type with color
     */
    public function getTypeColorAttribute()
    {
        return match($this->log_type) {
            'LOGIN' => 'text-green-800 bg-green-100',
            'ACTION' => 'text-blue-800 bg-blue-100', 
            'CRUD' => 'text-purple-800 bg-purple-100',
            'AUTH' => 'text-indigo-800 bg-indigo-100',
            'ERROR' => 'text-red-800 bg-red-100',
            'SYSTEM' => 'text-gray-800 bg-gray-100',
            'SECURITY' => 'text-orange-800 bg-orange-100',
            'TEST' => 'text-pink-800 bg-pink-100',
            default => 'text-gray-800 bg-gray-100'
        };
    }

    /**
     * Get log type badge color with dot
     */
    public function getTypeBadgeColor()
    {
        return match($this->log_type) {
            'LOGIN' => ['bg' => 'bg-green-100 text-green-800', 'dot' => 'bg-green-400'],
            'ACTION' => ['bg' => 'bg-blue-100 text-blue-800', 'dot' => 'bg-blue-400'],
            'CRUD' => ['bg' => 'bg-purple-100 text-purple-800', 'dot' => 'bg-purple-400'],
            'AUTH' => ['bg' => 'bg-indigo-100 text-indigo-800', 'dot' => 'bg-indigo-400'],
            'ERROR' => ['bg' => 'bg-red-100 text-red-800', 'dot' => 'bg-red-400'],
            'SYSTEM' => ['bg' => 'bg-gray-100 text-gray-800', 'dot' => 'bg-gray-400'],
            'SECURITY' => ['bg' => 'bg-orange-100 text-orange-800', 'dot' => 'bg-orange-400'],
            'TEST' => ['bg' => 'bg-pink-100 text-pink-800', 'dot' => 'bg-pink-400'],
            default => ['bg' => 'bg-gray-100 text-gray-800', 'dot' => 'bg-gray-400']
        };
    }

    /**
     * Get Vietnamese log type name
     */
    public function getTypeNameAttribute()
    {
        return match($this->log_type) {
            'LOGIN' => 'Đăng nhập',
            'ACTION' => 'Thao tác',
            'CRUD' => 'Dữ liệu',
            'AUTH' => 'Xác thực',
            'ERROR' => 'Lỗi',
            'SYSTEM' => 'Hệ thống', 
            'SECURITY' => 'Bảo mật',
            'TEST' => 'Kiểm thử',
            default => $this->log_type
        };
    }

    /**
     * Static method to log activities
     */
    public static function logActivity(array $data)
    {
        return self::create([
            'log_type' => $data['type'] ?? self::TYPE_ACTION,
            'user_id' => $data['user_id'] ?? auth()->id(),
            'action' => $data['action'],
            'description' => $data['description'] ?? null,
            'properties' => $data['properties'] ?? null,
            'ip_address' => $data['ip_address'] ?? request()->ip(),
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            'model_type' => $data['model_type'] ?? null,
            'model_id' => $data['model_id'] ?? null,
            'severity' => $data['severity'] ?? self::SEVERITY_LOW
        ]);
    }
}