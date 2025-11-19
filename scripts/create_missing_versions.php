<?php
/**
 * Script to create missing initial versions for papers without version tracking
 * Run: php scripts/create_missing_versions.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

echo "=== Creating Missing Initial Versions ===\n\n";

// Get papers that don't have version tracking
$papersWithoutVersions = DB::table('baibao as b')
    ->leftJoin('phienbanbaibao as v', 'b.paper_id', '=', 'v.paper_id')
    ->whereNull('v.paper_id')
    ->whereNotNull('b.file_path')
    ->select('b.paper_id', 'b.file_path', 'b.created_at')
    ->get();

$created = 0;

foreach ($papersWithoutVersions as $paper) {
    echo "Paper {$paper->paper_id}: {$paper->file_path}\n";
    
    if (Storage::exists($paper->file_path)) {
        // Create version 1 for this paper
        DB::table('phienbanbaibao')->insert([
            'paper_id' => $paper->paper_id,
            'version_no' => 1,
            'file_path' => $paper->file_path,
            'submitted_at' => $paper->created_at ?: now(),
            'note' => 'Initial submission'
        ]);
        
        echo "  ✓ Created version 1\n";
        $created++;
    } else {
        echo "  ✗ File not found: {$paper->file_path}\n";
    }
    
    echo "\n";
}

echo "=== Summary ===\n";
echo "Initial versions created: {$created}\n";
echo "Done!\n";