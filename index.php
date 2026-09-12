<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Now Showing';
$active = 'home';
$base = '';

// Filter
$filter = $_GET['status'] ?? 'Now Showing';
$allowedFilters = ['Now Showing', 'Coming Soon', 'All'];
if (!in_array($filter, $allowedFilters)) $filter = 'Now Showing';

if ($filter === 'All') {
    $sql = "SELECT * FROM movies WHERE status != 'Archived' ORDER BY created_at DESC";
    $result = $conn->query($sql);
} else {
    $stmt = $conn->prepare("SELECT * FROM movies WHERE status = ? ORDER BY created_at DESC");
    $stmt->bind_param('s', $filter);
    $stmt->execute();
    $result = $stmt->get_result();
}

include __DIR__ . '/includes/user_header.php';
?>

<section class="hero">
    <h1>Book Your Movie Tickets Instantly</h1>
    <p>Browse the latest movies and reserve your seats in a few clicks.</p>
</section>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
        <h2 class="page-title" style="margin-bottom:0;">Movies</h2>
        <div style="display:flex; gap:10px;">
            <a href="?status=Now Showing" class="btn <?php echo $filter==='Now Showing' ? '' : 'secondary'; ?> small">Now Showing</a>
            <a href="?status=Coming Soon" class="btn <?php echo $filter==='Coming Soon' ? '' : 'secondary'; ?> small">Coming Soon</a>
            <a href="?status=All" class="btn <?php echo $filter==='All' ? '' : 'secondary'; ?> small">All</a>
        </div>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="movie-grid">
            <?php while ($movie = $result->fetch_assoc()): ?>
                <div class="movie-card">
                    <img class="poster" src="assets/uploads/<?php echo h($movie['poster']); ?>"
                         onerror="this.src='https://via.placeholder.com/300x400?text=<?php echo urlencode($movie['title']); ?>'"
                         alt="<?php echo h($movie['title']); ?>">
                    <div class="info">
                        <span class="badge <?php echo $movie['status'] === 'Now Showing' ? 'now' : 'soon'; ?>">
                            <?php echo h($movie['status']); ?>
                        </span>
                        <h3><?php echo h($movie['title']); ?></h3>
                        <p><?php echo h($movie['genre']); ?></p>
                        <p><?php echo h($movie['language']); ?> &middot; <?php echo intval($movie['duration_minutes']); ?> min</p>
                        <a href="movie_details.php?id=<?php echo $movie['movie_id']; ?>" class="btn full">View Details</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p style="color:#888;">No movies found in this category.</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/user_footer.php'; ?>
