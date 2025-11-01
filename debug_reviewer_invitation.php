<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test the exact query from ReviewerInvitationController
$userId = 19; // User ID of honangquy1@gmail.com

echo "Testing ReviewerInvitationController query...\n\n";

try {
    // Exact query from the controller
    $conferences = DB::table('hoithao as ht')
        ->join('vaitronguoidung as vt', 'ht.conference_id', '=', 'vt.conference_id')
        ->where('vt.user_id', $userId)
        ->where('vt.role_code', 'CHAIR')
        ->where('ht.status', 'ACTIVE')
        ->select('ht.*')
        ->orderBy('ht.start_date', 'desc')
        ->get();

    echo "Query executed successfully!\n";
    echo "Number of conferences found: " . $conferences->count() . "\n\n";

    if ($conferences->count() > 0) {
        echo "Conferences found:\n";
        foreach ($conferences as $conf) {
            echo "- ID: {$conf->conference_id}\n";
            echo "  Title: {$conf->title}\n";
            echo "  Status: {$conf->status}\n\n";
        }
    } else {
        echo "❌ No conferences found! Let's debug...\n\n";
        
        // Debug step by step
        echo "Debug 1: Check if user exists\n";
        $user = DB::table('nguoidung')->where('user_id', $userId)->first();
        if ($user) {
            echo "✅ User found: {$user->full_name} ({$user->email})\n\n";
        } else {
            echo "❌ User not found!\n\n";
            exit;
        }
        
        echo "Debug 2: Check user roles\n";
        $roles = DB::table('vaitronguoidung')->where('user_id', $userId)->get();
        echo "User roles count: " . $roles->count() . "\n";
        foreach ($roles as $role) {
            echo "- Role: {$role->role_code}, Conference: {$role->conference_id}\n";
        }
        echo "\n";
        
        echo "Debug 3: Check conferences table\n";
        $allConferences = DB::table('hoithao')->get();
        echo "Total conferences in system: " . $allConferences->count() . "\n";
        foreach ($allConferences as $conf) {
            echo "- ID: {$conf->conference_id}, Title: {$conf->title}, Status: {$conf->status}\n";
        }
        echo "\n";
        
        echo "Debug 4: Check the JOIN without WHERE conditions\n";
        $joinResult = DB::table('hoithao as ht')
            ->join('vaitronguoidung as vt', 'ht.conference_id', '=', 'vt.conference_id')
            ->select('ht.conference_id', 'ht.title', 'ht.status', 'vt.user_id', 'vt.role_code')
            ->get();
        
        echo "Join result count: " . $joinResult->count() . "\n";
        foreach ($joinResult as $row) {
            echo "- Conference: {$row->conference_id} ({$row->title}), User: {$row->user_id}, Role: {$row->role_code}\n";
        }
        echo "\n";
        
        echo "Debug 5: Check with user filter\n";
        $userFilterResult = DB::table('hoithao as ht')
            ->join('vaitronguoidung as vt', 'ht.conference_id', '=', 'vt.conference_id')
            ->where('vt.user_id', $userId)
            ->select('ht.conference_id', 'ht.title', 'ht.status', 'vt.user_id', 'vt.role_code')
            ->get();
        
        echo "With user filter count: " . $userFilterResult->count() . "\n";
        foreach ($userFilterResult as $row) {
            echo "- Conference: {$row->conference_id} ({$row->title}), Role: {$row->role_code}, Status: {$row->status}\n";
        }
        echo "\n";
        
        echo "Debug 6: Check with role filter\n";
        $roleFilterResult = DB::table('hoithao as ht')
            ->join('vaitronguoidung as vt', 'ht.conference_id', '=', 'vt.conference_id')
            ->where('vt.user_id', $userId)
            ->where('vt.role_code', 'CHAIR')
            ->select('ht.conference_id', 'ht.title', 'ht.status', 'vt.user_id', 'vt.role_code')
            ->get();
        
        echo "With role filter count: " . $roleFilterResult->count() . "\n";
        foreach ($roleFilterResult as $row) {
            echo "- Conference: {$row->conference_id} ({$row->title}), Status: {$row->status}\n";
        }
    }

} catch (\Exception $e) {
    echo "❌ Query failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}