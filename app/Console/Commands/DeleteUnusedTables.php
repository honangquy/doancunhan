<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteUnusedTables extends Command
{
    protected $signature = 'db:delete-unused-tables';
    protected $description = 'Delete unused tables that are safe to remove';

    public function handle()
    {
        $safeTablesToDelete = [
            'chuyenmonreviewer',
            'lichsutrangthai', 
            'rutbaibao',
            'yeucauchinhsua'
        ];

        $this->info('Tables to be deleted:');
        foreach ($safeTablesToDelete as $table) {
            $count = DB::table($table)->count();
            $this->line("  - {$table} ({$count} records)");
        }

        $this->newLine();
        
        if (!$this->confirm('Are you sure you want to delete these tables? This action cannot be undone.')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->info('Deleting unused tables...');

        foreach ($safeTablesToDelete as $table) {
            try {
                DB::statement("DROP TABLE IF EXISTS `{$table}`");
                $this->line("✓ Deleted table: {$table}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to delete {$table}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info('✅ Cleanup completed!');
        
        $this->warn('Note: The following tables were kept because they have foreign key references:');
        $this->line('  - giatribidding (referenced by bidding table)');
        $this->line('  - loaikhuyennghi (referenced by phanbien table)');
        $this->line('  - trangthaiphancong (referenced by phancongphanbien table)');
        
        return 0;
    }
}