<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Chạy job xử lý thông báo đã lên lịch mỗi phút
        $schedule->job(new \App\Jobs\ProcessScheduledAnnouncementsJob)->everyMinute();

        // Xử lý email nhắc lịch hội thảo mỗi ngày lúc 7:00 sáng
        $schedule->command('reminders:process-conference')->dailyAt('07:00');

        // Tự động fix missing CHAIR role assignments mỗi ngày lúc 2:00 sáng
        $schedule->command('fix:chair-roles')->dailyAt('02:00');

        // Đảm bảo tính nhất quán của CHAIR roles mỗi 5 phút (safety net)
        $schedule->command('chair:ensure-roles --fix')->everyFiveMinutes();

        // Scheduled Backup
        try {
            $settings = \App\Models\SystemSetting::all()->pluck('value', 'key');
            if (isset($settings['auto_backup']) && $settings['auto_backup'] == '1') {
                $frequency = $settings['backup_frequency'] ?? 'daily';
                $time = $settings['backup_time'] ?? '00:00';

                $command = $schedule->command('backup:run');

                if ($frequency == 'daily') {
                    $command->dailyAt($time);
                } elseif ($frequency == 'weekly') {
                    $command->weekly()->at($time);
                } elseif ($frequency == 'monthly') {
                    $command->monthly()->at($time);
                }
            }
        } catch (\Exception $e) {
            // Ignore DB errors during migration/setup
        }
    }    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
