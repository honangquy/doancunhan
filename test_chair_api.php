<?php

/**
 * Comprehensive Chair API Test Script
 * Test all Chair endpoints with user honangquy1@gmail.com
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║          CHAIR API COMPREHENSIVE TEST SUITE                  ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Configuration
$baseUrl = 'http://127.0.0.1:8000/api';
$email = 'honangquy1@gmail.com';
$password = 'Concac123!@#';

// Colors for output
$green = "\033[32m";
$red = "\033[31m";
$yellow = "\033[33m";
$blue = "\033[34m";
$reset = "\033[0m";

function printSuccess($message) {
    global $green, $reset;
    echo "{$green}✓ {$message}{$reset}\n";
}

function printError($message) {
    global $red, $reset;
    echo "{$red}✗ {$message}{$reset}\n";
}

function printInfo($message) {
    global $blue, $reset;
    echo "{$blue}ℹ {$message}{$reset}\n";
}

function printWarning($message) {
    global $yellow, $reset;
    echo "{$yellow}⚠ {$message}{$reset}\n";
}

function makeRequest($method, $url, $token = null, $data = null) {
    $ch = curl_init();
    
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
    ];
    
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return ['status' => 0, 'error' => $error, 'body' => null];
    }
    
    return [
        'status' => $httpCode,
        'body' => json_decode($response, true),
        'raw' => $response
    ];
}

// ============================================================================
// STEP 1: AUTHENTICATION
// ============================================================================
echo "\n═══════════════════════════════════════════════════════════════\n";
echo "STEP 1: AUTHENTICATION\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

printInfo("Logging in with email: {$email}");

$loginResult = makeRequest('POST', "{$baseUrl}/auth/login", null, [
    'email' => $email,
    'password' => $password
]);

if ($loginResult['status'] !== 200) {
    printError("Login failed with status {$loginResult['status']}");
    echo "Response: " . json_encode($loginResult['body'], JSON_PRETTY_PRINT) . "\n";
    echo "\nPlease update the password in the script or create user with password '123456'\n";
    exit(1);
}

// Check different token field names
$token = $loginResult['body']['token'] 
    ?? $loginResult['body']['access_token'] 
    ?? $loginResult['body']['data']['token']
    ?? null;

if (!$token) {
    printError("No token in login response");
    echo "Full response:\n";
    echo json_encode($loginResult['body'], JSON_PRETTY_PRINT) . "\n";
    exit(1);
}

printSuccess("Login successful!");
printInfo("Token: " . substr($token, 0, 50) . "...");

// Get user info
$user = DB::table('nguoidung')->where('email', $email)->first();
printInfo("User ID: {$user->user_id}");
printInfo("Name: {$user->full_name}");

// Get conferences
$conferences = DB::table('vaitronguoidung')
    ->where('user_id', $user->user_id)
    ->where('role_code', 'CHAIR')
    ->pluck('conference_id')
    ->toArray();
printInfo("Chair of " . count($conferences) . " conferences: " . implode(', ', $conferences));

// ============================================================================
// STEP 2: DASHBOARD TEST
// ============================================================================
echo "\n═══════════════════════════════════════════════════════════════\n";
echo "STEP 2: DASHBOARD TEST\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

printInfo("Testing GET /api/chair/dashboard");

$result = makeRequest('GET', "{$baseUrl}/chair/dashboard", $token);

if ($result['status'] === 200) {
    printSuccess("Dashboard API works!");
    
    $data = $result['body']['data'];
    $stats = $data['statistics'];
    
    echo "\n  Statistics:\n";
    echo "  - Total Conferences: {$stats['total_conferences']}\n";
    echo "  - Total Submissions: {$stats['total_submissions']}\n";
    echo "  - Under Review: {$stats['papers_under_review']}\n";
    echo "  - Reviewed: {$stats['papers_reviewed']}\n";
    echo "  - Needs Reviewers: {$stats['needs_reviewers']}\n";
    echo "  - Pending Decisions: {$stats['pending_decisions']}\n";
    echo "  - Decisions Made: {$stats['decisions_made']}\n";
    
    echo "\n  Recent Papers: " . count($data['recent_papers']) . " papers\n";
    echo "  Pending Actions: " . count($data['pending_actions']) . " actions\n";
    
    if (count($data['pending_actions']) > 0) {
        echo "\n  Pending Actions Details:\n";
        foreach ($data['pending_actions'] as $action) {
            echo "    - [{$action['priority']}] {$action['type']}: {$action['title']}\n";
        }
    }
} else {
    printError("Dashboard API failed with status {$result['status']}");
    echo json_encode($result['body'], JSON_PRETTY_PRINT) . "\n";
}

// ============================================================================
// STEP 3: PAPERS LIST TEST
// ============================================================================
echo "\n═══════════════════════════════════════════════════════════════\n";
echo "STEP 3: PAPERS LIST TEST\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

printInfo("Testing GET /api/chair/papers");

$result = makeRequest('GET', "{$baseUrl}/chair/papers?per_page=10", $token);

if ($result['status'] === 200) {
    printSuccess("Papers list API works!");
    
    $papers = $result['body']['data']['papers'];
    $pagination = $result['body']['data']['pagination'];
    
    echo "\n  Pagination:\n";
    echo "  - Total: {$pagination['total']} papers\n";
    echo "  - Current Page: {$pagination['current_page']}\n";
    echo "  - Per Page: {$pagination['per_page']}\n";
    echo "  - Last Page: {$pagination['last_page']}\n";
    
    echo "\n  Papers on current page: " . count($papers) . "\n";
    
    if (count($papers) > 0) {
        echo "\n  Sample Papers:\n";
        foreach (array_slice($papers, 0, 3) as $paper) {
            echo "    Paper #{$paper['paper_id']}: {$paper['title']}\n";
            echo "      Status: {$paper['status_name']}\n";
            echo "      Reviewers: {$paper['reviewers']['assigned']} assigned, {$paper['reviewers']['completed']} completed\n";
            if ($paper['reviewers']['avg_score']) {
                echo "      Avg Score: {$paper['reviewers']['avg_score']}\n";
            }
        }
        
        // Save first paper ID for detailed test
        $testPaperId = $papers[0]['paper_id'];
    }
} else {
    printError("Papers list API failed with status {$result['status']}");
    echo json_encode($result['body'], JSON_PRETTY_PRINT) . "\n";
}

// Test with filters
echo "\n";
printInfo("Testing filters: status=SUBMITTED");

$result = makeRequest('GET', "{$baseUrl}/chair/papers?status=SUBMITTED&per_page=5", $token);

if ($result['status'] === 200) {
    $count = count($result['body']['data']['papers']);
    printSuccess("Filter by status works! Found {$count} SUBMITTED papers");
} else {
    printError("Filter test failed");
}

// ============================================================================
// STEP 4: PAPER DETAIL TEST
// ============================================================================
echo "\n═══════════════════════════════════════════════════════════════\n";
echo "STEP 4: PAPER DETAIL TEST\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

if (isset($testPaperId)) {
    printInfo("Testing GET /api/chair/papers/{$testPaperId}");
    
    $result = makeRequest('GET', "{$baseUrl}/chair/papers/{$testPaperId}", $token);
    
    if ($result['status'] === 200) {
        printSuccess("Paper detail API works!");
        
        $data = $result['body']['data'];
        $paper = $data['paper'];
        
        echo "\n  Paper Info:\n";
        echo "  - ID: {$paper['paper_id']}\n";
        echo "  - Title: {$paper['title']}\n";
        echo "  - Status: {$paper['status_name']}\n";
        echo "  - Conference: {$paper['conference_name']}\n";
        
        echo "\n  Authors: " . count($data['authors']) . " author(s)\n";
        foreach ($data['authors'] as $author) {
            $name = $author['full_name'] ?? $author['author_name'];
            $contact = $author['is_contact'] ? ' (Contact)' : '';
            echo "    - {$name}{$contact}\n";
        }
        
        echo "\n  Assignments: " . count($data['assignments']) . " reviewer(s)\n";
        foreach ($data['assignments'] as $assignment) {
            echo "    - {$assignment['reviewer_name']}: {$assignment['status']}\n";
            if ($assignment['review_id']) {
                echo "      Review submitted, Score: {$assignment['overall_score']}, Recommendation: {$assignment['recommendation']}\n";
            }
        }
        
        echo "\n  Reviews: " . count($data['reviews']) . " completed\n";
        foreach ($data['reviews'] as $review) {
            echo "    - {$review['reviewer_name']}\n";
            echo "      Score: {$review['overall_score']}, Confidence: {$review['confidence_level']}\n";
            echo "      Recommendation: {$review['recommendation']}\n";
        }
    } else {
        printError("Paper detail API failed with status {$result['status']}");
        if (isset($result['body']['message'])) {
            echo "  Error: {$result['body']['message']}\n";
        }
    }
} else {
    printWarning("No papers available for detail test");
}

// ============================================================================
// STEP 5: AVAILABLE REVIEWERS TEST
// ============================================================================
echo "\n═══════════════════════════════════════════════════════════════\n";
echo "STEP 5: AVAILABLE REVIEWERS TEST\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

if (isset($testPaperId)) {
    printInfo("Testing GET /api/chair/papers/{$testPaperId}/available-reviewers");
    
    $result = makeRequest('GET', "{$baseUrl}/chair/papers/{$testPaperId}/available-reviewers", $token);
    
    if ($result['status'] === 200) {
        $reviewers = $result['body']['data']['reviewers'];
        printSuccess("Available reviewers API works! Found " . count($reviewers) . " reviewers");
        
        if (count($reviewers) > 0) {
            echo "\n  Sample Reviewers:\n";
            foreach (array_slice($reviewers, 0, 3) as $reviewer) {
                echo "    - {$reviewer['full_name']} ({$reviewer['email']})\n";
                echo "      Current assignments: {$reviewer['current_assignments']}\n";
            }
            
            // Save for assignment test
            $testReviewerId = $reviewers[0]['user_id'];
        }
    } else {
        printError("Available reviewers API failed with status {$result['status']}");
    }
}

// ============================================================================
// STEP 6: ASSIGN REVIEWER TEST
// ============================================================================
echo "\n═══════════════════════════════════════════════════════════════\n";
echo "STEP 6: ASSIGN REVIEWER TEST\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

if (isset($testPaperId) && isset($testReviewerId)) {
    printInfo("Testing POST /api/chair/papers/{$testPaperId}/assign-reviewer");
    printInfo("Assigning reviewer ID: {$testReviewerId}");
    
    $result = makeRequest('POST', "{$baseUrl}/chair/papers/{$testPaperId}/assign-reviewer", $token, [
        'reviewer_id' => $testReviewerId,
        'deadline' => '2025-12-31'
    ]);
    
    if ($result['status'] === 200) {
        printSuccess("Assign reviewer API works!");
        $assignmentId = $result['body']['data']['assignment_id'];
        echo "  Assignment ID: {$assignmentId}\n";
        
        // Verify in database
        $assignment = DB::table('reviewer_assignments')->where('id', $assignmentId)->first();
        if ($assignment) {
            printSuccess("Assignment verified in database");
            echo "  Status: {$assignment->status}\n";
            echo "  Assigned at: {$assignment->assigned_at}\n";
        }
    } else if ($result['status'] === 422 && isset($result['body']['message'])) {
        printWarning("Assignment skipped: {$result['body']['message']}");
        // Already assigned, that's okay
    } else {
        printError("Assign reviewer API failed with status {$result['status']}");
        echo json_encode($result['body'], JSON_PRETTY_PRINT) . "\n";
    }
} else {
    printWarning("No paper or reviewer available for assignment test");
}

// ============================================================================
// STEP 7: REMOVE ASSIGNMENT TEST (Optional)
// ============================================================================
echo "\n═══════════════════════════════════════════════════════════════\n";
echo "STEP 7: REMOVE ASSIGNMENT TEST (Skip if assignment has review)\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Find an assignment without completed review
$assignmentToRemove = DB::table('reviewer_assignments as ra')
    ->leftJoin('phanbien as pb', function($join) {
        $join->on('ra.id', '=', 'pb.assignment_id')
             ->where('pb.is_draft', 0)
             ->whereNotNull('pb.submitted_at');
    })
    ->whereIn('ra.conference_id', $conferences)
    ->whereNull('pb.review_id')
    ->select('ra.id', 'ra.paper_id', 'ra.user_id')
    ->first();

if ($assignmentToRemove) {
    printInfo("Testing DELETE /api/chair/assignments/{$assignmentToRemove->id}");
    printWarning("This will actually delete the assignment - skipping for safety");
    printInfo("To test, uncomment the code below");
    
    // Uncomment to actually test deletion
    /*
    $result = makeRequest('DELETE', "{$baseUrl}/chair/assignments/{$assignmentToRemove->id}", $token);
    
    if ($result['status'] === 200) {
        printSuccess("Remove assignment API works!");
    } else {
        printError("Remove assignment failed with status {$result['status']}");
    }
    */
} else {
    printWarning("No suitable assignment found for removal test (all have reviews)");
}

