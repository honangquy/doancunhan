<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking Reviewer-Conference Distribution...\n\n";

$distribution = DB::table('VaiTroNguoiDung')
    ->where('role_code', 'REVIEWER')
    ->select('conference_id', DB::raw('COUNT(*) as count'))
    ->groupBy('conference_id')
    ->get();

echo "Reviewers per Conference:\n";
foreach ($distribution as $row) {
    echo "  Conference {$row->conference_id}: {$row->count} reviewers\n";
}

echo "\nTotal reviewers: " . DB::table('VaiTroNguoiDung')->where('role_code', 'REVIEWER')->count() . "\n";
