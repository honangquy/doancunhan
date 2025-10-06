<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Testing Paper #55 (Test lần 1)...\n\n";

$paperId = 55;

// Get paper
$paper = DB::table('BaiBao')->where('paper_id', $paperId)->first();
if (!$paper) {
    echo "Paper not found!\n";
    exit;
}

echo "Paper: {$paper->title}\n";
echo "Conference ID: {$paper->conference_id}\n\n";

// Get authors
$authors = DB::table('TacGiaBaiBao')
    ->where('paper_id', $paperId)
    ->pluck('user_id')
    ->toArray();
echo "Authors (" . count($authors) . "): " . implode(', ', $authors) . "\n\n";

// Get current assignments
$currentAssignments = DB::table('PhanCongPhanBien')
    ->where('paper_id', $paperId)
    ->pluck('reviewer_id')
    ->toArray();
echo "Current assignments (" . count($currentAssignments) . "): " . implode(', ', $currentAssignments) . "\n\n";

// Get available reviewers
$excludeIds = array_merge($authors, $currentAssignments);
echo "Excluding " . count($excludeIds) . " users\n\n";

$availableReviewers = DB::table('VaiTroNguoiDung as vt')
    ->join('NguoiDung as nd', 'vt.user_id', '=', 'nd.user_id')
    ->where('vt.role_code', 'REVIEWER')
    ->whereNotIn('vt.user_id', $excludeIds)
    ->select('nd.user_id', 'nd.full_name', 'nd.email', 'nd.organization')
    ->distinct()
    ->get();

echo "✅ Available reviewers: " . $availableReviewers->count() . "\n\n";

if ($availableReviewers->count() > 0) {
    echo "First 10:\n";
    foreach ($availableReviewers->take(10) as $r) {
        echo "  - {$r->full_name} ({$r->email})\n";
    }
    
    echo "\n\nJSON output (first 3):\n";
    echo json_encode($availableReviewers->take(3)->values(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo "❌ No reviewers available!\n";
}
