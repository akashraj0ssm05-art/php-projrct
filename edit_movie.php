<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdminLogin();

$pageTitle = 'Edit Movie';
$active = 'movies';
$error = '';

$id = intval($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM movies WHERE movie_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();

if (!$movie) {
    header('Location: movies.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $language = trim($_POST['language'] ?? '');
    $duration = intval($_POST['duration_minutes'] ?? 0);
    $release_date = $_POST['release_date'] ?? null;
    $status = $_POST['status'] ?? 'Now Showing';
    $poster = $movie['poster'];

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
        $stmt = $conn->prepare("UPDATE movies SET title=?, description=?, genre=?, language=?, duration_minutes=?, release_date=?, poster=?, status=? WHERE movie_id=?");
        $stmt->bind_param('ssssisssi', $title, $description, $genre, $language, $duration, $release_date, $poster, $status, $id);
        if ($stmt->execute()) {
            header('Location: movies.php?saved=1');
            exit;
        } else {
            $error = 'Failed to update movie.';
        }
    }
}

include __DIR__ . '/includes/admin_header.php';
?>

<div class="card">
    <h2>Edit Movie</h2>
    <?php if ($error): ?><div class="alert error"><?php echo h($error); ?></div><?php endif; ?>
    <form method="POST" action="edit_movie.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" value="<?php echo h($movie['title']); ?>" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="4"><?php echo h($movie['description']); ?></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Genre</label>
                <input type="text" name="genre" value="<?php echo h($movie['genre']); ?>">
            </div>
            <div class="form-group">
                <label>Language</label>
                <input type="text" name="language" value="<?php echo h($movie['language']); ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Duration (minutes)</label>
                <input type="number" name="duration_minutes" value="<?php echo intval($movie['duration_minutes']); ?>" min="1">
            </div>
            <div class="form-group">
                <label>Release Date</label>
                <input type="date" name="release_date" value="<?php echo h($movie['release_date']); ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <?php foreach (['Now Showing', 'Coming Soon', 'Archived'] as $s): ?>
                        <option value="<?php echo $s; ?>" <?php echo $movie['status'] === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Poster Image</label>
                <input type="file" name="poster" accept="image/*">
                <small style="color:#888;">Current: <?php echo h($movie['poster']); ?></small>
            </div>
        </div>
        <button type="submit" class="btn">Update Movie</button>
        <a href="movies.php" class="btn secondary">Cancel</a>
    </form>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
