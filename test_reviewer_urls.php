<?php
// Test direct URL access for reviewer routes
echo "=== TESTING REVIEWER URL ACCESS ===\n\n";

$baseUrl = 'http://127.0.0.1:8000';

// URLs to test
$urls = [
    '/reviewer/assignments' => 'Assignment list',
    '/reviewer/bidding' => 'Bidding interface', 
    '/reviewer/dashboard' => 'Reviewer dashboard',
    '/reviewer/assignments/stats' => 'Assignment stats API'
];

foreach ($urls as $url => $description) {
    echo "Testing: $description\n";
    echo "URL: $baseUrl$url\n";
    
    // Simple curl request without authentication
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Status: $httpCode\n";
    
    if ($httpCode == 302) {
        echo "   → Redirected (likely to login - this is expected)\n";
    } elseif ($httpCode == 403) {
        echo "   → Forbidden (middleware issue)\n";
    } elseif ($httpCode == 404) {
        echo "   → Not Found (route issue)\n";
    } elseif ($httpCode == 500) {
        echo "   → Server Error (controller issue)\n";
        // Try to extract error
        if (strpos($response, 'ErrorException') !== false) {
            if (preg_match('/Undefined property: stdClass::\$(\w+)/', $response, $matches)) {
                echo "   → Missing property: {$matches[1]}\n";
            }
        }
    } elseif ($httpCode == 200) {
        echo "   → Success\n";
    }
    
    echo "---\n\n";
}

echo "=== TEST COMPLETE ===\n";
echo "\n";
echo "EXPECTED RESULTS:\n";
echo "- All URLs should return 302 (redirect to login) when not authenticated\n";
echo "- No 403 (forbidden) errors should occur\n";
echo "- No 500 (server) errors should occur\n";
echo "\n";
echo "TO LOGIN AND TEST:\n";
echo "1. Login at: $baseUrl/login with hoquy902@gmail.com\n";
echo "2. Then visit: $baseUrl/reviewer/assignments\n";
echo "3. Should see assignments list\n";
?>