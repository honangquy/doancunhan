<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "PhanCongPhanBien table structure:\n";
echo "==================================\n";
$columns = DB::select('SHOW COLUMNS FROM PhanCongPhanBien');
foreach ($columns as $col) {
    echo "{$col->Field} ({$col->Type})\n";
}
