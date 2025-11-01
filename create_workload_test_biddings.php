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

echo "🔧 Creating Sample Biddings for Workload Balancing Test\n";
echo "====================================================\n\n";

// Clear existing biddings for paper ID 2  
$testPaperId = 2;
Capsule::table('reviewer_bidding')->where('paper_id', $testPaperId)->delete();

// Get all reviewers
$reviewers = Capsule::table('vaitronguoidung as vr')
    ->join('nguoidung as n', 'vr.user_id', '=', 'n.user_id')
    ->where('vr.role_code', 'REVIEWER')
    ->where('vr.conference_id', 1)
    ->select('n.user_id', 'n.full_name')
    ->get();

echo "👥 Available Reviewers:\n";
foreach ($reviewers as $reviewer) {
    echo "  - {$reviewer->full_name} (ID: {$reviewer->user_id})\n";
}
echo "\n";

// Create varied biddings to test workload balancing
$biddingData = [
    // High bid from reviewer with current workload
    ['user_id' => 5, 'bidding_value' => 3, 'coi' => false], // Phùng Canh Mộ (has 1 assignment)
    // Medium bid from reviewer with current workload  
    ['user_id' => 6, 'bidding_value' => 2, 'coi' => false], // Hồ Năng Quý (has 1 assignment)
    // High bid from reviewer with current workload
    ['user_id' => 7, 'bidding_value' => 3, 'coi' => false], // Quả Lọ (has 1 assignment)
    // Medium bid from reviewer with current workload
    ['user_id' => 8, 'bidding_value' => 2, 'coi' => false], // Mộ Xum Xuê (has 1 assignment)
];

// Get any additional reviewers who might not have workload yet
$additionalReviewers = Capsule::table('nguoidung as n')
    ->join('vaitronguoidung as vr', 'n.user_id', '=', 'vr.user_id')
    ->leftJoin('reviewer_assignments as ra', 'n.user_id', '=', 'ra.user_id')
    ->where('vr.role_code', 'REVIEWER')
    ->where('vr.conference_id', 1)
    ->whereNull('ra.user_id') // No current assignments
    ->select('n.user_id', 'n.full_name')
    ->limit(2)
    ->get();

foreach ($additionalReviewers as $reviewer) {
    $biddingData[] = [
        'user_id' => $reviewer->user_id,
        'bidding_value' => rand(2, 3), // Random high bid
        'coi' => false
    ];
    echo "➕ Added reviewer without workload: {$reviewer->full_name}\n";
}

echo "\n📝 Creating Biddings:\n";
foreach ($biddingData as $bid) {
    $reviewerName = Capsule::table('nguoidung')
        ->where('user_id', $bid['user_id'])
        ->value('full_name');
        
    if ($reviewerName) {
        Capsule::table('reviewer_bidding')->insert([
            'user_id' => $bid['user_id'],
            'paper_id' => $testPaperId,
            'conference_id' => 1,
            'bidding_value' => $bid['bidding_value'],
            'coi' => $bid['coi'],
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "  ✅ {$reviewerName}: Bid {$bid['bidding_value']}/3\n";
    }
}

echo "\n🎯 Sample biddings created for paper '{$testPaperId}'!\n";
echo "Now run: php test_workload_balancing.php\n";
?>