<?php
/**
 * Test Assignment Functionality
 * 
 * Tests if reviewer assignment feature is working
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=================================================\n";
echo "🧪 TEST: REVIEWER ASSIGNMENT FUNCTIONALITY\n";
echo "=================================================\n\n";

// Test 1: Check if tables exist
echo "📋 Test 1: Checking required tables...\n";
$requiredTables = [
    'BaiBao',
    'NguoiDung', 
    'PhanCongPhanBien',
    'COI',
    'TacGiaBaiBao',
    'VaiTroNguoiDung'
];

$allTablesExist = true;
foreach ($requiredTables as $table) {
    $exists = DB::select("SHOW TABLES LIKE '$table'");
    if (empty($exists)) {
        echo "   ❌ Table $table NOT FOUND\n";
        $allTablesExist = false;
    } else {
        echo "   ✅ Table $table exists\n";
    }
}
echo "\n";

if (!$allTablesExist) {
    echo "❌ Some required tables are missing. Cannot proceed.\n";
    exit(1);
}

// Test 2: Check for test data
echo "📋 Test 2: Checking test data...\n";

// Check for papers
$paperCount = DB::table('BaiBao')->count();
echo "   Papers in database: $paperCount\n";

// Check for reviewers
$reviewerCount = DB::table('NguoiDung')
    ->join('VaiTroNguoiDung', 'NguoiDung.user_id', '=', 'VaiTroNguoiDung.user_id')
    ->where('VaiTroNguoiDung.role_code', 'REVIEWER')
    ->distinct()
    ->count('NguoiDung.user_id');
echo "   Reviewers in database: $reviewerCount\n";

// Check for chairs
$chairCount = DB::table('VaiTroNguoiDung')
    ->where('role_code', 'CHAIR')
    ->count();
echo "   Chairs in database: $chairCount\n";

if ($paperCount === 0 || $reviewerCount === 0 || $chairCount === 0) {
    echo "   ⚠️  WARNING: Insufficient test data\n";
} else {
    echo "   ✅ Test data available\n";
}
echo "\n";

// Test 3: Check assignment constraints
echo "📋 Test 3: Checking PhanCongPhanBien table structure...\n";
$columns = DB::select("DESCRIBE PhanCongPhanBien");
$requiredColumns = ['assignment_id', 'paper_id', 'reviewer_id', 'chair_id', 'status_code', 'token', 'assigned_at', 'deadline'];

echo "   Columns found:\n";
$allColumnsExist = true;
foreach ($requiredColumns as $col) {
    $found = false;
    foreach ($columns as $column) {
        if ($column->Field === $col) {
            $found = true;
            break;
        }
    }
    
    if ($found) {
        echo "   ✅ $col\n";
    } else {
        echo "   ❌ $col MISSING\n";
        $allColumnsExist = false;
    }
}
echo "\n";

// Test 4: Check existing assignments
echo "📋 Test 4: Checking existing assignments...\n";
$existingAssignments = DB::table('PhanCongPhanBien as pc')
    ->join('BaiBao as bb', 'pc.paper_id', '=', 'bb.paper_id')
    ->join('NguoiDung as nr', 'pc.reviewer_id', '=', 'nr.user_id')
    ->join('NguoiDung as nc', 'pc.chair_id', '=', 'nc.user_id')
    ->select(
        'pc.assignment_id',
        'bb.title as paper_title',
        'nr.full_name as reviewer_name',
        'nc.full_name as chair_name',
        'pc.status_code',
        'pc.assigned_at',
        'pc.deadline'
    )
    ->get();

if ($existingAssignments->isEmpty()) {
    echo "   ℹ️  No assignments found yet\n";
} else {
    echo "   Found " . $existingAssignments->count() . " existing assignments:\n\n";
    foreach ($existingAssignments as $assignment) {
        echo "   Assignment #{$assignment->assignment_id}:\n";
        echo "     Paper: {$assignment->paper_title}\n";
        echo "     Reviewer: {$assignment->reviewer_name}\n";
        echo "     Assigned by: {$assignment->chair_name}\n";
        echo "     Status: {$assignment->status_code}\n";
        echo "     Deadline: {$assignment->deadline}\n";
        echo "\n";
    }
}
echo "\n";

// Test 5: Test assignment logic (dry run)
echo "📋 Test 5: Testing assignment logic (simulation)...\n";

// Get first paper
$testPaper = DB::table('BaiBao')->first();
if (!$testPaper) {
    echo "   ⚠️  No paper found for testing\n";
} else {
    echo "   Test Paper: #{$testPaper->paper_id} - {$testPaper->title}\n";
    
    // Get first chair
    $testChair = DB::table('VaiTroNguoiDung as vt')
        ->join('NguoiDung as nd', 'vt.user_id', '=', 'nd.user_id')
        ->where('vt.role_code', 'CHAIR')
        ->where('vt.conference_id', $testPaper->conference_id)
        ->select('nd.*')
        ->first();
    
    if (!$testChair) {
        echo "   ⚠️  No chair found for this conference\n";
    } else {
        echo "   Test Chair: {$testChair->full_name}\n";
        
        // Get available reviewers (not authors, no COI, not assigned)
        $availableReviewers = DB::table('NguoiDung as nd')
            ->join('VaiTroNguoiDung as vt', 'nd.user_id', '=', 'vt.user_id')
            ->where('vt.role_code', 'REVIEWER')
            ->where('vt.conference_id', $testPaper->conference_id)
            ->whereNotIn('nd.user_id', function($query) use ($testPaper) {
                $query->select('user_id')
                      ->from('TacGiaBaiBao')
                      ->where('paper_id', $testPaper->paper_id);
            })
            ->whereNotIn('nd.user_id', function($query) use ($testPaper) {
                $query->select('reviewer_id')
                      ->from('PhanCongPhanBien')
                      ->where('paper_id', $testPaper->paper_id);
            })
            ->whereNotIn('nd.user_id', function($query) use ($testPaper) {
                $query->select('reviewer_id')
                      ->from('COI')
                      ->where('paper_id', $testPaper->paper_id);
            })
            ->select('nd.*')
            ->get();
        
        if ($availableReviewers->isEmpty()) {
            echo "   ⚠️  No available reviewers found\n";
        } else {
            echo "   ✅ Found {$availableReviewers->count()} available reviewers:\n";
            foreach ($availableReviewers->take(5) as $reviewer) {
                echo "      - {$reviewer->full_name} ({$reviewer->email})\n";
            }
            if ($availableReviewers->count() > 5) {
                echo "      ... and " . ($availableReviewers->count() - 5) . " more\n";
            }
        }
    }
}
echo "\n";

// Test 6: Check routes
echo "📋 Test 6: Checking assignment routes...\n";
$routes = [
    'chair.papers.assign',
    'chair.papers.assign.store',
    'chair.assignments.remove'
];

foreach ($routes as $routeName) {
    try {
        $url = route($routeName, ['id' => 1]);
        echo "   ✅ Route '$routeName' exists: $url\n";
    } catch (Exception $e) {
        echo "   ❌ Route '$routeName' NOT FOUND\n";
    }
}
echo "\n";

// Final Summary
echo "=================================================\n";
echo "📊 TEST SUMMARY\n";
echo "=================================================\n\n";

$canAssign = $allTablesExist && 
             $allColumnsExist && 
             $paperCount > 0 && 
             $reviewerCount > 0 && 
             $chairCount > 0;

if ($canAssign) {
    echo "✅ RESULT: Reviewer assignment feature is READY!\n\n";
    echo "You can test by:\n";
    echo "1. Login as Chair\n";
    echo "2. Go to Dashboard\n";
    echo "3. Click on a paper\n";
    echo "4. Click 'Phân công phản biện'\n";
    echo "5. Select a reviewer and deadline\n";
    echo "6. Click 'Phân công'\n\n";
    
    if (!$existingAssignments->isEmpty()) {
        echo "Note: {$existingAssignments->count()} assignment(s) already exist.\n";
    }
} else {
    echo "❌ RESULT: Some issues detected. Please fix:\n\n";
    if (!$allTablesExist) echo "- Missing required tables\n";
    if (!$allColumnsExist) echo "- Missing required columns\n";
    if ($paperCount === 0) echo "- No papers in database\n";
    if ($reviewerCount === 0) echo "- No reviewers in database\n";
    if ($chairCount === 0) echo "- No chairs in database\n";
}

echo "\n=================================================\n";
