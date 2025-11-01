<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing role assignment manually...\n\n";

$joinRequestId = 11;
$userId = 29;
$conferenceId = 7;
$role = 'REVIEWER';

echo "Simulating the approval process...\n";
echo "- Join Request ID: {$joinRequestId}\n";
echo "- User ID: {$userId}\n";
echo "- Conference ID: {$conferenceId}\n";  
echo "- Role: {$role}\n\n";

// Check if user already has this role
echo "1. Checking existing role...\n";
$existingRole = DB::table('vaitronguoidung')
    ->where('user_id', $userId)
    ->where('role_code', $role)
    ->where('conference_id', $conferenceId)
    ->first();

if ($existingRole) {
    echo "✅ User already has this role\n";
} else {
    echo "❌ User doesn't have this role - should be assigned\n";
    
    echo "\n2. Attempting to assign role...\n";
    try {
        DB::table('vaitronguoidung')->insert([
            'user_id' => $userId,
            'role_code' => $role,
            'conference_id' => $conferenceId
        ]);
        echo "✅ Role assigned successfully!\n";
    } catch (\Exception $e) {
        echo "❌ Error assigning role: " . $e->getMessage() . "\n";
    }
}

echo "\n3. Current status check...\n";
$currentRoles = DB::table('vaitronguoidung as vt')
    ->leftJoin('hoithao as ht', 'vt.conference_id', '=', 'ht.conference_id') 
    ->where('vt.user_id', $userId)
    ->select('vt.*', 'ht.title as conference_title')
    ->get();

echo "User's current roles:\n";
foreach ($currentRoles as $role) {
    echo "- Role: {$role->role_code}, Conference: " . ($role->conference_title ?: 'Global') . " (ID: {$role->conference_id})\n";
}

echo "\n4. Checking processed_by issue...\n";
$joinRequest = DB::table('join_requests')->where('id', $joinRequestId)->first();
echo "Processed by: " . ($joinRequest->processed_by ?: 'NULL') . "\n";
echo "Processed at: " . ($joinRequest->processed_at ?: 'NULL') . "\n";

if (!$joinRequest->processed_by) {
    echo "❌ This indicates automatic approval, not manual admin approval!\n";
}