<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Creating Test Login ===" . PHP_EOL;

try {
    // Find a test user with REVIEWER role
    $reviewerUser = DB::table('nguoidung as u')
        ->join('vaitronguoidung as vr', 'u.user_id', '=', 'vr.user_id')
        ->where('vr.role_code', 'REVIEWER')
        ->select('u.user_id', 'u.email', 'u.full_name')
        ->first();
        
    if ($reviewerUser) {
        echo "Test Reviewer User Found:" . PHP_EOL;
        echo "  - ID: {$reviewerUser->user_id}" . PHP_EOL;
        echo "  - Email: {$reviewerUser->email}" . PHP_EOL;
        echo "  - Name: {$reviewerUser->full_name}" . PHP_EOL . PHP_EOL;
        
        echo "You can login with:" . PHP_EOL;
        echo "  Email: {$reviewerUser->email}" . PHP_EOL;
        echo "  Password: (check database or use default password)" . PHP_EOL;
        
        // Check login route
        echo "\nLogin URL: http://127.0.0.1:8000/login" . PHP_EOL;
        
    } else {
        echo "No reviewer users found." . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
?>