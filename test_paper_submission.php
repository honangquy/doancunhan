<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST PAPER SUBMISSION ===\n\n";

// Test 1: Check if routes are registered
echo "Test 1: Check Routes\n";
echo "-------------------\n";
try {
    $router = app('router');
    $routes = $router->getRoutes();
    
    $paperRoutes = [
        'author.papers.index',
        'author.papers.create', 
        'author.papers.store',
        'author.papers.show',
        'author.papers.edit',
        'author.papers.update',
    ];
    
    foreach ($paperRoutes as $routeName) {
        $route = $routes->getByName($routeName);
        if ($route) {
            echo "✓ Route '$routeName' exists: " . $route->uri() . "\n";
        } else {
            echo "✗ Route '$routeName' NOT FOUND\n";
        }
    }
} catch (Exception $e) {
    echo "✗ Error checking routes: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 2: Check active conferences
echo "Test 2: Check Active Conferences\n";
echo "--------------------------------\n";
try {
    $conferences = DB::table('HoiThao')
        ->where('status', 'ACTIVE')
        ->where('deadline_submission', '>', now())
        ->select('conference_id', 'title', 'deadline_submission', 'status')
        ->get();
    
    echo "Found " . $conferences->count() . " active conferences:\n";
    foreach ($conferences as $conf) {
        echo "  - ID: {$conf->conference_id}\n";
        echo "    Title: {$conf->title}\n";
        echo "    Deadline: {$conf->deadline_submission}\n";
        echo "    Status: {$conf->status}\n\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Check test author account
echo "Test 3: Check Test Author Account\n";
echo "----------------------------------\n";
try {
    $author = DB::table('NguoiDung')
        ->where('email', 'author@test.com')
        ->first();
    
    if ($author) {
        echo "✓ Author account found:\n";
        echo "  - User ID: {$author->user_id}\n";
        echo "  - Name: {$author->full_name}\n";
        echo "  - Email: {$author->email}\n";
        echo "  - Role: {$author->role}\n";
    } else {
        echo "✗ Author account not found\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Check storage directory
echo "Test 4: Check Storage Directory\n";
echo "--------------------------------\n";
$storagePath = storage_path('app/papers');
if (file_exists($storagePath)) {
    echo "✓ Storage directory exists: $storagePath\n";
    if (is_writable($storagePath)) {
        echo "✓ Storage directory is writable\n";
    } else {
        echo "✗ Storage directory is NOT writable\n";
    }
} else {
    echo "✗ Storage directory does NOT exist\n";
    echo "  Creating directory...\n";
    mkdir($storagePath, 0755, true);
    if (file_exists($storagePath)) {
        echo "✓ Directory created successfully\n";
    }
}
echo "\n";

// Test 5: Check paper table structure
echo "Test 5: Check BaiBao Table Structure\n";
echo "-------------------------------------\n";
try {
    $columns = DB::select("SHOW COLUMNS FROM BaiBao");
    echo "Table columns:\n";
    foreach ($columns as $col) {
        echo "  - {$col->Field} ({$col->Type})\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 6: Check TacGiaBaiBao table structure
echo "Test 6: Check TacGiaBaiBao Table Structure\n";
echo "-------------------------------------------\n";
try {
    $columns = DB::select("SHOW COLUMNS FROM TacGiaBaiBao");
    echo "Table columns:\n";
    foreach ($columns as $col) {
        echo "  - {$col->Field} ({$col->Type})\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 7: Simulate paper submission (dry run)
echo "Test 7: Simulate Paper Submission Data\n";
echo "---------------------------------------\n";
try {
    $author = DB::table('NguoiDung')
        ->where('email', 'author@test.com')
        ->first();
    
    $conference = DB::table('HoiThao')
        ->where('status', 'ACTIVE')
        ->where('deadline_submission', '>', now())
        ->first();
    
    if (!$author) {
        echo "✗ No author account found\n";
    } elseif (!$conference) {
        echo "✗ No active conference found\n";
    } else {
        echo "Submission data would be:\n";
        echo "  - Submitter: {$author->full_name} (ID: {$author->user_id})\n";
        echo "  - Conference: {$conference->title} (ID: {$conference->conference_id})\n";
        echo "  - Title: Test Paper Title\n";
        echo "  - Abstract: This is a test abstract...\n";
        echo "  - Keywords: test, paper, submission\n";
        echo "  - Status: SUBMITTED\n";
        echo "✓ All required data available for submission\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 8: Check upload configuration
echo "Test 8: Check PHP Upload Configuration\n";
echo "---------------------------------------\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "file_uploads: " . (ini_get('file_uploads') ? 'enabled' : 'disabled') . "\n";
echo "\n";

echo "=== ALL TESTS COMPLETED ===\n";

