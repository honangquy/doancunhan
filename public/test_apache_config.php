<?php
echo "<h2>Apache Modules Check for PDF Configuration</h2>";

// Check if mod_headers is loaded
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    echo "<h3>Apache Modules Status:</h3>";
    
    if (in_array('mod_headers', $modules)) {
        echo "✅ mod_headers: <strong style='color:green'>ENABLED</strong><br>";
    } else {
        echo "❌ mod_headers: <strong style='color:red'>DISABLED</strong> - Cần enable để cấu hình PDF headers<br>";
    }
    
    if (in_array('mod_mime', $modules)) {
        echo "✅ mod_mime: <strong style='color:green'>ENABLED</strong><br>";
    } else {
        echo "❌ mod_mime: <strong style='color:red'>DISABLED</strong><br>";
    }
    
    if (in_array('mod_rewrite', $modules)) {
        echo "✅ mod_rewrite: <strong style='color:green'>ENABLED</strong><br>";
    } else {
        echo "❌ mod_rewrite: <strong style='color:red'>DISABLED</strong><br>";
    }
    
    echo "<hr>";
    echo "<h3>All Loaded Modules:</h3>";
    echo "<pre>" . implode("\n", $modules) . "</pre>";
    
} else {
    echo "❌ Cannot check modules - apache_get_modules() not available<br>";
}

// Test headers for PDF
echo "<hr>";
echo "<h3>Test PDF Headers:</h3>";
echo "<a href='/storage/cfp_files/sample.pdf' target='_blank'>Test PDF Link</a><br>";
echo "<small>Check if this link opens PDF inline instead of downloading</small>";
?>