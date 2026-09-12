<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();

$pageTitle = 'Dashboard';
$active = 'dashboard';

$totalMovies = $conn->query("SELECT COUNT(*) c FROM movies")->fetch_assoc()['c'];
$totalUsers = $conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()['c'];
$totalBookings = $conn->query("SELECT COUNT(*) c FROM bookings WHERE booking_status = 'Confirmed'")->fetch_assoc()['c'];
$totalRevenue = $conn->query("SELECT IFNULL(SUM(total_amount),0) r FROM bookings WHERE booking_status = 'Confirmed'")->fetch_assoc()['r'];
$totalShowtimes = $conn->query("SELECT COUNT(*) c FROM showtimes WHERE show_date >= CURDATE()")->fetch_assoc()['c'];

$recentBookings = $conn->query("
    SELECT b.booking_id, b.seats, b.total_amount, b.booking_status, b.booked_at,
           u.full_name, m.title
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN showtimes s ON b.showtime_id = s.showtime_id
    JOIN movies m ON s.movie_id = m.movie_id
    ORDER BY b.booked_at DESC LIMIT 8
");

include __DIR__ . '/includes/admin_header.php';
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="num"><?php echo $totalMovies; ?></div>
        <div class="label">Total Movies</div>
    </div>
    <div class="stat-card">
        <div class="num"><?php echo $totalShowtimes; ?></div>
        <div class="label">Upcoming Showtimes</div>
    </div>
    <div class="stat-card">
        <div class="num"><?php echo $totalBookings; ?></div>
        <div class="label">Confirmed Bookings</div>
    </div>
    <div class="stat-card">
        <div class="num"><?php echo $totalUsers; ?></div>
        <div class="label">Registered Users</div>
    </div>
    <div class="stat-card">
        <div class="num"><?php echo formatPrice($totalRevenue); ?></div>
        <div class="label">Total Revenue</div>
    </div>
</div>

<div class="card">
    <h2>Recent Bookings</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th><th>User</th><th>Movie</th><th>Seats</th><th>Amount</th><th>Status</th><th>Booked At</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($recentBookings->num_rows === 0): ?>
            <tr><td colspan="7" style="text-align:center; color:#888;">No bookings yet.</td></tr>
        <?php else: ?>
            <?php while ($row = $recentBookings->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo str_pad($row['booking_id'], 6, '0', STR_PAD_LEFT); ?></td>
                    <td><?php echo h($row['full_name']); ?></td>
                    <td><?php echo h($row['title']); ?></td>
                    <td><?php echo h($row['seats']); ?></td>
                    <td><?php echo formatPrice($row['total_amount']); ?></td>
                    <td><span class="status-tag <?php echo strtolower($row['booking_status']); ?>"><?php echo h($row['booking_status']); ?></span></td>
                    <td><?php echo date('d M Y, h:i A', strtotime($row['booked_at'])); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
