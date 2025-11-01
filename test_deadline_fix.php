<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Updated Dashboard With Deadline ===" . PHP_EOL;

try {
    $userId = 11; // Test user
    
    echo "Testing for User ID: $userId" . PHP_EOL . PHP_EOL;
    
    // Test the updated query with deadline
    $assignments = DB::table('reviewer_assignments as ra')
        ->where('ra.user_id', $userId)
        ->join('baibao', 'ra.paper_id', '=', 'baibao.paper_id')
        ->join('hoithao', 'baibao.conference_id', '=', 'hoithao.conference_id')
        ->join('nguoidung as Submitter', 'baibao.submitter_id', '=', 'Submitter.user_id')
        ->leftjoin('phanbien', 'ra.id', '=', 'phanbien.assignment_id')
        ->leftJoin('loaikhuyennghi as LoaiKhuyenNghi', 'phanbien.recommendation_code', '=', 'LoaiKhuyenNghi.recommendation_code')
        ->select(
            'ra.id as assignment_id',
            'ra.status as assignment_status',
            'ra.assigned_at',
            'hoithao.deadline_review as deadline',
            'baibao.paper_id',
            'baibao.title as paper_title',
            'hoithao.title as conference_name',
            'Submitter.full_name as author_name',
            'phanbien.review_id',
            'phanbien.recommendation_code',
            'LoaiKhuyenNghi.recommendation_name',
            'phanbien.score',
            'ra.review_submitted_at'
        )
        ->orderBy('ra.assigned_at', 'desc')
        ->get();
    
    echo "Assignments found: " . $assignments->count() . PHP_EOL;
    
    foreach ($assignments as $assignment) {
        echo "  - Assignment ID: {$assignment->assignment_id}" . PHP_EOL;
        echo "    Status: {$assignment->assignment_status}" . PHP_EOL;
        echo "    Paper: {$assignment->paper_title}" . PHP_EOL;
        echo "    Conference: {$assignment->conference_name}" . PHP_EOL;
        echo "    Author: {$assignment->author_name}" . PHP_EOL;
        echo "    Deadline: " . ($assignment->deadline ?: 'No deadline set') . PHP_EOL;
        echo "    Assigned at: {$assignment->assigned_at}" . PHP_EOL . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo "Stack trace:" . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}
?>