<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$base = '';
requireUserLogin();

$pageTitle = 'My Bookings';
$active = 'bookings';
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT b.*, s.show_date, s.show_time, m.title, m.poster, t.name AS theater_name
    FROM bookings b
    JOIN showtimes s ON b.showtime_id = s.showtime_id
    JOIN movies m ON s.movie_id = m.movie_id
    JOIN theaters t ON s.theater_id = t.theater_id
    WHERE b.user_id = ?
    ORDER BY b.booked_at DESC
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$bookings = $stmt->get_result();

include __DIR__ . '/includes/user_header.php';
?>
<div class="container">
    <h2 class="page-title">My Bookings</h2>

    <?php if ($bookings->num_rows === 0): ?>
        <p style="color:#888;">You haven't booked any tickets yet. <a href="index.php" style="color:#e50914;">Browse movies</a>.</p>
    <?php else: ?>
        <?php while ($b = $bookings->fetch_assoc()): ?>
            <div class="ticket-card <?php echo $b['booking_status'] === 'Cancelled' ? 'cancelled' : ''; ?>">
                <div>
                    <h3><?php echo h($b['title']); ?></h3>
                    <div class="meta">
                        <?php echo h($b['theater_name']); ?> &middot;
                        <?php echo formatDate($b['show_date']); ?> &middot;
                        <?php echo formatTime($b['show_time']); ?>
                    </div>
                    <div class="meta">Seats: <?php echo h($b['seats']); ?> &middot; Total: <?php echo formatPrice($b['total_amount']); ?></div>
                    <div class="meta">Booking ID: #<?php echo str_pad($b['booking_id'], 6, '0', STR_PAD_LEFT); ?></div>
                </div>
                <div style="text-align:right;">
                    <span class="badge <?php echo $b['booking_status'] === 'Confirmed' ? 'now' : 'soon'; ?>">
                        <?php echo h($b['booking_status']); ?>
                    </span><br><br>
                    <?php if ($b['booking_status'] === 'Confirmed' && strtotime($b['show_date'] . ' ' . $b['show_time']) > time()): ?>
                        <a href="cancel_booking.php?id=<?php echo $b['booking_id']; ?>"
                           class="btn small danger"
                           onclick="return confirm('Cancel this booking?');">Cancel</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/user_footer.php'; ?>
