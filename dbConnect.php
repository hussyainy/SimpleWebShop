<?php
// Prevent direct access
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    http_response_code(403);
    exit('Forbidden');
}

// Load .env variables
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env');
    foreach ($lines as $line) {
        if (trim($line) && strpos($line, '=') !== false) {
            list($name, $value) = explode('=', trim($line), 2);
            putenv("$name=$value");
        }
    }
}

$host = getenv('DB_HOST');
$db = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    // Do not echo sensitive info
    error_log('DB connection failed: ' . $conn->connect_error);
    http_response_code(500);
    exit('Database connection error');
}
?>
