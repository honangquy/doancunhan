<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST PAPER CREATION FLOW ===\n\n";

// Test data
$authorId = 251; // author@test.com
$conferenceId = 1; // HUIT ICT 2025

echo "Step 1: Check prerequisites\n";
echo "----------------------------\n";

// Check author exists
$author = DB::table('NguoiDung')->where('user_id', $authorId)->first();
if ($author) {
    echo "✓ Author found: {$author->full_name} ({$author->email})\n";
} else {
    echo "✗ Author not found\n";
    exit(1);
}

// Check conference exists and is active
$conference = DB::table('HoiThao')->where('conference_id', $conferenceId)->first();
if ($conference && $conference->status === 'ACTIVE' && $conference->deadline_submission > date('Y-m-d')) {
    echo "✓ Conference found: {$conference->title}\n";
    echo "  Deadline: {$conference->deadline_submission}\n";
} else {
    echo "✗ Conference not active or deadline passed\n";
    exit(1);
}

echo "\nStep 2: Simulate paper submission WITHOUT co-authors\n";
echo "-----------------------------------------------------\n";

DB::beginTransaction();

try {
    // Insert paper
    $paperId = DB::table('BaiBao')->insertGetId([
        'conference_id' => $conferenceId,
        'submitter_id' => $authorId,
        'title' => 'Test Paper - Machine Learning Applications',
        'abstract' => 'This is a comprehensive test abstract for machine learning applications in real-world scenarios.',
        'keywords' => 'machine learning, AI, deep learning, neural networks',
        'status_code' => 'SUBMITTED',
        'file_path' => 'papers/1/test_' . time() . '.pdf',
        'created_at' => now(),
    ]);
    
    echo "✓ Paper inserted with ID: {$paperId}\n";
    
    // Add submitter as author
    DB::table('TacGiaBaiBao')->insert([
        'paper_id' => $paperId,
        'user_id' => $authorId,
        'author_order' => 1,
        'is_contact' => 1,
    ]);
    
    echo "✓ Author record created\n";
    
    // Verify
    $paper = DB::table('BaiBao')->where('paper_id', $paperId)->first();
    $authors = DB::table('TacGiaBaiBao')->where('paper_id', $paperId)->get();
    
    echo "\nPaper details:\n";
    echo "  ID: {$paper->paper_id}\n";
    echo "  Title: {$paper->title}\n";
    echo "  Status: {$paper->status_code}\n";
    echo "  Authors: " . $authors->count() . "\n";
    
    DB::rollBack(); // Don't actually save
    echo "\n✓ Transaction rolled back (test only)\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nStep 3: Simulate paper submission WITH co-authors\n";
echo "--------------------------------------------------\n";

DB::beginTransaction();

try {
    // Insert paper
    $paperId = DB::table('BaiBao')->insertGetId([
        'conference_id' => $conferenceId,
        'submitter_id' => $authorId,
        'title' => 'Test Paper with Co-authors',
        'abstract' => 'Test abstract with multiple authors.',
        'keywords' => 'test, co-authors, collaboration',
        'status_code' => 'SUBMITTED',
        'file_path' => 'papers/1/test_' . time() . '.pdf',
        'created_at' => now(),
    ]);
    
    echo "✓ Paper inserted with ID: {$paperId}\n";
    
    // Add submitter as first author
    DB::table('TacGiaBaiBao')->insert([
        'paper_id' => $paperId,
        'user_id' => $authorId,
        'author_order' => 1,
        'is_contact' => 1,
    ]);
    
    echo "✓ Primary author added\n";
    
    // Simulate co-authors from form
    $coAuthors = [
        [
            'name' => 'Dr. John Smith',
            'email' => 'john.smith@example.com',
            'organization' => 'MIT'
        ],
        [
            'name' => 'Prof. Jane Doe',
            'email' => 'jane.doe@stanford.edu',
            'organization' => 'Stanford University'
        ]
    ];
    
    $order = 2;
    foreach ($coAuthors as $coAuthor) {
        // Find or create user
        $coAuthorUser = DB::table('NguoiDung')
            ->where('email', $coAuthor['email'])
            ->first();
        
        if (!$coAuthorUser) {
            $coAuthorUserId = DB::table('NguoiDung')->insertGetId([
                'email' => $coAuthor['email'],
                'full_name' => $coAuthor['name'],
                'organization' => $coAuthor['organization'],
                'password_hash' => bcrypt('temporary_password_' . time()),
                'created_at' => now(),
            ]);
            echo "  ✓ Created new user: {$coAuthor['name']} (ID: {$coAuthorUserId})\n";
        } else {
            $coAuthorUserId = $coAuthorUser->user_id;
            echo "  ✓ Found existing user: {$coAuthorUser->full_name} (ID: {$coAuthorUserId})\n";
        }
        
        // Add to TacGiaBaiBao
        DB::table('TacGiaBaiBao')->insert([
            'paper_id' => $paperId,
            'user_id' => $coAuthorUserId,
            'author_order' => $order,
            'is_contact' => 0,
            'organization' => $coAuthor['organization'],
        ]);
        
        $order++;
    }
    
    echo "✓ Co-authors added\n";
    
    // Verify
    $authors = DB::table('TacGiaBaiBao')
        ->join('NguoiDung', 'TacGiaBaiBao.user_id', '=', 'NguoiDung.user_id')
        ->where('paper_id', $paperId)
        ->orderBy('author_order')
        ->get();
    
    echo "\nFinal author list:\n";
    foreach ($authors as $a) {
        echo "  {$a->author_order}. {$a->full_name} ({$a->email})";
        if ($a->is_contact) {
            echo " [Contact Author]";
        }
        echo "\n";
    }
    
    DB::rollBack(); // Don't actually save
    echo "\n✓ Transaction rolled back (test only)\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n=== ALL TESTS PASSED ===\n";
echo "✓ Controller logic should work correctly\n";
echo "✓ You can now test via browser\n";

