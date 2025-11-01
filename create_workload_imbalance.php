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

echo "🧪 Creating Workload Imbalance for Testing\n";
echo "=========================================\n\n";

// Give one reviewer (ID 5) lots of assignments to test workload balancing
$heavyWorkloadUserId = 5; // "Tác giả 5"

echo "📊 Adding extra assignments to user ID {$heavyWorkloadUserId}...\n";

// Add 3 more assignments to this user to create workload imbalance
$paperIds = [1, 2, 4]; // Different papers

foreach ($paperIds as $paperId) {
    // Check if assignment already exists to avoid duplicate
    $exists = Capsule::table('reviewer_assignments')
        ->where('user_id', $heavyWorkloadUserId)
        ->where('paper_id', $paperId)
        ->exists();
        
    if (!$exists) {
        Capsule::table('reviewer_assignments')->insert([
            'user_id' => $heavyWorkloadUserId,
            'paper_id' => $paperId,
            'conference_id' => 1,
            'assigned_by' => 1,
            'assigned_at' => date('Y-m-d H:i:s'),
            'status' => 'PENDING',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        echo "  ✅ Assigned to paper {$paperId}\n";
    } else {
        echo "  ⚠️ Assignment to paper {$paperId} already exists\n";
    }
}

echo "\n✅ Extra assignments processing completed\n\n";

// Show new workload distribution
echo "📊 Updated Reviewer Workloads:\n";
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

echo "\n🎯 Now test with: php test_workload_balancing.php\n";
echo "This should show the workload balancing effect!\n";
?>