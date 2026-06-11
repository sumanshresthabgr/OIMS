<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

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

    if (!empty($title) && !empty($date_time) && !empty($venue) && !empty($theme)) {
        $share_token = bin2hex(random_bytes(4)); 

        $chk = $pdo->prepare("SELECT id FROM invitations WHERE share_token = ?");
        $chk->execute([$share_token]);
        while($chk->fetch()) {
            $share_token = bin2hex(random_bytes(4));
            $chk->execute([$share_token]);
        }

        $stmt = $pdo->prepare("INSERT INTO invitations (admin_id, title, date_time, venue, description, dress_code, registry, special_notes, theme, share_token) VALUES (:admin_id, :title, :date_time, :venue, :description, :dress_code, :registry, :special_notes, :theme, :share_token)");
        
        $stmt->execute([
            'admin_id'      => $_SESSION['admin_id'],
            'title'         => $title,
            'date_time'     => $date_time,
            'venue'         => $venue,
            'description'   => !empty($description) ? $description : null,
            'dress_code'    => !empty($dress_code) ? $dress_code : null,
            'registry'      => !empty($registry) ? $registry : null,
            'special_notes' => !empty($special_notes) ? $special_notes : null,
            'theme'         => $theme,
            'share_token'   => $share_token
        ]);

        header("Location: dashboard.php");
        exit();
    } else {
        die("Data Processing Exception: Missing payload tracking signatures.");
    }
}
?>