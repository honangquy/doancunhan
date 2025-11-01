<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\NguoiDung;
use App\Models\VaiTroNguoiDung;
use App\Models\HoiThao;
use App\Models\BaiBao;
use Illuminate\Support\Facades\DB;

echo "=== DEBUG REVIEWER DATA ===\n";

// Find reviewer
$reviewerEmail = 'tabifap692@dropeso.com';
$reviewer = NguoiDung::where('email', $reviewerEmail)->first();

if (!$reviewer) {
    echo "❌ Reviewer not found with email: $reviewerEmail\n";
    exit;
}

echo "✅ Reviewer found:\n";
echo "   - ID: {$reviewer->user_id}\n";
echo "   - Name: {$reviewer->full_name}\n";
echo "   - Email: {$reviewer->email}\n\n";

// Check reviewer roles
$roles = VaiTroNguoiDung::where('user_id', $reviewer->user_id)->get();
echo "📋 Total roles for this user: {$roles->count()}\n";

foreach ($roles as $role) {
    echo "   - Role: {$role->role_code}";
    if ($role->conference_id) {
        echo " in Conference ID: {$role->conference_id}";
    } else {
        echo " (Global role)";
    }
    echo "\n";
}

// Check REVIEWER roles specifically
$reviewerRoles = VaiTroNguoiDung::where('user_id', $reviewer->user_id)
    ->where('role_code', 'REVIEWER')
    ->get();

echo "\n🔍 REVIEWER roles: {$reviewerRoles->count()}\n";

if ($reviewerRoles->count() === 0) {
    echo "❌ This user has NO REVIEWER roles assigned!\n";
    echo "💡 Solution: Need to add REVIEWER role to user for specific conferences\n\n";
} else {
    foreach ($reviewerRoles as $role) {
        if ($role->conference_id) {
            $conference = HoiThao::find($role->conference_id);
            if ($conference) {
                echo "   - Conference: {$conference->title} (ID: {$role->conference_id})\n";
                echo "     Status: {$conference->status}\n";
                
                // Check papers in this conference
                $papers = BaiBao::where('conference_id', $role->conference_id)->get();
                $approvedPapers = BaiBao::where('conference_id', $role->conference_id)
                    ->where('status_code', 'APPROVED')
                    ->get();
                
                echo "     Total papers: {$papers->count()}\n";
                echo "     APPROVED papers: {$approvedPapers->count()}\n";
                
                if ($approvedPapers->count() > 0) {
                    echo "     Papers available for bidding:\n";
                    foreach ($approvedPapers as $paper) {
                        echo "       • {$paper->title} (Status: {$paper->status_code})\n";
                    }
                }
            }
        } else {
            echo "   - Global REVIEWER role\n";
        }
    }
}

// Check chair's conferences and papers
echo "\n📊 Chair's conferences and papers:\n";
$chairEmail = 'honangquy1@gmail.com';
$chair = NguoiDung::where('email', $chairEmail)->first();

if ($chair) {
    echo "Chair found: {$chair->full_name}\n";
    
    // Find conferences where this user is CHAIR
    $chairRoles = VaiTroNguoiDung::where('user_id', $chair->user_id)
        ->where('role_code', 'CHAIR')
        ->get();
    
    foreach ($chairRoles as $chairRole) {
        if ($chairRole->conference_id) {
            $conference = HoiThao::find($chairRole->conference_id);
            if ($conference) {
                echo "  Conference: {$conference->title} (ID: {$conference->conference_id})\n";
                
                $papers = BaiBao::where('conference_id', $conference->conference_id)->get();
                echo "    Total papers: {$papers->count()}\n";
                
                foreach ($papers as $paper) {
                    echo "      • {$paper->title} (Status: {$paper->status_code})\n";
                }
            }
        }
    }
}

echo "\n=== SOLUTION ===\n";
echo "If reviewer can't see papers for bidding, likely solutions:\n";
echo "1. Add reviewer to conference: INSERT INTO vaitronguoidung (user_id, role_code, conference_id) VALUES ({$reviewer->user_id}, 'REVIEWER', <conference_id>)\n";
echo "2. Make sure papers have status_code = 'APPROVED'\n";
echo "3. Make sure conference status = 'ACTIVE'\n";

?>