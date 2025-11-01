<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$userId = 19; // honangquy1@gmail.com

echo "Checking roles for conferences 6, 7, 8...\n\n";

// Check roles for these specific conferences
$roles = DB::table('vaitronguoidung')
    ->whereIn('conference_id', [6, 7, 8])
    ->get();

echo "Roles found for conferences 6, 7, 8:\n";
foreach ($roles as $role) {
    $user = DB::table('nguoidung')->where('user_id', $role->user_id)->first();
    echo "- Conference {$role->conference_id}: User {$role->user_id} ({$user->full_name}) - Role {$role->role_code}\n";
}

echo "\n";

// Check if honangquy has any roles for these conferences
$userRolesForConfs = DB::table('vaitronguoidung')
    ->where('user_id', $userId)
    ->whereIn('conference_id', [6, 7, 8])
    ->get();

echo "honangquy1@gmail.com roles for conferences 6, 7, 8:\n";
if ($userRolesForConfs->count() > 0) {
    foreach ($userRolesForConfs as $role) {
        echo "- Conference {$role->conference_id}: Role {$role->role_code}\n";
    }
} else {
    echo "❌ No roles found! This is why dropdown is empty.\n\n";
    
    echo "Let's check who created these conferences...\n";
    $conferences = DB::table('hoithao')
        ->whereIn('conference_id', [6, 7, 8])
        ->get();
    
    foreach ($conferences as $conf) {
        echo "- Conference {$conf->conference_id}: {$conf->title}\n";
        echo "  Chair ID: {$conf->chair_id}\n";
        if ($conf->chair_id) {
            $chair = DB::table('nguoidung')->where('user_id', $conf->chair_id)->first();
            if ($chair) {
                echo "  Chair: {$chair->full_name} ({$chair->email})\n";
            }
        }
        echo "\n";
    }
}