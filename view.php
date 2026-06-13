<?php
require_once 'config/database.php';

// Start session to support flash feedback storage across redirect instances
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['id']) || strlen($_GET['id']) !== 8) {
    http_response_code(404);
    include 'includes/header.php';
    echo "<div class='alert alert-danger' style='text-align:center; margin-top:50px;'><h4>404: Invitation Record Unresolved</h4></div>";
    include 'includes/footer.php';
    exit();
}

$token = trim($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM invitations WHERE share_token = :token");
$stmt->execute(['token' => $token]);
$event = $stmt->fetch();

if (!$event) {
    http_response_code(404);
    include 'includes/header.php';
    echo "<div class='alert alert-danger' style='text-align:center; margin-top:50px;'><h4>404: Invitation Record Missing</h4></div>";
    include 'includes/footer.php';
    exit();
}

// Track if an RSVP was just successfully completed in this request loop
$rsvp_completed = isset($_SESSION['rsvp_flash_completed']) ? $_SESSION['rsvp_flash_completed'] : false;
$saved_guest_name = isset($_SESSION['rsvp_flash_guest_name']) ? $_SESSION['rsvp_flash_guest_name'] : '';
$saved_status = isset($_SESSION['rsvp_flash_status']) ? $_SESSION['rsvp_flash_status'] : '';
$feedbackMessage = isset($_SESSION['rsvp_flash_message']) ? $_SESSION['rsvp_flash_message'] : '';
$feedbackClass = isset($_SESSION['rsvp_flash_class']) ? $_SESSION['rsvp_flash_class'] : '';

// Wipe temporary flash metrics immediately so a manual reload drops them completely
unset($_SESSION['rsvp_flash_completed']);
unset($_SESSION['rsvp_flash_guest_name']);
unset($_SESSION['rsvp_flash_status']);
unset($_SESSION['rsvp_flash_message']);
unset($_SESSION['rsvp_flash_class']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rsvp'])) {
    $guest_name = trim($_POST['guest_name']);
    $status     = trim($_POST['status']);

    if (!empty($guest_name) && in_array($status, ['Attending', 'Declined'])) {
        
        // Check if this attendee has already submitted an RSVP for this specific event
        $checkDuplicate = $pdo->prepare("SELECT COUNT(*) FROM rsvps WHERE invitation_id = :invitation_id AND LOWER(TRIM(guest_name)) = LOWER(:guest_name)");
        $checkDuplicate->execute([
            'invitation_id' => $event['id'],
            'guest_name'    => $guest_name
        ]);
        
        if ($checkDuplicate->fetchColumn() > 0) {
            $_SESSION['rsvp_flash_message'] = "Submission Error: An RSVP has already been submitted under the name '" . htmlspecialchars($guest_name) . "' for this invitation.";
            $_SESSION['rsvp_flash_class'] = "alert-danger";
        } else {
            $ins = $pdo->prepare("INSERT INTO rsvps (invitation_id, guest_name, status) VALUES (:invitation_id, :guest_name, :status)");
            $ins->execute([
                'invitation_id' => $event['id'],
                'guest_name'    => $guest_name,
                'status'        => $status
            ]);
            
            $_SESSION['rsvp_flash_completed'] = true;
            $_SESSION['rsvp_flash_guest_name'] = htmlspecialchars($guest_name);
            $_SESSION['rsvp_flash_status'] = $status;
            
            $_SESSION['rsvp_flash_message'] = "Thank you! Your RSVP submission status ('" . htmlspecialchars($status) . "') has been saved.";
            $_SESSION['rsvp_flash_class'] = "alert-success";
        }
    } else {
        $_SESSION['rsvp_flash_message'] = "Submission Error: Please supply an entry name value row attribute.";
        $_SESSION['rsvp_flash_class'] = "alert-danger";
    }

    // Post/Redirect/Get enforcement loop target
    header("Location: view.php?id=" . urlencode($token));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['title']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&family=Great+Vibes&family=Montserrat:wght@300;400;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        /* Essential structure bypass fixes (No styling alterations) */
        .theme-wedding .card::after {
            pointer-events: none !important;
        }
        .rsvp-check-label {
            position: relative;
            z-index: 10;
            cursor: pointer !important;
        }

        /* Desktop & Mobile Fluid Adaptation Controls */
        html, body {
            max-width: 100% !important;
            overflow-x: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        /* Enforce absolute safety sizing across all elements */
        *, *::before, *::after {
            box-sizing: border-box !important;
        }

        /* Global fluid responsive typography and structural center alignment */
        .theme-card-layout h1 {
            font-size: clamp(1.6rem, 5vw, 2.5rem) !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            white-space: normal !important;
            max-width: 100% !important;
            text-align: center !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        .theme-card-layout h2 { font-size: clamp(1.4rem, 4.5vw, 2.2rem) !important; text-align: center !important; margin-left: auto !important; margin-right: auto !important; }
        .theme-card-layout h3 { font-size: clamp(1.2rem, 4vw, 1.8rem) !important; text-align: center !important; margin-left: auto !important; margin-right: auto !important; }
        .theme-card-layout h4 { font-size: clamp(1.05rem, 3.5vw, 1.4rem) !important; text-align: center !important; margin-left: auto !important; margin-right: auto !important; }
        .theme-card-layout h5 { font-size: clamp(0.9rem, 3vw, 1.1rem) !important; text-align: center !important; margin-left: auto !important; margin-right: auto !important; }
        
        .theme-card-layout p, 
        .theme-card-layout span,
        .alert {
            font-size: clamp(0.85rem, 2.5vw, 1.05rem) !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            white-space: normal !important;
            max-width: 100% !important;
            text-align: center !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        /* Forces everything inside containers to respect centering and strip default layout skew resets */
        .theme-card-layout div {
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            white-space: normal !important;
            max-width: 100% !important;
            text-align: center !important;
            padding: 0 !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        /* Target grid blocks (When, Where) to align center natively */
        .theme-card-layout div[style*="display: grid"] {
            text-align: center !important;
            justify-content: center !important;
            justify-items: center !important;
            align-items: center !important;
        }

        /* Adjust internal container areas to align their components to the center */
        .theme-card-layout div[style*="text-align: left"] {
            text-align: center !important;
            justify-content: center !important;
            justify-items: center !important;
        }

        .public-view-container {
            width: 100% !important;
            max-width: 600px !important; /* Aligns with standard invitation wrapper limits */
            margin: 0 auto !important;
            padding: 12px !important;
            float: none !important;
        }

        /* Force upper invitation card to stay locked inside viewports */
        .public-view-container .card {
            width: 100% !important;
            max-width: 100% !important;
            overflow: hidden !important;
            margin: 0 auto !important;
            float: none !important;
        }

        /* Contain layout inner contents perfectly */
        .theme-card-layout {
            max-width: 100% !important;
            overflow: hidden !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 auto !important;
        }

        @media (max-width: 576px) {
            /* Compress padding values seamlessly on mobile displays */
            .public-view-container .card-body {
                padding: 30px 20px !important;
            }

            /* Form selection layout handling */
            .rsvp-selection-grid {
                display: flex;
                flex-direction: column;
                gap: 10px;
                width: 100% !important;
                align-items: center !important;
                justify-content: center !important;
            }
            
            .rsvp-check-label {
                width: 100%;
                display: block;
                box-sizing: border-box;
                text-align: center;
                font-size: clamp(0.85rem, 3vw, 1rem) !important;
            }
        }
    </style>
</head>
<body class="theme-<?= htmlspecialchars($event['theme']) ?>">

<div class="public-view-container">
    
    <?php if (!empty($feedbackMessage)): ?>
        <div class="alert <?= $feedbackClass ?>" style="text-align: center; border-radius: 12px; margin-bottom: 10px; margin-left: auto; margin-right: auto; width: 100%;">
            <?= $feedbackMessage ?>
        </div>
    <?php endif; ?>

    <div class="card animate-fade-in">
        <div class="card-body">
            <div class="theme-card-layout">
                
                <?php if ($rsvp_completed): ?>
                    <div style="border: 1px dashed currentColor; padding: 20px !important; border-radius: 8px; margin-bottom: 20px; background: rgba(0,0,0,0.02); box-sizing: border-box; display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; margin-left: auto; margin-right: auto;">
                        <?php if ($saved_status === 'Attending'): ?>
                            <h1 style="text-align: center; width: 100%;">Welcome, <?= $saved_guest_name ?>!</h1>
                            <p style="margin-top: 10px; text-align: center; width: 100%;">Your presence has been formally registered on the attendance roster. We look forward to hosting you!</p>
                        <?php else: ?>
                            <h1 style="text-align: center; width: 100%;">Thank You, <?= $saved_guest_name ?>.</h1>
                            <p style="margin-top: 10px; text-align: center; width: 100%;">Your decline notice has been processed. You will be missed!</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <h1 style="text-align: center; width: 100%;"><?= htmlspecialchars($event['title']) ?></h1>
                
                <div style="height: 1px; background: currentColor; width: 50px; margin: 10px auto !important; opacity: 0.15; float: none;"></div>
                
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: 5px; margin-bottom: 15px; width: 100%; margin-left: auto; margin-right: auto;">
                    <h5 style="text-align: center; width: 100%; margin: 0 auto;">📅 When</h5>
                    <p style="text-align: center; margin: 0 auto; width: 100%;"><?= date('F d, Y @ h:i A', strtotime($event['date_time'])) ?></p>
                </div>

                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: 5px; margin-bottom: 15px; width: 100%; margin-left: auto; margin-right: auto;">
                    <h5 style="text-align: center; width: 100%; margin: 0 auto;">📍 Where</h5>
                    <p style="word-break: break-word; text-align: center; margin: 0 auto; width: 100%;"><?= htmlspecialchars($event['venue']) ?></p>
                </div>

                <?php if (!empty($event['description'])): ?>
                    <div style="margin-top: 5px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; width: 100%; margin-left: auto; margin-right: auto;">
                        <p style="opacity: 0.95; line-height: 1.6; word-break: break-word; text-align: center; width: 100%; margin: 0 auto;">
                            <?= nl2br(htmlspecialchars($event['description'])) ?>
                        </p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($event['dress_code']) || !empty($event['registry']) || !empty($event['special_notes'])): ?>
                    <div style="margin-top: 15px; border-top: 1px dashed rgba(0,0,0,0.1); padding-top: 25px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 20px; box-sizing: border-box; width: 100%; margin-left: auto; margin-right: auto;">
                        
                        <?php if (!empty($event['dress_code'])): ?>
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: 4px; width: 100%; margin-left: auto; margin-right: auto;">
                                <h5 style="text-align: center; width: 100%;">👗 Dress Code</h5>
                                <p style="text-align: center; opacity: 0.85; word-break: break-word; margin: 0 auto; width: 100%;"><?= htmlspecialchars($event['dress_code']) ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($event['registry'])): ?>
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: 4px; width: 100%; margin-left: auto; margin-right: auto;">
                                <h5 style="text-align: center; width: 100%;">🎁 Gift Registry</h5>
                                <p style="text-align: center; margin: 0 auto; width: 100%;">
                                    <a href="<?= htmlspecialchars($event['registry']) ?>" target="_blank" class="btn btn-theme" style="display: inline-block; padding: 8px 18px; font-size: clamp(0.75rem, 2.5vw, 0.85rem); text-decoration: none; font-weight:600; text-transform: none; max-width: 100%; box-sizing: border-box; white-space: normal; word-break: break-all; margin: 0 auto;">
                                        View Registry Destination &rarr;
                                    </a>
                                </p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($event['special_notes'])): ?>
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: 4px; background: rgba(0,0,0,0.02); padding: 15px !important; border-radius: 8px; box-sizing: border-box; width: 100%; margin-left: auto; margin-right: auto;">
                                <h5 style="color: currentColor; opacity: 0.7; text-align: center; width: 100%;">⚠️ Important Notes</h5>
                                <p style="line-height: 1.5; font-style: italic; opacity: 0.85; word-break: break-word; text-align: center; margin: 0 auto; width: 100%;">
                                    <?= nl2br(htmlspecialchars($event['special_notes'])) ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        
                    </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>

    <?php if (!$rsvp_completed): ?>
        <div class="card" style="margin-left: auto; margin-right: auto;">
            <div class="card-body">
                <h3 style="text-align: center; font-weight: 700; margin-bottom: 30px; letter-spacing: -0.5px; text-transform: uppercase; opacity: 0.8; width: 100%;">
                    Confirm Attendance
                </h3>
                
                <form action="view.php?id=<?= htmlspecialchars($token) ?>" method="POST" style="width: 100%; margin: 0 auto;">
                    <div class="form-group" style="width: 100%; text-align: center;">
                        <label class="form-label" style="font-size: clamp(0.85rem, 2.5vw, 1rem); display: block; text-align: center; width: 100%;">Your Guest Name</label>
                        <input type="text" name="guest_name" class="form-control" placeholder="Enter full name parameter space" required autocomplete="off" style="font-size: clamp(0.85rem, 2.5vw, 1rem); text-align: center; margin: 0 auto; display: block;">
                    </div>
                    
                    <div class="form-group" style="width: 100%; text-align: center;">
                        <label class="form-label" style="font-size: clamp(0.85rem, 2.5vw, 1rem); display: block; text-align: center; width: 100%;">Response Track Signature</label>
                        <div class="rsvp-selection-grid" style="margin: 0 auto;">
                            <div style="text-align: center; width: 100%;">
                                <input type="radio" class="btn-check" name="status" id="status_attending" value="Attending" checked>
                                <label class="rsvp-check-label" for="status_attending" style="display: inline-block; margin: 0 auto;">Yes, I will attend</label>
                            </div>
                            <div style="text-align: center; width: 100%;">
                                <input type="radio" class="btn-check" name="status" id="status_declined" value="Declined">
                                <label class="rsvp-check-label" for="status_declined" style="display: inline-block; margin: 0 auto;">No, I cannot attend</label>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" name="submit_rsvp" class="btn btn-theme" style="width: 100%; font-size: clamp(0.85rem, 2.5vw, 1rem); margin: 0 auto; display: block;">
                         click to confirm
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
    
</div>

</body>
</html>