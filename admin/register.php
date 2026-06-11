<?php
require_once '../config/database.php';
session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (!empty($username) && !empty($password) && !empty($confirm_password)) {
        if ($password !== $confirm_password) {
            $error = 'Security encryption keys do not match.';
        } elseif (strlen($password) < 6) {
            $error = 'Access key must be at least 6 characters long.';
        } else {
            // Check if user handle already exists inside the database index matrix
            $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = :username");
            $stmt->execute(['username' => $username]);
            
            if ($stmt->fetch()) {
                $error = 'Username is already registered. Please select another.';
            } else {
                // Securely hash the password vector string using native algorithms
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                
                $insert = $pdo->prepare("INSERT INTO admins (username, password) VALUES (:username, :password)");
                $insert->execute([
                    'username' => $username,
                    'password' => $hashed_password
                ]);
                
                $success = 'Host account initialized successfully! Proceeding to access gate.';
                // Automated route execution context redirection
                header("Refresh: 2; URL=login.php");
            }
        }
    } else {
        $error = 'Please fill out all identification parameters.';
    }
}
include '../includes/header.php';
?>

<div class="grid-center">
    <div class="card auth-grid-width">
        <div class="card-header">
            <h3>Create New Account</h3>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="e.g., organizer2026" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label class="form-label">password</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm password</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Repeat password" required>
                </div>
                <button type="submit" class="btn btn-success" style="width: 100%; padding: 12px; font-weight:700;">signup</button>
            </form>
            
            <div style="margin-top: 20px; text-align: center; font-size: 0.9rem;">
                <span style="color:#64748b;">Already have a configured profile?</span> 
                <a href="login.php" style="color: #2563eb; font-weight:600; text-decoration:none;">Log In Here</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>