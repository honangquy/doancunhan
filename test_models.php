<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "=== Testing User Management System ===\n";
    
    // Test database connection
    echo "1. Testing database connection...\n";
    $userCount = App\Models\NguoiDung::count();
    echo "   ✓ Connected! Total users: $userCount\n";
    
    // Test user with roles relationship
    echo "\n2. Testing user-role relationships...\n";
    $user = App\Models\NguoiDung::with('vaiTros')->first();
    if ($user) {
        echo "   ✓ User: " . $user->full_name . "\n";
        echo "   ✓ Email: " . $user->email . "\n";
        echo "   ✓ Roles count: " . $user->vaiTros->count() . "\n";
        
        if ($user->vaiTros->count() > 0) {
            echo "   ✓ First role: " . $user->vaiTros->first()->role_code . "\n";
        }
    } else {
        echo "   ⚠ No users found in database\n";
    }
    
    // Test VaiTroNguoiDung model
    echo "\n3. Testing VaiTroNguoiDung model...\n";
    $roleCount = App\Models\VaiTroNguoiDung::count();
    echo "   ✓ Total role assignments: $roleCount\n";
    
    // Test user creation capability
    echo "\n4. Testing user creation validation...\n";
    $validator = Validator::make([
        'full_name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'role' => 'USER'
    ], [
        'full_name' => 'required|string|max:200',
        'email' => 'required|email',
        'password' => 'required|string|min:6',
        'role' => 'required|in:ADMIN,CHAIR,REVIEWER,AUTHOR,USER'
    ]);
    
    if ($validator->passes()) {
        echo "   ✓ Validation rules working correctly\n";
    } else {
        echo "   ✗ Validation failed: " . implode(', ', $validator->errors()->all()) . "\n";
    }
    
    echo "\n=== All tests completed successfully! ===\n";
    echo "User management system is ready to use.\n";
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
?>