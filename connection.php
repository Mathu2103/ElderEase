<?php
$host = 'localhost';         // or '127.0.0.1'
$db   = 'elderease';         // replace with your database name
$user = 'root';              // replace with your MySQL username
$pass = '';                  // replace with your MySQL password
$charset = 'utf8mb4';

// Set Data Source Name (DSN)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Enable error reporting
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Attempt connection
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // You can echo "Connected!" here for testing
} catch (\PDOException $e) {
    exit("Database connection failed: " . $e->getMessage());
}
?>
