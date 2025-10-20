<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\JoinRequest;

try {
    // Create a test join request for user ID 50 to request AUTHOR role for conference 1
    $joinRequest = JoinRequest::create([
        'user_id' => 50, // Assuming user ID 50 exists
        'conference_id' => 1, // Assuming conference ID 1 exists
        'role' => 'AUTHOR',
        'reason' => 'Testing automatic role assignment after approval',
        'status' => 'PENDING',
        'created_at' => now(),
    ]);

    echo "✅ Test join request created successfully!\n";
    echo "Request ID: " . $joinRequest->id . "\n";
    echo "User ID: " . $joinRequest->user_id . "\n";
    echo "Conference ID: " . $joinRequest->conference_id . "\n";
    echo "Role: " . $joinRequest->role . "\n";
    echo "Status: " . $joinRequest->status . "\n";
    echo "\nNow you can test the approval process via admin dashboard!\n";

} catch (Exception $e) {
    echo "❌ Error creating test join request: " . $e->getMessage() . "\n";
}