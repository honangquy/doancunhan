<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Users WITH assigned roles ===\n";
$withRoles = DB::table('vaitronguoidung')
    ->select('role_code', DB::raw('count(distinct user_id) as count'))
    ->groupBy('role_code')
    ->get();

foreach($withRoles as $row) {
    echo $row->role_code . ': ' . $row->count . "\n";
}

echo "\n=== Users WITHOUT any assigned roles ===\n";
$withoutRoles = DB::table('nguoidung')
    ->leftJoin('vaitronguoidung', 'nguoidung.user_id', '=', 'vaitronguoidung.user_id')
    ->whereNull('vaitronguoidung.user_id')
    ->count();

echo "Count: " . $withoutRoles . "\n";

echo "\n=== List of users without roles ===\n";
$users = DB::table('nguoidung')
    ->select('nguoidung.user_id', 'full_name', 'email')
    ->leftJoin('vaitronguoidung', 'nguoidung.user_id', '=', 'vaitronguoidung.user_id')
    ->whereNull('vaitronguoidung.user_id')
    ->get();

foreach($users as $user) {
    echo "ID: {$user->user_id}, Name: {$user->full_name}, Email: {$user->email}\n";
}
