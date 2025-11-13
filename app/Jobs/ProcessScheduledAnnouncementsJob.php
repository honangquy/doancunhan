<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProcessScheduledAnnouncementsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Lấy các thông báo đã lên lịch và đến hạn gửi
            $scheduledAnnouncements = DB::table('thongbao')
                ->where('status', 'SCHEDULED')
                ->where('scheduled_at', '<=', Carbon::now())
                ->whereNull('sent_at')
                ->get();

            Log::info("Found {$scheduledAnnouncements->count()} scheduled announcements to process");

            foreach ($scheduledAnnouncements as $announcement) {
                try {
                    // Dispatch job gửi thông báo
                    SendAnnouncementJob::dispatch(
                        $announcement->announcement_id,
                        $announcement->channels ? json_decode($announcement->channels, true) : ['EMAIL', 'SYSTEM']
                    );

                    // Cập nhật trạng thái thành SENT và ghi thời gian gửi
                    DB::table('thongbao')
                        ->where('announcement_id', $announcement->announcement_id)
                        ->update([
                            'status' => 'SENT',
                            'sent_at' => Carbon::now()
                        ]);

                    Log::info("Sent announcement #{$announcement->announcement_id}: {$announcement->title}");
                    
                } catch (\Exception $e) {
                    // Đánh dấu thất bại nếu có lỗi
                    DB::table('thongbao')
                        ->where('announcement_id', $announcement->announcement_id)
                        ->update(['status' => 'FAILED']);
                    
                    Log::error("Failed to send announcement #{$announcement->announcement_id}: " . $e->getMessage());
                }
            }

            Log::info("Processed {$scheduledAnnouncements->count()} scheduled announcements");
            
        } catch (\Exception $e) {
            Log::error('ProcessScheduledAnnouncementsJob failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
