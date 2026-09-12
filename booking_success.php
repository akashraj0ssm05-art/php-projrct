<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$base = '';
requireUserLogin();

$pageTitle = 'Booking Confirmed';
$booking_id = intval($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT b.*, s.show_date, s.show_time, s.price, m.title, t.name AS theater_name
    FROM bookings b
    JOIN showtimes s ON b.showtime_id = s.showtime_id
    JOIN movies m ON s.movie_id = m.movie_id
    JOIN theaters t ON s.theater_id = t.theater_id
    WHERE b.booking_id = ? AND b.user_id = ?
");
$stmt->bind_param('ii', $booking_id, $user_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) redirect('index.php');

include __DIR__ . '/includes/user_header.php';
?>
<div class="container">
    <div class="form-card" style="text-align:center; max-width:520px;">
        <div style="font-size:50px; color:#2ecc71; margin-bottom:10px;">&#10003;</div>
        <h2>Booking Confirmed!</h2>
        <p style="color:#666; margin-bottom:20px;">Your tickets have been booked successfully.</p>

        <div style="text-align:left; background:#f9f9f9; padding:18px; border-radius:8px; margin-bottom:20px;">
            <p><strong>Movie:</strong> <?php echo h($booking['title']); ?></p>
            <p><strong>Theater:</strong> <?php echo h($booking['theater_name']); ?></p>
            <p><strong>Date:</strong> <?php echo formatDate($booking['show_date']); ?></p>
            <p><strong>Time:</strong> <?php echo formatTime($booking['show_time']); ?></p>
            <p><strong>Seats:</strong> <?php echo h($booking['seats']); ?></p>
            <p><strong>Total Paid:</strong> <?php echo formatPrice($booking['total_amount']); ?></p>
            <p><strong>Booking ID:</strong> #<?php echo str_pad($booking['booking_id'], 6, '0', STR_PAD_LEFT); ?></p>
        </div>

        <a href="my_bookings.php" class="btn full">View My Bookings</a>
        <a href="index.php" class="btn full secondary" style="margin-top:10px;">Browse More Movies</a>
    </div>
</div>
<?php include __DIR__ . '/includes/user_footer.php'; ?>
