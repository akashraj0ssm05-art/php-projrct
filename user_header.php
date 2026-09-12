<?php if (!isset($conn)) { require_once __DIR__ . '/../config/db.php'; require_once __DIR__ . '/functions.php'; } ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? h($pageTitle) . ' - CineBook' : 'CineBook - Movie Tickets'; ?></title>
<link rel="stylesheet" href="<?php echo isset($base) ? $base : ''; ?>css/style.css">
</head>
<body>
<header class="navbar">
    <a href="<?php echo isset($base) ? $base : ''; ?>index.php" class="logo">Cine<span>Book</span></a>
    <nav>
        <ul>
            <li><a href="<?php echo isset($base) ? $base : ''; ?>index.php" class="<?php echo ($active ?? '') === 'home' ? 'active' : ''; ?>">Movies</a></li>
            <?php if (isUserLoggedIn()): ?>
                <li><a href="<?php echo isset($base) ? $base : ''; ?>my_bookings.php" class="<?php echo ($active ?? '') === 'bookings' ? 'active' : ''; ?>">My Bookings</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <div>
        <?php if (isUserLoggedIn()): ?>
            <span style="margin-right:16px; color:#ccc; font-size:14px;">Hi, <?php echo h($_SESSION['user_name']); ?></span>
            <a href="<?php echo isset($base) ? $base : ''; ?>logout.php" class="nav-btn">Logout</a>
        <?php else: ?>
            <a href="<?php echo isset($base) ? $base : ''; ?>login.php" class="nav-btn">Login</a>
        <?php endif; ?>
    </div>
</header>
