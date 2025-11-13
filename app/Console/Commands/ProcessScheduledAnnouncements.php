<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ProcessScheduledAnnouncementsJob;

class ProcessScheduledAnnouncements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'announcements:process-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xử lý và gửi các thông báo đã lên lịch';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Đang xử lý thông báo đã lên lịch...');
        
        try {
            ProcessScheduledAnnouncementsJob::dispatch();
            $this->info('✓ Job đã được dispatch thành công!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('✗ Lỗi: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
