<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

// Quick check: Stop execution if session is missing
if (empty($_SESSION['admin_id'])) {
    die("Access Denied: Unauthorized administrative session.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title         = trim($_POST['title']);
    $date_time     = trim($_POST['date_time']);
    $venue         = trim($_POST['venue']);
    $description   = trim($_POST['description']);
    $theme         = trim($_POST['theme']);
    
    // Extracted Parameter Sets
    $dress_code    = trim($_POST['dress_code']);
    $registry      = trim($_POST['registry']);
    $special_notes = trim($_POST['special_notes']);

    // Ensure all required fields are filled out
    if (!empty($title) && !empty($date_time) && !empty($venue) && !empty($theme)) {
        
        // Robust token generator loop to prevent collisions safely
        $chk = $pdo->prepare("SELECT COUNT(*) FROM invitations WHERE share_token = ?");
        do {
            $share_token = bin2hex(random_bytes(4)); // Generates a unique 8-character token
            $chk->execute([$share_token]);
            $token_exists = $chk->fetchColumn();
        } while ($token_exists > 0);

        // Prepare insertion into database
        $stmt = $pdo->prepare("INSERT INTO invitations (admin_id, title, date_time, venue, description, dress_code, registry, special_notes, theme, share_token) VALUES (:admin_id, :title, :date_time, :venue, :description, :dress_code, :registry, :special_notes, :theme, :share_token)");
        
        // Fixed: Array keys now correctly map to the named placeholders with leading colons
        $stmt->execute([
            ':admin_id'      => $_SESSION['admin_id'],
            ':title'         => $title,
            ':date_time'     => $date_time,
            ':venue'         => $venue,
            ':description'   => !empty($description) ? $description : null,
            ':dress_code'    => !empty($dress_code) ? $dress_code : null,
            ':registry'      => !empty($registry) ? $registry : null,
            ':special_notes' => !empty($special_notes) ? $special_notes : null,
            ':theme'         => $theme,
            ':share_token'   => $share_token
        ]);

        header("Location: dashboard.php");
        exit();
    } else {
        die("Data Processing Exception: Missing required payload properties.");
    }
}
?>