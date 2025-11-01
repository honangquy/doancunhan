<?php
require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
    'database' => $_ENV['DB_DATABASE'] ?? 'laravel',
    'username' => $_ENV['DB_USERNAME'] ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'charset' => 'utf8mb4',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "📋 assignment_notifications table structure:\n";
$columns = Capsule::select('SHOW COLUMNS FROM assignment_notifications');
foreach($columns as $col) {
    echo "  Column: {$col->Field}, Type: {$col->Type}\n";
}
?>