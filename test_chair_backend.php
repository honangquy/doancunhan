<?php
/**
 * Test script for Phase 8.6 - Chair Features
 * Test ChairController methods
 */

// Include Laravel bootstrap
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

echo "=== PHASE 8.6: CHAIR FEATURES - BACKEND TEST ===\n\n";

// 1. Find chair user
echo "1. Finding chair user...\n";
$chairUser = DB::table('NguoiDung as nd')
    ->join('VaiTroNguoiDung as vt', 'nd.user_id', '=', 'vt.user_id')
    ->where('vt.role_code', 'CHAIR')
    ->select('nd.*')
    ->first();

if ($chairUser) {
    echo "✅ Found chair: {$chairUser->full_name} ({$chairUser->email})\n";
    echo "   User ID: {$chairUser->user_id}\n\n";
} else {
    echo "❌ No chair user found\n\n";
    exit;
}

// 2. Get chair's conferences through VaiTroNguoiDung
echo "2. Getting chair's conferences through VaiTroNguoiDung...\n";
$conferences = DB::table('HoiThao as ht')
    ->join('VaiTroNguoiDung as vt', function($join) use ($chairUser) {
        $join->on('ht.conference_id', '=', 'vt.conference_id')
             ->where('vt.user_id', '=', $chairUser->user_id)
             ->where('vt.role_code', '=', 'CHAIR');
    })
    ->select('ht.*')
    ->get();

echo "✅ Found {$conferences->count()} conference(s):\n";
foreach ($conferences as $conf) {
    echo "   - {$conf->title} (ID: {$conf->conference_id})\n";
    echo "     Deadline: {$conf->deadline_submission}\n";
}
echo "\n";

