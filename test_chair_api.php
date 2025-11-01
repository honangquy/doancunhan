<?php
/**
 * Test Chair Assignment API
 * Run: php test_chair_api.php
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\VaiTroNguoiDung;
use App\Models\BaiBao;
use App\Models\ReviewerBidding;
use App\Models\ReviewerAssignment;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST CHAIR ASSIGNMENT API ===\n\n";

$chairUserId = 19; // Chair user ID
$conferenceId = 8; // Conference with bidding data

// 1. Test Chair Access
echo "1. Testing Chair Access:\n";
$hasAccess = VaiTroNguoiDung::where('user_id', $chairUserId)
    ->where('conference_id', $conferenceId)
    ->where('role_code', 'CHAIR')
    ->exists();
    
echo "   Chair has access to conference {$conferenceId}: " . ($hasAccess ? 'YES' : 'NO') . "\n";

// 2. Test Papers Query (same as controller)
echo "\n2. Testing Papers Query:\n";
$papers = DB::table('baibao as b')
    ->leftJoin('nguoidung as submitter', 'b.submitter_id', '=', 'submitter.user_id')
    ->leftJoin('reviewer_bidding as rb', 'b.paper_id', '=', 'rb.paper_id')
    ->leftJoin('reviewer_assignments as ra', 'b.paper_id', '=', 'ra.paper_id')
    ->where('b.conference_id', $conferenceId)
    ->whereIn('b.status_code', ['SUBMITTED', 'UNDER_REVIEW'])
    ->select(
        'b.*',
        'submitter.full_name as submitted_by_name',
        DB::raw('COUNT(DISTINCT rb.user_id) as total_bidders'),
        DB::raw('AVG(rb.bidding_value) as avg_bid'),
        DB::raw('MAX(rb.bidding_value) as max_bid'),
        DB::raw('COUNT(DISTINCT CASE WHEN rb.coi = true THEN rb.user_id END) as coi_count'),
        DB::raw('COUNT(DISTINCT ra.user_id) as assigned_reviewers')
    )
    ->groupBy('b.paper_id')
    ->orderBy('b.title')
    ->get();

echo "   Papers found: {$papers->count()}\n";
foreach ($papers as $paper) {
    echo "   - Paper {$paper->paper_id}: {$paper->title}\n";
    echo "     Bidders: {$paper->total_bidders}, Avg bid: {$paper->avg_bid}, Assigned: {$paper->assigned_reviewers}\n";
}

// 3. Test Statistics Query
echo "\n3. Testing Statistics Query:\n";
$stats = [
    'total_papers' => BaiBao::where('conference_id', $conferenceId)
        ->whereIn('status_code', ['SUBMITTED', 'UNDER_REVIEW'])->count(),
    'papers_with_assignments' => DB::table('baibao as b')
        ->join('reviewer_assignments as ra', 'b.paper_id', '=', 'ra.paper_id')
        ->where('b.conference_id', $conferenceId)
        ->distinct('b.paper_id')
        ->count(),
    'total_assignments' => ReviewerAssignment::where('conference_id', $conferenceId)->count(),
    'total_bidders' => ReviewerBidding::where('conference_id', $conferenceId)
        ->distinct('user_id')
        ->count(),
    'coi_declarations' => ReviewerBidding::where('conference_id', $conferenceId)
        ->where('coi', true)
        ->count()
];

echo "   Statistics:\n";
foreach ($stats as $key => $value) {
    echo "   - {$key}: {$value}\n";
}

// 4. Test Direct API Response Format
echo "\n4. API Response Format:\n";
$response = [
    'success' => true,
    'papers' => $papers->toArray()
];

echo "   Response success: " . ($response['success'] ? 'true' : 'false') . "\n";
echo "   Response papers count: " . count($response['papers']) . "\n";
echo "   Sample paper structure:\n";
if (count($response['papers']) > 0) {
    $samplePaper = $response['papers'][0];
    foreach ($samplePaper as $key => $value) {
        echo "     {$key}: {$value}\n";
    }
}

echo "\n=== END TEST ===\n";