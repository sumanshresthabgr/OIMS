<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

$stmt = $pdo->prepare("SELECT i.*, COUNT(r.id) as total_rsvps FROM invitations i LEFT JOIN rsvps r ON i.id = r.invitation_id WHERE i.admin_id = :admin_id GROUP BY i.id ORDER BY i.created_at DESC");
$stmt->execute(['admin_id' => $_SESSION['admin_id']]);
$invitations = $stmt->fetchAll();

include '../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px;">
    <div>
        <h2 style="font-weight: 700;">Live Host Tracking Dashboard</h2>
        <p style="color: #64748b;">Welcome back, <?= htmlspecialchars($_SESSION['username']) ?></p>
    </div>
    <a href="create_event.php" class="btn btn-primary">+ Design New Invitation Model</a>
</div>

<?php if (empty($invitations)): ?>
    <div class="alert alert-info">
        <h4>No invitations registered on your profile matrix yet.</h4>
        <p style="margin-top: 5px; font-size: 0.9rem;">Deploy your first dynamic aesthetic template engine parameters right now!</p>
    </div>
<?php else: ?>
    <div class="card">
        <div class="table-responsive">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Event Target Title</th>
                        <th>Execution Window</th>
                        <th>Theme Layout</th>
                        <th>RSVP Tallies</th>
                        <th>Secure Link Routing</th>
                        <th>Action Arrays</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invitations as $row): 
                        // Automatically calculate directory sub-structures 
                        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                        $hostName = $_SERVER['HTTP_HOST'];
                        $currentDir = dirname($_SERVER['REQUEST_URI']); // Resolves /project_folder/admin
                        $rootDir = str_replace('/admin', '', $currentDir); // Strip admin to get root path
                        
                        $publicUrl = $protocol . "://" . $hostName . $rootDir . "/view.php?id=" . $row['share_token'];
                        $btnId = "copyBtn_" . $row['id'];
                    ?>
                    <tr>
                        <td style="font-weight: 600;"><?= htmlspecialchars($row['title']) ?></td>
                        <td><span style="font-size: 0.85rem; color: #64748b;"><?= htmlspecialchars($row['date_time']) ?></span></td>
                        <td><span class="badge badge-secondary"><?= htmlspecialchars($row['theme']) ?></span></td>
                        <td><span class="badge badge-info"><?= (int)$row['total_rsvps'] ?> Records</span></td>
                        <td>
                            <div class="input-copy-group">
                                <input type="text" class="form-control" style="padding: 6px; font-size:0.85rem;" value="<?= $publicUrl ?>" readonly>
                                <button class="btn btn-outline" style="padding: 6px 12px; font-size:0.85rem;" type="button" id="<?= $btnId ?>" onclick="copyToClipboard('<?= $publicUrl ?>', '<?= $btnId ?>')">Copy</button>
                            </div>
                        </td>
                        <td style="white-space: nowrap; display: flex; gap: 6px; justify-content: flex-start; align-items: center;">
                            <a href="view_rsvps.php?id=<?= $row['id'] ?>" class="btn btn-outline" style="padding: 6px 12px; font-size: 0.85rem;">Open Ledger</a>
                            
                            <a href="edit_event.php?id=<?= $row['id'] ?>" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.85rem; background-color: #3182ce; border-color: #3182ce;">Edit</a>
                            
                            <a href="delete_event.php?id=<?= $row['id'] ?>" class="btn btn-danger" style="padding: 6px 12px; font-size: 0.85rem; background-color: #e53e3e; border-color: #e53e3e; color: white;" onclick="return confirm('⚠️ Are you completely sure you want to drop this invitation layout sequence? All associated guest RSVPs will be immediately purged!');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>