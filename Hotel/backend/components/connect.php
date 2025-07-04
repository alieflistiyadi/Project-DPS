<?php
// Enable mysqli error reporting (optional but very helpful for debugging)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Database connection configuration
$host = 'mysql'; // Docker service name
$user = 'root';
$password = 'root';
$dbname = 'hotel_db';

// Create a database connection
try {
    $conn = new mysqli($host, $user, $password, $dbname);
    // Set character set (optional, but important to prevent encoding issues)
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    // Handle connection error
    die("Database connection failed: " . $e->getMessage());
}
?>

