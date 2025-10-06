<?php

/**
 * Create Test Accounts for Each Role
 * 
 * This script creates test accounts with known passwords for each role:
 * - author@test.com / password123
 * - reviewer@test.com / password123  
 * - chair@test.com / password123
 * - admin@test.com / password123
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "=== CREATING TEST ACCOUNTS ===\n\n";

try {
    DB::beginTransaction();
    
    // Test accounts configuration
    $testAccounts = [
        [
            'email' => 'author@test.com',
            'password' => 'password123',
            'full_name' => 'Test Author',
            'role_code' => 'AUTHOR',
            'role_name' => 'Author',
        ],
        [
            'email' => 'reviewer@test.com',
            'password' => 'password123',
            'full_name' => 'Test Reviewer',
            'role_code' => 'REVIEWER',
            'role_name' => 'Reviewer',
        ],
        [
            'email' => 'chair@test.com',
            'password' => 'password123',
            'full_name' => 'Test Chair',
            'role_code' => 'CHAIR',
            'role_name' => 'Chair',
        ],
        [
            'email' => 'admin@test.com',
            'password' => 'password123',
            'full_name' => 'Test Admin',
            'role_code' => 'ADMIN',
            'role_name' => 'Administrator',
        ],
    ];
    
    foreach ($testAccounts as $account) {
        // Check if user exists
        $existingUser = DB::table('NguoiDung')
            ->where('email', $account['email'])
            ->first();
        
        if ($existingUser) {
            echo "⚠️  User {$account['email']} already exists. Updating password...\n";
            
            // Update password
            DB::table('NguoiDung')
                ->where('user_id', $existingUser->user_id)
                ->update([
                    'password_hash' => Hash::make($account['password']),
                    'updated_at' => now()
                ]);
            
            $userId = $existingUser->user_id;
        } else {
            echo "✅ Creating user: {$account['email']}\n";
            
            // Create new user
            $userId = DB::table('NguoiDung')->insertGetId([
                'email' => $account['email'],
                'password_hash' => Hash::make($account['password']),
                'full_name' => $account['full_name'],
                'organization' => 'HUIT - Test Account',
                'is_student' => 0,
                'locked' => 0,
                'created_at' => now(),
            ]);
        }
        
        // Check if role assignment exists
        $existingRole = DB::table('VaiTroNguoiDung')
            ->where('user_id', $userId)
            ->where('role_code', $account['role_code'])
            ->first();
        
        if (!$existingRole) {
            echo "   → Assigning role: {$account['role_name']}\n";
            
            DB::table('VaiTroNguoiDung')->insert([
                'user_id' => $userId,
                'role_code' => $account['role_code'],
            ]);
        } else {
            echo "   → Role already assigned: {$account['role_name']}\n";
        }
        
        echo "\n";
    }
    
    DB::commit();
    
    echo "\n=== TEST ACCOUNTS READY ===\n\n";
    echo "Login Credentials:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Author:   author@test.com   / password123\n";
    echo "Reviewer: reviewer@test.com / password123\n";
    echo "Chair:    chair@test.com    / password123\n";
    echo "Admin:    admin@test.com    / password123\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "✅ All test accounts created successfully!\n";
    echo "You can now login at: http://127.0.0.1:8000/login\n\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
}
