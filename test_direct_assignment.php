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

echo "🎯 Testing Auto Assignment Algorithm Directly\n";
echo "=============================================\n\n";

// Clear existing assignments for paper 2
echo "🧹 Clearing existing assignments for paper 2...\n";
Capsule::table('reviewer_assignments')->where('paper_id', 2)->delete();

// Test auto assignment logic directly
$paperId = 2;
$reviewerCount = 2;
$minBid = 1;
$conferenceId = 1;

echo "\n📊 Testing Auto Assignment for Paper {$paperId}:\n";
echo "  • Required reviewers: {$reviewerCount}\n";
echo "  • Minimum bid: {$minBid}\n\n";

// Get paper info
$paper = Capsule::table('baibao')->where('paper_id', $paperId)->first();
if (!$paper) {
    echo "❌ Paper not found!\n";
    exit;
}

echo "📄 Paper: {$paper->title}\n\n";

// Get available reviewers with workload balancing (same logic as controller)
$availableReviewers = Capsule::table('reviewer_bidding as rb')
    ->join('nguoidung as n', 'rb.user_id', '=', 'n.user_id')
    ->leftJoin('reviewer_assignments as existing_ra', function($join) use ($paperId) {
        $join->on('rb.user_id', '=', 'existing_ra.user_id')
             ->where('existing_ra.paper_id', '=', $paperId);
    })
    ->leftJoin(
        Capsule::raw('(SELECT user_id, COUNT(*) as current_workload 
                     FROM reviewer_assignments 
                     WHERE conference_id = ' . $conferenceId . ' 
                     GROUP BY user_id) as workload'), 
        'rb.user_id', '=', 'workload.user_id'
    )
    ->where('rb.paper_id', $paperId)
    ->where('rb.coi', false)
    ->where('rb.bidding_value', '>=', $minBid)
    ->whereNull('existing_ra.id') // Not already assigned to this paper
    ->select(
        'rb.user_id', 
        'rb.bidding_value', 
        'n.full_name',
        Capsule::raw('COALESCE(workload.current_workload, 0) as current_workload')
    )
    // Advanced scoring: bid_value * 100 - current_workload * 10
    ->orderByRaw('(rb.bidding_value * 100 - COALESCE(workload.current_workload, 0) * 10) DESC')
    ->limit($reviewerCount)
    ->get();

echo "🔍 Available Reviewers (ranked by balanced score):\n";
foreach ($availableReviewers as $reviewer) {
    $balancedScore = ($reviewer->bidding_value * 100) - ($reviewer->current_workload * 10);
    echo "  👤 {$reviewer->full_name}:\n";
    echo "    📊 Bid: {$reviewer->bidding_value}/3\n";
    echo "    💼 Current Workload: {$reviewer->current_workload}\n";
    echo "    🏆 Balanced Score: {$balancedScore}\n\n";
}

if ($availableReviewers->count() < $reviewerCount) {
    echo "❌ Insufficient reviewers! Need {$reviewerCount}, found {$availableReviewers->count()}\n";
    exit;
}

// Create assignments
echo "✅ Creating assignments...\n";
foreach ($availableReviewers as $reviewer) {
    Capsule::table('reviewer_assignments')->insert([
        'user_id' => $reviewer->user_id,
        'paper_id' => $paperId,
        'conference_id' => $conferenceId,
        'assigned_by' => 1, // Assuming chair user ID = 1
        'assigned_at' => now(),
        'status' => 'PENDING',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "  ✅ Assigned: {$reviewer->full_name}\n";
}

echo "\n🎉 Auto assignment completed successfully!\n\n";

// Show final results
echo "📋 Final Assignment Results:\n";
$finalAssignments = Capsule::table('reviewer_assignments as ra')
    ->join('nguoidung as n', 'ra.user_id', '=', 'n.user_id')
    ->where('ra.paper_id', $paperId)
    ->select('n.full_name', 'ra.status', 'ra.assigned_at')
    ->get();

foreach ($finalAssignments as $assignment) {
    echo "  ✅ {$assignment->full_name} - {$assignment->status} - {$assignment->assigned_at}\n";
}

echo "\n🔍 Updated Workload Distribution:\n";
$updatedWorkloads = Capsule::table('reviewer_assignments as ra')
    ->join('nguoidung as n', 'ra.user_id', '=', 'n.user_id')
    ->select(
        'ra.user_id',
        'n.full_name',
        Capsule::raw('COUNT(*) as assignment_count')
    )
    ->where('ra.conference_id', $conferenceId)
    ->groupBy('ra.user_id', 'n.full_name')
    ->orderBy('assignment_count', 'desc')
    ->get();

foreach ($updatedWorkloads as $workload) {
    echo "  👤 {$workload->full_name}: {$workload->assignment_count} assignments\n";
}
?>