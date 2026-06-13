<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OIMS - Online Invitation Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&family=Great+Vibes&family=Montserrat:wght@300;400;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<nav class="main-navbar">
    <div class="nav-container">
        <a class="nav-logo" href="../index.php"> OIMS ENGINE</a>
        <div class="nav-links">
            <?php if (session_status() == PHP_SESSION_NONE) { session_start(); } ?>
            <?php if (isset($_SESSION['admin_id'])): ?>
                <a class="btn btn-outline" href="../admin/dashboard.php">Dashboard</a>
                <a class="btn btn-danger" href="../admin/logout.php">Logout</a>
            <?php else: ?>
                <a class="btn btn-outline" href="../admin/register.php">Sign Up</a>
                <a class="btn btn-primary" href="../admin/login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<main class="main-content">