<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

if (isUserLoggedIn()) redirect('index.php');

$pageTitle = 'Login';
$base = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $conn->prepare("SELECT user_id, full_name, password FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['full_name'];
                redirect('index.php');
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

include __DIR__ . '/includes/user_header.php';
?>
<div class="container">
    <div class="form-card">
        <h2>Welcome Back</h2>
        <?php if ($error): ?><div class="alert error"><?php echo h($error); ?></div><?php endif; ?>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo h($_POST['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn full">Login</button>
        </form>
        <p class="form-footer">Don't have an account? <a href="register.php">Register here</a></p>
        <p class="form-footer"><a href="admin/login.php" style="color:#888;">Admin Login &rarr;</a></p>
    </div>
</div>
<?php include __DIR__ . '/includes/user_footer.php'; ?>
