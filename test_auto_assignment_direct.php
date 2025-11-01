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

echo "🎯 Testing Auto Assignment with Workload Balancing\n";
echo "=================================================\n\n";

$paperId = 3;
$reviewerCount = 2;
$minBid = 1;

// Get paper info
$paper = Capsule::table('baibao')->where('paper_id', $paperId)->first();
if (!$paper) {
    echo "❌ Paper not found\n";
    exit;
}

echo "📄 Paper: {$paper->title}\n";
echo "🎯 Target: Assign {$reviewerCount} reviewers (min bid: {$minBid})\n\n";

// Show current assignments (should be empty)
$currentAssignments = Capsule::table('reviewer_assignments as ra')
    ->join('nguoidung as n', 'ra.user_id', '=', 'n.user_id')
    ->where('ra.paper_id', $paperId)
    ->select('n.full_name', 'ra.status')
    ->get();

if ($currentAssignments->count() > 0) {
    echo "📋 Current Assignments:\n";
    foreach ($currentAssignments as $assignment) {
        echo "  👤 {$assignment->full_name} - {$assignment->status}\n";
    }
} else {
    echo "📋 No current assignments\n";
}

echo "\n";

// Show available biddings before assignment
echo "📊 Available Biddings (Before Assignment):\n";
$biddings = Capsule::table('reviewer_bidding as rb')
    ->join('nguoidung as n', 'rb.user_id', '=', 'n.user_id')
    ->leftJoin(
        Capsule::raw('(SELECT user_id, COUNT(*) as current_workload 
                     FROM reviewer_assignments 
                     WHERE conference_id = ' . $paper->conference_id . ' 
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
        Capsule::raw('COALESCE(workload.current_workload, 0) as current_workload'),
        Capsule::raw('(rb.bidding_value * 100 - COALESCE(workload.current_workload, 0) * 10) as balanced_score')
    )
    ->orderByRaw('(rb.bidding_value * 100 - COALESCE(workload.current_workload, 0) * 10) DESC')
    ->get();

foreach ($biddings as $bid) {
    echo "  👤 {$bid->full_name}: Bid {$bid->bidding_value}/3, Workload {$bid->current_workload}, Score {$bid->balanced_score}\n";
}

// Simulate the assignment logic
echo "\n🤖 Performing Auto Assignment...\n";
$selectedReviewers = $biddings->take($reviewerCount);

Capsule::beginTransaction();

try {
    foreach ($selectedReviewers as $reviewer) {
        $assignmentData = [
            'user_id' => $reviewer->user_id,
            'paper_id' => $paperId,
            'conference_id' => $paper->conference_id,
            'assigned_by' => 1, // Assume chair user ID = 1
            'assigned_at' => date('Y-m-d H:i:s'),
            'status' => 'PENDING',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        Capsule::table('reviewer_assignments')->insert($assignmentData);
        echo "  ✅ Assigned {$reviewer->full_name} (Score: {$reviewer->balanced_score})\n";
        
        // Get assignment ID for notification
        $assignmentId = Capsule::table('reviewer_assignments')
            ->where('user_id', $reviewer->user_id)
            ->where('paper_id', $paperId)
            ->value('id');
            
        // Create notification
        Capsule::table('assignment_notifications')->insert([
            'assignment_id' => $assignmentId,
            'notification_type' => 'ASSIGNMENT',
            'status' => 'PENDING',
            'email_content' => "Bạn đã được phân công phản biện bài báo: {$paper->title}",
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    Capsule::commit();
    echo "\n🎉 Auto assignment completed successfully!\n";
    
    // Show final assignments
    echo "\n📋 Final Assignments:\n";
    $finalAssignments = Capsule::table('reviewer_assignments as ra')
        ->join('nguoidung as n', 'ra.user_id', '=', 'n.user_id')
        ->where('ra.paper_id', $paperId)
        ->select('n.full_name', 'ra.status', 'ra.assigned_at')
        ->get();
    
    foreach ($finalAssignments as $assignment) {
        echo "  👤 {$assignment->full_name} - {$assignment->status} (at {$assignment->assigned_at})\n";
    }
    
} catch (Exception $e) {
    Capsule::rollback();
    echo "❌ Assignment failed: " . $e->getMessage() . "\n";
}
?>