<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking reviewer_assignments status values ===" . PHP_EOL;

try {
    // Check distinct status values in reviewer_assignments
    $statuses = DB::table('reviewer_assignments')
        ->distinct()
        ->pluck('status');
    
    echo "Status values in reviewer_assignments:" . PHP_EOL;
    foreach ($statuses as $status) {
        $count = DB::table('reviewer_assignments')->where('status', $status)->count();
        echo "   - $status: $count records" . PHP_EOL;
    }
    
    // Check sample data
    echo "\nSample assignments (first 5):" . PHP_EOL;
    $samples = DB::table('reviewer_assignments as ra')
        ->join('baibao', 'ra.paper_id', '=', 'baibao.paper_id')
        ->select('ra.id', 'ra.status', 'ra.user_id', 'baibao.title')
        ->limit(5)
        ->get();
        
    foreach ($samples as $sample) {
        echo "   - ID: {$sample->id}, Status: {$sample->status}, User: {$sample->user_id}, Paper: {$sample->title}" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
?>