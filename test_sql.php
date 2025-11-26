<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking database schema for baibao table:\n";
$columns = DB::select('DESCRIBE baibao');
foreach ($columns as $column) {
    if ($column->Field === 'decision') {
        echo "Decision column details:\n";
        print_r($column);
    }
}

echo "\nTesting direct SQL update:\n";
$result = DB::statement("UPDATE baibao SET decision = 'PUBLISHED' WHERE paper_id = 3");
echo "Direct SQL result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";

$check = DB::select("SELECT paper_id, decision, published_at FROM baibao WHERE paper_id = 3")[0];
echo "After direct SQL: Decision='{$check->decision}', Published='{$check->published_at}'\n";