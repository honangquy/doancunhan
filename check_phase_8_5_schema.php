<?php
/**
 * PHASE 8.5 - CHECK DATABASE SCHEMA
 * Kiểm tra cấu trúc các bảng cần thiết cho Reviewer features
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== PHASE 8.5: DATABASE SCHEMA CHECK ===\n\n";

// 1. Check PhanCongPhanBien (Review Assignments)
echo "1. Checking PhanCongPhanBien table...\n";
$assignments = DB::select("DESCRIBE PhanCongPhanBien");
echo "   Columns:\n";
foreach ($assignments as $col) {
    echo "   - {$col->Field} ({$col->Type})\n";
}

$assignmentCount = DB::table('PhanCongPhanBien')->count();
echo "   Total records: $assignmentCount\n\n";

// 2. Check PhanBien (Reviews)
echo "2. Checking PhanBien table...\n";
try {
    $reviews = DB::select("DESCRIBE PhanBien");
    echo "   Columns:\n";
    foreach ($reviews as $col) {
        echo "   - {$col->Field} ({$col->Type})\n";
    }
    
    $reviewCount = DB::table('PhanBien')->count();
    echo "   Total records: $reviewCount\n\n";
} catch (Exception $e) {
    echo "   ❌ ERROR: {$e->getMessage()}\n";
    echo "   Table PhanBien may not exist!\n\n";
}

// 3. Check DauThau (Bidding)
echo "3. Checking DauThau table...\n";
try {
    $bidding = DB::select("DESCRIBE DauThau");
    echo "   Columns:\n";
    foreach ($bidding as $col) {
        echo "   - {$col->Field} ({$col->Type})\n";
    }
    
    $biddingCount = DB::table('DauThau')->count();
    echo "   Total records: $biddingCount\n\n";
} catch (Exception $e) {
    echo "   ❌ ERROR: {$e->getMessage()}\n";
    echo "   Table DauThau may not exist!\n\n";
}

// 4. Check status codes in PhanCongPhanBien
echo "4. Checking assignment status codes...\n";
try {
    $statuses = DB::table('PhanCongPhanBien')
        ->select('status_code', DB::raw('COUNT(*) as count'))
        ->groupBy('status_code')
        ->get();
    
    if ($statuses->isEmpty()) {
        echo "   No assignments yet.\n\n";
    } else {
        echo "   Status distribution:\n";
        foreach ($statuses as $status) {
            echo "   - {$status->status_code}: {$status->count}\n";
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo "   ❌ ERROR: {$e->getMessage()}\n\n";
}

// 5. Check BaiBao papers available for review
echo "5. Checking available papers...\n";
$papers = DB::table('BaiBao')
    ->join('HoiThao', 'BaiBao.conference_id', '=', 'HoiThao.conference_id')
    ->select('BaiBao.paper_id', 'BaiBao.title', 'BaiBao.status_code', 'HoiThao.title as conference')
    ->get();

echo "   Total papers: " . $papers->count() . "\n";
foreach ($papers as $paper) {
    echo "   - Paper #{$paper->paper_id}: {$paper->title} ({$paper->status_code})\n";
    echo "     Conference: {$paper->conference}\n";
}
echo "\n";

// 6. Check reviewers
echo "6. Checking reviewers...\n";
$reviewers = DB::table('NguoiDung')
    ->join('VaiTroNguoiDung', 'NguoiDung.user_id', '=', 'VaiTroNguoiDung.user_id')
    ->join('VaiTro', 'VaiTroNguoiDung.role_id', '=', 'VaiTro.role_id')
    ->where('VaiTro.role_code', 'REVIEWER')
    ->select('NguoiDung.user_id', 'NguoiDung.full_name', 'NguoiDung.email')
    ->get();

echo "   Total reviewers: " . $reviewers->count() . "\n";
foreach ($reviewers as $reviewer) {
    echo "   - User #{$reviewer->user_id}: {$reviewer->full_name} ({$reviewer->email})\n";
}
echo "\n";

// Summary
echo "=== SUMMARY ===\n";
echo "✓ PhanCongPhanBien: " . (isset($assignments) ? count($assignments) . " columns" : "Not checked") . "\n";
echo (isset($reviews) ? "✓" : "❌") . " PhanBien: " . (isset($reviews) ? count($reviews) . " columns" : "Table missing") . "\n";
echo (isset($bidding) ? "✓" : "❌") . " DauThau: " . (isset($bidding) ? count($bidding) . " columns" : "Table missing") . "\n";
echo "✓ Papers available: {$papers->count()}\n";
echo "✓ Reviewers: {$reviewers->count()}\n";

echo "\n=== READY FOR PHASE 8.5 ===\n";
