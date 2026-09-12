<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();

$pageTitle = 'All Bookings';
$active = 'bookings';

// Optional cancel action from admin
if (isset($_GET['cancel'])) {
    $id = intval($_GET['cancel']);
    $stmt = $conn->prepare("UPDATE bookings SET booking_status = 'Cancelled' WHERE booking_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    header('Location: bookings.php?cancelled=1');
    exit;
}

$bookings = $conn->query("
    SELECT b.*, u.full_name, u.email, m.title, s.show_date, s.show_time, t.name AS theater_name
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN showtimes s ON b.showtime_id = s.showtime_id
    JOIN movies m ON s.movie_id = m.movie_id
    JOIN theaters t ON s.theater_id = t.theater_id
    ORDER BY b.booked_at DESC
");

include __DIR__ . '/includes/admin_header.php';
?>

<?php if (isset($_GET['cancelled'])): ?><div class="alert success">Booking cancelled successfully.</div><?php endif; ?>

<div class="card">
    <h2>All Bookings</h2>
    <table>
        <thead>
            <tr><th>ID</th><th>Customer</th><th>Movie</th><th>Theater</th><th>Show Date/Time</th><th>Seats</th><th>Amount</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php if ($bookings->num_rows === 0): ?>
            <tr><td colspan="9" style="text-align:center; color:#888;">No bookings yet.</td></tr>
        <?php else: ?>
            <?php while ($b = $bookings->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo str_pad($b['booking_id'], 6, '0', STR_PAD_LEFT); ?></td>
                    <td><?php echo h($b['full_name']); ?><br><small style="color:#888;"><?php echo h($b['email']); ?></small></td>
                    <td><?php echo h($b['title']); ?></td>
                    <td><?php echo h($b['theater_name']); ?></td>
                    <td><?php echo formatDate($b['show_date']); ?>, <?php echo formatTime($b['show_time']); ?></td>
                    <td><?php echo h($b['seats']); ?></td>
                    <td><?php echo formatPrice($b['total_amount']); ?></td>
                    <td><span class="status-tag <?php echo strtolower($b['booking_status']); ?>"><?php echo h($b['booking_status']); ?></span></td>
                    <td class="action-links">
                        <?php if ($b['booking_status'] === 'Confirmed'): ?>
                            <a class="delete" href="bookings.php?cancel=<?php echo $b['booking_id']; ?>"
                               onclick="return confirm('Cancel this booking?');">Cancel</a>
                        <?php else: ?>
                            &mdash;
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
