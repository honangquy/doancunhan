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

echo "📋 Testing Assignment Tracking System\n";
echo "===================================\n\n";

// Check current assignments
echo "📊 Current Assignment Status:\n";
$assignments = Capsule::table('reviewer_assignments as ra')
    ->join('baibao as b', 'ra.paper_id', '=', 'b.paper_id')
    ->join('nguoidung as reviewer', 'ra.user_id', '=', 'reviewer.user_id')
    ->join('nguoidung as assigner', 'ra.assigned_by', '=', 'assigner.user_id')
    ->select(
        'ra.id',
        'ra.status',
        'ra.assigned_at',
        'b.title as paper_title',
        'reviewer.full_name as reviewer_name',
        'assigner.full_name as assigned_by_name',
        'ra.assignment_method'
    )
    ->orderBy('ra.assigned_at', 'desc')
    ->get();

if ($assignments->isEmpty()) {
    echo "  ❌ No assignments found\n";
} else {
    foreach ($assignments as $assignment) {
        $statusIcon = [
            'PENDING' => '⏳',
            'ACCEPTED' => '✅', 
            'DECLINED' => '❌',
            'COMPLETED' => '🎯'
        ][$assignment->status] ?? '❓';
        
        echo "  {$statusIcon} Assignment #{$assignment->id}:\n";
        echo "    📄 Paper: {$assignment->paper_title}\n";
        echo "    👤 Reviewer: {$assignment->reviewer_name}\n";
        echo "    📋 Status: {$assignment->status}\n";
        echo "    📅 Assigned: {$assignment->assigned_at}\n";
        echo "    🔄 Method: {$assignment->assignment_method}\n";
        echo "    👨‍💼 Assigned by: {$assignment->assigned_by_name}\n\n";
    }
}

// Test assignment tracking statistics
echo "📈 Assignment Statistics by Status:\n";
$statusStats = Capsule::table('reviewer_assignments')
    ->selectRaw('status, COUNT(*) as count')
    ->groupBy('status')
    ->get();

foreach ($statusStats as $stat) {
    $statusIcon = [
        'PENDING' => '⏳',
        'ACCEPTED' => '✅', 
        'DECLINED' => '❌',
        'COMPLETED' => '🎯'
    ][$stat->status] ?? '❓';
    
    echo "  {$statusIcon} {$stat->status}: {$stat->count} assignments\n";
}

echo "\n📊 Assignment Statistics by Reviewer:\n";
$reviewerStats = Capsule::table('reviewer_assignments as ra')
    ->join('nguoidung as n', 'ra.user_id', '=', 'n.user_id')
    ->selectRaw('n.full_name, ra.status, COUNT(*) as count')
    ->groupBy('n.full_name', 'ra.status')
    ->orderBy('n.full_name')
    ->get();

$reviewerStatsGrouped = $reviewerStats->groupBy('full_name');

foreach ($reviewerStatsGrouped as $reviewerName => $stats) {
    echo "  👤 {$reviewerName}:\n";
    foreach ($stats as $stat) {
        $statusIcon = [
            'PENDING' => '⏳',
            'ACCEPTED' => '✅', 
            'DECLINED' => '❌',
            'COMPLETED' => '🎯'
        ][$stat->status] ?? '❓';
        echo "    {$statusIcon} {$stat->status}: {$stat->count}\n";
    }
    echo "\n";
}

echo "🔍 Assignment Method Distribution:\n";
$methodStats = Capsule::table('reviewer_assignments')
    ->selectRaw('assignment_method, COUNT(*) as count')
    ->groupBy('assignment_method')
    ->get();

foreach ($methodStats as $stat) {
    $methodIcon = $stat->assignment_method === 'AUTO' ? '🤖' : '👤';
    echo "  {$methodIcon} {$stat->assignment_method}: {$stat->count} assignments\n";
}

echo "\n✅ Assignment tracking test completed!\n";
echo "🌐 You can now view assignments at: /reviewer/assignments\n";
?>