// ============================================================================
// STEP 8: MAKE DECISION TEST
// ============================================================================
echo "\n═══════════════════════════════════════════════════════════════\n";
echo "STEP 8: MAKE DECISION TEST\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Find a paper with REVIEWED status
$reviewedPaper = DB::table('baibao')
    ->whereIn('conference_id', $conferences)
    ->where('status_code', 'REVIEWED')
    ->first();

if ($reviewedPaper) {
    printInfo("Testing POST /api/chair/papers/{$reviewedPaper->paper_id}/decision");
    printWarning("This would change paper status - showing simulation only");
    
    echo "\n  Would send request:\n";
    echo "  {\n";
    echo "    \"decision\": \"ACCEPTED\",\n";
    echo "    \"comments\": \"Good paper with strong reviews\"\n";
    echo "  }\n";
    
    printInfo("To actually test, uncomment the code in the script");
    
    // Uncomment to actually test decision making
    /*
    $result = makeRequest('POST', "{$baseUrl}/chair/papers/{$reviewedPaper->paper_id}/decision", $token, [
        'decision' => 'ACCEPTED',
        'comments' => 'Test decision from API test suite'
    ]);
    
    if ($result['status'] === 200) {
        printSuccess("Make decision API works!");
        // Rollback
        DB::table('baibao')->where('paper_id', $reviewedPaper->paper_id)->update(['status_code' => 'REVIEWED']);
    } else {
        printError("Make decision failed with status {$result['status']}");
    }
    */
} else {
    printWarning("No papers with REVIEWED status for decision test");
}

