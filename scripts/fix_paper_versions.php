<?php
/**
 * Script to fix paper versions with missing files
 * Run: php scripts/fix_paper_versions.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

echo "=== Fixing Paper Versions ===\n\n";

// Get all papers with versions
$papers = DB::table('baibao')
    ->select('paper_id', 'file_path')
    ->whereNotNull('file_path')
    ->get();

$fixedCount = 0;
$errorCount = 0;

foreach ($papers as $paper) {
    echo "Checking Paper {$paper->paper_id}...\n";
    
    // Get all versions for this paper
    $versions = DB::table('phienbanbaibao')
        ->where('paper_id', $paper->paper_id)
        ->orderBy('version_no')
        ->get();
    
    if ($versions->isEmpty()) {
        echo "  ⚠️  No versions found, skipping\n";
        continue;
    }
    
    $validVersions = [];
    $invalidVersions = [];
    
    // Check which versions have valid files
    foreach ($versions as $version) {
        if (Storage::exists($version->file_path)) {
            $validVersions[] = $version;
            echo "  ✓ Version {$version->version_no}: {$version->file_path} - EXISTS\n";
        } else {
            $invalidVersions[] = $version;
            echo "  ✗ Version {$version->version_no}: {$version->file_path} - MISSING\n";
        }
    }
    
    // If no valid versions but baibao has a file, use that
    if (empty($validVersions) && Storage::exists($paper->file_path)) {
        echo "  → Using file from baibao table: {$paper->file_path}\n";
        
        // Delete all invalid versions
        DB::table('phienbanbaibao')
            ->where('paper_id', $paper->paper_id)
            ->delete();
        
        // Create version 1 with correct file
        DB::table('phienbanbaibao')->insert([
            'paper_id' => $paper->paper_id,
            'version_no' => 1,
            'file_path' => $paper->file_path,
            'submitted_at' => now(),
            'note' => 'Initial submission'
        ]);
        
        echo "  ✓ Fixed: Created version 1 with correct file\n";
        $fixedCount++;
        continue;
    }
    
    // If we have invalid versions, clean them up
    if (!empty($invalidVersions)) {
        // Keep only the highest valid version
        $highestValid = end($validVersions);
        
        // Delete all versions
        DB::table('phienbanbaibao')
            ->where('paper_id', $paper->paper_id)
            ->delete();
        
        // Recreate version 1 with the highest valid version's file
        DB::table('phienbanbaibao')->insert([
            'paper_id' => $paper->paper_id,
            'version_no' => 1,
            'file_path' => $highestValid->file_path,
            'submitted_at' => $highestValid->submitted_at,
            'note' => $highestValid->note
        ]);
        
        echo "  ✓ Fixed: Kept version 1 with file {$highestValid->file_path}\n";
        $fixedCount++;
    } else {
        echo "  ✓ All versions valid\n";
    }
    
    echo "\n";
}

echo "\n=== Summary ===\n";
echo "Papers fixed: {$fixedCount}\n";
echo "Papers with errors: {$errorCount}\n";
echo "\nDone!\n";
