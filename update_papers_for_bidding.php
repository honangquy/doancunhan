<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BaiBao;
use Illuminate\Support\Facades\DB;

echo "=== UPDATING PAPERS TO ALLOW IMMEDIATE BIDDING ===\n";

// Get all papers currently in UNDER_REVIEW and set back to SUBMITTED
$papers = BaiBao::whereIn('status_code', ['UNDER_REVIEW'])->get();

foreach ($papers as $paper) {
    $paper->status_code = 'SUBMITTED';
    $paper->save();
    echo "✅ Set paper '{$paper->title}' back to SUBMITTED for immediate bidding\n";
}

echo "\n=== VERIFICATION ===\n";

// Check papers by conference
$conferences = DB::table('hoithao')->where('status', 'ACTIVE')->get();

foreach ($conferences as $conference) {
    $submittedPapers = BaiBao::where('conference_id', $conference->conference_id)
        ->where('status_code', 'SUBMITTED')
        ->get();
    
    if ($submittedPapers->count() > 0) {
        echo "Conference: {$conference->title}\n";
        echo "  SUBMITTED papers (available for bidding): {$submittedPapers->count()}\n";
        foreach ($submittedPapers as $paper) {
            echo "    - {$paper->title}\n";
        }
        echo "\n";
    }
}

echo "✨ All papers are now available for immediate bidding upon submission!\n";

?>