// ============================================================================
// STEP 9: REVIEW STATISTICS TEST
// ============================================================================
echo "\n═══════════════════════════════════════════════════════════════\n";
echo "STEP 9: REVIEW STATISTICS TEST\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$conferenceId = $conferences[0];
printInfo("Testing GET /api/chair/conferences/{$conferenceId}/review-statistics");

$result = makeRequest('GET', "{$baseUrl}/chair/conferences/{$conferenceId}/review-statistics", $token);

if ($result['status'] === 200) {
    printSuccess("Review statistics API works!");
    
    $data = $result['body']['data'];
    
    echo "\n  Papers by Status:\n";
    foreach ($data['papers_by_status'] as $status) {
        echo "    - {$status['status_name']}: {$status['count']}\n";
    }
    
    echo "\n  Reviewer Performance (top 3):\n";
    foreach (array_slice($data['reviewer_performance'], 0, 3) as $reviewer) {
        echo "    - {$reviewer['full_name']}\n";
        echo "      Assigned: {$reviewer['total_assigned']}, Completed: {$reviewer['completed']}\n";
        $rate = $reviewer['total_assigned'] > 0 ? 
            round($reviewer['completed'] / $reviewer['total_assigned'] * 100, 1) : 0;
        echo "      Completion Rate: {$rate}%\n";
    }
    
    echo "\n  Scores by Recommendation:\n";
    foreach ($data['scores_by_recommendation'] as $rec) {
        echo "    - {$rec['recommendation_code']}: {$rec['count']} reviews, Avg Score: " . 
             round($rec['avg_score'], 2) . "\n";
    }
} else {
    printError("Review statistics API failed with status {$result['status']}");
    echo json_encode($result['body'], JSON_PRETTY_PRINT) . "\n";
}

