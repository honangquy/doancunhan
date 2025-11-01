<?php
// Check user roles for hoquy902@gmail.com  
echo "=== CHECKING USER ROLES FOR REVIEWER ACCESS ===\n\n";

try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;dbname=quanly_hoithao;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $email = 'hoquy902@gmail.com';
    echo "Checking roles for: $email\n\n";

    // Get user ID
    $stmt = $pdo->prepare("SELECT user_id, full_name FROM nguoidung WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo "❌ User not found\n";
        exit;
    }
    
    $userId = $user['user_id'];
    echo "User ID: $userId\n";
    echo "Name: {$user['full_name']}\n\n";

    // Check user roles in vaitronguoidung table
    echo "1. USER ROLES (vaitronguoidung table):\n";
    $stmt = $pdo->prepare("
        SELECT vtn.*, lvt.role_name
        FROM vaitronguoidung vtn
        JOIN loaivaitro lvt ON vtn.role_code = lvt.role_code
        WHERE vtn.user_id = ?
    ");
    $stmt->execute([$userId]);
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($roles)) {
        echo "   ❌ No roles found\n";
    } else {
        echo "   ✅ Roles found:\n";
        $hasReviewerRole = false;
        
        foreach ($roles as $role) {
            echo "      Role: {$role['role_name']} ({$role['role_code']})\n";
            echo "      Conference: {$role['conference_id']}\n";
            echo "      ---\n";
            
            if (strtolower($role['role_code']) == 'reviewer' || 
                strpos(strtolower($role['role_name']), 'reviewer') !== false ||
                strpos(strtolower($role['role_name']), 'phản biện') !== false) {
                $hasReviewerRole = true;
            }
        }
        
        echo "   Has Reviewer Role: " . ($hasReviewerRole ? "✅ YES" : "❌ NO") . "\n";
    }
    echo "\n";

    // Check available role codes
    echo "2. AVAILABLE ROLE CODES:\n";
    $stmt = $pdo->query("SELECT * FROM loaivaitro");
    $availableRoles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($availableRoles as $roleType) {
        echo "   {$roleType['role_code']} - {$roleType['role_name']}\n";
    }
    echo "\n";

    // Check if user should have reviewer access based on assignments
    echo "3. REVIEWER ACCESS EXPECTATION:\n";
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM reviewer_assignments WHERE user_id = ?");
    $stmt->execute([$userId]);
    $assignmentCount = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "   User has {$assignmentCount['count']} assignments\n";
    
    if ($assignmentCount['count'] > 0) {
        echo "   → User SHOULD have reviewer access (has assignments)\n";
        
        // Get conferences from assignments
        $stmt = $pdo->prepare("
            SELECT DISTINCT ra.conference_id, h.title
            FROM reviewer_assignments ra
            JOIN hoithao h ON ra.conference_id = h.conference_id  
            WHERE ra.user_id = ?
        ");
        $stmt->execute([$userId]);
        $conferences = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "   Conferences with assignments:\n";
        foreach ($conferences as $conf) {
            echo "      - {$conf['title']} (ID: {$conf['conference_id']})\n";
            
            // Check if user has reviewer role for this conference
            $stmt2 = $pdo->prepare("
                SELECT * FROM vaitronguoidung 
                WHERE user_id = ? AND conference_id = ? 
                AND role_code LIKE '%reviewer%'
            ");
            $stmt2->execute([$userId, $conf['conference_id']]);
            $confRole = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            if ($confRole) {
                echo "         ✅ Has reviewer role for this conference\n";
            } else {
                echo "         ❌ Missing reviewer role for this conference\n";
                echo "         → Need to add reviewer role for conference {$conf['conference_id']}\n";
            }
        }
    } else {
        echo "   → User should NOT have reviewer access (no assignments)\n";
    }

    // Final recommendation
    echo "\n4. RECOMMENDATION:\n";
    if ($assignmentCount['count'] > 0 && !$hasReviewerRole) {
        echo "   ❌ USER MISSING REVIEWER ROLE - Need to add reviewer role to vaitronguoidung table\n";
        echo "   Suggested SQL:\n";
        
        $stmt = $pdo->prepare("
            SELECT DISTINCT conference_id FROM reviewer_assignments WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $confIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($confIds as $confId) {
            echo "      INSERT INTO vaitronguoidung (user_id, role_code, conference_id) VALUES ($userId, 'REVIEWER', $confId);\n";
        }
    } elseif ($hasReviewerRole) {
        echo "   ✅ User has proper reviewer role - should be able to access reviewer interface\n";
    } else {
        echo "   ℹ️ User has no assignments and no reviewer role - normal state\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== CHECK COMPLETE ===\n";
?>