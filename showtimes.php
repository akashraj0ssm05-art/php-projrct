<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();

$pageTitle = 'Manage Showtimes';
$active = 'showtimes';
$error = '';

// Add showtime
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_id = intval($_POST['movie_id'] ?? 0);
    $theater_id = intval($_POST['theater_id'] ?? 0);
    $show_date = $_POST['show_date'] ?? '';
    $show_time = $_POST['show_time'] ?? '';
    $price = floatval($_POST['price'] ?? 0);

    if (!$movie_id || !$theater_id || !$show_date || !$show_time || $price <= 0) {
        $error = 'Please fill in all fields correctly.';
    } else {
        $stmt = $conn->prepare("INSERT INTO showtimes (movie_id, theater_id, show_date, show_time, price) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('iissd', $movie_id, $theater_id, $show_date, $show_time, $price);
        $stmt->execute();
        header('Location: showtimes.php?saved=1');
        exit;
    }
}

// Delete showtime
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM showtimes WHERE showtime_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    header('Location: showtimes.php?deleted=1');
    exit;
}

$movies = $conn->query("SELECT movie_id, title FROM movies WHERE status != 'Archived' ORDER BY title");
$theaters = $conn->query("SELECT theater_id, name FROM theaters ORDER BY name");

$showtimes = $conn->query("
    SELECT s.*, m.title, t.name AS theater_name
    FROM showtimes s
    JOIN movies m ON s.movie_id = m.movie_id
    JOIN theaters t ON s.theater_id = t.theater_id
    ORDER BY s.show_date DESC, s.show_time DESC
");

include __DIR__ . '/includes/admin_header.php';
?>

<?php if (isset($_GET['saved'])): ?><div class="alert success">Showtime added successfully.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="alert success">Showtime deleted successfully.</div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?php echo h($error); ?></div><?php endif; ?>

<div class="card">
    <h2>Add New Showtime</h2>
    <form method="POST" action="showtimes.php">
        <div class="form-row">
            <div class="form-group">
                <label>Movie *</label>
                <select name="movie_id" required>
                    <option value="">-- Select Movie --</option>
                    <?php while ($m = $movies->fetch_assoc()): ?>
                        <option value="<?php echo $m['movie_id']; ?>"><?php echo h($m['title']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Theater *</label>
                <select name="theater_id" required>
                    <option value="">-- Select Theater --</option>
                    <?php while ($t = $theaters->fetch_assoc()): ?>
                        <option value="<?php echo $t['theater_id']; ?>"><?php echo h($t['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Show Date *</label>
                <input type="date" name="show_date" required>
            </div>
            <div class="form-group">
                <label>Show Time *</label>
                <input type="time" name="show_time" required>
            </div>
        </div>
        <div class="form-group" style="max-width:200px;">
            <label>Ticket Price (₹) *</label>
            <input type="number" name="price" min="1" step="0.01" value="150" required>
        </div>
        <button type="submit" class="btn">Add Showtime</button>
    </form>
</div>

<div class="card">
    <h2>All Showtimes</h2>
    <table>
        <thead><tr><th>Movie</th><th>Theater</th><th>Date</th><th>Time</th><th>Price</th><th>Actions</th></tr></thead>
        <tbody>
        <?php if ($showtimes->num_rows === 0): ?>
            <tr><td colspan="6" style="text-align:center; color:#888;">No showtimes added yet.</td></tr>
        <?php else: ?>
            <?php while ($s = $showtimes->fetch_assoc()): ?>
                <tr>
                    <td><?php echo h($s['title']); ?></td>
                    <td><?php echo h($s['theater_name']); ?></td>
                    <td><?php echo formatDate($s['show_date']); ?></td>
                    <td><?php echo formatTime($s['show_time']); ?></td>
                    <td><?php echo formatPrice($s['price']); ?></td>
                    <td class="action-links">
                        <a class="delete" href="showtimes.php?delete=<?php echo $s['showtime_id']; ?>"
                           onclick="return confirm('Delete this showtime? Related bookings will also be removed.');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
