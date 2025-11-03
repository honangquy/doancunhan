<?php
/**
 * Debug review form submission
 */
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 DEBUGGING REVIEW FORM SUBMISSION\n";
echo str_repeat("=", 50) . "\n";

// Test 1: Check route exists
echo "1. Checking route registration...\n";
try {
    $routes = app()->make('router')->getRoutes();
    $reviewStoreRoute = null;
    
    foreach ($routes as $route) {
        if ($route->getName() === 'reviewer.reviews.store') {
            $reviewStoreRoute = $route;
            break;
        }
    }
    
    if ($reviewStoreRoute) {
        echo "   ✅ Route 'reviewer.reviews.store' found\n";
        echo "   URI: " . $reviewStoreRoute->uri() . "\n";
        echo "   Methods: " . implode(', ', $reviewStoreRoute->methods()) . "\n";
    } else {
        echo "   ❌ Route 'reviewer.reviews.store' NOT found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Test 2: Check controller method exists
echo "\n2. Checking controller method...\n";
try {
    $controller = new \App\Http\Controllers\Reviewer\ReviewerController();
    if (method_exists($controller, 'storeReview')) {
        echo "   ✅ Method 'storeReview' exists in ReviewerController\n";
    } else {
        echo "   ❌ Method 'storeReview' NOT found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Test 3: Check middleware
echo "\n3. Checking middleware requirements...\n";
echo "   - Route requires middleware: role:REVIEWER\n";
echo "   - User 11 (hoquy902@gmail.com) has assignments\n";

// Test 4: Check CSRF token requirement
echo "\n4. CSRF token requirement...\n";
echo "   - POST routes require valid CSRF token\n";
echo "   - Ensure form includes @csrf directive\n";

// Test 5: Test database connection
echo "\n5. Testing database operations...\n";
try {
    $assignment = DB::table('reviewer_assignments')->where('id', 1)->first();
    if ($assignment) {
        echo "   ✅ Assignment ID 1 exists\n";
        echo "   User ID: {$assignment->user_id}\n";
        echo "   Paper ID: {$assignment->paper_id}\n";
        echo "   Status: {$assignment->status}\n";
    } else {
        echo "   ❌ Assignment ID 1 not found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🎯 DEBUGGING STEPS:\n";
echo "1. Open browser dev tools (F12)\n";
echo "2. Go to Network tab\n";
echo "3. Login as hoquy902@gmail.com\n";
echo "4. Navigate to review form\n";
echo "5. Fill form and click 'Gửi phản biện chính thức'\n";
echo "6. Check Network tab for:\n";
echo "   - POST request to /reviewer/reviews/{id}/store\n";
echo "   - Response status code\n";
echo "   - Any JavaScript errors in Console tab\n";

echo "\n📋 MANUAL TEST COMMANDS:\n";
echo "# Test route manually:\n";
echo "curl -X POST http://127.0.0.1:8000/reviewer/reviews/1/store\n";
echo "\n# Check Laravel logs:\n";
echo "tail -f storage/logs/laravel.log\n";

echo "\n🔧 POSSIBLE ISSUES:\n";
echo "- Missing CSRF token\n";
echo "- JavaScript errors preventing form submission\n";
echo "- Middleware blocking request\n";
echo "- Validation errors not displayed\n";
echo "- Route parameter mismatch\n";