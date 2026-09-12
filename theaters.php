<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();

$pageTitle = 'Manage Theaters';
$active = 'theaters';
$error = '';

// Add theater
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $total_seats = intval($_POST['total_seats'] ?? 0);

    if (!$name || $total_seats < 1) {
        $error = 'Please provide a valid name and seat count.';
    } else {
        $stmt = $conn->prepare("INSERT INTO theaters (name, location, total_seats) VALUES (?, ?, ?)");
        $stmt->bind_param('ssi', $name, $location, $total_seats);
        $stmt->execute();
        header('Location: theaters.php?saved=1');
        exit;
    }
}

// Delete theater
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM theaters WHERE theater_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    header('Location: theaters.php?deleted=1');
    exit;
}

$theaters = $conn->query("SELECT * FROM theaters ORDER BY theater_id DESC");

include __DIR__ . '/includes/admin_header.php';
?>

<?php if (isset($_GET['saved'])): ?><div class="alert success">Theater added successfully.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="alert success">Theater deleted successfully.</div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?php echo h($error); ?></div><?php endif; ?>

<div class="card">
    <h2>Add New Theater / Screen</h2>
    <form method="POST" action="theaters.php">
        <div class="form-row">
            <div class="form-group">
                <label>Screen Name *</label>
                <input type="text" name="name" placeholder="e.g. Screen 1" required>
            </div>
            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" placeholder="e.g. Downtown Multiplex">
            </div>
        </div>
        <div class="form-group" style="max-width:200px;">
            <label>Total Seats *</label>
            <input type="number" name="total_seats" min="1" value="50" required>
        </div>
        <button type="submit" class="btn">Add Theater</button>
    </form>
</div>

<div class="card">
    <h2>All Theaters</h2>
    <table>
        <thead><tr><th>Name</th><th>Location</th><th>Total Seats</th><th>Actions</th></tr></thead>
        <tbody>
        <?php if ($theaters->num_rows === 0): ?>
            <tr><td colspan="4" style="text-align:center; color:#888;">No theaters added yet.</td></tr>
        <?php else: ?>
            <?php while ($t = $theaters->fetch_assoc()): ?>
                <tr>
                    <td><?php echo h($t['name']); ?></td>
                    <td><?php echo h($t['location']); ?></td>
                    <td><?php echo intval($t['total_seats']); ?></td>
                    <td class="action-links">
                        <a class="delete" href="theaters.php?delete=<?php echo $t['theater_id']; ?>"
                           onclick="return confirm('Delete this theater? Related showtimes will also be removed.');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
