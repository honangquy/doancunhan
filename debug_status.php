<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Current papers status in conference 8:\n";
$papers = DB::table('baibao')->where('conference_id', 8)->get(['paper_id', 'title', 'decision', 'published_at']);

foreach ($papers as $paper) {
    $decision = $paper->decision ?: 'EMPTY';
    echo "ID: {$paper->paper_id} | Decision: '{$decision}' | Published: " . ($paper->published_at ?? 'NULL') . "\n";
}

echo "\nCounting by status:\n";
$published = DB::table('baibao')->where('conference_id', 8)->where('decision', 'PUBLISHED')->count();
$accepted = DB::table('baibao')->where('conference_id', 8)
    ->where(function($query) {
        $query->where('decision', 'ACCEPT')
              ->orWhere('decision', '')
              ->orWhereNull('decision');
    })
    ->where('decision', '!=', 'PUBLISHED')
    ->where('decision', '!=', 'REJECT')
    ->count();

echo "Published: {$published}\n";
echo "Accepted (eligible for publishing): {$accepted}\n";