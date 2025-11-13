<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendAnnouncementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $announcementId;
    protected $channels;

    /**
     * Create a new job instance.
     *
     * @param int $announcementId
     * @param array $channels
     * @return void
     */
    public function __construct($announcementId, $channels = ['EMAIL', 'SYSTEM'])
    {
        $this->announcementId = $announcementId;
        $this->channels = $channels;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Lấy thông tin thông báo
            $announcement = DB::table('thongbao')
                ->where('announcement_id', $this->announcementId)
                ->first();

            if (!$announcement) {
                Log::error("Announcement #{$this->announcementId} not found");
                return;
            }

            // Lấy danh sách người nhận
            $recipients = $this->getRecipients($announcement->audience, $announcement->conference_id);

            if ($recipients->isEmpty()) {
                Log::warning("No recipients found for announcement #{$this->announcementId}");
                return;
            }

            $conference = DB::table('hoithao')
                ->where('conference_id', $announcement->conference_id)
                ->first();

            $sentCount = 0;
            $systemNotifCount = 0;

            // Gửi email nếu channel EMAIL được chọn
            if (in_array('EMAIL', $this->channels)) {
                foreach ($recipients as $recipient) {
                    try {
                        Mail::send('emails.announcement', [
                            'title' => $announcement->title,
                            'body' => $announcement->content,
                            'recipient_name' => $recipient->full_name,
                            'conference_name' => $conference->title ?? ''
                        ], function($message) use ($recipient, $announcement) {
                            $message->to($recipient->email, $recipient->full_name)
                                    ->subject($announcement->title);
                        });
                        
                        $sentCount++;
                    } catch (\Exception $e) {
                        Log::error("Failed to send email to {$recipient->email}: " . $e->getMessage());
                    }
                }
            }

            // Lưu thông báo hệ thống nếu channel SYSTEM được chọn
            if (in_array('SYSTEM', $this->channels)) {
                foreach ($recipients as $recipient) {
                    try {
                        DB::table('user_notifications')->insert([
                            'user_id' => $recipient->user_id,
                            'conference_id' => $announcement->conference_id,
                            'announcement_id' => $announcement->announcement_id,
                            'type' => 'ANNOUNCEMENT',
                            'title' => $announcement->title,
                            'message' => $announcement->content,
                            'is_read' => false,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        
                        $systemNotifCount++;
                    } catch (\Exception $e) {
                        Log::error("Failed to create system notification for user {$recipient->user_id}: " . $e->getMessage());
                    }
                }
            }

            Log::info("Announcement #{$this->announcementId} sent", [
                'recipients' => $recipients->count(),
                'sent_emails' => $sentCount,
                'system_notifications' => $systemNotifCount
            ]);

        } catch (\Exception $e) {
            Log::error("SendAnnouncementJob failed for announcement #{$this->announcementId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Lấy danh sách người nhận theo đối tượng
     *
     * @param string $audience
     * @param int $conferenceId
     * @return \Illuminate\Support\Collection
     */
    private function getRecipients($audience, $conferenceId)
    {
        switch ($audience) {
            case 'ALL':
                // Lấy TẤT CẢ thành viên đã tham gia hội thảo (join_requests APPROVED)
                return DB::table('nguoidung as u')
                    ->join('join_requests as jr', 'jr.user_id', '=', 'u.user_id')
                    ->where('jr.conference_id', $conferenceId)
                    ->where('jr.status', 'APPROVED')
                    ->select('u.user_id', 'u.full_name', 'u.email')
                    ->distinct()
                    ->get();
                
            case 'AUTHORS':
                // Lấy tác giả thông qua bảng baibao
                return DB::table('nguoidung as u')
                    ->join('baibao as bb', 'bb.submitter_id', '=', 'u.user_id')
                    ->where('bb.conference_id', $conferenceId)
                    ->select('u.user_id', 'u.full_name', 'u.email')
                    ->distinct()
                    ->get();
                
            case 'REVIEWERS':
                // Lấy phản biện từ join_requests với role REVIEWER
                return DB::table('nguoidung as u')
                    ->join('join_requests as jr', 'jr.user_id', '=', 'u.user_id')
                    ->where('jr.conference_id', $conferenceId)
                    ->where('jr.status', 'APPROVED')
                    ->where('jr.role', 'REVIEWER')
                    ->select('u.user_id', 'u.full_name', 'u.email')
                    ->distinct()
                    ->get();
                
            case 'CHAIRS':
                // Lấy Chair từ bảng hoithao
                return DB::table('nguoidung as u')
                    ->join('hoithao as ht', 'ht.chair_id', '=', 'u.user_id')
                    ->where('ht.conference_id', $conferenceId)
                    ->select('u.user_id', 'u.full_name', 'u.email')
                    ->distinct()
                    ->get();
                
            default:
                return collect([]);
        }
    }
}
