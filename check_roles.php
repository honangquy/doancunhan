<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking users by role ===\n";
$roleStats = \DB::table('vaitronguoidung')
    ->select('vaitronguoidung.role_code', 'loaivaitro.role_name')
    ->join('loaivaitro', 'vaitronguoidung.role_code', '=', 'loaivaitro.role_code')
    ->groupBy('vaitronguoidung.role_code', 'loaivaitro.role_name')
    ->selectRaw('count(*) as user_count')
    ->orderBy('loaivaitro.role_name')
    ->get();

echo "From vaitronguoidung join loaivaitro:\n";
foreach($roleStats as $stat) {
    echo $stat->role_code . ' => ' . $stat->role_name . ': ' . $stat->user_count . " người\n";
}

echo "\n=== Looking for duplicate role entries in loaivaitro ===\n";
$allRoles = \DB::table('loaivaitro')->get();
$byName = [];
foreach($allRoles as $role) {
    if(!isset($byName[$role->role_name])) {
        $byName[$role->role_name] = [];
    }
    $byName[$role->role_name][] = $role->role_code;
}

echo "Total roles in loaivaitro: " . count($allRoles) . "\n";

$hasDuplicates = false;
foreach($byName as $name => $codes) {
    if(count($codes) > 1) {
        echo "DUPLICATE NAME FOUND: '{$name}'\n";
        echo "  Codes: " . implode(', ', $codes) . "\n";
        $hasDuplicates = true;
    }
}

if(!$hasDuplicates) {
    echo "No exact duplicates found in role names.\n";
}

echo "\n=== All role_codes and role_names ===\n";
foreach($allRoles as $role) {
    echo "'{$role->role_code}' => '{$role->role_name}'\n";
}
