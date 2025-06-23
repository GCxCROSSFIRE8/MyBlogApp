<?php
$host = 'localhost';
$db   = 'myblogapp';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // turn errors into exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // fetch associative arrays by default
    PDO::ATTR_EMULATE_PREPARES   => false,                  // disable emulation to use real prepared statements
];

try {
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die('Database Connection failed: ' . $e->getMessage());
}
?>
