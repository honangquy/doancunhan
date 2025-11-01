<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking join request #11...\n\n";

// Check the specific join request
$joinRequest = DB::table('join_requests')
    ->where('id', 11)
    ->first();

if (!$joinRequest) {
    echo "Join request #11 not found!\n";
    exit;
}

echo "Join Request #11 Details:\n";
echo "- User ID: {$joinRequest->user_id}\n";
echo "- Conference ID: {$joinRequest->conference_id}\n";
echo "- Role: {$joinRequest->role}\n";
echo "- Status: {$joinRequest->status}\n";
echo "- Email: {$joinRequest->email_contact}\n";
echo "- Full Name: {$joinRequest->full_name}\n";
echo "- Organization: {$joinRequest->organization}\n";
echo "- Invitation Token: {$joinRequest->invitation_token}\n";
echo "- Created At: {$joinRequest->created_at}\n";
echo "- Updated At: {$joinRequest->updated_at}\n";

if ($joinRequest->processed_by) {
    echo "- Processed By: {$joinRequest->processed_by}\n";
    echo "- Processed At: {$joinRequest->processed_at}\n";
    echo "- Admin Notes: {$joinRequest->admin_notes}\n";
}

echo "\n";

// Check if user exists and their current roles
$user = DB::table('nguoidung')->where('user_id', $joinRequest->user_id)->first();
if ($user) {
    echo "User Details:\n";
    echo "- Email: {$user->email}\n";
    echo "- Full Name: {$user->full_name}\n";
    echo "- Organization: {$user->organization}\n";
    echo "\n";
    
    // Check current roles
    $roles = DB::table('vaitronguoidung as vt')
        ->leftJoin('hoithao as ht', 'vt.conference_id', '=', 'ht.conference_id')
        ->where('vt.user_id', $joinRequest->user_id)
        ->select('vt.*', 'ht.title as conference_title')
        ->get();
    
    echo "Current Roles:\n";
    if ($roles->count() > 0) {
        foreach ($roles as $role) {
            echo "- Role: {$role->role_code}, Conference: " . ($role->conference_title ?: 'Global') . " (ID: {$role->conference_id})\n";
        }
    } else {
        echo "- No roles assigned yet\n";
    }
} else {
    echo "❌ User not found!\n";
}

echo "\n";

// Check if this role should have been assigned
if ($joinRequest->status === 'APPROVED') {
    echo "Status is APPROVED - checking if role was assigned...\n";
    
    $assignedRole = DB::table('vaitronguoidung')
        ->where('user_id', $joinRequest->user_id)
        ->where('conference_id', $joinRequest->conference_id)
        ->where('role_code', $joinRequest->role)
        ->first();
    
    if ($assignedRole) {
        echo "✅ Role {$joinRequest->role} is assigned for conference {$joinRequest->conference_id}\n";
    } else {
        echo "❌ Role {$joinRequest->role} is NOT assigned for conference {$joinRequest->conference_id}\n";
        echo "   This is the problem - approved request but no role assigned!\n";
    }
}