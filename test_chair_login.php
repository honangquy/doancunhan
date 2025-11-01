<?php

// Test login and access chair assignments
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a mock request
$request = Illuminate\Http\Request::create('/');
$response = $kernel->handle($request);

echo "=== Testing Login and Chair Access ===\n";

try {
    // Find a chair user
    $chairUser = \App\Models\NguoiDung::where('email', 'honangquy1@gmail.com')->first();
    if (!$chairUser) {
        echo "Chair user not found\n";
        exit;
    }
    
    echo "Found chair user: {$chairUser->full_name} ({$chairUser->email})\n";
    
    // Check chair roles
    $chairRoles = \App\Models\VaiTroNguoiDung::where('user_id', $chairUser->user_id)
        ->where('role_code', 'CHAIR')
        ->with('hoithao')
        ->get();
    
    echo "Chair roles: " . $chairRoles->count() . "\n";
    foreach ($chairRoles as $role) {
        $title = isset($role->hoithao->title) ? $role->hoithao->title : 'N/A';
        echo "- Conference {$role->conference_id}: {$title}\n";
    }
    
    // Try to create a controller response manually
    \Illuminate\Support\Facades\Auth::login($chairUser);
    
    echo "\nTesting controller response...\n";
    
    $controller = new \App\Http\Controllers\Chair\ReviewerAssignmentController();
    $request = new \Illuminate\Http\Request();
    
    try {
        ob_start();
        $response = $controller->index($request);
        $output = ob_get_clean();
        
        if ($response instanceof \Illuminate\View\View) {
            echo "✅ Controller returned a View\n";
            
            // Try to render the view
            try {
                $rendered = $response->render();
                
                // Check for JSON errors in rendered content
                if (strpos($rendered, 'json_decode') !== false) {
                    echo "❌ Found json_decode error in rendered view\n";
                    
                    // Extract error context
                    $lines = explode('\n', $rendered);
                    foreach ($lines as $i => $line) {
                        if (strpos($line, 'json_decode') !== false) {
                            echo "Error at line " . ($i + 1) . ": " . trim($line) . "\n";
                            if ($i > 0) echo "  Previous: " . trim($lines[$i-1]) . "\n";
                            if ($i < count($lines) - 1) echo "  Next: " . trim($lines[$i+1]) . "\n";
                            break;
                        }
                    }
                } else {
                    echo "✅ No JSON decode errors found\n";
                }
            } catch (\Exception $e) {
                echo "❌ Error rendering view: " . $e->getMessage() . "\n";
                echo "Error line: " . $e->getFile() . ":" . $e->getLine() . "\n";
            }
        } else {
            echo "Response type: " . get_class($response) . "\n";
        }
        
        if ($output) {
            echo "Output: $output\n";
        }
        
    } catch (\Exception $e) {
        echo "❌ Controller error: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ General error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}