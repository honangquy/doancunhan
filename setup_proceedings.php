<?php

/**
 * Setup test data for Proceedings System
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Setting up test data for Proceedings System ===\n\n";

try {
    // 1. Get current user (assuming logged in user from your screenshot is user_id = 1)
    $currentUserId = 1; // Adjust this based on your login
    
    // 2. Find a conference to work with
    $conference = DB::table('hoithao')->first();
    
    if (!$conference) {
        echo "No conferences found. Creating test conference...\n";
        
        $conferenceId = DB::table('hoithao')->insertGetId([
            'title' => 'Hội thảo Test Proceedings',
            'conference_name' => 'Test Conference for Proceedings',
            'acronym' => 'TCP2025',
            'year' => 2025,
            'venue' => 'HUIT Campus',
            'conference_date' => '2025-12-01',
            'status' => 'ACTIVE',
            'created_at' => now(),
        ]);
        
        echo "Created conference with ID: $conferenceId\n";
    } else {
        $conferenceId = $conference->conference_id;
        echo "Using existing conference: {$conference->title} (ID: $conferenceId)\n";
    }
    
    // 3. Make current user a CHAIR of this conference
    $existingRole = DB::table('vaitronguoidung')
        ->where('user_id', $currentUserId)
        ->where('conference_id', $conferenceId)
        ->where('role_code', 'CHAIR')
        ->first();
        
    if (!$existingRole) {
        DB::table('vaitronguoidung')->insert([
            'user_id' => $currentUserId,
            'conference_id' => $conferenceId,
            'role_code' => 'CHAIR',
            'created_at' => now(),
        ]);
        echo "Added CHAIR role for user $currentUserId in conference $conferenceId\n";
    } else {
        echo "User $currentUserId is already CHAIR of conference $conferenceId\n";
    }
    
    // 4. Create some test papers if they don't exist
    $paperCount = DB::table('baibao')->where('conference_id', $conferenceId)->count();
    
    if ($paperCount < 3) {
        echo "Creating test papers...\n";
        
        for ($i = 1; $i <= 3; $i++) {
            $paperId = DB::table('baibao')->insertGetId([
                'conference_id' => $conferenceId,
                'submitter_id' => $currentUserId,
                'title' => "Bài báo test số $i cho kỷ yếu",
                'abstract' => "Đây là tóm tắt cho bài báo test số $i. Bài báo này được tạo để test hệ thống proceedings.",
                'keywords' => "test, proceedings, conference",
                'status_code' => 'SUBMITTED',
                'decision' => 'ACCEPTED', // Make it accepted so it shows in proceedings
                'page_start' => $i * 10 - 9, // Pages 1-10, 11-20, 21-30
                'page_end' => $i * 10,
                'created_at' => now(),
            ]);
            
            echo "Created paper: Bài báo test số $i (ID: $paperId)\n";
        }
    } else {
        echo "Conference already has $paperCount papers\n";
        
        // Update some existing papers to be ACCEPTED
        DB::table('baibao')
            ->where('conference_id', $conferenceId)
            ->limit(3)
            ->update([
                'decision' => 'ACCEPTED',
                'page_start' => null,
                'page_end' => null
            ]);
            
        echo "Updated some papers to ACCEPTED status\n";
    }
    
    // 5. Show summary
    echo "\n=== Setup Complete ===\n";
    echo "Conference ID: $conferenceId\n";
    echo "Conference Name: " . DB::table('hoithao')->where('conference_id', $conferenceId)->value('title') . "\n";
    echo "User $currentUserId is CHAIR of this conference\n";
    
    $acceptedCount = DB::table('baibao')
        ->where('conference_id', $conferenceId)
        ->where('decision', 'ACCEPTED')
        ->count();
        
    echo "Accepted papers: $acceptedCount\n";
    
    $publishedCount = DB::table('baibao')
        ->where('conference_id', $conferenceId)
        ->where('decision', 'PUBLISHED')
        ->count();
        
    echo "Published papers: $publishedCount\n";
    
    echo "\nYou can now test the proceedings system:\n";
    echo "1. Login to the application\n";
    echo "2. Go to 'Xuất bản kỷ yếu' menu\n";
    echo "3. Select the conference: $conferenceId\n";
    echo "4. Manage proceedings for accepted papers\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

?>