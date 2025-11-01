<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Log user activity
     */
    protected function logActivity(string $action, string $description, array $properties = [], string $logType = 'ACTION', string $severity = 'low')
    {
        $request = request();
        
        ActivityLog::create([
            'log_type' => $logType,
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'properties' => array_merge($properties, [
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'route' => $request->route() ? $request->route()->getName() : null,
                'method' => $request->method()
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'severity' => $severity
        ]);
    }

    /**
     * Log CRUD operations
     */
    protected function logCrudOperation(string $operation, string $model, $modelId = null, array $data = [])
    {
        $actions = [
            'create' => 'Tạo mới',
            'update' => 'Cập nhật',
            'delete' => 'Xóa',
            'view' => 'Xem',
            'list' => 'Danh sách'
        ];

        $action = $actions[$operation] ?? $operation;
        $description = "{$action} {$model}" . ($modelId ? " (ID: {$modelId})" : "");
        
        $this->logActivity(
            $action . ' ' . $model,
            $description,
            array_merge($data, [
                'model' => $model,
                'model_id' => $modelId,
                'operation' => $operation
            ]),
            'CRUD'
        );
    }

    /**
     * Log system events
     */
    protected function logSystemEvent(string $event, string $description, array $data = [], string $severity = 'low')
    {
        $this->logActivity(
            $event,
            $description,
            $data,
            'SYSTEM',
            $severity
        );
    }

    /**
     * Log error events
     */
    protected function logError(string $error, string $description, array $data = [])
    {
        $this->logActivity(
            'Lỗi hệ thống',
            $description,
            array_merge($data, ['error' => $error]),
            'ERROR',
            'critical'
        );
    }

    /**
     * Log security events
     */
    protected function logSecurityEvent(string $event, string $description, array $data = [])
    {
        $this->logActivity(
            $event,
            $description,
            $data,
            'SECURITY',
            'high'
        );
    }
}