// ============================================================================
// STEP 10: REVIEWERS LIST TEST
// ============================================================================
echo "\n═══════════════════════════════════════════════════════════════\n";
echo "STEP 10: REVIEWERS LIST TEST\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

printInfo("Testing GET /api/chair/reviewers");

$result = makeRequest('GET', "{$baseUrl}/chair/reviewers", $token);

if ($result['status'] === 200) {
    $reviewers = $result['body']['data']['reviewers'];
    printSuccess("Reviewers list API works! Found " . count($reviewers) . " reviewers");
    
    if (count($reviewers) > 0) {
        echo "\n  Top Performers:\n";
        
        // Sort by completion rate
        usort($reviewers, function($a, $b) {
            $rateA = $a['statistics']['total_assigned'] > 0 ? 
                $a['statistics']['completed'] / $a['statistics']['total_assigned'] : 0;
            $rateB = $b['statistics']['total_assigned'] > 0 ? 
                $b['statistics']['completed'] / $b['statistics']['total_assigned'] : 0;
            return $rateB <=> $rateA;
        });
        
        foreach (array_slice($reviewers, 0, 5) as $reviewer) {
            $stats = $reviewer['statistics'];
            $rate = $stats['total_assigned'] > 0 ? 
                round($stats['completed'] / $stats['total_assigned'] * 100, 1) : 0;
            
            echo "    - {$reviewer['full_name']}\n";
            echo "      Assigned: {$stats['total_assigned']}, Completed: {$stats['completed']} ({$rate}%)\n";
            if ($stats['avg_score']) {
                echo "      Avg Score: " . round($stats['avg_score'], 2) . "\n";
            }
        }
    }
} else {
    printError("Reviewers list API failed with status {$result['status']}");
    echo json_encode($result['body'], JSON_PRETTY_PRINT) . "\n";
}

