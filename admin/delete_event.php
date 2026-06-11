<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

if (isset($_GET['id'])) {
    $event_id = intval($_GET['id']);
    $admin_id = $_SESSION['admin_id'];

    // Verify ownership structure space before issuing delete array query sequence
    $check = $pdo->prepare("SELECT id FROM invitations WHERE id = :id AND admin_id = :admin_id");
    $check->execute(['id' => $event_id, 'admin_id' => $admin_id]);
    
    if ($check->fetch()) {
        $pdo->beginTransaction();
        try {
            // Cascade manually: Clean up dependent guest RSVP rows first to prevent foreign key errors
            $del_rsvps = $pdo->prepare("DELETE FROM rsvps WHERE invitation_id = :invitation_id");
            $del_rsvps->execute(['invitation_id' => $event_id]);

            // Delete core event entity 
            $del_event = $pdo->prepare("DELETE FROM invitations WHERE id = :id AND admin_id = :admin_id");
            $del_event->execute(['id' => $event_id, 'admin_id' => $admin_id]);

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Database Deletion Pipeline Exception: Transaction failure execution abort.");
        }
    }
}

// Seamless redirect straight back to host panel control room index ledger space
header("Location: dashboard.php");
exit();
?>