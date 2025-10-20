<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('log_type') && $request->log_type !== 'all') {
            $query->where('log_type', $request->log_type);
        }

        if ($request->filled('severity') && $request->severity !== 'all') {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('user_id') && $request->user_id !== 'all') {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('ip_address', 'LIKE', "%{$search}%");
            });
        }

        $logs = $query->paginate(20);
        $logs->appends($request->all());

        // Get filter options
        $logTypes = ActivityLog::select('log_type')
            ->distinct()
            ->pluck('log_type');

        $users = DB::table('NguoiDung')
            ->select('user_id', 'full_name', 'email')
            ->whereIn('user_id', ActivityLog::whereNotNull('user_id')->pluck('user_id')->unique())
            ->get();

        return view('admin.logs', compact('logs', 'logTypes', 'users'))
            ->with('title', 'Nhật ký hệ thống');
    }

    public function show(ActivityLog $log): JsonResponse
    {
        $log->load('user');
        
        return response()->json([
            'success' => true,
            'log' => [
                'id' => $log->id,
                'type' => $log->type_name,
                'type_color' => $log->type_color,
                'action' => $log->action,
                'description' => $log->description,
                'user' => $log->user ? [
                    'name' => $log->user->full_name ?? $log->user->email,
                    'email' => $log->user->email
                ] : null,
                'ip_address' => $log->ip_address,
                'user_agent' => $log->user_agent,
                'severity' => $log->severity,
                'severity_color' => $log->severity_color,
                'properties' => $log->properties,
                'created_at' => $log->created_at->format('d/m/Y H:i:s'),
                'model_info' => $log->model_type && $log->model_id ? [
                    'type' => class_basename($log->model_type),
                    'id' => $log->model_id
                ] : null
            ]
        ]);
    }

    public function export(Request $request)
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('log_type') && $request->log_type !== 'all') {
            $query->where('log_type', $request->log_type);
        }

        if ($request->filled('severity') && $request->severity !== 'all') {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->get();

        $filename = 'activity_logs_' . now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ];

        return response()->stream(function () use ($logs) {
            $handle = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($handle, "\xEF\xBB\xBF");
            
            // CSV Header
            fputcsv($handle, [
                'ID',
                'Thời gian',
                'Loại',
                'Người dùng',
                'Hành động',
                'Mô tả',
                'IP',
                'Mức độ nghiêm trọng'
            ]);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->id,
                    $log->created_at->format('d/m/Y H:i:s'),
                    $log->type_name,
                    $log->user ? ($log->user->full_name ?? $log->user->email) : 'Hệ thống',
                    $log->action,
                    $log->description,
                    $log->ip_address,
                    $log->severity
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function clear(Request $request): JsonResponse
    {
        try {
            $count = ActivityLog::count();
            
            // Keep logs from last 7 days and high/critical severity logs
            $keepDate = Carbon::now()->subDays(7);
            
            $deletedCount = ActivityLog::where('created_at', '<', $keepDate)
                ->whereNotIn('severity', ['high', 'critical'])
                ->delete();

            // Log this action
            ActivityLog::logActivity([
                'type' => ActivityLog::TYPE_SYSTEM,
                'action' => 'Xóa nhật ký hệ thống',
                'description' => "Đã xóa {$deletedCount} bản ghi nhật ký cũ",
                'severity' => ActivityLog::SEVERITY_MEDIUM,
                'properties' => [
                    'total_before' => $count,
                    'deleted_count' => $deletedCount,
                    'kept_recent_days' => 7
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => "Đã xóa {$deletedCount} bản ghi nhật ký cũ. Giữ lại nhật ký 7 ngày gần đây và các nhật ký mức độ cao.",
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            ActivityLog::logActivity([
                'type' => ActivityLog::TYPE_ERROR,
                'action' => 'Lỗi xóa nhật ký',
                'description' => 'Có lỗi xảy ra khi xóa nhật ký: ' . $e->getMessage(),
                'severity' => ActivityLog::SEVERITY_HIGH
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa nhật ký'
            ], 500);
        }
    }

    public function stats(): JsonResponse
    {
        $stats = [
            'total_logs' => ActivityLog::count(),
            'today_logs' => ActivityLog::whereDate('created_at', today())->count(),
            'this_week_logs' => ActivityLog::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count(),
            'error_logs_today' => ActivityLog::whereDate('created_at', today())
                ->where('log_type', ActivityLog::TYPE_ERROR)
                ->count(),
            'critical_logs' => ActivityLog::where('severity', ActivityLog::SEVERITY_CRITICAL)
                ->whereDate('created_at', '>=', Carbon::now()->subDays(7))
                ->count(),
            'by_type' => ActivityLog::select('log_type', DB::raw('COUNT(*) as count'))
                ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('log_type')
                ->pluck('count', 'log_type'),
            'by_severity' => ActivityLog::select('severity', DB::raw('COUNT(*) as count'))
                ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('severity')
                ->pluck('count', 'severity')
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}