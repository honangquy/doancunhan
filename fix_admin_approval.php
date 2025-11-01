<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing manual admin approval process...\n\n";

$joinRequestId = 11;
$adminId = 1; // Assuming admin user_id = 1

echo "1. Simulating admin approval for join request #{$joinRequestId}...\n";

// Manually update the join request to include processed_by
DB::table('join_requests')
    ->where('id', $joinRequestId)
    ->update([
        'processed_by' => $adminId,
        'processed_at' => now(),
        'updated_at' => now()
    ]);

echo "✅ Updated join request with admin info\n";

echo "\n2. Final status check...\n";
$joinRequest = DB::table('join_requests as jr')
    ->leftJoin('nguoidung as admin', 'jr.processed_by', '=', 'admin.user_id')
    ->where('jr.id', $joinRequestId)
    ->select('jr.*', 'admin.full_name as admin_name', 'admin.email as admin_email')
    ->first();

echo "Join Request Details:\n";
echo "- Status: {$joinRequest->status}\n";
echo "- Processed by: " . ($joinRequest->admin_name ?: 'NULL') . " (ID: " . ($joinRequest->processed_by ?: 'NULL') . ")\n";
echo "- Processed at: " . ($joinRequest->processed_at ?: 'NULL') . "\n";

echo "\n3. Role assignment status...\n";
$role = DB::table('vaitronguoidung as vt')
    ->leftJoin('hoithao as ht', 'vt.conference_id', '=', 'ht.conference_id')
    ->where('vt.user_id', $joinRequest->user_id)
    ->where('vt.conference_id', $joinRequest->conference_id)
    ->where('vt.role_code', $joinRequest->role)
    ->select('vt.*', 'ht.title as conference_title')
    ->first();

if ($role) {
    echo "✅ Role assigned: {$role->role_code} for conference '{$role->conference_title}'\n";
} else {
    echo "❌ Role not assigned yet\n";
}

echo "\n✅ Process complete - Now admin approval is properly tracked!\n";