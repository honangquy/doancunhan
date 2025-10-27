<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Columns in vaitronguoidung ===\n";
$columns = DB::select("DESCRIBE vaitronguoidung");
foreach($columns as $col) {
    echo $col->Field . " (" . $col->Type . ")\n";
}

echo "\n=== Columns in loaivaitro ===\n";
$columns = DB::select("DESCRIBE loaivaitro");
foreach($columns as $col) {
    echo $col->Field . " (" . $col->Type . ")\n";
}

echo "\n=== Sample data from vaitronguoidung ===\n";
$data = DB::table('vaitronguoidung')->limit(5)->get();
foreach($data as $row) {
    echo json_encode($row) . "\n";
}
