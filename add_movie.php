<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();

$pageTitle = 'Add Movie';
$active = 'movies';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $language = trim($_POST['language'] ?? '');
    $duration = intval($_POST['duration_minutes'] ?? 0);
    $release_date = $_POST['release_date'] ?? null;
    $status = $_POST['status'] ?? 'Now Showing';
    $poster = 'default.jpg';

    // Handle poster upload
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $allowed)) {
            $poster = 'movie_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['poster']['tmp_name'], __DIR__ . '/../assets/uploads/' . $poster);
        }
    }

    if (!$title) {
        $error = 'Title is required.';
    } else {
        $stmt = $conn->prepare("INSERT INTO movies (title, description, genre, language, duration_minutes, release_date, poster, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssisss', $title, $description, $genre, $language, $duration, $release_date, $poster, $status);
        if ($stmt->execute()) {
            header('Location: movies.php?saved=1');
            exit;
        } else {
            $error = 'Failed to save movie.';
        }
    }
}

include __DIR__ . '/includes/admin_header.php';
?>

<div class="card">
    <h2>Add New Movie</h2>
    <?php if ($error): ?><div class="alert error"><?php echo h($error); ?></div><?php endif; ?>
    <form method="POST" action="add_movie.php" enctype="multipart/form-data">
        <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="4"></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Genre</label>
                <input type="text" name="genre" placeholder="e.g. Action, Drama">
            </div>
            <div class="form-group">
                <label>Language</label>
                <input type="text" name="language" placeholder="e.g. English">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Duration (minutes)</label>
                <input type="number" name="duration_minutes" min="1">
            </div>
            <div class="form-group">
                <label>Release Date</label>
                <input type="date" name="release_date">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="Now Showing">Now Showing</option>
                    <option value="Coming Soon">Coming Soon</option>
                    <option value="Archived">Archived</option>
                </select>
            </div>
            <div class="form-group">
                <label>Poster Image</label>
                <input type="file" name="poster" accept="image/*">
            </div>
        </div>
        <button type="submit" class="btn">Save Movie</button>
        <a href="movies.php" class="btn secondary">Cancel</a>
    </form>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
