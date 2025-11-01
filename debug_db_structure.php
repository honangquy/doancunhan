<?php
// Check database structure
echo "=== DATABASE STRUCTURE CHECK ===\n\n";

try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;dbname=quanly_hoithao;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Show all tables
    echo "1. ALL TABLES:\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        echo "   - $table\n";
    }
    echo "\n";

    // Check specific user
    echo "2. USER hoquy902@gmail.com:\n";
    $stmt = $pdo->prepare("SELECT * FROM nguoidung WHERE email = ?");
    $stmt->execute(['hoquy902@gmail.com']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "   User ID: {$user['user_id']}\n";
        echo "   Name: {$user['full_name']}\n";
        echo "   Email: {$user['email']}\n";
        
        $userId = $user['user_id'];
        
        // Check roles table structure 
        echo "\n3. CHECKING ROLE TABLES:\n";
        
        // Check if loaivaitra exists
        $tableExists = false;
        foreach ($tables as $table) {
            if (strtolower($table) == 'loaivaitra') {
                $tableExists = true;
                break;
            }
        }
        
        if (!$tableExists) {
            echo "   ❌ Table 'loaivaitra' not found\n";
            
            // Look for similar tables
            $roleTables = [];
            foreach ($tables as $table) {
                if (strpos(strtolower($table), 'vai') !== false || 
                    strpos(strtolower($table), 'role') !== false) {
                    $roleTables[] = $table;
                }
            }
            
            if (!empty($roleTables)) {
                echo "   Found similar tables:\n";
                foreach ($roleTables as $table) {
                    echo "      - $table\n";
                }
            }
        }
        
        // Check reviewer_assignments
        echo "\n4. CHECKING REVIEWER_ASSIGNMENTS:\n";
        if (in_array('reviewer_assignments', $tables)) {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM reviewer_assignments WHERE reviewer_id = ?");
            $stmt->execute([$userId]);
            $assignmentCount = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "   Assignments for user: {$assignmentCount['count']}\n";
            
            if ($assignmentCount['count'] > 0) {
                $stmt = $pdo->prepare("
                    SELECT ra.*, b.title 
                    FROM reviewer_assignments ra 
                    LEFT JOIN baibao b ON ra.paper_id = b.paper_id 
                    WHERE ra.reviewer_id = ?
                ");
                $stmt->execute([$userId]);
                $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($assignments as $assignment) {
                    echo "      - Paper: {$assignment['title']}\n";
                    echo "        Status: {$assignment['status']}\n";
                    echo "        Created: {$assignment['created_at']}\n";
                }
            }
        } else {
            echo "   ❌ Table 'reviewer_assignments' not found\n";
        }
        
        // Check reviewer_bidding
        echo "\n5. CHECKING REVIEWER_BIDDING:\n";
        if (in_array('reviewer_bidding', $tables)) {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM reviewer_bidding WHERE user_id = ?");
            $stmt->execute([$userId]);
            $biddingCount = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "   Biddings by user: {$biddingCount['count']}\n";
        } else {
            echo "   ❌ Table 'reviewer_bidding' not found\n";
        }
        
    } else {
        echo "   ❌ User not found\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== CHECK COMPLETE ===\n";
?>