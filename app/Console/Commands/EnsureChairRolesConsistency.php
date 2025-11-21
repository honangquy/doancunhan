<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EnsureChairRolesConsistency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chair:ensure-roles {--fix : Automatically fix missing roles}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure all conferences have proper CHAIR role assignments';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $fix = $this->option('fix');

        $this->info('Checking CHAIR role consistency...');
        $this->newLine();

        // Find conferences without CHAIR roles
        $missing = DB::table('hoithao as h')
            ->leftJoin('vaitronguoidung as v', function($join) {
                $join->on('h.conference_id', '=', 'v.conference_id')
                     ->where('v.role_code', '=', 'CHAIR')
                     ->where('v.user_id', '=', DB::raw('h.chair_id'));
            })
            ->whereNotNull('h.chair_id')
            ->whereNull('v.user_role_id')
            ->select('h.conference_id', 'h.title', 'h.chair_id')
            ->get();

        if ($missing->count() === 0) {
            $this->info('✅ All conferences have proper CHAIR roles');
            return 0;
        }

        $this->warn("Found {$missing->count()} conference(s) without CHAIR role:");
        $this->newLine();

        foreach ($missing as $conf) {
            $this->line("  Conference #{$conf->conference_id}: {$conf->title} (Chair: {$conf->chair_id})");

            if ($fix) {
                try {
                    DB::table('vaitronguoidung')->insert([
                        'user_id' => $conf->chair_id,
                        'role_code' => 'CHAIR',
                        'conference_id' => $conf->conference_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $this->info("    ✓ Created CHAIR role");
                } catch (\Exception $e) {
                    $this->error("    ✗ Failed: {$e->getMessage()}");
                }
            }
        }

        if (!$fix) {
            $this->newLine();
            $this->warn('Run with --fix option to automatically create missing roles:');
            $this->line('  php artisan chair:ensure-roles --fix');
        }

        return 0;
    }
}
