<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

requireUserLogin();

$booking_id = intval($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("UPDATE bookings SET booking_status = 'Cancelled' WHERE booking_id = ? AND user_id = ?");
$stmt->bind_param('ii', $booking_id, $user_id);
$stmt->execute();

redirect('my_bookings.php');
