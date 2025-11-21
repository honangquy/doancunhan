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
