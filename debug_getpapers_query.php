<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG GETCONFERENCEPAPERS METHOD ===\n\n";

$conferenceId = 8;
echo "Testing getConferencePapers for conference ID: $conferenceId\n\n";

// 1. Check papers exist in conference
$allPapers = DB::table('baibao')->where('conference_id', $conferenceId)->get();
echo "1. All papers in conference $conferenceId:\n";
echo "   Count: " . $allPapers->count() . "\n";
foreach ($allPapers as $paper) {
    echo "   - Paper ID: {$paper->paper_id}, Title: {$paper->title}, Status: {$paper->status_code}\n";
}
echo "\n";

// 2. Check column names in baibao table
echo "2. Column names in baibao table:\n";
$columns = \Schema::getColumnListing('baibao');
echo "   " . implode(', ', $columns) . "\n\n";

// 3. Test the exact query from getConferencePapers
echo "3. Testing the query from getConferencePapers:\n";

try {
    $papers = DB::table('baibao as b')
        ->leftJoin('nguoidung as submitter', 'b.submitter_id', '=', 'submitter.user_id')
        ->leftJoin('reviewer_bidding as rb', 'b.paper_id', '=', 'rb.paper_id')
        ->leftJoin('reviewer_assignments as ra', 'b.paper_id', '=', 'ra.paper_id')
        ->where('b.conference_id', $conferenceId)
        ->whereIn('b.status_code', ['SUBMITTED', 'UNDER_REVIEW'])
        ->select(
            'b.paper_id',
            'b.conference_id',
            'b.track_id',
            'b.submitter_id',
            'b.title',
            'b.abstract',
            'b.keywords',
            'b.file_path',
            'b.current_version_id',
            'b.status_code',
            'b.created_at',
            'submitter.full_name as submitted_by_name',
            DB::raw('COUNT(DISTINCT rb.user_id) as total_bidders'),
            DB::raw('AVG(rb.bidding_value) as avg_bid'),
            DB::raw('MAX(rb.bidding_value) as max_bid'),
            DB::raw('COUNT(DISTINCT CASE WHEN rb.coi = true THEN rb.user_id END) as coi_count'),
            DB::raw('COUNT(DISTINCT ra.user_id) as assigned_reviewers')
        )
        ->groupBy(
            'b.paper_id',
            'b.conference_id',
            'b.track_id',
            'b.submitter_id',
            'b.title',
            'b.abstract',
            'b.keywords',
            'b.file_path',
            'b.current_version_id',
            'b.status_code',
            'b.created_at',
            'submitter.full_name'
        )
        ->orderBy('b.title')
        ->get();
        
    echo "   Query executed successfully!\n";
    echo "   Papers found: " . $papers->count() . "\n\n";
    
    foreach ($papers as $paper) {
        echo "   Paper: {$paper->title}\n";
        echo "     - Submitter: {$paper->submitted_by_name}\n";  
        echo "     - Total bidders: {$paper->total_bidders}\n";
        echo "     - Avg bid: {$paper->avg_bid}\n";
        echo "     - Max bid: {$paper->max_bid}\n";
        echo "     - COI count: {$paper->coi_count}\n";
        echo "     - Assigned reviewers: {$paper->assigned_reviewers}\n\n";
    }
    
} catch (\Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n";
}

echo "=== END DEBUG ===\n";