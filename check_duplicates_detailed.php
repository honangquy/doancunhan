<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Raw SELECT from loaivaitro table ===\n";
$raw = DB::select('SELECT * FROM loaivaitro ORDER BY role_name');
foreach($raw as $row) {
    echo "role_code: {$row->role_code}, role_name: {$row->role_name}\n";
}

echo "\n=== Looking at raw data - Maybe display issue ===\n";
echo "Total unique role_names: " . DB::table('loaivaitro')->distinct('role_name')->count() . "\n";
echo "Total unique role_codes: " . DB::table('loaivaitro')->distinct('role_code')->count() . "\n";

echo "\n=== Check for similar names ===\n";
$all = DB::table('loaivaitro')->get();
foreach($all as $role) {
    // Check for whitespace issues
    echo "Code: '" . $role->role_code . "' | Name: '" . $role->role_name . "' | Bytes: " . strlen($role->role_name) . "\n";
}
