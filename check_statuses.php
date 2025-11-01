<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$statuses = DB::table('trangthaibaibao')->get();
echo "Available status codes:\n";
foreach ($statuses as $status) {
    echo "  - {$status->status_code} ({$status->status_name})\n";
}

?>