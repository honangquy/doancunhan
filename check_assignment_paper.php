<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING ASSIGNMENT AND PAPER DATA ===\n";

$assignment = DB::table('reviewer_assignments')->where('id', 12)->first();
if ($assignment) {
    echo "Assignment 12 found:\n";
    echo "  - Paper ID: " . $assignment->paper_id . "\n";
    echo "  - User ID: " . $assignment->user_id . "\n";
    echo "  - Status: " . $assignment->status . "\n";
    
    $paper = DB::table('baibao')->where('paper_id', $assignment->paper_id)->first();
    if ($paper) {
        echo "\nPaper found:\n";
        echo "  - ID: " . $paper->paper_id . "\n";
        echo "  - Title: " . $paper->title . "\n";
        echo "  - File path: " . ($paper->file_path ?? 'NULL') . "\n";
        
        if ($paper->file_path) {
            $fullPath = storage_path('app/public/' . $paper->file_path);
            echo "  - Full path: " . $fullPath . "\n";
            echo "  - File exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
        }
    } else {
        echo "\n❌ Paper not found with ID: " . $assignment->paper_id . "\n";
    }
} else {
    echo "❌ Assignment 12 not found\n";
}