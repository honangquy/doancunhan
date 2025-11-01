<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TESTING NEW WORKFLOW ===\n";

// Simulate reviewer checking available papers
$userId = 33; // reviewer tabifap692@dropeso.com

$availablePapers = DB::table('baibao as b')
    ->join('hoithao as h', 'b.conference_id', '=', 'h.conference_id')
    ->join('vaitronguoidung as vr', function($join) use ($userId) {
        $join->on('h.conference_id', '=', 'vr.conference_id')
             ->where('vr.user_id', '=', $userId)
             ->where('vr.role_code', '=', 'REVIEWER');
    })
    ->leftJoin('reviewer_bidding as rb', function($join) use ($userId) {
        $join->on('b.paper_id', '=', 'rb.paper_id')
             ->where('rb.user_id', '=', $userId);
    })
    ->whereIn('b.status_code', ['SUBMITTED', 'UNDER_REVIEW'])
    ->select('b.*', 'h.title as conference_name', 'rb.bidding_value')
    ->get();

echo "Papers available for reviewer to bid:\n";
foreach ($availablePapers as $paper) {
    $bidStatus = $paper->bidding_value !== null ? "Already bid: {$paper->bidding_value}" : "Not bid yet";
    echo "  - {$paper->title} ({$paper->status_code}) - {$bidStatus}\n";
}

echo "\n✅ Total available for bidding: {$availablePapers->count()}\n";
echo "📝 Papers can be bid immediately upon SUBMITTED status!\n";

?>