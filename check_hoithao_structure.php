<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "CHECKING HOITHAO TABLE STRUCTURE\n";
echo "=================================\n\n";

try {
    // Get table structure
    $columns = DB::select('DESCRIBE hoithao');
    
    echo "HOITHAO TABLE COLUMNS:\n";
    echo "---------------------\n";
    
    $duplicates = [];
    $similar = [];
    
    foreach ($columns as $column) {
        echo sprintf("%-30s | %-15s | %-8s | %-8s | %-15s\n", 
            $column->Field, 
            $column->Type, 
            $column->Null, 
            $column->Key,
            $column->Default ?? 'NULL'
        );
        
        // Check for potential duplicates based on field names
        $field = $column->Field;
        
        // Deadline fields
        if (strpos($field, 'deadline') !== false) {
            $similar['deadline'][] = $field;
        }
        
        // Date fields  
        if (strpos($field, 'date') !== false) {
            $similar['date'][] = $field;
        }
        
        // Title/Name fields
        if (strpos($field, 'title') !== false || strpos($field, 'name') !== false) {
            $similar['title_name'][] = $field;
        }
        
        // Chair/Contact fields
        if (strpos($field, 'chair') !== false || strpos($field, 'contact') !== false) {
            $similar['chair_contact'][] = $field;
        }
    }
    
    echo "\n\nPOTENTIAL DUPLICATE/SIMILAR FIELDS:\n";
    echo "===================================\n";
    
    foreach ($similar as $category => $fields) {
        if (count($fields) > 1) {
            echo "\n$category fields:\n";
            foreach ($fields as $field) {
                echo "  - $field\n";
            }
        }
    }
    
    // Check sample data for empty/duplicate values
    echo "\n\nSAMPLE DATA ANALYSIS:\n";
    echo "====================\n";
    
    $sampleData = DB::table('hoithao')->limit(5)->get();
    if ($sampleData->count() > 0) {
        $first = $sampleData->first();
        foreach ($first as $field => $value) {
            echo sprintf("%-30s: %s\n", $field, $value ?? 'NULL');
        }
    } else {
        echo "No data found in hoithao table.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";
