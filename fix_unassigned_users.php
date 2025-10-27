<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// The two users without roles
$usersToAssign = [11, 12];

echo "=== Assigning USER role to unassigned users ===\n";

foreach($usersToAssign as $userId) {
    // Check if already assigned
    $existing = DB::table('vaitronguoidung')->where('user_id', $userId)->first();
    
    if($existing) {
        echo "User {$userId}: Already has role {$existing->role_code}\n";
    } else {
        // Assign USER role
        DB::table('vaitronguoidung')->insert([
            'user_id' => $userId,
            'role_code' => 'USER',
            'conference_id' => null
        ]);
        echo "User {$userId}: Assigned USER role ✓\n";
    }
}

echo "\n=== Verification ===\n";
$roles = DB::table('vaitronguoidung')
    ->select('role_code', DB::raw('count(distinct user_id) as count'))
    ->groupBy('role_code')
    ->get();

foreach($roles as $row) {
    echo $row->role_code . ': ' . $row->count . "\n";
}

$unassigned = DB::table('nguoidung')
    ->leftJoin('vaitronguoidung', 'nguoidung.user_id', '=', 'vaitronguoidung.user_id')
    ->whereNull('vaitronguoidung.user_id')
    ->count();

echo "Unassigned: " . $unassigned . "\n";
