<?php
// Test reviewer assignments API/query after controller update
echo "=== TEST REVIEWER ASSIGNMENTS AFTER CONTROLLER UPDATE ===\n\n";

try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;dbname=quanly_hoithao;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $userId = 11; // hoquy902@gmail.com
    echo "Testing assignments for User ID: $userId (hoquy902@gmail.com)\n\n";

    // Simulate the updated controller query
    echo "1. ASSIGNMENTS QUERY (NEW CONTROLLER LOGIC):\n";
    $stmt = $pdo->prepare("
        SELECT 
            ra.id as assignment_id,
            ra.paper_id,
            ra.status,
            ra.assigned_at,
            ra.responded_at,
            bb.title as paper_title,
            bb.abstract,
            bb.keywords,
            ht.title as conference_name,
            ht.conference_id,
            pb.review_id,
            pb.submitted_at,
            pb.recommendation_code
        FROM reviewer_assignments ra
        JOIN baibao bb ON ra.paper_id = bb.paper_id
        JOIN hoithao ht ON bb.conference_id = ht.conference_id
        LEFT JOIN phanbien pb ON ra.id = pb.assignment_id
        WHERE ra.user_id = ?
        ORDER BY ra.assigned_at DESC
    ");
    $stmt->execute([$userId]);
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($assignments)) {
        echo "   ❌ No assignments found\n";
    } else {
        echo "   ✅ Found " . count($assignments) . " assignments:\n";
        
        $pending = $accepted = $completed = 0;
        
        foreach ($assignments as $assignment) {
            echo "\n      Assignment #{$assignment['assignment_id']}:\n";
            echo "      Paper: {$assignment['paper_title']}\n";
            echo "      Conference: {$assignment['conference_name']}\n";
            echo "      Status: {$assignment['status']}\n";
            echo "      Assigned: {$assignment['assigned_at']}\n";
            echo "      Responded: " . ($assignment['responded_at'] ?? 'Not yet') . "\n";
            echo "      Review Submitted: " . ($assignment['submitted_at'] ?? 'Not yet') . "\n";
            
            // Count stats
            if ($assignment['status'] == 'PENDING') $pending++;
            elseif ($assignment['status'] == 'ACCEPTED') $accepted++;
            elseif ($assignment['status'] == 'COMPLETED') $completed++;
        }
        
        echo "\n   STATISTICS:\n";
        echo "      Total: " . count($assignments) . "\n";
        echo "      Pending: $pending\n";
        echo "      Accepted: $accepted\n";
        echo "      Completed: $completed\n";
    }
    echo "\n";

    // Test assignment details for creating review
    if (!empty($assignments)) {
        $firstAssignmentId = $assignments[0]['assignment_id'];
        echo "2. ASSIGNMENT DETAILS FOR REVIEW (Assignment ID: $firstAssignmentId):\n";
        
        $stmt = $pdo->prepare("
            SELECT 
                ra.id as assignment_id,
                ra.paper_id,
                ra.status,
                ra.assigned_at,
                bb.title,
                bb.abstract,
                bb.keywords,
                bb.file_path,
                ht.title as conference_name,
                ht.conference_id
            FROM reviewer_assignments ra
            JOIN baibao bb ON ra.paper_id = bb.paper_id
            JOIN hoithao ht ON bb.conference_id = ht.conference_id
            WHERE ra.id = ? AND ra.user_id = ?
        ");
        $stmt->execute([$firstAssignmentId, $userId]);
        $assignmentDetail = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($assignmentDetail) {
            echo "   ✅ Assignment detail found:\n";
            echo "      Paper: {$assignmentDetail['title']}\n";
            echo "      Abstract: " . substr($assignmentDetail['abstract'], 0, 100) . "...\n";
            echo "      Keywords: {$assignmentDetail['keywords']}\n";
            echo "      Conference: {$assignmentDetail['conference_name']}\n";
            echo "      Status: {$assignmentDetail['status']}\n";
            echo "      Can create review: " . ($assignmentDetail['status'] == 'ACCEPTED' ? 'Yes' : 'No (need to accept first)') . "\n";
        } else {
            echo "   ❌ Assignment detail not found\n";
        }
    }
    echo "\n";

    // Check what user should see in UI
    echo "3. UI EXPECTATIONS:\n";
    echo "   - User should see " . count($assignments) . " assignments in reviewer dashboard\n";
    echo "   - Each assignment should have accept/decline buttons if status is PENDING\n";
    echo "   - Accepted assignments should have 'Create Review' button\n";
    echo "   - Controller methods (accept/decline) should work with new table structure\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>