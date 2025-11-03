<?php

// Test download route functionality
echo "=== TESTING DOWNLOAD ROUTE ===\n";

// Test paper ID from our assignment
$paperId = 52; // From assignment 12
$url = 'http://127.0.0.1:8000/reviewer/papers/' . $paperId . '/download';

echo "Testing download URL: " . $url . "\n";

// Test if route exists
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$headers = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Response Code: " . $httpCode . "\n";

if ($httpCode == 200) {
    echo "✅ Download route accessible\n";
} elseif ($httpCode == 404) {
    echo "❌ Route not found - check route configuration\n";
} elseif ($httpCode == 302) {
    echo "🔄 Route redirected - might need authentication\n";
} else {
    echo "❓ Unexpected response code: " . $httpCode . "\n";
}

// Check if paper exists and has file
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$paper = DB::table('baibao')->where('paper_id', $paperId)->first();

if ($paper) {
    echo "\n✅ Paper found in database:\n";
    echo "  - ID: " . $paper->paper_id . "\n";
    echo "  - Title: " . $paper->title . "\n";
    echo "  - File path: " . ($paper->file_path ?? 'NULL') . "\n";
    
    if ($paper->file_path) {
        $fullPath = storage_path('app/public/' . $paper->file_path);
        echo "  - Full path: " . $fullPath . "\n";
        
        if (file_exists($fullPath)) {
            echo "  - ✅ File exists on disk\n";
            echo "  - File size: " . number_format(filesize($fullPath) / 1024) . " KB\n";
        } else {
            echo "  - ❌ File NOT found on disk\n";
        }
    } else {
        echo "  - ❌ No file path in database\n";
    }
} else {
    echo "\n❌ Paper not found in database\n";
}

echo "\n=== TEST COMPLETE ===\n";