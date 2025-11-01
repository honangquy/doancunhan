<?php
// Check table structures for reviewer system
echo "=== REVIEWER SYSTEM TABLE STRUCTURES ===\n\n";

try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;dbname=quanly_hoithao;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Check reviewer_assignments structure
    echo "1. REVIEWER_ASSIGNMENTS TABLE STRUCTURE:\n";
    $stmt = $pdo->query("DESCRIBE reviewer_assignments");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "   {$column['Field']} - {$column['Type']} - {$column['Null']} - {$column['Key']}\n";
    }
    echo "\n";

    // Check reviewer_bidding structure
    echo "2. REVIEWER_BIDDING TABLE STRUCTURE:\n";
    $stmt = $pdo->query("DESCRIBE reviewer_bidding");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "   {$column['Field']} - {$column['Type']} - {$column['Null']} - {$column['Key']}\n";
    }
    echo "\n";

    // Check vaitronguoidung structure
    echo "3. VAITRONGUOIDUNG TABLE STRUCTURE:\n";
    $stmt = $pdo->query("DESCRIBE vaitronguoidung");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "   {$column['Field']} - {$column['Type']} - {$column['Null']} - {$column['Key']}\n";
    }
    echo "\n";

    // Check loaivaitro structure
    echo "4. LOAIVAITRO TABLE STRUCTURE:\n";
    $stmt = $pdo->query("DESCRIBE loaivaitro");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "   {$column['Field']} - {$column['Type']} - {$column['Null']} - {$column['Key']}\n";
    }
    echo "\n";

    // Check sample data
    echo "5. SAMPLE DATA FROM REVIEWER_ASSIGNMENTS:\n";
    $stmt = $pdo->query("SELECT * FROM reviewer_assignments LIMIT 5");
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($assignments)) {
        echo "   No data found\n";
    } else {
        foreach ($assignments as $assignment) {
            echo "   Assignment: " . json_encode($assignment) . "\n";
        }
    }
    echo "\n";

    // Check user hoquy902@gmail.com specifically
    echo "6. USER hoquy902@gmail.com DATA:\n";
    $stmt = $pdo->prepare("SELECT user_id FROM nguoidung WHERE email = ?");
    $stmt->execute(['hoquy902@gmail.com']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $userId = $user['user_id'];
        echo "   User ID: $userId\n";
        
        // Check roles
        echo "   Roles:\n";
        $stmt = $pdo->prepare("
            SELECT vtn.*, lvt.ten_vai_tro 
            FROM vaitronguoidung vtn 
            JOIN loaivaitro lvt ON vtn.role_id = lvt.role_id 
            WHERE vtn.user_id = ?
        ");
        $stmt->execute([$userId]);
        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($roles)) {
            echo "      No roles found\n";
        } else {
            foreach ($roles as $role) {
                echo "      Role: {$role['ten_vai_tro']} (Conference: {$role['conference_id']})\n";
            }
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== CHECK COMPLETE ===\n";
?>