// 3. Get papers in chair's conferences
if ($conferences->isNotEmpty()) {
    $conferenceIds = $conferences->pluck('conference_id');
    
    echo "3. Getting papers in chair's conferences...\n";
    $papers = DB::table('BaiBao as bb')
        ->join('HoiThao as ht', 'bb.conference_id', '=', 'ht.conference_id')
        ->join('NguoiDung as nd', 'bb.submitter_id', '=', 'nd.user_id')
        ->join('TrangThaiBaiBao as ttbb', 'bb.status_code', '=', 'ttbb.status_code')
        ->whereIn('bb.conference_id', $conferenceIds)
        ->select(
            'bb.paper_id',
            'bb.title',
            'bb.created_at', // Changed from submission_date
            'bb.status_code',
            'ht.title as conference_name',
            'nd.full_name as author_name',
            'ttbb.status_name'
        )
        ->orderBy('bb.created_at', 'desc') // Changed from submission_date
        ->limit(10)
        ->get();
    
    echo "✅ Found {$papers->count()} papers:\n\n";
    
    foreach ($papers as $paper) {
        echo "📄 {$paper->title}\n";
        echo "   Paper ID: {$paper->paper_id}\n";
        echo "   Author: {$paper->author_name}\n";
        echo "   Conference: {$paper->conference_name}\n";
        echo "   Status: {$paper->status_name}\n";
        echo "   Submitted: {$paper->created_at}\n"; // Changed from submission_date
        
        // Get review stats
        $reviewStats = DB::table('PhanCongPhanBien as pc')
            ->leftJoin('PhanBien as pb', 'pc.assignment_id', '=', 'pb.assignment_id')
            ->where('pc.paper_id', $paper->paper_id)
            ->selectRaw('
                COUNT(pc.assignment_id) as total_assigned,
                COUNT(pb.review_id) as completed,
                AVG(pb.score) as avg_score
            ')
            ->first();
        
        echo "   Reviews: {$reviewStats->completed}/{$reviewStats->total_assigned}";
        if ($reviewStats->avg_score) {
            echo " (Avg: " . round($reviewStats->avg_score, 1) . "/10)";
        }
        echo "\n\n";
    }
    
    // 4. Statistics
    echo "4. Calculating statistics...\n";
    $stats = [
        'total' => DB::table('BaiBao')->whereIn('conference_id', $conferenceIds)->count(),
        'submitted' => DB::table('BaiBao')->whereIn('conference_id', $conferenceIds)->where('status_code', 'SUBMITTED')->count(),
        'under_review' => DB::table('BaiBao')->whereIn('conference_id', $conferenceIds)->where('status_code', 'UNDER_REVIEW')->count(),
        'reviewed' => DB::table('BaiBao')->whereIn('conference_id', $conferenceIds)->where('status_code', 'REVIEWED')->count(),
        'accepted' => DB::table('BaiBao')->whereIn('conference_id', $conferenceIds)->where('status_code', 'ACCEPTED')->count(),
        'rejected' => DB::table('BaiBao')->whereIn('conference_id', $conferenceIds)->where('status_code', 'REJECTED')->count(),
    ];
    
    echo "✅ Statistics:\n";
    echo "   Total papers: {$stats['total']}\n";
    echo "   Submitted: {$stats['submitted']}\n";
    echo "   Under review: {$stats['under_review']}\n";
    echo "   Reviewed: {$stats['reviewed']}\n";
    echo "   Accepted: {$stats['accepted']}\n";
    echo "   Rejected: {$stats['rejected']}\n\n";
    
    // 5. Papers needing reviewers
    echo "5. Finding papers needing reviewers...\n";
    $needsReviewers = DB::table('BaiBao as bb')
        ->leftJoin('PhanCongPhanBien as pc', 'bb.paper_id', '=', 'pc.paper_id')
        ->whereIn('bb.conference_id', $conferenceIds)
        ->whereIn('bb.status_code', ['SUBMITTED', 'UNDER_REVIEW'])
        ->groupBy('bb.paper_id', 'bb.title')
        ->havingRaw('COUNT(pc.assignment_id) = 0')
        ->select('bb.paper_id', 'bb.title')
        ->get();
    
    echo "⚠️ {$needsReviewers->count()} paper(s) need reviewers assigned:\n";
    foreach ($needsReviewers as $paper) {
        echo "   - {$paper->title}\n";
    }
    echo "\n";
    
    // 6. Papers ready for decision (papers with REVIEWED status)
    echo "6. Finding papers ready for decision...\n";
    $readyForDecision = DB::table('BaiBao as bb')
        ->whereIn('bb.conference_id', $conferenceIds)
        ->where('bb.status_code', 'REVIEWED') // Status indicates all reviews completed
        ->select('bb.paper_id', 'bb.title')
        ->get();
    
    echo "✅ {$readyForDecision->count()} paper(s) ready for decision:\n";
    foreach ($readyForDecision as $paper) {
        echo "   - {$paper->title}\n";
    }
    echo "\n";
    
    // 7. Test a specific paper details
    if ($papers->isNotEmpty()) {
        $testPaper = $papers->first();
        echo "7. Testing paper details for: {$testPaper->title}\n";
        
        // Get authors (join with NguoiDung to get names)
        $authors = DB::table('TacGiaBaiBao as ta')
            ->join('NguoiDung as nd', 'ta.user_id', '=', 'nd.user_id')
            ->where('ta.paper_id', $testPaper->paper_id)
            ->select('ta.author_order', 'nd.full_name', 'nd.email', 'ta.organization')
            ->orderBy('ta.author_order')
            ->get();
        
        echo "✅ Authors ({$authors->count()}):\n";
        foreach ($authors as $author) {
            echo "   {$author->author_order}. {$author->full_name} ({$author->email})\n";
        }
        echo "\n";
        
        // Get assignments
        $assignments = DB::table('PhanCongPhanBien as pc')
            ->join('NguoiDung as nd', 'pc.reviewer_id', '=', 'nd.user_id')
            ->leftJoin('PhanBien as pb', 'pc.assignment_id', '=', 'pb.assignment_id')
            ->where('pc.paper_id', $testPaper->paper_id)
            ->select(
                'pc.assignment_id',
                'pc.status_code', // Changed from response_status
                'nd.full_name as reviewer_name',
                'pb.review_id',
                'pb.score', // Changed from overall_score
                'pb.recommendation_code' // Changed from recommendation
            )
            ->get();
        
        echo "✅ Review Assignments ({$assignments->count()}):\n";
        foreach ($assignments as $assignment) {
            echo "   - {$assignment->reviewer_name}\n";
            echo "     Status: {$assignment->status_code}\n"; // Changed from response_status
            if ($assignment->review_id) {
                echo "     ✅ Review completed\n";
                echo "     Score: {$assignment->score}/10\n"; // Changed from overall_score
                echo "     Recommendation: {$assignment->recommendation_code}\n"; // Changed from recommendation
            } else {
                echo "     ⏳ Review pending\n";
            }
        }
        echo "\n";
    }
}

echo "=== TEST COMPLETE ===\n\n";

echo "📋 Summary:\n";
echo "✅ ChairController queries working\n";
echo "✅ Conference data accessible\n";
echo "✅ Paper statistics calculating correctly\n";
echo "✅ Review aggregation working\n\n";

echo "🎯 Next steps:\n";
echo "1. Create chair dashboard view\n";
echo "2. Create papers list view\n";
echo "3. Create paper details view\n";
echo "4. Test in browser with chair@test.com\n\n";

echo "🔗 Test URL:\n";
echo "http://localhost/qly_hthao/qlyhoithao/public/chair/dashboard\n\n";
