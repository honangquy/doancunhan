<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Conference;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConferenceReminderController extends Controller
{
    /**
     * Hiển thị trang quản lý reminder cho Chair
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        
        // Lấy danh sách hội thảo mà user là Chair (qua chair_id)
        $conferences = DB::table('hoithao')
            ->where('chair_id', $userId)
            ->select(
                'conference_id',
                'title',
                'status',
                'deadline_submission',
                'deadline_review',
                'deadline_camera_ready',
                'start_date',
                'end_date'
            )
            ->orderBy('start_date', 'desc')
            ->get();
        
        // Lấy tất cả templates reminder
        $templates = NotificationTemplate::whereIn('template_code', [
            'SUBMISSION_REMINDER_7D',
            'SUBMISSION_REMINDER_3D',
            'REVIEW_REMINDER_7D',
            'REVIEW_REMINDER_3D',
            'CAMERA_READY_REMINDER_3D',
            'CONFERENCE_START_7D',
            'CONFERENCE_END_1D'
        ])->get()->keyBy('template_code');
        
        // Đếm số lượng reminder đã gửi (từ logs)
        $reminderStats = $this->getReminderStats($conferences->pluck('conference_id'));
        
        return view('chair.reminders.index', [
            'conferences' => $conferences,
            'templates' => $templates,
            'reminderStats' => $reminderStats
        ]);
    }
    
    /**
     * Xem logs reminder của một hội thảo cụ thể
     */
    public function logs(Request $request, $conferenceId)
    {
        // Kiểm tra quyền: User phải là Chair của hội thảo này (qua chair_id)
        $isChair = DB::table('hoithao')
            ->where('conference_id', $conferenceId)
            ->where('chair_id', Auth::id())
            ->exists();
        
        if (!$isChair) {
            abort(403, 'Bạn không có quyền xem logs của hội thảo này.');
        }
        
        // Lấy thông tin hội thảo
        $conference = DB::table('hoithao')
            ->where('conference_id', $conferenceId)
            ->first();
        
        if (!$conference) {
            abort(404, 'Không tìm thấy hội thảo.');
        }
        
        // Parse logs từ file laravel.log
        $logs = $this->parseReminderLogs($conferenceId);
        
        return view('chair.reminders.logs', [
            'conference' => $conference,
            'logs' => $logs
        ]);
    }
    
    /**
     * Test gửi reminder ngay lập tức (for debugging)
     */
    public function testSend(Request $request)
    {
        // Không cần kiểm tra conferenceId vì test sẽ chạy cho tất cả conferences
        // Chỉ cần kiểm tra user có role CHAIR không
        $isChair = DB::table('hoithao')
            ->where('chair_id', Auth::id())
            ->exists();
        
        if (!$isChair) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        try {
            // Chạy command ngay lập tức
            \Artisan::call('reminders:process-conference');
            $output = \Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'Đã chạy lệnh gửi reminder thành công',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Lấy thống kê số lượng reminder đã gửi cho các hội thảo
     */
    private function getReminderStats($conferenceIds)
    {
        if ($conferenceIds->isEmpty()) {
            return collect();
        }
        
        $logFile = storage_path('logs/laravel.log');
        
        if (!file_exists($logFile)) {
            return collect();
        }
        
        $stats = [];
        foreach ($conferenceIds as $confId) {
            $stats[$confId] = [
                'total' => 0,
                'by_type' => []
            ];
        }
        
        // Đọc 10000 dòng cuối của log file
        $command = "tail -n 10000 " . escapeshellarg($logFile) . " | grep 'Conference reminder queued'";
        exec($command, $output);
        
        foreach ($output as $line) {
            // Parse JSON từ log line
            if (preg_match('/\{.*\}/', $line, $matches)) {
                $data = json_decode($matches[0], true);
                if ($data && isset($data['conference_id'])) {
                    $confId = $data['conference_id'];
                    if (in_array($confId, $conferenceIds->toArray())) {
                        $stats[$confId]['total']++;
                        
                        $eventType = $data['event_type'] ?? 'unknown';
                        if (!isset($stats[$confId]['by_type'][$eventType])) {
                            $stats[$confId]['by_type'][$eventType] = 0;
                        }
                        $stats[$confId]['by_type'][$eventType]++;
                    }
                }
            }
        }
        
        return collect($stats);
    }
    
    /**
     * Parse logs reminder từ file log
     */
    private function parseReminderLogs($conferenceId, $limit = 100)
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (!file_exists($logFile)) {
            return collect();
        }
        
        // Đọc 10000 dòng cuối của log file
        $command = "tail -n 10000 " . escapeshellarg($logFile) . " | grep 'Conference reminder queued'";
        exec($command, $output);
        
        $logs = [];
        foreach ($output as $line) {
            // Parse timestamp và JSON data
            if (preg_match('/\[(.*?)\].*Conference reminder queued.*\{.*\}/', $line, $matches)) {
                $timestamp = $matches[1] ?? null;
                
                // Extract JSON
                if (preg_match('/\{.*\}/', $line, $jsonMatch)) {
                    $data = json_decode($jsonMatch[0], true);
                    
                    if ($data && isset($data['conference_id']) && $data['conference_id'] == $conferenceId) {
                        $logs[] = [
                            'timestamp' => $timestamp,
                            'recipient' => $data['recipient'] ?? 'N/A',
                            'event_type' => $data['event_type'] ?? 'N/A',
                            'template_code' => $data['template_code'] ?? 'N/A',
                            'data' => $data
                        ];
                    }
                }
            }
        }
        
        // Sắp xếp theo timestamp mới nhất
        usort($logs, function($a, $b) {
            return strcmp($b['timestamp'], $a['timestamp']);
        });
        
        // Giới hạn số lượng
        return collect(array_slice($logs, 0, $limit));
    }
}
