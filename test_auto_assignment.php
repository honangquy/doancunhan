<?php
/**
 * Test Auto Assignment Feature
 * Run with: php artisan tinker --execute="require 'test_auto_assignment.php'"
 */

use Illuminate\Support\Facades\DB;
use App\Models\BaiBao;
use App\Models\ReviewerBidding;
use App\Models\ReviewerAssignment;

echo "=== TESTING AUTO ASSIGNMENT FEATURE ===\n\n";

// Test 1: Check database tables
echo "1. Checking required database tables...\n";
$tables = ['baibao', 'reviewer_bidding', 'reviewer_assignments', 'nguoidung'];

try {
    foreach ($tables as $table) {
        $count = DB::table($table)->count();
        echo "   ✓ {$table}: {$count} records\n";
    }
} catch (Exception $e) {
    echo "   ✗ Database error: " . $e->getMessage() . "\n";
}

// Test 2: Check papers without assignments
echo "\n2. Papers without assignments:\n";
try {
    $unassignedPapers = DB::table('baibao as b')
        ->leftJoin('reviewer_assignments as ra', 'b.paper_id', '=', 'ra.paper_id')
        ->whereNull('ra.paper_id')
        ->select('b.paper_id', 'b.title', 'b.conference_id')
        ->get();
    
    foreach ($unassignedPapers as $paper) {
        echo "   Paper {$paper->paper_id}: {$paper->title}\n";
    }
    
    if ($unassignedPapers->count() === 0) {
        echo "   All papers already have assignments\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Test 3: Check available reviewers
echo "\n3. Available reviewers for auto assignment:\n";
try {
    $reviewers = DB::table('reviewer_bidding as rb')
        ->join('nguoidung as n', 'rb.user_id', '=', 'n.user_id')
        ->where('rb.coi', false)
        ->where('rb.bidding_value', '>=', 1)
        ->select('rb.user_id', 'n.full_name', 'rb.paper_id', 'rb.bidding_value')
        ->get()
        ->groupBy('paper_id');
    
    foreach ($reviewers as $paperId => $paperReviewers) {
        echo "   Paper {$paperId}: {$paperReviewers->count()} available reviewers\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Test 4: Simulation test
echo "\n4. Simulating auto assignment...\n";
if (isset($unassignedPapers) && $unassignedPapers->count() > 0) {
    $testPaper = $unassignedPapers->first();
    echo "   Testing with Paper ID: {$testPaper->paper_id}\n";
    echo "   Paper Title: {$testPaper->title}\n";
    
    // Check biddings for this paper
    try {
        $biddings = DB::table('reviewer_bidding')
            ->where('paper_id', $testPaper->paper_id)
            ->where('coi', false)
            ->where('bidding_value', '>=', 1)
            ->count();
        
        echo "   Available biddings: {$biddings}\n";
        
        if ($biddings >= 3) {
            echo "   ✓ Can assign 3 reviewers\n";
        } else {
            echo "   ⚠ Can only assign {$biddings} reviewers\n";
        }
    } catch (Exception $e) {
        echo "   ✗ Error checking biddings: " . $e->getMessage() . "\n";
    }
} else {
    echo "   No unassigned papers available for testing\n";
}

echo "\n=== TEST COMPLETE ===\n";