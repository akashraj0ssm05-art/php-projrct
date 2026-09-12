<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$base = '';
$movie_id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM movies WHERE movie_id = ?");
$stmt->bind_param('i', $movie_id);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();

if (!$movie) {
    redirect('index.php');
}

$pageTitle = $movie['title'];

// Fetch upcoming showtimes (today onwards), grouped by date
$stmt = $conn->prepare("
    SELECT s.showtime_id, s.show_date, s.show_time, s.price, t.name AS theater_name, t.location
    FROM showtimes s
    JOIN theaters t ON s.theater_id = t.theater_id
    WHERE s.movie_id = ? AND s.show_date >= CURDATE()
    ORDER BY s.show_date ASC, s.show_time ASC
");
$stmt->bind_param('i', $movie_id);
$stmt->execute();
$showtimes = $stmt->get_result();

$grouped = [];
while ($row = $showtimes->fetch_assoc()) {
    $grouped[$row['show_date']][] = $row;
}

include __DIR__ . '/includes/user_header.php';
?>
<div class="container">
    <div class="movie-detail">
        <img src="assets/uploads/<?php echo h($movie['poster']); ?>"
             onerror="this.src='https://via.placeholder.com/320x440?text=<?php echo urlencode($movie['title']); ?>'"
             alt="<?php echo h($movie['title']); ?>">
        <div>
            <span class="badge <?php echo $movie['status'] === 'Now Showing' ? 'now' : 'soon'; ?>">
                <?php echo h($movie['status']); ?>
            </span>
            <h1><?php echo h($movie['title']); ?></h1>
            <p class="meta">
                <?php echo h($movie['genre']); ?> &middot;
                <?php echo h($movie['language']); ?> &middot;
                <?php echo intval($movie['duration_minutes']); ?> min &middot;
                Released <?php echo formatDate($movie['release_date']); ?>
            </p>
            <p class="desc"><?php echo nl2br(h($movie['description'])); ?></p>

            <h3 style="margin-bottom:10px;">Available Showtimes</h3>
            <?php if (count($grouped) === 0): ?>
                <p style="color:#888;">No showtimes scheduled currently. Please check back later.</p>
            <?php else: ?>
                <?php foreach ($grouped as $date => $shows): ?>
                    <p style="font-weight:600; margin-top:14px; color:#333;"><?php echo formatDate($date); ?></p>
                    <div class="showtime-list">
                        <?php foreach ($shows as $show): ?>
                            <a class="showtime-chip" href="book.php?showtime_id=<?php echo $show['showtime_id']; ?>">
                                <?php echo formatTime($show['show_time']); ?><br>
                                <small><?php echo h($show['theater_name']); ?> &middot; <?php echo formatPrice($show['price']); ?></small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/user_footer.php'; ?>
