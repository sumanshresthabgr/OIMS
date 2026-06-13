<?php
require_once '../config/database.php';
session_start();

// If the user is already logged in, bypass the login page and send them to the dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        // Prepared statement query mapping isolation loop
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            // Verify and assign the dynamic host session array vectors
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = 'Invalid credentials. Please verify your username and password again.';
        }
    } else {
        $error = 'Please fill out all details.';
    }
}
include '../includes/header.php';
?>

<div class="grid-center">
    <div class="card auth-grid-width">
        <div class="card-header">
            <h3>Login to OIMS Engine</h3>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-weight:700;">Login</button>
            </form>
            
            <div style="margin-top: 20px; text-align: center; font-size: 0.9rem;">
                <span style="color:#64748b;">New event planner?</span> 
                <a href="register.php" style="color: #16a34a; font-weight:600; text-decoration:none;">Create New User Account</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>