<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixChairRoleAssignments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:chair-roles {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix missing CHAIR role assignments for conferences';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 Running in DRY RUN mode - no changes will be made');
        }

        $this->info('Scanning for conferences without proper CHAIR role assignments...');
        $this->newLine();

        // Find all conferences
        $conferences = DB::table('hoithao')
            ->select('conference_id', 'title', 'chair_id')
            ->whereNotNull('chair_id')
            ->orderBy('conference_id')
            ->get();

        $fixed = 0;
        $alreadyOk = 0;
        $errors = 0;

        foreach ($conferences as $conference) {
            // Check if CHAIR role exists for this conference
            $existingRole = DB::table('vaitronguoidung')
                ->where('user_id', $conference->chair_id)
                ->where('role_code', 'CHAIR')
                ->where('conference_id', $conference->conference_id)
                ->first();

            if ($existingRole) {
                $alreadyOk++;
                $this->line("✅ Conference #{$conference->conference_id} ({$conference->title}) - Role OK");
                continue;
            }

            // Missing role - fix it
            $this->warn("⚠️  Conference #{$conference->conference_id} ({$conference->title}) - Missing CHAIR role for user #{$conference->chair_id}");

            if (!$dryRun) {
                try {
                    DB::table('vaitronguoidung')->insert([
                        'user_id' => $conference->chair_id,
                        'role_code' => 'CHAIR',
                        'conference_id' => $conference->conference_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $this->info("   ✓ Created CHAIR role for user #{$conference->chair_id}");
                    $fixed++;
                } catch (\Exception $e) {
                    $this->error("   ✗ Failed to create role: {$e->getMessage()}");
                    $errors++;
                }
            } else {
                $this->info("   → Would create CHAIR role for user #{$conference->chair_id}");
                $fixed++;
            }
        }

        $this->newLine();
        $this->info('Checking for orphan CHAIR roles (NULL conference_id)...');

        $orphanRoles = DB::table('vaitronguoidung as vt')
            ->leftJoin('nguoidung as u', 'vt.user_id', '=', 'u.user_id')
            ->where('vt.role_code', 'CHAIR')
            ->whereNull('vt.conference_id')
            ->select('vt.user_role_id', 'vt.user_id', 'u.email', 'u.full_name')
            ->get();

        $deletedOrphans = 0;

        foreach ($orphanRoles as $role) {
            $this->warn("⚠️  User #{$role->user_id} ({$role->email}) has orphan NULL conference_id role");

            if (!$dryRun) {
                try {
                    DB::table('vaitronguoidung')
                        ->where('user_role_id', $role->user_role_id)
                        ->delete();
                    $this->info("   ✓ Deleted orphan role #{$role->user_role_id}");
                    $deletedOrphans++;
                } catch (\Exception $e) {
                    $this->error("   ✗ Failed to delete role: {$e->getMessage()}");
                    $errors++;
                }
            } else {
                $this->info("   → Would delete orphan role #{$role->user_role_id}");
                $deletedOrphans++;
            }
        }

        if ($orphanRoles->count() === 0) {
            $this->info("✅ No orphan CHAIR roles found");
        }

        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info("Summary:");
        $this->info("  Total conferences: {$conferences->count()}");
        $this->info("  Already correct: {$alreadyOk}");
        if ($dryRun) {
            $this->warn("  Would fix missing roles: {$fixed}");
            $this->warn("  Would delete orphan roles: {$deletedOrphans}");
        } else {
            $this->info("  Fixed missing roles: {$fixed}");
            $this->info("  Deleted orphan roles: {$deletedOrphans}");
        }
        if ($errors > 0) {
            $this->error("  Errors: {$errors}");
        }
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        if ($dryRun && ($fixed > 0 || $deletedOrphans > 0)) {
            $this->newLine();
            $this->warn('This was a DRY RUN. To apply changes, run:');
            $this->line('  php artisan fix:chair-roles');
        }

        return 0;
    }
}
