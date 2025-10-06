<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "BaiBao table structure:\n";
echo "=======================\n";
$columns = DB::select('SHOW COLUMNS FROM BaiBao');
foreach ($columns as $col) {
    echo "{$col->Field} ({$col->Type}) - Null: {$col->Null}, Key: {$col->Key}\n";
}
