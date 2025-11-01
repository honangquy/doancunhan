<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ReviewerBidding;
use App\Models\BaiBao;
use App\Models\VaiTroNguoiDung;
use Illuminate\Support\Facades\DB;

echo "=== CREATING SAMPLE BIDDINGS FOR ASSIGNMENT TESTING ===\n";

// Get all papers
$papers = BaiBao::whereIn('status_code', ['SUBMITTED', 'UNDER_REVIEW'])->get();

// Get all reviewers across conferences
$reviewers = DB::table('vaitronguoidung as vr')
    ->join('nguoidung as n', 'vr.user_id', '=', 'n.user_id')
    ->where('vr.role_code', 'REVIEWER')
    ->select('vr.user_id', 'vr.conference_id', 'n.full_name', 'n.email')
    ->get();

echo "Found {$papers->count()} papers and {$reviewers->count()} reviewers\n\n";

// Create diverse biddings
$bidValues = [0, 1, 2, 3]; // No bid, Willing, Able, Eager
$coiChance = 0.1; // 10% chance of COI

foreach ($papers as $paper) {
    echo "Creating bids for paper: {$paper->title}\n";
    
    $relevantReviewers = $reviewers->where('conference_id', $paper->conference_id);
    
    foreach ($relevantReviewers as $reviewer) {
        // Skip existing bids
        $existingBid = ReviewerBidding::where('user_id', $reviewer->user_id)
            ->where('paper_id', $paper->paper_id)
            ->exists();
            
        if ($existingBid) {
            echo "  - Skip {$reviewer->full_name} (already has bid)\n";
            continue;
        }
        
        // Random bid value (weighted toward positive bids)
        $bidValue = rand(0, 10) > 2 ? $bidValues[rand(1, 3)] : 0; // 80% chance of positive bid
        
        // Random COI (low chance)
        $hasCOI = rand(1, 100) <= ($coiChance * 100);
        
        $bidding = ReviewerBidding::create([
            'user_id' => $reviewer->user_id,
            'paper_id' => $paper->paper_id,
            'conference_id' => $paper->conference_id,
            'bidding_value' => $bidValue,
            'coi' => $hasCOI,
            'coi_reason' => $hasCOI ? 'Sample COI for testing purposes' : null,
            'note' => $bidValue > 2 ? 'Very interested in this research area' : ($bidValue == 0 ? 'Outside my expertise' : 'Can contribute if needed')
        ]);
        
        $bidLabel = ['No bid', 'Willing', 'Able', 'Eager'][$bidValue];
        $coiStatus = $hasCOI ? ' [COI]' : '';
        echo "  - {$reviewer->full_name}: {$bidLabel}{$coiStatus}\n";
    }
    echo "\n";
}

echo "=== BIDDING STATISTICS ===\n";

foreach ($papers as $paper) {
    $bids = ReviewerBidding::where('paper_id', $paper->paper_id)->get();
    $avgBid = $bids->avg('bidding_value');
    $maxBid = $bids->max('bidding_value');
    $coiCount = $bids->where('coi', true)->count();
    $totalBidders = $bids->count();
    
    echo "Paper: {$paper->title}\n";
    echo "  Total bidders: {$totalBidders}\n";
    echo "  Average bid: " . number_format($avgBid, 2) . "\n";
    echo "  Max bid: {$maxBid}\n";
    echo "  COI declarations: {$coiCount}\n";
    
    // Show bid distribution
    for ($i = 0; $i <= 3; $i++) {
        $count = $bids->where('bidding_value', $i)->count();
        $label = ['No bid', 'Willing', 'Able', 'Eager'][$i];
        echo "  {$label}: {$count}\n";
    }
    echo "\n";
}

echo "✅ Sample biddings created for assignment algorithm testing!\n";

?>