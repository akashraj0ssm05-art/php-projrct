<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();

$pageTitle = 'Manage Movies';
$active = 'movies';

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM movies WHERE movie_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    header('Location: movies.php?deleted=1');
    exit;
}

$movies = $conn->query("SELECT * FROM movies ORDER BY created_at DESC");

include __DIR__ . '/includes/admin_header.php';
?>

<?php if (isset($_GET['deleted'])): ?>
    <div class="alert success">Movie deleted successfully.</div>
<?php endif; ?>
<?php if (isset($_GET['saved'])): ?>
    <div class="alert success">Movie saved successfully.</div>
<?php endif; ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h2 style="margin:0;">All Movies</h2>
        <a href="add_movie.php" class="btn">+ Add New Movie</a>
    </div>
    <table>
        <thead>
            <tr><th>Title</th><th>Genre</th><th>Language</th><th>Duration</th><th>Release Date</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php if ($movies->num_rows === 0): ?>
            <tr><td colspan="7" style="text-align:center; color:#888;">No movies added yet.</td></tr>
        <?php else: ?>
            <?php while ($m = $movies->fetch_assoc()): ?>
                <tr>
                    <td><?php echo h($m['title']); ?></td>
                    <td><?php echo h($m['genre']); ?></td>
                    <td><?php echo h($m['language']); ?></td>
                    <td><?php echo intval($m['duration_minutes']); ?> min</td>
                    <td><?php echo formatDate($m['release_date']); ?></td>
                    <td>
                        <span class="status-tag <?php
                            echo $m['status'] === 'Now Showing' ? 'now' : ($m['status'] === 'Coming Soon' ? 'soon' : 'archived');
                        ?>"><?php echo h($m['status']); ?></span>
                    </td>
                    <td class="action-links">
                        <a class="edit" href="edit_movie.php?id=<?php echo $m['movie_id']; ?>">Edit</a>
                        <a class="delete" href="movies.php?delete=<?php echo $m['movie_id']; ?>"
                           onclick="return confirm('Delete this movie? This will also remove its showtimes and bookings.');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
