<?php
/**
 * Debug Assignment Page Data
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=================================================\n";
echo "🔍 DEBUG: Assignment Page Data\n";
echo "=================================================\n\n";

// Test with a specific paper
$paperId = 1; // Deep Learning Optimization Techniques

echo "Testing with Paper ID: $paperId\n\n";

// 1. Get paper info
echo "📋 Step 1: Getting paper info...\n";
$paper = DB::table('BaiBao as bb')
    ->join('HoiThao as ht', 'bb.conference_id', '=', 'ht.conference_id')
    ->where('bb.paper_id', $paperId)
    ->select('bb.*', 'ht.title as conference_name', 'ht.conference_id')
    ->first();

if ($paper) {
    echo "   ✅ Paper found:\n";
    echo "      ID: {$paper->paper_id}\n";
    echo "      Title: {$paper->title}\n";
    echo "      Conference: {$paper->conference_name} (ID: {$paper->conference_id})\n";
} else {
    echo "   ❌ Paper not found!\n";
    exit(1);
}
echo "\n";

// 2. Get authors
echo "📋 Step 2: Getting authors...\n";
$authors = DB::table('TacGiaBaiBao')
    ->join('NguoiDung as nd', 'TacGiaBaiBao.user_id', '=', 'nd.user_id')
    ->where('TacGiaBaiBao.paper_id', $paperId)
    ->select('nd.user_id', 'nd.full_name')
    ->get();

echo "   Authors: " . $authors->count() . "\n";
foreach ($authors as $author) {
    echo "      - {$author->full_name} (ID: {$author->user_id})\n";
}
$authorIds = $authors->pluck('user_id')->toArray();
echo "\n";

// 3. Get current assignments
echo "📋 Step 3: Getting current assignments...\n";
$currentAssignments = DB::table('PhanCongPhanBien as pc')
    ->join('NguoiDung as nd', 'pc.reviewer_id', '=', 'nd.user_id')
    ->where('pc.paper_id', $paperId)
    ->select('pc.reviewer_id', 'nd.full_name', 'pc.status_code')
    ->get();

echo "   Current Assignments: " . $currentAssignments->count() . "\n";
foreach ($currentAssignments as $assignment) {
    echo "      - {$assignment->full_name} (ID: {$assignment->reviewer_id}) - {$assignment->status_code}\n";
}
$assignedIds = $currentAssignments->pluck('reviewer_id')->toArray();
echo "\n";

// 4. Get all reviewers for this conference
echo "📋 Step 4: Getting ALL reviewers for conference {$paper->conference_id}...\n";
$allReviewers = DB::table('VaiTroNguoiDung as vt')
    ->join('NguoiDung as nd', 'vt.user_id', '=', 'nd.user_id')
    ->where('vt.role_code', 'REVIEWER')
    ->where('vt.conference_id', $paper->conference_id)
    ->select('nd.user_id', 'nd.full_name', 'nd.email')
    ->get();

echo "   Total Reviewers in Conference: " . $allReviewers->count() . "\n";
if ($allReviewers->count() > 0) {
    echo "   First 5 reviewers:\n";
    foreach ($allReviewers->take(5) as $reviewer) {
        echo "      - {$reviewer->full_name} ({$reviewer->email})\n";
    }
}
echo "\n";

// 5. Get available reviewers (excluding authors and assigned)
echo "📋 Step 5: Filtering available reviewers...\n";
$excludeIds = array_merge($authorIds, $assignedIds);
echo "   Excluding " . count($excludeIds) . " users (authors + assigned)\n";
echo "   Author IDs: " . implode(', ', $authorIds) . "\n";
echo "   Assigned IDs: " . implode(', ', $assignedIds) . "\n";
echo "\n";

$availableReviewers = DB::table('VaiTroNguoiDung as vt')
    ->join('NguoiDung as nd', 'vt.user_id', '=', 'nd.user_id')
    ->where('vt.role_code', 'REVIEWER')
    ->whereNotIn('vt.user_id', $excludeIds)
    ->select('nd.user_id', 'nd.full_name', 'nd.email', 'nd.organization')
    ->distinct()
    ->get();

echo "   ✅ Available Reviewers: " . $availableReviewers->count() . "\n";
if ($availableReviewers->count() > 0) {
    echo "\n   First 10 available reviewers:\n";
    foreach ($availableReviewers->take(10) as $reviewer) {
        echo "      - {$reviewer->full_name} ({$reviewer->email})\n";
        echo "        Org: " . ($reviewer->organization ?? 'N/A') . "\n";
    }
} else {
    echo "   ⚠️  WARNING: No available reviewers!\n";
    echo "\n   Possible reasons:\n";
    echo "   1. All reviewers are already assigned to this paper\n";
    echo "   2. All reviewers are authors of this paper\n";
    echo "   3. No reviewers registered for this conference\n";
}
echo "\n";

// 6. Add workload calculation
echo "📋 Step 6: Calculating workload...\n";
$workload = DB::table('PhanCongPhanBien')
    ->select('reviewer_id', DB::raw('COUNT(*) as assignment_count'))
    ->whereIn('status_code', ['INVITED', 'ACCEPTED'])
    ->groupBy('reviewer_id')
    ->pluck('assignment_count', 'reviewer_id');

echo "   Workload calculated for " . $workload->count() . " reviewers\n";
foreach ($availableReviewers->take(5) as $reviewer) {
    $reviewer->workload = $workload[$reviewer->user_id] ?? 0;
    echo "      - {$reviewer->full_name}: {$reviewer->workload} active assignments\n";
}
echo "\n";

// 7. Check COI
echo "📋 Step 7: Checking COI...\n";
$coiCount = DB::table('COI')
    ->where('paper_id', $paperId)
    ->count();
echo "   COI records for this paper: $coiCount\n";

if ($coiCount > 0) {
    $coiList = DB::table('COI')
        ->join('NguoiDung', 'COI.reviewer_id', '=', 'NguoiDung.user_id')
        ->where('paper_id', $paperId)
        ->select('NguoiDung.full_name', 'COI.coi_code')
        ->get();
    
    foreach ($coiList as $coi) {
        echo "      - {$coi->full_name} has COI: {$coi->coi_code}\n";
    }
}
echo "\n";

// 8. Summary
echo "=================================================\n";
echo "📊 SUMMARY\n";
echo "=================================================\n\n";

echo "Paper: {$paper->title}\n";
echo "Conference: {$paper->conference_name}\n";
echo "Authors: " . $authors->count() . "\n";
echo "Current Assignments: " . $currentAssignments->count() . "\n";
echo "Total Reviewers in Conference: " . $allReviewers->count() . "\n";
echo "Available Reviewers: " . $availableReviewers->count() . "\n";
echo "COI Cases: $coiCount\n";
echo "\n";

if ($availableReviewers->count() > 0) {
    echo "✅ STATUS: Can assign reviewers!\n";
    echo "\nJSON data that will be sent to view:\n";
    echo json_encode($availableReviewers->take(3), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo "❌ STATUS: Cannot assign reviewers!\n";
    echo "\nREASON: No available reviewers for this paper\n";
    echo "\nSUGGESTIONS:\n";
    echo "1. Try a different paper that has fewer assignments\n";
    echo "2. Add more reviewers to the conference\n";
    echo "3. Remove some current assignments to free up reviewers\n";
}

echo "\n\n=================================================\n";
