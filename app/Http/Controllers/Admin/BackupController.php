<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class BackupController extends Controller
{
    public function index()
    {
        $disk = Storage::disk('local');
        $files = $disk->files('backups');
        $backups = [];

        foreach ($files as $file) {
            if (substr($file, -4) == '.sql') {
                $backups[] = [
                    'filename' => basename($file),
                    'size' => $this->formatSize($disk->size($file)),
                    'created_at' => Carbon::createFromTimestamp($disk->lastModified($file))->format('Y-m-d H:i:s'),
                    'path' => $file
                ];
            }
        }

        // Sort by created_at desc
        usort($backups, function ($a, $b) {
            return $b['created_at'] <=> $a['created_at'];
        });

        // Get Settings
        $settings = SystemSetting::all()->pluck('value', 'key')->toArray();

        // Get DB Structure
        $dbStructure = $this->getDatabaseStructure();

        return view('admin.settings', [
            'title' => 'Cấu hình hệ thống & Database',
            'backups' => $backups,
            'settings' => $settings,
            'dbStructure' => $dbStructure
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'backup_frequency' => 'required|in:daily,weekly,monthly',
            'backup_time' => 'required',
            'auto_backup' => 'nullable|in:on,off'
        ]);

        SystemSetting::updateOrCreate(
            ['key' => 'backup_frequency'],
            ['value' => $data['backup_frequency'], 'description' => 'Tần suất backup tự động']
        );

        SystemSetting::updateOrCreate(
            ['key' => 'backup_time'],
            ['value' => $data['backup_time'], 'description' => 'Thời gian chạy backup']
        );

        SystemSetting::updateOrCreate(
            ['key' => 'auto_backup'],
            ['value' => $request->has('auto_backup') ? '1' : '0', 'description' => 'Bật/tắt backup tự động']
        );

        return redirect()->back()->with('success', 'Cập nhật cấu hình thành công.');
    }

    private function getDatabaseStructure()
    {
        $tables = DB::select('SHOW TABLES');
        $structure = [];

        foreach ($tables as $table) {
            $tableArray = (array)$table;
            $tableName = array_values($tableArray)[0];

            // Get columns
            $columns = DB::select("DESCRIBE `$tableName`");

            // Get row count
            $count = DB::table($tableName)->count();

            $structure[$tableName] = [
                'columns' => $columns,
                'rows' => $count
            ];
        }
        return $structure;
    }

    public function create(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        if (!Hash::check($request->password, Auth::user()->getAuthPassword())) {
            return redirect()->back()->with('error', 'Mật khẩu xác nhận không chính xác.');
        }

        try {
            $filename = 'backup-' . Carbon::now()->format('Y-m-d-H-i-s') . '.sql';
            $path = storage_path('app/backups/' . $filename);

            // Ensure directory exists
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            $dbHost = config('database.connections.mysql.host');
            $dbPort = config('database.connections.mysql.port');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPassword = config('database.connections.mysql.password');

            // Determine mysqldump path (adjust for XAMPP)
            $mysqldumpPath = 'mysqldump';
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $mysqldumpPath = 'c:\xampp\mysql\bin\mysqldump.exe';
                if (!file_exists($mysqldumpPath)) {
                    // Try to find it in path or default location
                    $mysqldumpPath = 'mysqldump';
                }
            } elseif (strtoupper(substr(PHP_OS, 0, 6)) === 'DARWIN') {
                // macOS - XAMPP path
                $mysqldumpPath = '/Applications/XAMPP/xamppfiles/bin/mysqldump';
                if (!file_exists($mysqldumpPath)) {
                    // Fallback to system path
                    $mysqldumpPath = 'mysqldump';
                }
            }

            // Build command with proper password handling
            if (empty($dbPassword)) {
                $command = "\"{$mysqldumpPath}\" --user=\"{$dbUser}\" --host=\"{$dbHost}\" --port=\"{$dbPort}\" \"{$dbName}\" > \"{$path}\" 2>&1";
            } else {
                $command = "\"{$mysqldumpPath}\" --user=\"{$dbUser}\" --password=\"{$dbPassword}\" --host=\"{$dbHost}\" --port=\"{$dbPort}\" \"{$dbName}\" > \"{$path}\" 2>&1";
            }

            // Mask password in log
            $logCommand = empty($dbPassword) ? $command : str_replace($dbPassword, '*****', $command);
            \Log::info("Starting backup: " . $logCommand);

            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                $outputStr = implode("\n", $output);
                \Log::error("Backup failed. Return code: $returnVar. Output: $outputStr");
                throw new \Exception("Backup failed with exit code $returnVar. Output: $outputStr");
            }

            // Verify backup file was created
            if (!file_exists($path) || filesize($path) === 0) {
                throw new \Exception("Backup file was not created or is empty");
            }

            return redirect()->route('admin.settings.index')->with('success', 'Tạo bản sao lưu thành công: ' . $filename);

        } catch (\Exception $e) {
            \Log::error('Backup failed: ' . $e->getMessage());
            return redirect()->route('admin.settings.index')->with('error', 'Lỗi khi tạo bản sao lưu: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        $path = 'backups/' . $filename;
        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->download($path);
        }
        return redirect()->back()->with('error', 'File không tồn tại.');
    }

    public function delete($filename)
    {
        $path = 'backups/' . $filename;
        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
            return redirect()->route('admin.settings.index')->with('success', 'Đã xóa bản sao lưu.');
        }
        return redirect()->back()->with('error', 'File không tồn tại.');
    }

    public function restore(Request $request, $filename)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        if (!Hash::check($request->password, Auth::user()->getAuthPassword())) {
            return redirect()->back()->with('error', 'Mật khẩu xác nhận không chính xác.');
        }

        try {
            $path = storage_path('app/backups/' . $filename);

            if (!file_exists($path)) {
                throw new \Exception("File backup không tồn tại.");
            }

            $dbHost = config('database.connections.mysql.host');
            $dbPort = config('database.connections.mysql.port');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPassword = config('database.connections.mysql.password');

            // Determine mysql path
            $mysqlPath = 'mysql';
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $mysqlPath = 'c:\xampp\mysql\bin\mysql.exe';
                if (!file_exists($mysqlPath)) {
                    $mysqlPath = 'mysql';
                }
            } elseif (strtoupper(substr(PHP_OS, 0, 6)) === 'DARWIN') {
                // macOS - XAMPP path
                $mysqlPath = '/Applications/XAMPP/xamppfiles/bin/mysql';
                if (!file_exists($mysqlPath)) {
                    $mysqlPath = 'mysql';
                }
            }

            // Build command with proper password handling
            if (empty($dbPassword)) {
                $command = "\"{$mysqlPath}\" --user=\"{$dbUser}\" --host=\"{$dbHost}\" --port=\"{$dbPort}\" \"{$dbName}\" < \"{$path}\" 2>&1";
            } else {
                $command = "\"{$mysqlPath}\" --user=\"{$dbUser}\" --password=\"{$dbPassword}\" --host=\"{$dbHost}\" --port=\"{$dbPort}\" \"{$dbName}\" < \"{$path}\" 2>&1";
            }

            \Log::info("Starting restore from: $filename");
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                $outputStr = implode("\n", $output);
                \Log::error("Restore failed. Return code: $returnVar. Output: $outputStr");
                throw new \Exception("Restore failed with exit code $returnVar. Output: $outputStr");
            }

            return redirect()->route('admin.settings.index')->with('success', 'Phục hồi dữ liệu thành công từ: ' . $filename);

        } catch (\Exception $e) {
            \Log::error('Restore failed: ' . $e->getMessage());
            return redirect()->route('admin.settings.index')->with('error', 'Lỗi khi phục hồi dữ liệu: ' . $e->getMessage());
        }
    }

    private function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
