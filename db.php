<?php
/**
 * Database Connection
 * Update these credentials according to your local / server MySQL setup
 */
define('DB_HOST', 'sql112.infinityfree.com');
define('DB_USER', 'if0_42876059');
define('DB_PASS', '1JIMEJH4F8s');
define('DB_NAME', 'if0_42876059_movie_db');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

// Start session globally (safe to call once here)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
