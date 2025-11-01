<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$userId = 19; // honangquy1@gmail.com

echo "Fixing missing CHAIR roles...\n\n";

// Check conferences where user is chair_id but doesn't have CHAIR role
$conferences = DB::table('hoithao')
    ->where('chair_id', $userId)
    ->get();

foreach ($conferences as $conf) {
    echo "Conference {$conf->conference_id}: {$conf->title}\n";
    
    // Check if CHAIR role exists
    $existingRole = DB::table('vaitronguoidung')
        ->where('user_id', $userId)
        ->where('conference_id', $conf->conference_id)
        ->where('role_code', 'CHAIR')
        ->first();
    
    if ($existingRole) {
        echo "  ✅ CHAIR role already exists\n";
    } else {
        echo "  ❌ Missing CHAIR role - Adding...\n";
        
        try {
            DB::table('vaitronguoidung')->insert([
                'user_id' => $userId,
                'conference_id' => $conf->conference_id,
                'role_code' => 'CHAIR'
            ]);
            echo "  ✅ CHAIR role added successfully!\n";
        } catch (\Exception $e) {
            echo "  ❌ Error adding CHAIR role: " . $e->getMessage() . "\n";
        }
    }
    echo "\n";
}

echo "Done! Now testing the dropdown query...\n\n";

// Test the dropdown query again
$conferences = DB::table('hoithao as ht')
    ->join('vaitronguoidung as vt', 'ht.conference_id', '=', 'vt.conference_id')
    ->where('vt.user_id', $userId)
    ->where('vt.role_code', 'CHAIR')
    ->where('ht.status', 'ACTIVE')
    ->select('ht.*')
    ->orderBy('ht.start_date', 'desc')
    ->get();

echo "Conferences now available in dropdown:\n";
foreach ($conferences as $conf) {
    echo "- {$conf->conference_id}: {$conf->title}\n";
}