<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ReviewerAssignment;
use App\Models\ReviewerBidding;
use App\Models\BaiBao;
use App\Models\VaiTroNguoiDung;
use Illuminate\Support\Facades\DB;

echo "=== TESTING ASSIGNMENT ALGORITHMS ===\n";

// Get a paper with good bidding data
$paper = BaiBao::where('title', 'ssssssss')->first(); // Has 2 Eager bids

echo "Testing with paper: {$paper->title} (ID: {$paper->paper_id})\n";

// Show available reviewers for this paper
$biddings = ReviewerBidding::where('paper_id', $paper->paper_id)
    ->join('nguoidung as n', 'reviewer_bidding.user_id', '=', 'n.user_id')
    ->select('reviewer_bidding.*', 'n.full_name')
    ->get();

echo "\nAvailable biddings:\n";
foreach ($biddings as $bid) {
    $bidLabel = ['No bid', 'Willing', 'Able', 'Eager'][$bid->bidding_value];
    $coiStatus = $bid->coi ? ' [COI]' : '';
    echo "  - {$bid->full_name}: {$bidLabel}{$coiStatus} (User ID: {$bid->user_id})\n";
}

// Test Manual Assignment
echo "\n=== TESTING MANUAL ASSIGNMENT ===\n";

// Get chair user (should be user who has CHAIR role for this conference)
$chair = VaiTroNguoiDung::where('conference_id', $paper->conference_id)
    ->where('role_code', 'CHAIR')
    ->first();

if (!$chair) {
    echo "❌ No chair found for conference {$paper->conference_id}\n";
    return;
}

echo "Chair: User ID {$chair->user_id}\n";

// Get reviewers with positive bids (no COI)
$availableReviewers = $biddings->where('coi', false)
    ->where('bidding_value', '>', 0)
    ->pluck('user_id')
    ->toArray();

echo "Reviewers available for assignment: " . implode(', ', $availableReviewers) . "\n";

if (count($availableReviewers) >= 2) {
    // Create manual assignment for first 2 reviewers
    $assignedReviewers = array_slice($availableReviewers, 0, 2);
    
    echo "Creating manual assignment for reviewers: " . implode(', ', $assignedReviewers) . "\n";
    
    foreach ($assignedReviewers as $reviewerId) {
        // Check if already assigned
        $existingAssignment = ReviewerAssignment::where('user_id', $reviewerId)
            ->where('paper_id', $paper->paper_id)
            ->exists();
            
        if ($existingAssignment) {
            echo "  - Reviewer {$reviewerId} already assigned, skipping\n";
            continue;
        }
        
        $bidding = $biddings->where('user_id', $reviewerId)->first();
        
        $assignment = ReviewerAssignment::create([
            'user_id' => $reviewerId,
            'paper_id' => $paper->paper_id,
            'conference_id' => $paper->conference_id,
            'assigned_by' => $chair->user_id,
            'assignment_method' => 'MANUAL',
            'status' => 'PENDING',
            'assigned_at' => now(),
            'assignment_metadata' => [
                'bid_value' => $bidding->bidding_value,
                'coi_status' => $bidding->coi,
                'assigned_timestamp' => now()->toISOString()
            ]
        ]);
        
        echo "  ✅ Assigned reviewer {$reviewerId} (bid: {$bidding->bidding_value})\n";
    }
}

// Show final assignment results
echo "\n=== ASSIGNMENT RESULTS ===\n";

$assignments = ReviewerAssignment::where('reviewer_assignments.paper_id', $paper->paper_id)
    ->join('nguoidung as n', 'reviewer_assignments.user_id', '=', 'n.user_id')
    ->leftJoin('reviewer_bidding as rb', function($join) use ($paper) {
        $join->on('reviewer_assignments.user_id', '=', 'rb.user_id')
             ->where('rb.paper_id', '=', $paper->paper_id);
    })
    ->select(
        'reviewer_assignments.*', 
        'n.full_name',
        'rb.bidding_value',
        'rb.coi'
    )
    ->get();

echo "Paper: {$paper->title}\n";
echo "Total assignments: {$assignments->count()}\n\n";

foreach ($assignments as $assignment) {
    $bidLabel = ['No bid', 'Willing', 'Able', 'Eager'][$assignment->bidding_value ?? 0];
    $coiStatus = $assignment->coi ? ' [COI]' : '';
    echo "  - {$assignment->full_name}\n";
    echo "    Method: {$assignment->assignment_method}\n";
    echo "    Status: {$assignment->status}\n";
    echo "    Bid: {$bidLabel}{$coiStatus}\n";
    echo "    Assigned: {$assignment->assigned_at}\n\n";
}

// Test another paper for auto-assignment
echo "=== TESTING AUTO-ASSIGNMENT ===\n";

$paper2 = BaiBao::where('title', 'Nhận được tin ông đã từ trần')->first();
echo "Testing auto-assignment with paper: {$paper2->title}\n";

$biddings2 = ReviewerBidding::where('paper_id', $paper2->paper_id)
    ->join('nguoidung as n', 'reviewer_bidding.user_id', '=', 'n.user_id')
    ->select('reviewer_bidding.*', 'n.full_name')
    ->where('reviewer_bidding.coi', false) // No COI
    ->where('reviewer_bidding.bidding_value', '>=', 2) // Bid >= 2 (Able or Eager)
    ->orderBy('reviewer_bidding.bidding_value', 'desc')
    ->get();

echo "Auto-assignment candidates (bid >= 2, no COI):\n";
foreach ($biddings2 as $bid) {
    $bidLabel = ['No bid', 'Willing', 'Able', 'Eager'][$bid->bidding_value];
    echo "  - {$bid->full_name}: {$bidLabel} (bid: {$bid->bidding_value})\n";
}

if ($biddings2->count() >= 2) {
    $selectedReviewers = $biddings2->take(2);
    
    echo "\nCreating auto-assignments:\n";
    foreach ($selectedReviewers as $reviewer) {
        $existingAssignment = ReviewerAssignment::where('user_id', $reviewer->user_id)
            ->where('paper_id', $paper2->paper_id)
            ->exists();
            
        if ($existingAssignment) {
            echo "  - Reviewer {$reviewer->user_id} already assigned, skipping\n";
            continue;
        }
        
        $assignment = ReviewerAssignment::create([
            'user_id' => $reviewer->user_id,
            'paper_id' => $paper2->paper_id,
            'conference_id' => $paper2->conference_id,
            'assigned_by' => $chair->user_id,
            'assignment_method' => 'AUTO',
            'status' => 'PENDING',
            'assigned_at' => now(),
            'assignment_metadata' => [
                'bid_value' => $reviewer->bidding_value,
                'coi_status' => false,
                'assigned_timestamp' => now()->toISOString(),
                'auto_assignment_criteria' => [
                    'min_bid' => 2,
                    'reviewer_count' => 2
                ]
            ]
        ]);
        
        echo "  ✅ Auto-assigned {$reviewer->full_name} (bid: {$reviewer->bidding_value})\n";
    }
}

echo "\n🎉 Assignment algorithm testing completed!\n";

?>