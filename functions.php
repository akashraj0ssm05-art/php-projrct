<?php
/**
 * Common helper functions used across the app
 */

// Sanitize output to prevent XSS
function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// Redirect helper
function redirect($url) {
    header("Location: $url");
    exit;
}

// Check if a normal user is logged in
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if an admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Require user login, else redirect to login page
function requireUserLogin() {
    if (!isUserLoggedIn()) {
        redirect('login.php');
    }
}

// Require admin login, else redirect to admin login page
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        redirect('login.php');
    }
}

// Format currency
function formatPrice($amount) {
    return '₹' . number_format($amount, 2);
}

// Format date nicely
function formatDate($date) {
    return date('D, d M Y', strtotime($date));
}

// Format time nicely
function formatTime($time) {
    return date('h:i A', strtotime($time));
}

// Generate simple seat map labels (Row A-E, seats 1-10 => 50 seats)
function generateSeatMap($totalSeats) {
    $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
    $seatsPerRow = ceil($totalSeats / count($rows));
    $seats = [];
    $count = 0;
    foreach ($rows as $row) {
        for ($i = 1; $i <= $seatsPerRow; $i++) {
            if ($count >= $totalSeats) break 2;
            $seats[] = $row . $i;
            $count++;
        }
    }
    return $seats;
}
