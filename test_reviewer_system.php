<?php
/**
 * Test script for Reviewer Invitation System
 * Run this from command line: php test_reviewer_system.php
 */

require __DIR__ . '/vendor/autoload.php';

// Initialize Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Reviewer Invitation System Test ===\n\n";

// Test 1: Check if reviewer_invitations table exists
echo "1. Checking reviewer_invitations table...\n";
try {
    $tableExists = DB::select("SHOW TABLES LIKE 'reviewer_invitations'");
    if (!empty($tableExists)) {
        echo "✅ Table exists\n";
        
        // Show table structure
        $columns = DB::select("DESCRIBE reviewer_invitations");
        echo "   Columns: " . implode(', ', array_column($columns, 'Field')) . "\n";
    } else {
        echo "❌ Table does not exist\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Check if controllers exist and can be instantiated
echo "2. Testing Controllers...\n";
try {
    $chairController = app('App\Http\Controllers\Chair\ReviewerInvitationController');
    echo "✅ Chair ReviewerInvitationController loaded\n";
} catch (Exception $e) {
    echo "❌ Chair ReviewerInvitationController error: " . $e->getMessage() . "\n";
}

try {
    $reviewerController = app('App\Http\Controllers\Reviewer\InvitationController');
    echo "✅ Reviewer InvitationController loaded\n";
} catch (Exception $e) {
    echo "❌ Reviewer InvitationController error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Check if CheckRole middleware exists
echo "3. Testing CheckRole Middleware...\n";
try {
    $middleware = app('App\Http\Middleware\CheckRole');
    echo "✅ CheckRole middleware loaded\n";
} catch (Exception $e) {
    echo "❌ CheckRole middleware error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Test email template rendering
echo "4. Testing Email Template...\n";
try {
    $html = view('emails.reviewer-invitation', [
        'name' => 'Test Reviewer',
        'invitation_url' => 'http://localhost/test-invite-url',
        'chair_name' => 'Dr. Test Chair',
        'email' => 'test@example.com',
        'conference' => (object)[
            'title' => 'Test Conference 2024',
            'year' => 2024,
            'start_date' => '2024-12-01',
            'end_date' => '2024-12-03'
        ]
    ])->render();
    
    if (strlen($html) > 100) {
        echo "✅ Email template renders successfully (" . strlen($html) . " chars)\n";
    } else {
        echo "❌ Email template too short or empty\n";
    }
} catch (Exception $e) {
    echo "❌ Email template error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Check if routes are registered
echo "5. Testing Routes...\n";
try {
    $routes = collect(Route::getRoutes()->getRoutes())
        ->filter(function($route) {
            $name = $route->getName();
            return $name && (
                str_contains($name, 'chair.reviewers.invite') ||
                str_contains($name, 'reviewer.invitation')
            );
        })
        ->map(function($route) {
            return $route->getName() . ' => ' . $route->uri();
        });
    
    if ($routes->count() > 0) {
        echo "✅ Routes registered:\n";
        foreach ($routes as $route) {
            echo "   - $route\n";
        }
    } else {
        echo "❌ No reviewer invitation routes found\n";
    }
} catch (Exception $e) {
    echo "❌ Routes error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 6: Test database connection and basic queries
echo "6. Testing Database Connection...\n";
try {
    $userCount = DB::table('nguoidung')->count();
    echo "✅ Database connected (Users: $userCount)\n";
    
    $chairUsers = DB::table('nguoidung')
        ->join('vaitro_nguoidung', 'nguoidung.id', '=', 'vaitro_nguoidung.nguoidung_id')
        ->join('loai_vai_tro', 'vaitro_nguoidung.loai_vai_tro_id', '=', 'loai_vai_tro.id')
        ->where('loai_vai_tro.user_role', 'CHAIR')
        ->count();
    echo "   Chair users: $chairUsers\n";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "\nNext Steps:\n";
echo "1. Login as a Chair user\n";
echo "2. Navigate to 'Mời reviewer' from the sidebar\n";
echo "3. Try sending an invitation\n";
echo "4. Check email (or email logs)\n";
echo "5. Test the invitation acceptance flow\n";