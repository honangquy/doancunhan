<?php
/**
 * Debug Chair Assignment Data
 * Run: php debug_chair_assignment.php
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG CHAIR ASSIGNMENT DATA ===\n\n";

// 1. Check Chair user
$chairUser = DB::table('nguoidung')->where('email', 'honangquy1@gmail.com')->first();
echo "1. Chair User:\n";
if ($chairUser) {
    echo "   - ID: {$chairUser->user_id}\n";
    echo "   - Name: {$chairUser->full_name}\n";
    echo "   - Email: {$chairUser->email}\n";
} else {
    echo "   - Chair user NOT FOUND!\n";
}
echo "\n";

// 2. Check Chair roles
$chairRoles = DB::table('vaitronguoidung as vr')
    ->join('hoithao as h', 'vr.conference_id', '=', 'h.conference_id')
    ->where('vr.user_id', $chairUser->user_id ?? 0)
    ->where('vr.role_code', 'CHAIR')
    ->select('vr.*', 'h.title as conference_title', 'h.status as conference_status')
    ->get();

echo "2. Chair Roles:\n";
foreach ($chairRoles as $role) {
    echo "   - Conference: {$role->conference_title} (ID: {$role->conference_id})\n";
    echo "     Status: {$role->conference_status}\n";
}
echo "\n";

// 3. Check papers in conferences
foreach ($chairRoles as $role) {
    echo "3. Papers in Conference '{$role->conference_title}' (ID: {$role->conference_id}):\n";
    
    $papers = DB::table('baibao')->where('conference_id', $role->conference_id)->get();
    echo "   Total papers: {$papers->count()}\n";
    
    foreach ($papers as $paper) {
        echo "   - Paper ID: {$paper->paper_id}\n";
        echo "     Title: {$paper->title}\n";
        echo "     Status: {$paper->status_code}\n";
        
        // Check biddings for this paper
        $biddings = DB::table('reviewer_bidding')->where('paper_id', $paper->paper_id)->get();
        echo "     Biddings: {$biddings->count()}\n";
        
        foreach ($biddings as $bid) {
            $reviewer = DB::table('nguoidung')->where('user_id', $bid->user_id)->first();
            $reviewerEmail = $reviewer ? $reviewer->email : 'Unknown';
            echo "       * {$reviewerEmail}: bid {$bid->bidding_value}, COI: " . ($bid->coi ? 'Yes' : 'No') . "\n";
        }
        echo "\n";
    }
}

// 4. Check reviewer with biddings
echo "4. Reviewer rodowim871@haotuwu.com:\n";
$reviewer = DB::table('nguoidung')->where('email', 'rodowim871@haotuwu.com')->first();
if ($reviewer) {
    echo "   - ID: {$reviewer->user_id}\n";
    echo "   - Name: {$reviewer->full_name}\n";
    
    $biddings = DB::table('reviewer_bidding as rb')
        ->join('baibao as b', 'rb.paper_id', '=', 'b.paper_id')
        ->where('rb.user_id', $reviewer->user_id)
        ->select('rb.*', 'b.title', 'b.conference_id')
        ->get();
        
    echo "   - Biddings: {$biddings->count()}\n";
    foreach ($biddings as $bid) {
        echo "     * Paper: {$bid->title} (Conference: {$bid->conference_id})\n";
        echo "       Bid: {$bid->bidding_value}, COI: " . ($bid->coi ? 'Yes' : 'No') . "\n";
    }
} else {
    echo "   - Reviewer NOT FOUND!\n";
}

echo "\n=== END DEBUG ===\n";