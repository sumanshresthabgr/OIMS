<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$invitation_id = (int)$_GET['id'];

$stmt_event = $pdo->prepare("SELECT * FROM invitations WHERE id = :id AND admin_id = :admin_id");
$stmt_event->execute(['id' => $invitation_id, 'admin_id' => $_SESSION['admin_id']]);
$event = $stmt_event->fetch();

if (!$event) {
    die("Access Interception Wall triggered: Unauthorized access array.");
}

$stmt_rsvps = $pdo->prepare("SELECT * FROM rsvps WHERE invitation_id = :invitation_id ORDER BY submitted_at DESC");
$stmt_rsvps->execute(['invitation_id' => $invitation_id]);
$rsvps = $stmt_rsvps->fetchAll();

// Calculate operational transaction metrics for the ledger header
$count_attending = 0;
$count_declined = 0;

foreach ($rsvps as $rsvp) {
    if ($rsvp['status'] === 'Attending') {
        $count_attending++;
    } else {
        $count_declined++;
    }
}

include '../includes/header.php';
?>

<div style="margin-bottom: 25px;">
    <a href="dashboard.php" class="btn btn-outline" style="padding: 6px 12px; margin-bottom: 15px;">&larr; Back to Console</a>
    <h2>Interactive Guest Ledger: <span style="color: #2563eb;"><?= htmlspecialchars($event['title']) ?></span></h2>
    
    <div style="display: flex; gap: 15px; margin-top: 15px; font-family: 'Montserrat', sans-serif;">
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 8px 16px; border-radius: 6px; font-size: 0.9rem; font-weight: 600;">
            Attending: <span style="font-weight: 700; font-size: 1rem;"><?= $count_attending ?></span>
        </div>
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 8px 16px; border-radius: 6px; font-size: 0.9rem; font-weight: 600;">
            Declined: <span style="font-weight: 700; font-size: 1rem;"><?= $count_declined ?></span>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>S.N.</th>
                    <th>Guest Nominal Trace</th>
                    <th>Dual-State Feedback</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rsvps)): ?>
                    <tr><td colspan="4" style="text-align: center; color: #64748b;">No transaction entries recorded.</td></tr>
                <?php else: $sn = 1; foreach ($rsvps as $rsvp): ?>
                <tr>
                    <td style="font-weight: 700;"><?= $sn++ ?></td>
                    <td><?= htmlspecialchars($rsvp['guest_name']) ?></td>
                    <td>
                        <?php if ($rsvp['status'] === 'Attending'): ?>
                            <span class="badge badge-success">Attending ✓</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Declined ✗</span>
                        <?php endif; ?>
                    </td>
                    <td><span style="font-size:0.85rem; color:#64748b;"><?= htmlspecialchars($rsvp['submitted_at']) ?></span></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>