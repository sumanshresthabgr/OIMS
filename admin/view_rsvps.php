<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$invitation_id = (int)$_GET['id'];

// Restructured event query to pull smoothly by Invitation ID parameter
$stmt_event = $pdo->prepare("SELECT * FROM invitations WHERE id = :id");
$stmt_event->execute(['id' => $invitation_id]);
$event = $stmt_event->fetch();

if (!$event) {
    die("Access Interception Wall triggered: Unauthorized access array.");
}

// Get the current filename dynamically so it never throws a 404 on submission
$current_page = basename(__FILE__);

// Secure Deletion processing router logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_rsvp_id'])) {
    $delete_rsvp_id = (int)$_POST['delete_rsvp_id'];
    
    // Verify the target entry strictly belongs to this valid controlled invitation reference link
    $stmt_del = $pdo->prepare("DELETE FROM rsvps WHERE id = :id AND invitation_id = :invitation_id");
    $stmt_del->execute([
        'id' => $delete_rsvp_id,
        'invitation_id' => $invitation_id
    ]);
    
    header("Location: " . $current_page . "?id=" . $invitation_id);
    exit();
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

<style>
    /* CSS Grid Fluid Layout Adjustments for Responsive Display */
    .ledger-action-btn {
        background: #ef4444;
        color: #ffffff;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.8rem;
        cursor: pointer;
        font-weight: 600;
        transition: background 0.2s ease;
    }
    .ledger-action-btn:hover {
        background: #dc2626;
    }
    
    .ledger-flex-container {
        display: flex; 
        gap: 15px; 
        margin-top: 15px; 
        font-family: 'Montserrat', sans-serif;
    }

    @media (max-width: 600px) {
        .ledger-flex-container {
            flex-direction: column;
            gap: 10px;
        }
        .dashboard-table thead {
            display: none; /* Hide standard head blocks on small viewports */
        }
        .dashboard-table, .dashboard-table tbody, .dashboard-table tr, .dashboard-table td {
            display: block;
            width: 100%;
        }
        .dashboard-table tr {
            margin-bottom: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px;
            background: #ffffff;
            box-sizing: border-box;
        }
        .dashboard-table td {
            text-align: right !important;
            padding-left: 50% !important;
            position: relative;
            border: none !important;
            box-sizing: border-box;
            min-height: 35px;
        }
        .dashboard-table td::before {
            content: attr(data-label);
            position: absolute;
            left: 10px;
            width: 45%;
            font-weight: 700;
            text-align: left;
            color: #475569;
        }
    }
</style>

<div style="margin-bottom: 25px;">
    <a href="dashboard.php" class="btn btn-outline" style="padding: 6px 12px; margin-bottom: 15px; display: inline-block; text-decoration: none;">&larr; Back to Console</a>
    <h2 style="word-wrap: break-word; overflow-wrap: break-word;">Interactive Guest Ledger: <span style="color: #2563eb;"><?= htmlspecialchars($event['title']) ?></span></h2>
    
    <div class="ledger-flex-container">
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 8px 16px; border-radius: 6px; font-size: 0.9rem; font-weight: 600; text-align: center;">
            Attending: <span style="font-weight: 700; font-size: 1rem;"><?= $count_attending ?></span>
        </div>
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 8px 16px; border-radius: 6px; font-size: 0.9rem; font-weight: 600; text-align: center;">
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
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rsvps)): ?>
                    <tr><td colspan="5" style="text-align: center; color: #64748b;">No transaction entries recorded.</td></tr>
                <?php else: $sn = 1; foreach ($rsvps as $rsvp): ?>
                <tr>
                    <td data-label="S.N." style="font-weight: 700;"><?= $sn++ ?></td>
                    <td data-label="Guest Nominal Trace" style="word-break: break-all;"><?= htmlspecialchars($rsvp['guest_name']) ?></td>
                    <td data-label="Dual-State Feedback">
                        <?php if ($rsvp['status'] === 'Attending'): ?>
                            <span class="badge badge-success">Attending ✓</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Declined ✗</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Timestamp"><span style="font-size:0.85rem; color:#64748b;"><?= htmlspecialchars($rsvp['submitted_at']) ?></span></td>
                    <td data-label="Actions" style="text-align: center;">
                        <form action="<?= htmlspecialchars($current_page) ?>?id=<?= $invitation_id ?>" method="POST" onsubmit="return confirm('Are you sure you want to completely drop this guest transaction record?');" style="display: inline-block; margin: 0;">
                            <input type="hidden" name="delete_rsvp_id" value="<?= $rsvp['id'] ?>">
                            <button type="submit" class="ledger-action-btn">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>