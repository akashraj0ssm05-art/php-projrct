<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = $conn->prepare("SELECT admin_id, username, password, full_name FROM admins WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_name'] = $admin['full_name'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - CineBook</title>
<link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<div class="admin-login-wrap">
    <div class="admin-login-box">
        <h2>Admin Login</h2>
        <p class="sub">CineBook Management Panel</p>
        <?php if ($error): ?><div class="alert error"><?php echo h($error); ?></div><?php endif; ?>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn full">Login</button>
        </form>
        <p style="text-align:center; margin-top:16px; font-size:13px; color:#888;">
            Default: admin / admin123
        </p>
        <p style="text-align:center; margin-top:10px; font-size:13px;">
            <a href="../index.php" style="color:#e50914;">&larr; Back to Site</a>
        </p>
    </div>
</div>
</body>
</html>
