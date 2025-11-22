<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run database backup';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $filename = 'backup-auto-' . Carbon::now()->format('Y-m-d-H-i-s') . '.sql';
            $path = storage_path('app/backups/' . $filename);
            
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            $dbHost = config('database.connections.mysql.host');
            $dbPort = config('database.connections.mysql.port');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPassword = config('database.connections.mysql.password');

            $mysqldumpPath = 'mysqldump';
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $mysqldumpPath = 'c:\xampp\mysql\bin\mysqldump.exe'; 
                if (!file_exists($mysqldumpPath)) {
                    $mysqldumpPath = 'mysqldump'; 
                }
            }

            $command = "\"{$mysqldumpPath}\" --user=\"{$dbUser}\" --password=\"{$dbPassword}\" --host=\"{$dbHost}\" --port=\"{$dbPort}\" \"{$dbName}\" > \"{$path}\"";
            
            $this->info("Starting backup...");
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                $this->error("Backup failed with exit code $returnVar");
                return Command::FAILURE;
            }

            $this->info("Backup created successfully: $filename");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Backup failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
