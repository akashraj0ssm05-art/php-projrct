<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();

$pageTitle = 'Registered Users';
$active = 'users';

$users = $conn->query("
    SELECT u.*, 
        (SELECT COUNT(*) FROM bookings b WHERE b.user_id = u.user_id AND b.booking_status='Confirmed') AS booking_count
    FROM users u
    ORDER BY u.created_at DESC
");

include __DIR__ . '/includes/admin_header.php';
?>

<div class="card">
    <h2>Registered Users</h2>
    <table>
        <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Bookings</th><th>Joined</th></tr></thead>
        <tbody>
        <?php if ($users->num_rows === 0): ?>
            <tr><td colspan="5" style="text-align:center; color:#888;">No registered users yet.</td></tr>
        <?php else: ?>
            <?php while ($u = $users->fetch_assoc()): ?>
                <tr>
                    <td><?php echo h($u['full_name']); ?></td>
                    <td><?php echo h($u['email']); ?></td>
                    <td><?php echo h($u['phone']); ?></td>
                    <td><?php echo intval($u['booking_count']); ?></td>
                    <td><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
