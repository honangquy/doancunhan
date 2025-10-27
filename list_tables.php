<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== All Tables ===\n";
$tables = DB::select('SHOW TABLES');
foreach($tables as $t) {
    $name = $t->{array_key_first((array)$t)};
    echo $name . "\n";
}
