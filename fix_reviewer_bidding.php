<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\VaiTroNguoiDung;
use App\Models\BaiBao;
use Illuminate\Support\Facades\DB;

echo "=== FIXING REVIEWER BIDDING ISSUES ===\n";

// 1. Add reviewer to conference ID=1 (has papers)
$existingRole = VaiTroNguoiDung::where('user_id', 33)
    ->where('conference_id', 1)
    ->where('role_code', 'REVIEWER')
    ->first();

if (!$existingRole) {
    VaiTroNguoiDung::create([
        'user_id' => 33,
        'role_code' => 'REVIEWER', 
        'conference_id' => 1
    ]);
    echo "✅ Added reviewer to conference ID=1\n";
} else {
    echo "ℹ️ Reviewer already in conference ID=1\n";
}

// 2. Set papers to UNDER_REVIEW in conference ID=1 for bidding
$papers = BaiBao::where('conference_id', 1)->get();
foreach ($papers as $paper) {
    if ($paper->status_code !== 'UNDER_REVIEW') {
        $paper->status_code = 'UNDER_REVIEW';
        $paper->save();
        echo "✅ Set paper to UNDER_REVIEW: {$paper->title}\n";
    }
}

// 3. Also set papers to UNDER_REVIEW in conference ID=8
$papers = BaiBao::where('conference_id', 8)->get();
foreach ($papers as $paper) {
    if ($paper->status_code !== 'UNDER_REVIEW') {
        $paper->status_code = 'UNDER_REVIEW';
        $paper->save();
        echo "✅ Set paper to UNDER_REVIEW: {$paper->title}\n";
    }
}

// Add reviewer to conference ID=8 as well
$existingRole = VaiTroNguoiDung::where('user_id', 33)
    ->where('conference_id', 8)
    ->where('role_code', 'REVIEWER')
    ->first();

if (!$existingRole) {
    VaiTroNguoiDung::create([
        'user_id' => 33,
        'role_code' => 'REVIEWER', 
        'conference_id' => 8
    ]);
    echo "✅ Added reviewer to conference ID=8\n";
}

echo "\n=== VERIFICATION ===\n";

// Check reviewer's conferences now
$roles = VaiTroNguoiDung::where('user_id', 33)
    ->where('role_code', 'REVIEWER')
    ->get();

foreach ($roles as $role) {
    $conference = DB::table('hoithao')->where('conference_id', $role->conference_id)->first();
    if ($conference) {
        echo "Conference: {$conference->title}\n";
        $reviewPapers = BaiBao::where('conference_id', $role->conference_id)
            ->where('status_code', 'UNDER_REVIEW')
            ->get();
        echo "  UNDER_REVIEW papers: {$reviewPapers->count()}\n";
        foreach ($reviewPapers as $paper) {
            echo "    - {$paper->title}\n";
        }
    }
}

echo "\n✨ Now reviewer should see papers for bidding!\n";

?>