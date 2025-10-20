<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Check user 50's current roles
$userRoles = DB::table('VaiTroNguoiDung')
    ->where('user_id', 50)
    ->select('role_code', 'conference_id')
    ->get();

echo "🔍 Current roles for User ID 50:\n";
if ($userRoles->count() > 0) {
    foreach ($userRoles as $role) {
        echo "- Role: {$role->role_code}";
        if ($role->conference_id) {
            echo " (Conference ID: {$role->conference_id})";
        } else {
            echo " (Global role)";
        }
        echo "\n";
    }
} else {
    echo "- No roles assigned yet\n";
}

echo "\n📋 Pending join request:\n";
$joinRequest = DB::table('join_requests')->where('id', 4)->first();
if ($joinRequest) {
    echo "- Request ID: {$joinRequest->id}\n";
    echo "- User ID: {$joinRequest->user_id}\n";
    echo "- Conference ID: {$joinRequest->conference_id}\n";
    echo "- Requested Role: {$joinRequest->role}\n";
    echo "- Status: {$joinRequest->status}\n";
}