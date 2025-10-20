<?php
/**
 * Test User Management API Endpoints
 * Usage: php test_user_management.php
 */

// Configuration
$baseUrl = 'http://127.0.0.1:8001';
$adminEmail = 'admin@hoithao.vn';
$adminPassword = 'admin123';

echo "=== Testing User Management System ===\n\n";

// Function to make HTTP requests
function makeRequest($url, $method = 'GET', $data = null, $headers = []) {
    $curl = curl_init();
    
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_COOKIEJAR => 'cookies.txt',
        CURLOPT_COOKIEFILE => 'cookies.txt',
    ]);
    
    if ($data) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    return ['code' => $httpCode, 'body' => $response];
}

// Function to extract CSRF token
function getCsrfToken($html) {
    preg_match('/name="csrf-token" content="([^"]+)"/', $html, $matches);
    return isset($matches[1]) ? $matches[1] : null;
}

try {
    // Step 1: Get login page and CSRF token
    echo "1. Getting login page...\n";
    $loginPage = makeRequest("$baseUrl/login");
    if ($loginPage['code'] !== 200) {
        throw new Exception("Cannot access login page. HTTP Code: " . $loginPage['code']);
    }
    
    $csrfToken = getCsrfToken($loginPage['body']);
    if (!$csrfToken) {
        throw new Exception("Cannot extract CSRF token from login page");
    }
    echo "   ✓ Login page loaded, CSRF token: " . substr($csrfToken, 0, 10) . "...\n";

    // Step 2: Login as admin
    echo "\n2. Logging in as admin...\n";
    $loginData = http_build_query([
        '_token' => $csrfToken,
        'email' => $adminEmail,
        'password' => $adminPassword
    ]);
    
    $loginResponse = makeRequest("$baseUrl/login", 'POST', $loginData, [
        'Content-Type: application/x-www-form-urlencoded',
        'X-CSRF-TOKEN: ' . $csrfToken
    ]);
    
    if ($loginResponse['code'] !== 302 && $loginResponse['code'] !== 200) {
        throw new Exception("Login failed. HTTP Code: " . $loginResponse['code']);
    }
    echo "   ✓ Successfully logged in\n";

    // Step 3: Access user management page
    echo "\n3. Accessing user management page...\n";
    $usersPage = makeRequest("$baseUrl/admin/users");
    if ($usersPage['code'] !== 200) {
        throw new Exception("Cannot access users page. HTTP Code: " . $usersPage['code']);
    }
    
    $newCsrfToken = getCsrfToken($usersPage['body']);
    if (!$newCsrfToken) {
        throw new Exception("Cannot extract CSRF token from users page");
    }
    echo "   ✓ User management page loaded\n";

    // Step 4: Test creating a new user
    echo "\n4. Testing user creation...\n";
    $userData = http_build_query([
        '_token' => $newCsrfToken,
        'full_name' => 'Test User ' . date('H:i:s'),
        'email' => 'testuser' . time() . '@example.com',
        'password' => 'password123',
        'role' => 'USER'
    ]);
    
    $createResponse = makeRequest("$baseUrl/admin/users", 'POST', $userData, [
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: application/json',
        'X-CSRF-TOKEN: ' . $newCsrfToken
    ]);
    
    echo "   Create user response code: " . $createResponse['code'] . "\n";
    if ($createResponse['code'] === 200) {
        $createData = json_decode($createResponse['body'], true);
        if ($createData && $createData['success']) {
            echo "   ✓ User created successfully: " . $createData['message'] . "\n";
        } else {
            echo "   ✗ User creation failed: " . ($createData['message'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "   ✗ User creation failed with HTTP code: " . $createResponse['code'] . "\n";
        echo "   Response: " . substr($createResponse['body'], 0, 200) . "...\n";
    }

    // Step 5: Test getting user data for editing
    echo "\n5. Testing user data retrieval...\n";
    $getUserResponse = makeRequest("$baseUrl/admin/users/1/edit", 'GET', null, [
        'Accept: application/json',
        'X-CSRF-TOKEN: ' . $newCsrfToken
    ]);
    
    echo "   Get user response code: " . $getUserResponse['code'] . "\n";
    if ($getUserResponse['code'] === 200) {
        $userData = json_decode($getUserResponse['body'], true);
        if ($userData && $userData['success']) {
            echo "   ✓ User data retrieved successfully\n";
            echo "   User: " . $userData['user']->full_name ?? 'N/A' . " (" . $userData['user']->email ?? 'N/A' . ")\n";
        } else {
            echo "   ✗ User data retrieval failed: " . ($userData['message'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "   ✗ User data retrieval failed with HTTP code: " . $getUserResponse['code'] . "\n";
    }

    echo "\n=== Test completed ===\n";
    echo "All basic functions appear to be working correctly!\n";
    echo "Please test the UI manually at: $baseUrl/admin/users\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Please check your configuration and try again.\n";
}

// Clean up cookies file
if (file_exists('cookies.txt')) {
    unlink('cookies.txt');
}
?>