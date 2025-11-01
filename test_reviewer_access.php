<?php
// Test reviewer routes and access for hoquy902@gmail.com
echo "=== TESTING REVIEWER ACCESS FOR hoquy902@gmail.com ===\n\n";

try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;dbname=quanly_hoithao;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $email = 'hoquy902@gmail.com';
    echo "Testing for: $email\n\n";

    // Get user
    $stmt = $pdo->prepare("SELECT user_id FROM nguoidung WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo "❌ User not found\n";
        exit;
    }
    
    $userId = $user['user_id'];
    echo "User ID: $userId\n\n";

    // Check role middleware compatibility
    echo "1. ROLE MIDDLEWARE CHECK:\n";
    
    // Check vaitronguoidung for REVIEWER role
    $stmt = $pdo->prepare("
        SELECT * FROM vaitronguoidung 
        WHERE user_id = ? AND role_code = 'REVIEWER'
    ");
    $stmt->execute([$userId]);
    $reviewerRoles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($reviewerRoles)) {
        echo "   ❌ NO REVIEWER ROLE FOUND - This will block access!\n";
        echo "   Middleware 'role:REVIEWER' requires exact role match\n";
        
        // Show existing roles
        $stmt = $pdo->prepare("SELECT * FROM vaitronguoidung WHERE user_id = ?");
        $stmt->execute([$userId]);
        $allRoles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "   Current roles:\n";
        foreach ($allRoles as $role) {
            echo "      - {$role['role_code']} (Conference: {$role['conference_id']})\n";
        }
        
        echo "\n   SOLUTION: Add REVIEWER role:\n";
        foreach ($reviewerRoles as $role) {
            echo "      INSERT INTO vaitronguoidung (user_id, role_code, conference_id) VALUES ($userId, 'REVIEWER', {$role['conference_id']});\n";
        }
        
    } else {
        echo "   ✅ REVIEWER role found:\n";
        foreach ($reviewerRoles as $role) {
            echo "      - Conference: {$role['conference_id']}\n";
        }
    }
    echo "\n";

    // Test assignment controller vs reviewer controller
    echo "2. CONTROLLER ROUTES CONFLICT:\n";
    echo "   There are TWO different reviewer routes:\n";
    echo "   - /reviewer/assignments → AssignmentController@index (NEW)\n";
    echo "   - /reviewer/assignments → ReviewerController@assignments (OLD)\n";
    echo "   → Laravel will use the LAST defined route\n\n";

    // Test direct assignment data
    echo "3. ASSIGNMENT DATA TEST:\n";
    
    // Test NEW controller query (AssignmentController)
    $stmt = $pdo->prepare("
        SELECT ra.*, b.title as paper_title, assigner.full_name as assigned_by_name
        FROM reviewer_assignments ra
        JOIN baibao b ON ra.paper_id = b.paper_id
        JOIN nguoidung assigner ON ra.assigned_by = assigner.user_id
        WHERE ra.user_id = ?
        ORDER BY ra.assigned_at DESC
    ");
    $stmt->execute([$userId]);
    $newControllerData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   NEW AssignmentController query result:\n";
    if (empty($newControllerData)) {
        echo "      ❌ No assignments found\n";
    } else {
        echo "      ✅ Found " . count($newControllerData) . " assignments:\n";
        foreach ($newControllerData as $assignment) {
            echo "         - {$assignment['paper_title']} ({$assignment['status']})\n";
        }
    }
    echo "\n";

    // Test OLD controller query (ReviewerController) - after our fix
    $stmt = $pdo->prepare("
        SELECT ra.id as assignment_id, ra.paper_id, ra.status, ra.assigned_at, ra.responded_at,
               bb.title as paper_title, bb.abstract, bb.keywords,
               ht.title as conference_name, ht.conference_id,
               pb.review_id, pb.submitted_at, pb.recommendation_code
        FROM reviewer_assignments ra
        JOIN baibao bb ON ra.paper_id = bb.paper_id
        JOIN hoithao ht ON bb.conference_id = ht.conference_id
        LEFT JOIN phanbien pb ON ra.id = pb.assignment_id
        WHERE ra.user_id = ?
        ORDER BY ra.assigned_at DESC
    ");
    $stmt->execute([$userId]);
    $oldControllerData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   OLD ReviewerController query result:\n";
    if (empty($oldControllerData)) {
        echo "      ❌ No assignments found\n";
    } else {
        echo "      ✅ Found " . count($oldControllerData) . " assignments:\n";
        foreach ($oldControllerData as $assignment) {
            echo "         - {$assignment['paper_title']} ({$assignment['status']})\n";
            if (!isset($assignment['status'])) {
                echo "           ⚠️ WARNING: 'status' field missing!\n";
            }
        }
    }
    echo "\n";

    // Check bidding access
    echo "4. BIDDING ACCESS TEST:\n";
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count FROM reviewer_bidding WHERE user_id = ?
    ");
    $stmt->execute([$userId]);
    $biddingCount = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "   User has {$biddingCount['count']} bidding records\n";
    echo "   Route: /reviewer/bidding → BiddingController@index\n";
    echo "   Requires: middleware('role:REVIEWER')\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>