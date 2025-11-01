<?php
require_once 'vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Configure Laravel Database
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
    'database' => $_ENV['DB_DATABASE'] ?? 'laravel',
    'username' => $_ENV['DB_USERNAME'] ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "🔔 Testing Assignment Notification System\n";
echo "========================================\n\n";

// Test assignment that should trigger notifications
$paperId = 3; // "địt cả lò nhà mày"
$reviewerCount = 2;
$minBid = 1;

echo "📋 Creating assignments for paper {$paperId} to test notifications...\n\n";

// Clear existing assignments and notifications for this paper
Capsule::table('reviewer_assignments')->where('paper_id', $paperId)->delete();

// Get available reviewers
$availableReviewers = Capsule::table('reviewer_bidding as rb')
    ->join('nguoidung as n', 'rb.user_id', '=', 'n.user_id')
    ->leftJoin(
        Capsule::raw('(SELECT user_id, COUNT(*) as current_workload 
                     FROM reviewer_assignments 
                     WHERE conference_id = 1 
                     GROUP BY user_id) as workload'), 
        'rb.user_id', '=', 'workload.user_id'
    )
    ->where('rb.paper_id', $paperId)
    ->where('rb.coi', false)
    ->where('rb.bidding_value', '>=', $minBid)
    ->select(
        'rb.user_id', 
        'rb.bidding_value', 
        'n.full_name',
        'n.email',
        Capsule::raw('COALESCE(workload.current_workload, 0) as current_workload')
    )
    ->orderByRaw('(rb.bidding_value * 100 - COALESCE(workload.current_workload, 0) * 10) DESC')
    ->limit($reviewerCount)
    ->get();

if ($availableReviewers->isEmpty()) {
    echo "❌ No reviewers available for paper {$paperId}\n";
    echo "Creating some biddings first...\n";
    
    // Get some reviewers to create biddings
    $reviewers = Capsule::table('vaitronguoidung as vr')
        ->join('nguoidung as n', 'vr.user_id', '=', 'n.user_id')
        ->where('vr.role_code', 'REVIEWER')
        ->where('vr.conference_id', 1)
        ->select('n.user_id', 'n.full_name', 'n.email')
        ->limit(3)
        ->get();
    
    foreach ($reviewers as $reviewer) {
        Capsule::table('reviewer_bidding')->insert([
            'user_id' => $reviewer->user_id,
            'paper_id' => $paperId,
            'conference_id' => 1,
            'bidding_value' => rand(2, 3),
            'coi' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "  ✅ Created bidding for {$reviewer->full_name}\n";
    }
    
    // Re-fetch available reviewers
    $availableReviewers = Capsule::table('reviewer_bidding as rb')
        ->join('nguoidung as n', 'rb.user_id', '=', 'n.user_id')
        ->where('rb.paper_id', $paperId)
        ->where('rb.coi', false)
        ->where('rb.bidding_value', '>=', $minBid)
        ->select('rb.user_id', 'rb.bidding_value', 'n.full_name', 'n.email')
        ->limit($reviewerCount)
        ->get();
}

echo "\n🎯 Creating assignments and notifications:\n";

foreach ($availableReviewers as $reviewer) {
    // Create assignment
    $assignmentId = Capsule::table('reviewer_assignments')->insertGetId([
        'user_id' => $reviewer->user_id,
        'paper_id' => $paperId,
        'conference_id' => 1,
        'assigned_by' => 1, // Chair user ID
        'assignment_method' => 'AUTO',
        'status' => 'PENDING',
        'assigned_at' => now(),
        'created_at' => now(),
        'updated_at' => now()
    ]);

    // Create notification
    Capsule::table('assignment_notifications')->insert([
        'assignment_id' => $assignmentId,
        'notification_type' => 'ASSIGNMENT',
        'status' => 'PENDING',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    echo "  ✅ Assigned {$reviewer->full_name} (Bid: {$reviewer->bidding_value})\n";
    echo "     📧 Email: {$reviewer->email}\n";
    echo "     🔔 Notification created\n\n";
}

echo "🔍 Checking notification status:\n";

$notifications = Capsule::table('assignment_notifications as an')
    ->join('reviewer_assignments as ra', 'an.assignment_id', '=', 'ra.id')
    ->join('nguoidung as n', 'ra.user_id', '=', 'n.user_id')
    ->join('baibao as b', 'ra.paper_id', '=', 'b.paper_id')
    ->where('ra.paper_id', $paperId)
    ->select(
        'an.id as notification_id',
        'an.status as notification_status',
        'n.full_name',
        'b.title as paper_title',
        'an.created_at'
    )
    ->get();

foreach ($notifications as $notification) {
    echo "  📩 {$notification->full_name}: {$notification->notification_status}\n";
    echo "     Paper: {$notification->paper_title}\n";
    echo "     Created: {$notification->created_at}\n\n";
}

echo "📊 Summary:\n";
echo "  Total assignments created: " . $availableReviewers->count() . "\n";
echo "  Total notifications created: " . $notifications->count() . "\n";

echo "\n✅ Notification system test completed!\n";
echo "🌐 You can now check the reviewer dashboard for live notifications.\n";
?>