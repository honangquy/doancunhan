<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a mock request
$request = Request::create('/');
$response = $kernel->handle($request);

echo "=== Debug Chair Assignments Controller ===\n";

try {
    // Login as a chair user
    $chairUser = \App\Models\NguoiDung::where('email', 'honangquy1@gmail.com')->first();
    if (!$chairUser) {
        echo "Chair user not found: honangquy1@gmail.com\n";
        exit;
    }
    
    Auth::login($chairUser);
    echo "Logged in as: {$chairUser->email}\n";
    
    // Get conferences for this chair
    $conferences = DB::table('hoithao as h')
        ->join('vaitronguoidung as vr', 'h.conference_id', '=', 'vr.conference_id')
        ->where('vr.user_id', $chairUser->user_id)
        ->where('vr.role_code', 'CHAIR')
        ->where('h.status', 'ACTIVE')
        ->select('h.*')
        ->get();
    
    echo "Conferences found: " . $conferences->count() . "\n";
    foreach ($conferences as $conf) {
        echo "- Conference {$conf->conference_id}: {$conf->title}\n";
    }
    
    $selectedConference = $conferences->first()->conference_id ?? null;
    echo "Selected conference: {$selectedConference}\n";
    
    // Test JSON encoding
    echo "\n=== Testing JSON Encoding ===\n";
    
    $conferencesJson = json_encode($conferences->toArray());
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "JSON encode error for conferences: " . json_last_error_msg() . "\n";
        // Try to find problematic data
        foreach ($conferences as $i => $conf) {
            $testJson = json_encode($conf);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo "Conference {$i} has JSON error: " . json_last_error_msg() . "\n";
                print_r($conf);
            }
        }
    } else {
        echo "Conferences JSON encoded successfully\n";
        echo "JSON length: " . strlen($conferencesJson) . " chars\n";
    }
    
    $selectedJson = json_encode($selectedConference);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "JSON encode error for selectedConference: " . json_last_error_msg() . "\n";
        var_dump($selectedConference);
    } else {
        echo "Selected conference JSON encoded successfully\n";
    }
    
    echo "\n=== Testing Controller Directly ===\n";
    
    $controller = new ReviewerAssignmentController();
    $request = new Request();
    
    try {
        $response = $controller->index($request);
        echo "Controller executed successfully\n";
        echo "Response type: " . get_class($response) . "\n";
    } catch (Exception $e) {
        echo "Controller error: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}