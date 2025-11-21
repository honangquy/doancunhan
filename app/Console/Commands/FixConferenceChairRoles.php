<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\HoiThao;

class FixConferenceChairRoles extends Command
{
    protected $signature = 'fix:conference-chair-roles';
    protected $description = 'Fix missing chair roles for active conferences';

    public function handle()
    {
        $this->info('Fixing missing chair roles for active conferences...');

        // Get active conferences without chair roles
        $conferences = DB::table('hoithao as h')
            ->leftJoin('vaitronguoidung as v', function($join) {
                $join->on('h.conference_id', '=', 'v.conference_id')
                     ->on('h.chair_id', '=', 'v.user_id')
                     ->where('v.role_code', '=', 'CHAIR');
            })
            ->where('h.status', 'ACTIVE')
            ->whereNotNull('h.chair_id')
            ->whereNull('v.user_role_id') // No chair role exists
            ->select('h.conference_id', 'h.title', 'h.chair_id')
            ->get();

        if ($conferences->isEmpty()) {
            $this->info('No conferences need fixing.');
            return;
        }

        $this->info("Found {$conferences->count()} conferences without chair roles:");

        $bar = $this->output->createProgressBar($conferences->count());

        foreach ($conferences as $conference) {
            $this->line("Conference {$conference->conference_id}: {$conference->title} (Chair: {$conference->chair_id})");

            // Assign chair role
            DB::table('vaitronguoidung')->insert([
                'user_id' => $conference->chair_id,
                'role_code' => 'CHAIR',
                'conference_id' => $conference->conference_id
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully assigned chair roles to {$conferences->count()} conferences.");
    }
}