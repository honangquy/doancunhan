<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AnalyzeUnusedTables extends Command
{
    protected $signature = 'db:analyze-unused-tables';
    protected $description = 'Analyze which database tables are unused in the codebase';

    // System/Framework tables that should not be deleted
    protected $systemTables = [
        'migrations',
        'password_resets', 
        'personal_access_tokens',
        'failed_jobs'
    ];

    // Core Laravel notification tables
    protected $coreNotificationTables = [
        'notifications',
        'database_notifications'
    ];

    public function handle()
    {
        $this->info('Analyzing database table usage...');
        $this->newLine();

        // Get all tables
        $tables = collect(DB::select('SHOW TABLES'))
            ->map(function($table) {
                $tableName = array_values((array)$table)[0];
                return $tableName;
            });

        $unusedTables = [];
        $emptyTables = [];
        $usageResults = [];

        foreach ($tables as $table) {
            $this->line("Checking table: {$table}");
            
            // Check if table is empty
            $count = DB::table($table)->count();
            if ($count === 0) {
                $emptyTables[] = $table;
            }

            // Skip system tables
            if (in_array($table, $this->systemTables)) {
                $usageResults[$table] = ['status' => 'system_table', 'count' => $count];
                continue;
            }

            // Check usage in codebase
            $usage = $this->checkTableUsage($table);
            $usageResults[$table] = [
                'status' => $usage > 0 ? 'used' : 'unused',
                'usage_count' => $usage,
                'record_count' => $count
            ];

            if ($usage === 0) {
                $unusedTables[] = $table;
            }
        }

        $this->displayResults($usageResults, $unusedTables, $emptyTables);
        return 0;
    }

    private function checkTableUsage($tableName)
    {
        $usage = 0;
        
        // Paths to check
        $paths = [
            app_path(),
            resource_path('views'),
            base_path('routes'),
            database_path('migrations')
        ];

        foreach ($paths as $path) {
            if (File::exists($path)) {
                $usage += $this->searchInPath($path, $tableName);
            }
        }

        return $usage;
    }

    private function searchInPath($path, $tableName)
    {
        $usage = 0;
        
        if (File::isDirectory($path)) {
            $files = File::allFiles($path);
            
            foreach ($files as $file) {
                if (in_array($file->getExtension(), ['php', 'blade.php'])) {
                    $content = File::get($file->getPathname());
                    
                    // Check for table usage patterns
                    $patterns = [
                        "DB::table('{$tableName}')",
                        "DB::table(\"{$tableName}\")",
                        "->table('{$tableName}')",
                        "->table(\"{$tableName}\")",
                        "'{$tableName}'", 
                        "\"{$tableName}\"",
                        "from {$tableName}",
                        "FROM {$tableName}",
                        "join {$tableName}",
                        "JOIN {$tableName}",
                    ];
                    
                    foreach ($patterns as $pattern) {
                        if (strpos($content, $pattern) !== false) {
                            $usage++;
                            break; // Count file only once
                        }
                    }
                }
            }
        }
        
        return $usage;
    }

    private function displayResults($usageResults, $unusedTables, $emptyTables)
    {
        $this->newLine();
        $this->info('=== ANALYSIS RESULTS ===');
        $this->newLine();

        // Show unused tables
        if (!empty($unusedTables)) {
            $this->error('🔴 UNUSED TABLES (candidates for deletion):');
            foreach ($unusedTables as $table) {
                $count = $usageResults[$table]['record_count'];
                $status = $count > 0 ? " (has {$count} records)" : " (empty)";
                $this->line("  - {$table}{$status}");
            }
            $this->newLine();
        }

        // Show empty tables
        if (!empty($emptyTables)) {
            $this->warn('🟡 EMPTY TABLES:');
            foreach ($emptyTables as $table) {
                $status = $usageResults[$table]['status'];
                $usage = $usageResults[$table]['usage_count'] ?? 0;
                $usageText = $usage > 0 ? " (used in {$usage} files)" : " (not used)";
                $this->line("  - {$table}{$usageText}");
            }
            $this->newLine();
        }

        // Show used tables summary
        $usedTables = array_filter($usageResults, function($result) {
            return $result['status'] === 'used';
        });
        
        $this->info("🟢 USED TABLES: " . count($usedTables));
        $this->info("🔴 UNUSED TABLES: " . count($unusedTables));
        $this->info("🟡 EMPTY TABLES: " . count($emptyTables));
        $this->info("🔵 SYSTEM TABLES: " . count(array_filter($usageResults, function($r) { return $r['status'] === 'system_table'; })));

        $this->newLine();
        
        if (!empty($unusedTables)) {
            if ($this->confirm('Do you want to see the deletion script for unused tables?')) {
                $this->showDeletionScript($unusedTables);
            }
        }
    }

    private function showDeletionScript($unusedTables)
    {
        $this->newLine();
        $this->warn('DELETION SCRIPT (review carefully before running):');
        $this->newLine();
        
        foreach ($unusedTables as $table) {
            $this->line("DROP TABLE IF EXISTS `{$table}`;");
        }
        
        $this->newLine();
        $this->error('⚠️  WARNING: Review each table carefully before deletion!');
    }
}