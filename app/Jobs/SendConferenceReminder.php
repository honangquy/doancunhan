<?php

namespace App\Jobs;

use App\Mail\ConferenceReminderMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Exception;

class SendConferenceReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 600]; // Retry sau 1 phút, 5 phút, 10 phút

    protected int $outboxId;

    public function __construct(int $outboxId)
    {
        $this->outboxId = $outboxId;
    }

    public function handle(): void
    {
        $notification = DB::table('notification_outbox')->find($this->outboxId);

        if (!$notification || $notification->status === 'sent') {
            return; // Đã gửi rồi hoặc không tồn tại
        }

        try {
            // Gửi email
            Mail::to($notification->recipient_email)
                ->send(new ConferenceReminderMail(
                    $notification->subject,
                    $notification->body,
                    $notification->recipient_name
                ));

            // Cập nhật trạng thái thành công
            DB::table('notification_outbox')
                ->where('id', $this->outboxId)
                ->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'error_message' => null
                ]);

            Log::info("Conference reminder sent", [
                'outbox_id' => $this->outboxId,
                'recipient' => $notification->recipient_email
            ]);

        } catch (Exception $e) {
            $retryCount = $notification->retry_count ?? 0;

            DB::table('notification_outbox')
                ->where('id', $this->outboxId)
                ->update([
                    'status' => $retryCount >= 2 ? 'failed' : 'pending',
                    'retry_count' => $retryCount + 1,
                    'error_message' => substr($e->getMessage(), 0, 500)
                ]);

            Log::error("Conference reminder failed", [
                'outbox_id' => $this->outboxId,
                'recipient' => $notification->recipient_email,
                'error' => $e->getMessage(),
                'retry_count' => $retryCount + 1
            ]);

            // Throw lại để Laravel queue tự retry
            throw $e;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(Exception $exception): void
    {
        DB::table('notification_outbox')
            ->where('id', $this->outboxId)
            ->update([
                'status' => 'failed',
                'error_message' => substr($exception->getMessage(), 0, 500)
            ]);

        Log::error("Conference reminder permanently failed", [
            'outbox_id' => $this->outboxId,
            'error' => $exception->getMessage()
        ]);
    }
}
