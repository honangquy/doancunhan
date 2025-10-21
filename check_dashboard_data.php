<?php
require_once 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== KIỂM TRA DỮ LIỆU DASHBOARD ===" . PHP_EOL;

// Kiểm tra join_requests
echo "1. JOIN REQUESTS TABLE:" . PHP_EOL;
try {
    $joinRequests = DB::table('join_requests')->get();
    echo "   Total records: " . $joinRequests->count() . PHP_EOL;
    
    if ($joinRequests->count() > 0) {
        echo "   Sample records:" . PHP_EOL;
        foreach($joinRequests->take(3) as $req) {
            echo "   - ID: {$req->id}, Status: {$req->status}, User: " . ($req->user_id ?? 'N/A') . PHP_EOL;
        }
        
        $pendingCount = DB::table('join_requests')->where('status', 'PENDING')->count();
        echo "   PENDING requests: {$pendingCount}" . PHP_EOL;
    }
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL;

// Kiểm tra yeucauhoithao  
echo "2. YEUCAUHOITHAO TABLE:" . PHP_EOL;
try {
    $confRequests = DB::table('yeucauhoithao')->get();
    echo "   Total records: " . $confRequests->count() . PHP_EOL;
    
    if ($confRequests->count() > 0) {
        echo "   Sample records:" . PHP_EOL;
        foreach($confRequests->take(3) as $req) {
            echo "   - ID: {$req->request_id}, Status: {$req->status}, Title: " . ($req->title ?? 'N/A') . PHP_EOL;
        }
        
        $pendingCount = DB::table('yeucauhoithao')->where('status', 'PENDING')->count();
        echo "   PENDING requests: {$pendingCount}" . PHP_EOL;
    }
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL;

// Test the exact query from controller
echo "3. CONTROLLER QUERY TEST:" . PHP_EOL;
try {
    $pendingJoinRequests = DB::table('join_requests')
        ->join('nguoidung', 'join_requests.user_id', '=', 'nguoidung.user_id')
        ->join('hoithao', 'join_requests.conference_id', '=', 'hoithao.conference_id')
        ->where('join_requests.status', 'PENDING')
        ->select(
            'join_requests.id',
            'join_requests.full_name',
            'join_requests.email_contact',
            'join_requests.role',
            'join_requests.created_at',
            'hoithao.title as conference_title',
            'hoithao.conference_id as conference_code'
        )
        ->orderBy('join_requests.created_at', 'desc')
        ->limit(10)
        ->get();
        
    echo "   Join requests with joins: " . $pendingJoinRequests->count() . PHP_EOL;
    
    if ($pendingJoinRequests->count() > 0) {
        foreach($pendingJoinRequests as $req) {
            echo "   - {$req->full_name} -> {$req->conference_title}" . PHP_EOL;
        }
    }
} catch (Exception $e) {
    echo "   Join query error: " . $e->getMessage() . PHP_EOL;
}

try {
    $pendingConferenceRequests = DB::table('yeucauhoithao')
        ->where('status', 'PENDING')
        ->select('request_id', 'title', 'chair_fullname', 'created_at')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
        
    echo "   Conference organization requests: " . $pendingConferenceRequests->count() . PHP_EOL;
    
    if ($pendingConferenceRequests->count() > 0) {
        foreach($pendingConferenceRequests as $req) {
            echo "   - {$req->title} by {$req->chair_fullname}" . PHP_EOL;
        }
    }
} catch (Exception $e) {
    echo "   Conference query error: " . $e->getMessage() . PHP_EOL;
}