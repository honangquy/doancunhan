<?php
// Check phanbien table structure
echo "=== CHECKING PHANBIEN TABLE ===\n\n";

try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;dbname=quanly_hoithao;charset=utf8mb4',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Check if phanbien table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'phanbien'");
    $hasPhanbien = $stmt->rowCount() > 0;
    
    if ($hasPhanbien) {
        echo "✅ Table 'phanbien' exists\n";
        
        // Check structure
        echo "Structure:\n";
        $stmt = $pdo->query("DESCRIBE phanbien");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($columns as $column) {
            echo "   {$column['Field']} - {$column['Type']}\n";
        }
        
        // Check data count
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM phanbien");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "\nTotal records: {$count['count']}\n";
        
        if ($count['count'] > 0) {
            echo "\nSample records:\n";
            $stmt = $pdo->query("SELECT * FROM phanbien LIMIT 3");
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($reviews as $review) {
                echo "   Review ID: {$review['review_id']}\n";
                echo "   Paper ID: " . ($review['paper_id'] ?? 'N/A') . "\n";
                echo "   Reviewer ID: " . ($review['reviewer_id'] ?? 'N/A') . "\n";
                echo "   Submitted: " . ($review['submitted_at'] ?? 'N/A') . "\n";
                echo "   ---\n";
            }
        }
        
    } else {
        echo "❌ Table 'phanbien' does not exist\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== CHECK COMPLETE ===\n";
?>