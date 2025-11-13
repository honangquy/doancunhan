<?php

namespace App\Console\Commands;

use App\Services\ConferenceReminderService;
use Illuminate\Console\Command;

class ProcessConferenceReminders extends Command
{
    protected $signature = 'reminders:process-conference
                            {--dry-run : Run without actually creating reminders}';

    protected $description = 'Process conference reminders and send notifications';

    public function handle(ConferenceReminderService $service): int
    {
        $this->info('🔔 Processing conference reminders...');
        
        if ($this->option('dry-run')) {
            $this->warn('⚠️  DRY RUN MODE - No emails will be sent');
        }

        try {
            $stats = $service->processReminders();

            $this->info("✅ Processing complete:");
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Conferences checked', $stats['conferences_checked']],
                    ['Reminders created', $stats['reminders_created']],
                    ['Recipients', $stats['recipients']]
                ]
            );

            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("❌ Error processing reminders: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
