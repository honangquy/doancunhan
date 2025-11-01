<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityLog;
use App\Models\NguoiDung;
use Carbon\Carbon;

class ActivityLogSeeder extends Seeder
{
    public function run()
    {
        // Lấy một số user để test
        $users = NguoiDung::limit(5)->get();
        
        if ($users->isEmpty()) {
            return; // Không có user nào
        }

        $logTypes = ['LOGIN', 'ACTION', 'ERROR', 'SYSTEM'];
        $severities = ['low', 'medium', 'high', 'critical'];
        $actions = [
            'Đăng nhập thành công',
            'Tạo mới hội thảo',
            'Cập nhật thông tin',
            'Xóa bài báo',
            'Gửi đánh giá',
            'Lỗi kết nối database',
            'Backup dữ liệu',
            'Khôi phục hệ thống'
        ];
        
        $descriptions = [
            'Người dùng đăng nhập từ trình duyệt Chrome',
            'Tạo mới hội thảo khoa học quốc tế',
            'Cập nhật thông tin cá nhân của người dùng',
            'Xóa bài báo không phù hợp',
            'Gửi đánh giá cho bài báo #123',
            'Lỗi kết nối database trong quá trình truy vấn',
            'Thực hiện backup dữ liệu hàng ngày',
            'Khôi phục hệ thống sau sự cố'
        ];

        // Tạo 50 logs trong 7 ngày qua
        for ($i = 0; $i < 50; $i++) {
            $user = $users->random();
            $logType = $logTypes[array_rand($logTypes)];
            $severity = $severities[array_rand($severities)];
            $action = $actions[array_rand($actions)];
            $description = $descriptions[array_rand($descriptions)];
            
            ActivityLog::create([
                'log_type' => $logType,
                'user_id' => $user->user_id,
                'action' => $action,
                'description' => $description,
                'properties' => [
                    'browser' => 'Chrome',
                    'os' => 'Windows 10',
                    'page' => '/admin/dashboard'
                ],
                'ip_address' => '192.168.1.' . rand(1, 254),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'severity' => $severity,
                'created_at' => Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23)),
                'updated_at' => Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23))
            ]);
        }
        
        // Tạo một số logs hệ thống không có user
        for ($i = 0; $i < 10; $i++) {
            ActivityLog::create([
                'log_type' => 'SYSTEM',
                'user_id' => null,
                'action' => 'Backup tự động',
                'description' => 'Hệ thống thực hiện backup dữ liệu tự động',
                'properties' => [
                    'backup_size' => rand(100, 1000) . 'MB',
                    'backup_path' => '/backups/' . date('Y-m-d') . '.sql'
                ],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'System Cron Job',
                'severity' => 'low',
                'created_at' => Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23)),
                'updated_at' => Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23))
            ]);
        }

        $this->command->info('Created ' . (50 + 10) . ' activity logs');
    }
}