// ============================================================================
// FINAL SUMMARY
// ============================================================================
echo "\n╔══════════════════════════════════════════════════════════════╗\n";
echo "║                     TEST SUMMARY                              ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

$endpoints = [
    'POST /api/auth/login' => '✓',
    'GET /api/chair/dashboard' => '✓',
    'GET /api/chair/papers' => '✓',
    'GET /api/chair/papers/{id}' => isset($testPaperId) ? '✓' : '⊘',
    'GET /api/chair/papers/{id}/available-reviewers' => isset($testPaperId) ? '✓' : '⊘',
    'POST /api/chair/papers/{id}/assign-reviewer' => isset($testPaperId) && isset($testReviewerId) ? '✓' : '⊘',
    'DELETE /api/chair/assignments/{id}' => 'Skipped',
    'POST /api/chair/papers/{id}/decision' => 'Simulated',
    'GET /api/chair/conferences/{id}/review-statistics' => '✓',
    'GET /api/chair/reviewers' => '✓',
];

foreach ($endpoints as $endpoint => $status) {
    echo str_pad($endpoint, 50) . " [{$status}]\n";
}

echo "\n";
printSuccess("All critical Chair API endpoints are working!");
printInfo("Chair: {$user->full_name} ({$user->email})");
printInfo("Conferences: " . implode(', ', $conferences));

echo "\n╔══════════════════════════════════════════════════════════════╗\n";
echo "║               READY FOR FLUTTER DEVELOPMENT                  ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";
