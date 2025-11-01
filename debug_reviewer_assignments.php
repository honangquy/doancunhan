<?php
// Debug reviewer assignments for hoquy902@gmail.com
echo "=== DEBUG REVIEWER ASSIGNMENTS FOR hoquy902@gmail.com ===\n\n";

$email = 'hoquy902@gmail.com';

try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;dbname=quanly_hoithao;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // 1. Check if user exists
    echo "1. CHECKING USER EXISTENCE:\n";
    $stmt = $pdo->prepare("SELECT user_id, full_name, email, created_at FROM nguoidung WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo "   ❌ User not found with email: $email\n";
        exit;
    }
    
    echo "   ✅ User found:\n";
    echo "      User ID: {$user['user_id']}\n";
    echo "      Name: {$user['full_name']}\n";
    echo "      Email: {$user['email']}\n";
    echo "      Created: {$user['created_at']}\n\n";
    
    $userId = $user['user_id'];

    // 2. Check user roles
    echo "2. CHECKING USER ROLES:\n";
    $stmt = $pdo->prepare("
        SELECT vtn.*, lvt.ten_vai_tro 
        FROM vaitronguoidung vtn 
        JOIN loaivaitra lvt ON vtn.role_id = lvt.role_id 
        WHERE vtn.user_id = ?
    ");
    $stmt->execute([$userId]);
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($roles)) {
        echo "   ❌ No roles found for user\n";
    } else {
        echo "   ✅ User roles:\n";
        foreach ($roles as $role) {
            echo "      Role: {$role['ten_vai_tro']} (ID: {$role['role_id']})\n";
            echo "      Conference ID: {$role['conference_id']}\n";
            echo "      Status: {$role['status']}\n";
            echo "      Created: {$role['created_at']}\n";
        }
    }
    echo "\n";

    // 3. Check reviewer assignments
    echo "3. CHECKING REVIEWER ASSIGNMENTS:\n";
    $stmt = $pdo->prepare("
        SELECT ra.*, b.title as paper_title, ht.title as conference_title
        FROM reviewer_assignments ra
        JOIN baibao b ON ra.paper_id = b.paper_id
        JOIN hoithao ht ON b.conference_id = ht.conference_id
        WHERE ra.reviewer_id = ?
        ORDER BY ra.created_at DESC
    ");
    $stmt->execute([$userId]);
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($assignments)) {
        echo "   ❌ No assignments found for this reviewer\n";
    } else {
        echo "   ✅ Found " . count($assignments) . " assignments:\n";
        foreach ($assignments as $assignment) {
            echo "      Assignment ID: {$assignment['assignment_id']}\n";
            echo "      Paper: {$assignment['paper_title']}\n";
            echo "      Conference: {$assignment['conference_title']}\n";
            echo "      Status: {$assignment['status']}\n";
            echo "      Assigned by: {$assignment['assigned_by']}\n";
            echo "      Created: {$assignment['created_at']}\n";
            echo "      ---\n";
        }
    }
    echo "\n";

    // 4. Check bidding history
    echo "4. CHECKING BIDDING HISTORY:\n";
    $stmt = $pdo->prepare("
        SELECT rb.*, b.title as paper_title, ht.title as conference_title
        FROM reviewer_bidding rb
        JOIN baibao b ON rb.paper_id = b.paper_id
        JOIN hoithao ht ON b.conference_id = ht.conference_id
        WHERE rb.user_id = ?
        ORDER BY rb.created_at DESC
    ");
    $stmt->execute([$userId]);
    $biddings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($biddings)) {
        echo "   ❌ No bidding history found\n";
    } else {
        echo "   ✅ Found " . count($biddings) . " biddings:\n";
        foreach ($biddings as $bidding) {
            echo "      Paper: {$bidding['paper_title']}\n";
            echo "      Conference: {$bidding['conference_title']}\n";
            echo "      Bid Value: {$bidding['bidding_value']}\n";
            echo "      COI: " . ($bidding['coi'] ? 'Yes' : 'No') . "\n";
            echo "      Created: {$bidding['created_at']}\n";
            echo "      ---\n";
        }
    }
    echo "\n";

    // 5. Check papers that have assignments (to see if there are any assignments at all)
    echo "5. CHECKING TOTAL ASSIGNMENTS IN SYSTEM:\n";
    $stmt = $pdo->query("
        SELECT COUNT(*) as total_assignments,
               COUNT(DISTINCT reviewer_id) as unique_reviewers,
               COUNT(DISTINCT paper_id) as papers_with_assignments
        FROM reviewer_assignments
    ");
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   Total assignments: {$stats['total_assignments']}\n";
    echo "   Unique reviewers: {$stats['unique_reviewers']}\n";
    echo "   Papers with assignments: {$stats['papers_with_assignments']}\n\n";

    // 6. Check recent assignments (last 10)
    echo "6. RECENT ASSIGNMENTS IN SYSTEM:\n";
    $stmt = $pdo->query("
        SELECT ra.assignment_id, ra.reviewer_id, ra.paper_id, ra.status, ra.created_at,
               n.email, b.title as paper_title
        FROM reviewer_assignments ra
        JOIN nguoidung n ON ra.reviewer_id = n.user_id
        JOIN baibao b ON ra.paper_id = b.paper_id
        ORDER BY ra.created_at DESC
        LIMIT 10
    ");
    $recentAssignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($recentAssignments)) {
        echo "   ❌ No assignments found in system\n";
    } else {
        echo "   ✅ Recent assignments:\n";
        foreach ($recentAssignments as $assignment) {
            echo "      Reviewer: {$assignment['email']}\n";
            echo "      Paper: {$assignment['paper_title']}\n";
            echo "      Status: {$assignment['status']}\n";
            echo "      Created: {$assignment['created_at']}\n";
            echo "      ---\n";
        }
    }

    // 7. Check if there are any papers this user should be able to see
    echo "\n7. CHECKING CONFERENCES WHERE USER HAS REVIEWER ROLE:\n";
    $stmt = $pdo->prepare("
        SELECT DISTINCT ht.conference_id, ht.title, ht.status
        FROM vaitronguoidung vtn
        JOIN hoithao ht ON vtn.conference_id = ht.conference_id
        JOIN loaivaitra lvt ON vtn.role_id = lvt.role_id
        WHERE vtn.user_id = ? AND lvt.ten_vai_tro = 'Reviewer'
    ");
    $stmt->execute([$userId]);
    $reviewerConferences = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($reviewerConferences)) {
        echo "   ❌ User has no Reviewer role in any conference\n";
    } else {
        echo "   ✅ User has Reviewer role in conferences:\n";
        foreach ($reviewerConferences as $conf) {
            echo "      Conference: {$conf['title']} (ID: {$conf['conference_id']})\n";
            echo "      Status: {$conf['status']}\n";
            
            // Check papers in this conference
            $stmt2 = $pdo->prepare("SELECT COUNT(*) as paper_count FROM baibao WHERE conference_id = ?");
            $stmt2->execute([$conf['conference_id']]);
            $paperCount = $stmt2->fetch(PDO::FETCH_ASSOC);
            echo "      Papers: {$paperCount['paper_count']}\n";
            echo "      ---\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
?>