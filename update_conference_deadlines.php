<?php

/**
 * Update Conference Deadlines for Testing
 * Run: php update_conference_deadlines.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== UPDATING CONFERENCE DEADLINES ===\n\n";

try {
    // Get first 2 conferences
    $conferences = DB::table('HoiThao')
        ->limit(2)
        ->get();
    
    if ($conferences->count() == 0) {
        echo "❌ No conferences found in database\n";
        exit(1);
    }
    
    foreach ($conferences as $conf) {
        // Set future deadlines
        $futureDate = now()->addMonths(2)->format('Y-m-d');
        $reviewDate = now()->addMonths(3)->format('Y-m-d');
        $cameraDate = now()->addMonths(4)->format('Y-m-d');
        
        DB::table('HoiThao')
            ->where('conference_id', $conf->conference_id)
            ->update([
                'status' => 'ACTIVE',
                'deadline_submission' => $futureDate,
                'deadline_review' => $reviewDate,
                'deadline_camera_ready' => $cameraDate,
            ]);
        
        echo "✅ Updated Conference ID {$conf->conference_id}: {$conf->title}\n";
        echo "   - Submission Deadline: {$futureDate}\n";
        echo "   - Review Deadline: {$reviewDate}\n";
        echo "   - Camera Ready Deadline: {$cameraDate}\n";
        echo "   - Status: ACTIVE\n\n";
    }
    
    echo "✅ Successfully updated {$conferences->count()} conference(s)\n";
    echo "🎯 You can now test paper submission!\n\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
