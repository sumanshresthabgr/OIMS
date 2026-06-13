<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$event_id = intval($_GET['id']);
$admin_id = $_SESSION['admin_id'];

// Fetch invitation row target parameters tracking owner signature maps
$stmt = $pdo->prepare("SELECT * FROM invitations WHERE id = :id AND admin_id = :admin_id");
$stmt->execute(['id' => $event_id, 'admin_id' => $admin_id]);
$event = $stmt->fetch();

if (!$event) {
    die("Access Forbidden Sequence: Data entity record match not verified or invalid ownership token layout.");
}

$feedbackMessage = '';
$feedbackClass = '';

// Process payload submission tracking mutations sequence updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title         = trim($_POST['title']);
    $date_time     = trim($_POST['date_time']);
    $venue         = trim($_POST['venue']);
    $description   = trim($_POST['description']);
    $theme         = trim($_POST['theme']);
    $dress_code    = trim($_POST['dress_code']);
    $registry      = trim($_POST['registry']);
    $special_notes = trim($_POST['special_notes']);

    if (!empty($title) && !empty($date_time) && !empty($venue) && !empty($theme)) {
        $update = $pdo->prepare("UPDATE invitations SET title = :title, date_time = :date_time, venue = :venue, description = :description, theme = :theme, dress_code = :dress_code, registry = :registry, special_notes = :special_notes WHERE id = :id AND admin_id = :admin_id");
        
        $update->execute([
            'title'         => $title,
            'date_time'     => $date_time,
            'venue'         => $venue,
            'description'   => !empty($description) ? $description : null,
            'theme'         => $theme,
            'dress_code'    => !empty($dress_code) ? $dress_code : null,
            'registry'      => !empty($registry) ? $registry : null,
            'special_notes' => !empty($special_notes) ? $special_notes : null,
            'id'            => $event_id,
            'admin_id'      => $admin_id
        ]);

        header("Location: dashboard.php");
        exit();
    } else {
        $feedbackMessage = "Update Aborted: Mandatory semantic tracking values cannot be committed blank.";
        $feedbackClass = "alert-danger";
    }
}

include '../includes/header.php';
?>

<div class="card form-grid-width" style="margin-top: 30px;">
    <div class="card-header">
        <h3>Modify Invitation Structural Configurations</h3>
    </div>
    <div class="card-body">
        
        <?php if (!empty($feedbackMessage)): ?>
            <div class="alert <?= $feedbackClass ?>"><?= $feedbackMessage ?></div>
        <?php endif; ?>

        <form action="edit_event.php?id=<?= $event_id ?>" method="POST" id="editInvitationForm">
            <div class="form-group">
                <label class="form-label"> Event Title</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($event['title']) ?>" required>
            </div>
            
            <div class="grid-2col">
                <div class="form-group">
                    <label class="form-label">Time</label>
                    <input type="datetime-local" name="date_time" id="event_date_time" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($event['date_time'])) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Design Theme</label>
                    <select name="theme" class="form-control" required>
                        <option value="wedding" <?= $event['theme'] === 'wedding' ? 'selected' : '' ?>>Romantic Floral Style (Weddings)</option>
                        <option value="birthday" <?= $event['theme'] === 'birthday' ? 'selected' : '' ?>>Vibrant Festive Style (Birthdays)</option>
                        <option value="corporate" <?= $event['theme'] === 'corporate' ? 'selected' : '' ?>>Modern Minimalist Style (Corporate)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label"> Venue Instructions</label>
                <input type="text" name="venue" class="form-control" value="<?= htmlspecialchars($event['venue']) ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Primary Event Description</label>
                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($event['description'] ?? '') ?></textarea>
            </div>

            <h4 style="margin: 35px 0 15px 0; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color:#718096; border-bottom: 1px solid #edf2f7; padding-bottom: 8px;">
              Extended Aesthetic Specifications (Optional Fields)
            </h4>

            <div class="grid-2col">
                <div class="form-group">
                    <label class="form-label"> Dress Code  Guidelines</label>
                    <input type="text" name="dress_code" class="form-control" value="<?= htmlspecialchars($event['dress_code'] ?? '') ?>" placeholder="e.g., Black Tie Optional">
                </div>
                <div class="form-group">
                    <label class="form-label"> Gift Registry Link Network</label>
                    <input type="url" name="registry" class="form-control" value="<?= htmlspecialchars($event['registry'] ?? '') ?>" placeholder="https://example.com/registry">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label"> Attendence Check </label>
                <textarea name="special_notes" class="form-control" rows="3" placeholder="Additional special assembly notes rows..."><?= htmlspecialchars($event['special_notes'] ?? '') ?></textarea>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-top: 40px; border-top: 1px solid #edf2f7; padding-top: 25px;">
                <a href="dashboard.php" class="btn btn-outline">Discard Adjustments</a>
                <button type="submit" class="btn btn-success" style="padding: 14px 35px; background-color: #054525; border-color: #2f855a;">Commit Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const dateTimeInput = document.getElementById("event_date_time");
    const form = document.getElementById("editInvitationForm");

    function getFormattedCurrentDateTime() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    // Set the native calendar pick selector min threshold to the current exact execution timeline
    const currentDateTimeString = getFormattedCurrentDateTime();
    dateTimeInput.min = currentDateTimeString;

    // Rigid check blocking user from overriding inputs manually via keystrokes
    form.addEventListener("submit", function(event) {
        const selectedDateTime = new Date(dateTimeInput.value);
        const dynamicNow = new Date();

        if (selectedDateTime < dynamicNow) {
            event.preventDefault();
            alert("The event date and time cannot be set in the past. Please update it to a future time.");
            dateTimeInput.focus();
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>