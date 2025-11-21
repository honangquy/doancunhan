<?php

namespace App\Services;

use App\Jobs\SendConferenceReminder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConferenceReminderMail;
use Carbon\Carbon;

class ConferenceReminderService
{
    /**
     * Xử lý tất cả reminders - GỬI TRỰC TIẾP không dùng outbox
     * Vì schema notification_outbox không hỗ trợ individual emails
     */
    public function processReminders(): array
    {
        $today = Carbon::today();
        $stats = [
            'conferences_checked' => 0,
            'reminders_created' => 0,
            'recipients' => 0
        ];

        $conferences = DB::table('hoithao')
            ->whereIn('status', ['APPROVED', 'ACTIVE'])
            ->whereNotNull('start_date')
            ->where('start_date', '>=', $today)
            ->get();

        $stats['conferences_checked'] = $conferences->count();

        $reminderConfigs = [
            ['event_type' => 'deadline_submission', 'days_before' => 7, 'role' => 'AUTHOR', 'template_code' => 'SUBMISSION_REMINDER_7D'],
            ['event_type' => 'deadline_submission', 'days_before' => 3, 'role' => 'AUTHOR', 'template_code' => 'SUBMISSION_REMINDER_3D'],
            ['event_type' => 'deadline_review', 'days_before' => 7, 'role' => 'REVIEWER', 'template_code' => 'REVIEW_REMINDER_7D'],
            ['event_type' => 'deadline_review', 'days_before' => 3, 'role' => 'REVIEWER', 'template_code' => 'REVIEW_REMINDER_3D'],
            ['event_type' => 'deadline_camera_ready', 'days_before' => 3, 'role' => 'AUTHOR', 'template_code' => 'CAMERA_READY_REMINDER_3D'],
            ['event_type' => 'start_date', 'days_before' => 7, 'role' => 'ALL', 'template_code' => 'CONFERENCE_START_7D'],
            ['event_type' => 'end_date', 'days_before' => 1, 'role' => 'ALL', 'template_code' => 'CONFERENCE_END_1D'],
        ];

        foreach ($conferences as $conference) {
            foreach ($reminderConfigs as $config) {
                $deadline = $this->getReminderDate($conference, $config['event_type']);
                
                if (!$deadline) continue;

                $reminderDate = Carbon::parse($deadline)->subDays($config['days_before']);
                
                if ($reminderDate->isSameDay($today)) {
                    $template = DB::table('notification_templates')
                        ->where('template_code', $config['template_code'])
                        ->first();
                    
                    if ($template) {
                        $created = $this->createReminder(
                            $conference, 
                            $template, 
                            $config['event_type'],
                            $config['role']
                        );
                        
                        $stats['reminders_created'] += $created['reminders'];
                        $stats['recipients'] += $created['recipients'];
                    }
                }
            }
        }

        return $stats;
    }

    private function getReminderDate($conference, string $eventType): ?string
    {
        return match($eventType) {
            'deadline_submission' => $conference->deadline_submission,
            'deadline_review' => $conference->deadline_review,
            'deadline_camera_ready' => $conference->deadline_camera_ready,
            'start_date' => $conference->start_date,
            'end_date' => $conference->end_date,
            default => null
        };
    }

    private function createReminder($conference, $template, string $eventType, string $targetRole): array
    {
        $recipients = $this->getRecipients($conference->conference_id, $targetRole);
        
        $stats = ['reminders' => 0, 'recipients' => 0];

        foreach ($recipients as $recipient) {
            $variables = [
                'user_name' => $recipient->full_name,
                'title' => $conference->title,
                'start_date' => Carbon::parse($conference->start_date)->format('d/m/Y'),
                'end_date' => $conference->end_date ? Carbon::parse($conference->end_date)->format('d/m/Y') : '',
                'location' => $conference->location ?? 'Sẽ thông báo sau',
                'deadline_submission' => $conference->deadline_submission ? Carbon::parse($conference->deadline_submission)->format('d/m/Y') : '',
                'deadline_review' => $conference->deadline_review ? Carbon::parse($conference->deadline_review)->format('d/m/Y') : '',
                'deadline_camera_ready' => $conference->deadline_camera_ready ? Carbon::parse($conference->deadline_camera_ready)->format('d/m/Y') : '',
                'conference_url' => url("/conference/{$conference->conference_id}")
            ];

            $body = $template->body_html;
            $subject = $template->subject;
            
            foreach ($variables as $key => $value) {
                $placeholder = '{{' . $key . '}}';
                $body = str_replace($placeholder, $value, $body);
                $subject = str_replace($placeholder, $value, $subject);
            }

            // Gửi email trực tiếp qua queue (không lưu outbox vì schema không phù hợp)
            try {
                Mail::to($recipient->email)
                    ->queue(new ConferenceReminderMail($subject, $body, $recipient->full_name));
                
                \Log::info("Conference reminder queued", [
                    'conference_id' => $conference->conference_id,
                    'recipient' => $recipient->email,
                    'event_type' => $eventType,
                    'template_code' => $template->template_code
                ]);

                $stats['reminders']++;
                $stats['recipients']++;
            } catch (\Exception $e) {
                \Log::error("Failed to queue conference reminder", [
                    'conference_id' => $conference->conference_id,
                    'recipient' => $recipient->email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $stats;
    }

    private function getRecipients(int $conferenceId, string $targetRole): array
    {
        $query = DB::table('join_requests as jr')
            ->join('nguoidung as u', 'jr.user_id', '=', 'u.user_id')
            ->leftJoin('user_notification_prefs as unp', 'u.user_id', '=', 'unp.user_id')
            ->where('jr.conference_id', $conferenceId)
            ->where('jr.status', 'APPROVED')
            ->where(function($q) {
                $q->whereNull('unp.email_enabled')
                  ->orWhere('unp.email_enabled', true);
            })
            ->select('u.user_id', 'u.email', 'u.full_name', 'jr.role');

        if ($targetRole !== 'ALL') {
            $query->where('jr.role', $targetRole);
        }

        return $query->get()->toArray();
    }

    public function retryReminder(int $outboxId): bool
    {
        // Not applicable vì không dùng outbox
        return false;
    }
}
