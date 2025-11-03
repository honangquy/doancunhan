<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING REVIEWER FORM ACCESS ===\n\n";

// Test assignment details
$assignmentId = 12;
echo "Testing assignment ID: $assignmentId\n";

$assignment = DB::table('reviewer_assignments as ra')
    ->join('baibao as bb', 'ra.paper_id', '=', 'bb.paper_id')
    ->leftJoin('hoithao as ht', 'bb.conference_id', '=', 'ht.conference_id')
    ->leftJoin('tieuban as tb', 'bb.track_id', '=', 'tb.track_id')
    ->leftJoin('nguoidung as nd', 'bb.submitter_id', '=', 'nd.user_id')
    ->where('ra.id', $assignmentId)
    ->select(
        'ra.id',
        'ra.user_id as reviewer_id',
        'ra.paper_id', 
        'ra.status',
        'ra.assigned_at',
        'bb.title',
        'bb.abstract', 
        'bb.keywords',
        'bb.file_path',
        'bb.created_at',
        'nd.full_name as author_name',
        'ht.title as conference_name',
        'tb.title as track_name'
    )
    ->first();

if ($assignment) {
    echo "✅ Assignment found:\n";
    echo "  - Paper: {$assignment->title}\n";
    echo "  - Author: {$assignment->author_name}\n";
    echo "  - Reviewer ID: {$assignment->reviewer_id}\n";
    echo "  - Status: {$assignment->status}\n";
    echo "  - Conference: {$assignment->conference_name}\n";
    echo "  - Track: {$assignment->track_name}\n\n";
    
    if ($assignment->status === 'ACCEPTED') {
        echo "✅ Assignment is ACCEPTED - form should be accessible\n";
    } else {
        echo "❌ Assignment status is {$assignment->status} - form access denied\n";
    }
} else {
    echo "❌ Assignment not found\n";
}

// Test URL generation
echo "\n=== URL TESTS ===\n";
echo "Direct form URL: http://127.0.0.1:8000/reviewer/reviews/create/12\n";
echo "Assignment detail URL: http://127.0.0.1:8000/reviewer/assignments/12\n";

// Check if review already exists
$existingReview = DB::table('phanbien')->where('assignment_id', $assignmentId)->first();
if ($existingReview) {
    echo "\n⚠️  Existing review found (ID: {$existingReview->review_id})\n";
    echo "   Draft status: " . ($existingReview->is_draft ? 'Yes' : 'No') . "\n";
} else {
    echo "\n✅ No existing review - fresh form expected\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>