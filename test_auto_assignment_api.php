<?php
require_once 'vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "🎯 Testing Auto Assignment with Workload Balancing\n";
echo "================================================\n\n";

// API endpoint
$url = 'http://127.0.0.1:8000/chair/assignments/auto-assign';

// Request data
$data = [
    'paper_id' => 2,
    'reviewer_count' => 2,
    'min_bid' => 1
];

// Headers
$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer 1|KsVW2rpOwUhIc4Ff7BF1KheJgHTAERLsnu8ZUIyl4683b037'
];

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "🌐 API Response (HTTP {$httpCode}):\n";
echo "==================================\n";

if ($response) {
    $result = json_decode($response, true);
    
    if ($result) {
        echo "✅ Success: " . ($result['success'] ? 'Yes' : 'No') . "\n";
        
        if (isset($result['message'])) {
            echo "📝 Message: {$result['message']}\n";
        }
        
        if (isset($result['assignments'])) {
            echo "\n👥 Assignments Created:\n";
            foreach ($result['assignments'] as $assignment) {
                echo "  • {$assignment['reviewer_name']}\n";
            }
        }
        
        if (isset($result['available_reviewers'])) {
            echo "\n📊 Available Reviewers:\n";
            foreach ($result['available_reviewers'] as $reviewer) {
                echo "  • {$reviewer['name']} (Bid: {$reviewer['bid']}, Workload: {$reviewer['current_workload']})\n";
            }
        }
    } else {
        echo "Raw response:\n{$response}\n";
    }
} else {
    echo "❌ No response received\n";
}

echo "\n🔍 Checking results in database...\n";

// Configure Laravel Database to check results
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

// Check assignments for paper 2
$assignments = Capsule::table('reviewer_assignments as ra')
    ->join('nguoidung as n', 'ra.user_id', '=', 'n.user_id')
    ->where('ra.paper_id', 2)
    ->select('n.full_name', 'ra.assigned_at', 'ra.status')
    ->get();

if ($assignments->count() > 0) {
    echo "\n📋 Current Assignments for Paper 2:\n";
    foreach ($assignments as $assignment) {
        echo "  ✅ {$assignment->full_name} - Status: {$assignment->status} - Assigned: {$assignment->assigned_at}\n";
    }
} else {
    echo "\n❌ No assignments found for paper 2\n";
}
?>