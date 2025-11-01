<?php
// Debug assignment tables and reviewer data
echo "=== DEBUGGING REVIEWER ASSIGNMENT TABLES ===\n\n";

try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;dbname=quanly_hoithao;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $userId = 11; // hoquy902@gmail.com

    echo "1. CHECKING PHANCONGPHANBIEN TABLE:\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'phancongphanbien'");
    $hasPhancongphanbien = $stmt->rowCount() > 0;
    
    if ($hasPhancongphanbien) {
        echo "   ✅ Table 'phancongphanbien' exists\n";
        
        // Check structure
        echo "   Structure:\n";
        $stmt = $pdo->query("DESCRIBE phancongphanbien");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($columns as $column) {
            echo "      {$column['Field']} - {$column['Type']}\n";
        }
        
        // Check data for user
        echo "   Data for user $userId:\n";
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM phancongphanbien WHERE reviewer_id = ?");
        $stmt->execute([$userId]);
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "      Assignments: {$count['count']}\n";
        
        if ($count['count'] > 0) {
            $stmt = $pdo->prepare("SELECT * FROM phancongphanbien WHERE reviewer_id = ?");
            $stmt->execute([$userId]);
            $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($assignments as $assignment) {
                echo "      - Assignment ID: {$assignment['assignment_id']}\n";
                echo "        Paper ID: {$assignment['paper_id']}\n";
                echo "        Status: {$assignment['status_code']}\n";
                echo "        Assigned: {$assignment['assigned_at']}\n";
            }
        }
        
    } else {
        echo "   ❌ Table 'phancongphanbien' does not exist\n";
    }
    echo "\n";

    echo "2. CHECKING REVIEWER_ASSIGNMENTS TABLE:\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'reviewer_assignments'");
    $hasReviewerAssignments = $stmt->rowCount() > 0;
    
    if ($hasReviewerAssignments) {
        echo "   ✅ Table 'reviewer_assignments' exists\n";
        
        // Check data for user
        echo "   Data for user $userId:\n";
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM reviewer_assignments WHERE user_id = ?");
        $stmt->execute([$userId]);
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "      Assignments: {$count['count']}\n";
        
        if ($count['count'] > 0) {
            $stmt = $pdo->prepare("
                SELECT ra.*, b.title as paper_title 
                FROM reviewer_assignments ra 
                LEFT JOIN baibao b ON ra.paper_id = b.paper_id 
                WHERE ra.user_id = ?
            ");
            $stmt->execute([$userId]);
            $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($assignments as $assignment) {
                echo "      - Assignment ID: {$assignment['id']}\n";
                echo "        Paper: {$assignment['paper_title']}\n";
                echo "        Conference: {$assignment['conference_id']}\n";
                echo "        Status: {$assignment['status']}\n";
                echo "        Assigned: {$assignment['assigned_at']}\n";
            }
        }
        
    } else {
        echo "   ❌ Table 'reviewer_assignments' does not exist\n";
    }
    echo "\n";

    // Check which table the reviewer controller should be using
    echo "3. RECOMMENDATION:\n";
    if ($hasPhancongphanbien && !$hasReviewerAssignments) {
        echo "   Use 'phancongphanbien' table\n";
    } elseif (!$hasPhancongphanbien && $hasReviewerAssignments) {
        echo "   Use 'reviewer_assignments' table\n";
    } elseif ($hasPhancongphanbien && $hasReviewerAssignments) {
        echo "   Both tables exist - need to check which has the actual data\n";
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM phancongphanbien");
        $phancongCount = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM reviewer_assignments");
        $reviewerCount = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "   phancongphanbien has {$phancongCount['count']} records\n";
        echo "   reviewer_assignments has {$reviewerCount['count']} records\n";
        
        if ($reviewerCount['count'] > $phancongCount['count']) {
            echo "   → Recommend using 'reviewer_assignments' (more data)\n";
        } else {
            echo "   → Recommend using 'phancongphanbien' (more data)\n";
        }
    } else {
        echo "   Neither table exists - major problem!\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
?>