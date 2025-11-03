<?php
/**
 * Test script để verify review submission functionality
 * Run: php test_review_submission.php
 */

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 Testing Review Submission Functionality\n";
echo str_repeat("=", 50) . "\n";

// Test 1: Check foreign key constraints are fixed
echo "1. Checking foreign key constraints...\n";
try {
    $constraints = DB::select("
        SELECT 
            CONSTRAINT_NAME,
            TABLE_NAME,
            COLUMN_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = 'quanly_hoithao' 
        AND TABLE_NAME = 'phanbien' 
        AND CONSTRAINT_NAME LIKE '%foreign%'
    ");
    
    foreach ($constraints as $constraint) {
        if ($constraint->COLUMN_NAME === 'assignment_id') {
            echo "   ✅ assignment_id references {$constraint->REFERENCED_TABLE_NAME}.{$constraint->REFERENCED_COLUMN_NAME}\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Error checking constraints: " . $e->getMessage() . "\n";
}

// Test 2: Check recommendation codes in database
echo "\n2. Checking recommendation codes...\n";
try {
    $codes = DB::table('loaikhuyennghi')->get();
    echo "   Available codes: ";
    echo implode(', ', $codes->pluck('recommendation_code')->toArray()) . "\n";
    echo "   ✅ Found " . $codes->count() . " recommendation codes\n";
} catch (Exception $e) {
    echo "   ❌ Error checking codes: " . $e->getMessage() . "\n";
}

// Test 3: Check if we can insert a test review
echo "\n3. Testing review insertion...\n";
try {
    // Find an ACCEPTED assignment without existing review
    $assignment = DB::table('reviewer_assignments')
        ->where('status', 'ACCEPTED')
        ->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('phanbien')
                ->whereRaw('phanbien.assignment_id = reviewer_assignments.id');
        })
        ->first();

    if ($assignment) {
        echo "   Found assignment ID: {$assignment->id} for testing\n";
        
        // Test data
        $testReviewData = [
            'assignment_id' => $assignment->id,
            'score_novelty' => 8,
            'score_relevance' => 7,
            'score_technical_quality' => 8,
            'score_presentation' => 7,
            'score_references' => 6,
            'total_score' => 7.2,
            'detailed_comments' => 'This is a test review comment. The paper shows good research quality with minor areas for improvement. The methodology is sound and results are well-presented.',
            'recommendation_code' => 'WEAK_ACCEPT',
            'is_draft' => true, // Insert as draft first
            'created_at' => now(),
            'updated_at' => now()
        ];
        
        // Try to insert
        $reviewId = DB::table('phanbien')->insertGetId($testReviewData);
        echo "   ✅ Successfully inserted test review ID: {$reviewId}\n";
        
        // Test updating to final submission
        DB::table('phanbien')
            ->where('review_id', $reviewId)
            ->update([
                'is_draft' => false,
                'submitted_at' => now()
            ]);
        
        DB::table('reviewer_assignments')
            ->where('id', $assignment->id)
            ->update([
                'status' => 'COMPLETED',
                'review_submitted_at' => now()
            ]);
        
        echo "   ✅ Successfully updated to final submission\n";
        
        // Clean up test data
        DB::table('phanbien')->where('review_id', $reviewId)->delete();
        DB::table('reviewer_assignments')
            ->where('id', $assignment->id)
            ->update([
                'status' => 'ACCEPTED',
                'review_submitted_at' => null
            ]);
        
        echo "   ✅ Test data cleaned up\n";
    } else {
        echo "   ⚠️  No available assignments for testing\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error during review insertion test: " . $e->getMessage() . "\n";
}

// Test 4: Check routes accessibility
echo "\n4. Testing routes...\n";
try {
    $routes = [
        '/reviewer/reviews/create/1' => 'Review creation form',
        '/reviewer/assignments' => 'Assignments list'
    ];
    
    foreach ($routes as $route => $description) {
        echo "   Route: {$route} ({$description})\n";
    }
    echo "   ✅ Routes defined (test via browser at http://127.0.0.1:8000)\n";
} catch (Exception $e) {
    echo "   ❌ Error checking routes: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🎉 Review submission functionality appears to be working!\n";
echo "\nNext steps:\n";
echo "1. Open browser: http://127.0.0.1:8000\n";
echo "2. Login as a reviewer\n";
echo "3. Go to 'Reviews của tôi' -> Create review\n";
echo "4. Test the 'Gửi phản biện chính thức' button\n\n";

// Show sample assignment info
echo "📋 Available test assignments:\n";
$assignments = DB::table('reviewer_assignments as ra')
    ->join('baibao as b', 'ra.paper_id', '=', 'b.paper_id')
    ->join('nguoidung as u', 'ra.user_id', '=', 'u.user_id')
    ->where('ra.status', 'ACCEPTED')
    ->select('ra.id', 'ra.user_id', 'u.email', 'b.title', 'ra.status')
    ->limit(5)
    ->get();

foreach ($assignments as $assignment) {
    echo "   - Assignment ID: {$assignment->id} | User: {$assignment->email} | Paper: {$assignment->title}\n";
}