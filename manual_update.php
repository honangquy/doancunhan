<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Manually updating paper 3 to PUBLISHED status...\n";

DB::beginTransaction();
try {
    $result = DB::table('baibao')
        ->where('paper_id', 3)
        ->where('conference_id', 8)
        ->update([
            'decision' => 'PUBLISHED',
            'published_at' => now(),
        ]);
    
    echo "Update result: {$result} rows affected\n";
    
    $check = DB::table('baibao')->where('paper_id', 3)->first(['decision', 'published_at']);
    echo "Paper 3 after update: Decision='{$check->decision}', Published='{$check->published_at}'\n";
    
    DB::commit();
    echo "Transaction committed successfully\n";
} catch (Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nFinal status check:\n";
$papers = DB::table('baibao')->where('conference_id', 8)->where('decision', 'PUBLISHED')->count();
echo "Papers with PUBLISHED status: {$papers}\n";