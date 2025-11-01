<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$columns = Schema::getColumnListing('baibao');
echo "Columns in baibao table:\n";
foreach ($columns as $column) {
    echo "  - $column\n";
}

echo "\nColumns in hoithao table:\n";
$hoithaoColumns = Schema::getColumnListing('hoithao');
foreach ($hoithaoColumns as $column) {
    echo "  - $column\n";
}

?>