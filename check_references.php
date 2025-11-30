<?php
// Check table references
echo "Checking table references...\n";

// Check giatribidding references
echo "=== giatribidding references ===\n";
try {
    $refs = DB::select("SELECT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_NAME = 'giatribidding'");
    if (empty($refs)) {
        echo "No foreign key references found\n";
    } else {
        print_r($refs);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Check loaikhuyennghi references  
echo "\n=== loaikhuyennghi references ===\n";
try {
    $refs = DB::select("SELECT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_NAME = 'loaikhuyennghi'");
    if (empty($refs)) {
        echo "No foreign key references found\n";
    } else {
        print_r($refs);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Check trangthaiphancong references
echo "\n=== trangthaiphancong references ===\n";
try {
    $refs = DB::select("SELECT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_NAME = 'trangthaiphancong'");
    if (empty($refs)) {
        echo "No foreign key references found\n";
    } else {
        print_r($refs);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Search for usage in code
echo "\n=== Code usage check ===\n";

// Check if bidding codes are used
$files = glob(app_path() . '/**/*.php', GLOB_BRACE) + glob(resource_path() . '/**/*.php', GLOB_BRACE);

$tables_to_check = ['giatribidding', 'loaikhuyennghi', 'trangthaiphancong'];
$found_usage = [];

foreach ($files as $file) {
    $content = file_get_contents($file);
    foreach ($tables_to_check as $table) {
        if (strpos($content, $table) !== false) {
            $found_usage[$table][] = $file;
        }
    }
}

foreach ($tables_to_check as $table) {
    echo "Files using {$table}: " . (count($found_usage[$table] ?? [])). "\n";
    if (!empty($found_usage[$table])) {
        foreach ($found_usage[$table] as $file) {
            echo "  - " . str_replace(base_path(), '', $file) . "\n";
        }
    }
}
?>