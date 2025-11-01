<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$email = 'honangquy1@gmail.com';

// Get user
$user = DB::table('nguoidung')->where('email', $email)->first();
if (!$user) {
    echo "User not found with email: $email\n";
    exit;
}

echo "User ID: {$user->user_id}\n";
echo "User Name: {$user->full_name}\n\n";

// Get user roles
$roles = DB::table('vaitronguoidung as vt')
    ->leftJoin('hoithao as ht', 'vt.conference_id', '=', 'ht.conference_id')
    ->where('vt.user_id', $user->user_id)
    ->select('vt.*', 'ht.title as conference_title')
    ->get();

echo "User Roles:\n";
foreach ($roles as $role) {
    echo "- Role: {$role->role_code}, Conference: {$role->conference_title} (ID: {$role->conference_id})\n";
}

// Get conferences where user is CHAIR
$chairConferences = DB::table('hoithao as ht')
    ->join('vaitronguoidung as vt', function($join) use ($user) {
        $join->on('ht.conference_id', '=', 'vt.conference_id')
             ->where('vt.user_id', '=', $user->user_id)
             ->where('vt.role_code', '=', 'CHAIR');
    })
    ->select('ht.*')
    ->get();

echo "\nConferences where user is CHAIR:\n";
foreach ($chairConferences as $conf) {
    echo "- {$conf->title} (ID: {$conf->conference_id}, Status: {$conf->status})\n";
}

// Get all conferences
$allConferences = DB::table('hoithao')->select('conference_id', 'title', 'status')->get();
echo "\nAll Conferences in system:\n";
foreach ($allConferences as $conf) {
    echo "- {$conf->title} (ID: {$conf->conference_id}, Status: {$conf->status})\n";
}