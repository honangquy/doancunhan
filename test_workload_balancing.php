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

echo "🧪 Testing Workload Balancing Algorithm\n";
echo "=====================================\n\n";

// Get current reviewer workloads
echo "📊 Current Reviewer Workloads:\n";
$workloads = Capsule::table('reviewer_assignments as ra')
    ->join('nguoidung as n', 'ra.user_id', '=', 'n.user_id')
    ->select(
        'ra.user_id',
        'n.full_name',
        Capsule::raw('COUNT(*) as assignment_count')
    )
    ->groupBy('ra.user_id', 'n.full_name')
    ->orderBy('assignment_count', 'desc')
    ->get();

foreach ($workloads as $workload) {
    echo "  👤 {$workload->full_name}: {$workload->assignment_count} assignments\n";
}
echo "\n";

// Test the balanced assignment algorithm for a specific paper
$testPaperId = 2; // "Địt"
$conferenceId = 1;

echo "🎯 Testing Balanced Assignment for Paper ID: {$testPaperId}\n";

// Show bidding status
echo "\n📋 Available Biddings:\n";
$biddings = Capsule::table('reviewer_bidding as rb')
    ->join('nguoidung as n', 'rb.user_id', '=', 'n.user_id')
    ->leftJoin(
        Capsule::raw('(SELECT user_id, COUNT(*) as current_workload 
                     FROM reviewer_assignments 
                     WHERE conference_id = ' . $conferenceId . ' 
                     GROUP BY user_id) as workload'), 
        'rb.user_id', '=', 'workload.user_id'
    )
    ->where('rb.paper_id', $testPaperId)
    ->where('rb.coi', false)
    ->where('rb.bidding_value', '>=', 1) // Min bid = 1
    ->select(
        'rb.user_id', 
        'rb.bidding_value', 
        'n.full_name',
        Capsule::raw('COALESCE(workload.current_workload, 0) as current_workload'),
        Capsule::raw('(rb.bidding_value * 100 - COALESCE(workload.current_workload, 0) * 10) as balanced_score')
    )
    ->orderByRaw('(rb.bidding_value * 100 - COALESCE(workload.current_workload, 0) * 10) DESC')
    ->get();

if ($biddings->isEmpty()) {
    echo "  ❌ No biddings found for paper ID {$testPaperId}\n";
    echo "  💡 Run: php create_workload_test_biddings.php first!\n";
    exit;
}

foreach ($biddings as $bid) {
    echo "  👤 {$bid->full_name}:\n";
    echo "    📊 Bid: {$bid->bidding_value}/3\n";
    echo "    💼 Current Workload: {$bid->current_workload} assignments\n";
    echo "    🏆 Balanced Score: {$bid->balanced_score}\n";
    echo "\n";
}

// Show selection priority
echo "🎲 Selection Priority (Top 2):\n";
$topReviewers = $biddings->take(2);
foreach ($topReviewers as $index => $reviewer) {
    echo "  " . ($index + 1) . ". {$reviewer->full_name} (Score: {$reviewer->balanced_score})\n";
}

echo "\n✅ Workload balancing test completed!\n\n";

echo "📝 Algorithm Explanation:\n";
echo "  • Balanced Score = (Bid Value × 100) - (Current Workload × 10)\n";
echo "  • Higher bid values are prioritized\n";
echo "  • Reviewers with lower workload get bonus points\n";
echo "  • COI conflicts are automatically excluded\n";
echo "  • Minimum bid threshold prevents unwilling assignments\n";
?>