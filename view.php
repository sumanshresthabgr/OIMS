<?php
require_once 'config/database.php';

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
$rsvp_completed = false;
$saved_guest_name = '';
$saved_status = '';
$feedbackMessage = '';
$feedbackClass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rsvp'])) {
    $guest_name = trim($_POST['guest_name']);
    $status     = trim($_POST['status']);

    if (!empty($guest_name) && in_array($status, ['Attending', 'Declined'])) {
        $ins = $pdo->prepare("INSERT INTO rsvps (invitation_id, guest_name, status) VALUES (:invitation_id, :guest_name, :status)");
        $ins->execute([
            'invitation_id' => $event['id'],
            'guest_name'    => $guest_name,
            'status'        => $status
        ]);
        
        $rsvp_completed = true;
        $saved_guest_name = htmlspecialchars($guest_name);
        $saved_status = $status;
        
        $feedbackMessage = "Thank you! Your RSVP submission status ('" . htmlspecialchars($status) . "') has been saved.";
        $feedbackClass = "alert-success";
    } else {
        $feedbackMessage = "Submission Error: Please supply an entry name value row attribute.";
        $feedbackClass = "alert-danger";
    }
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
        }

        .theme-card-layout h2 { font-size: clamp(1.4rem, 4.5vw, 2.2rem) !important; text-align: center !important; }
        .theme-card-layout h3 { font-size: clamp(1.2rem, 4vw, 1.8rem) !important; text-align: center !important; }
        .theme-card-layout h4 { font-size: clamp(1.05rem, 3.5vw, 1.4rem) !important; text-align: center !important; }
        .theme-card-layout h5 { font-size: clamp(0.9rem, 3vw, 1.1rem) !important; text-align: center !important; }
        
        .theme-card-layout p, 
        .theme-card-layout span,
        .alert {
            font-size: clamp(0.85rem, 2.5vw, 1.05rem) !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            white-space: normal !important;
            max-width: 100% !important;
            text-align: center !important;
        }

        /* Forces everything inside containers to respect centering */
        .theme-card-layout div {
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            white-space: normal !important;
            max-width: 100% !important;
            text-align: center !important;
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
        }

        /* Force upper invitation card to stay locked inside viewports */
        .public-view-container .card {
            width: 100% !important;
            max-width: 100% !important;
            overflow: hidden !important;
        }

        /* Contain layout inner contents perfectly */
        .theme-card-layout {
            max-width: 100% !important;
            overflow: hidden !important;
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
        <div class="alert <?= $feedbackClass ?>" style="text-align: center; border-radius: 12px; margin-bottom: 10px;">
            <?= $feedbackMessage ?>
        </div>
    <?php endif; ?>

    <div class="card animate-fade-in">
        <div class="card-body">
            <div class="theme-card-layout">
                
                <?php if ($rsvp_completed): ?>
                    <div style="border: 1px dashed currentColor; padding: 20px; border-radius: 8px; margin-bottom: 20px; background: rgba(0,0,0,0.02); box-sizing: border-box;">
                        <?php if ($saved_status === 'Attending'): ?>
                            <h1>Welcome, <?= $saved_guest_name ?>!</h1>
                            <p style="margin-top: 10px;">Your presence has been formally registered on the attendance roster. We look forward to hosting you!</p>
                        <?php else: ?>
                            <h1>Thank You, <?= $saved_guest_name ?>.</h1>
                            <p style="margin-top: 10px;">Your decline notice has been processed. You will be missed!</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <h1><?= htmlspecialchars($event['title']) ?></h1>
                
                <div style="height: 1px; background: currentColor; width: 50px; margin: 10px auto; opacity: 0.15;"></div>
                
                <div style="display: grid; gap: 5px;">
                    <h5>📅 When</h5>
                    <p><?= date('F d, Y @ h:i A', strtotime($event['date_time'])) ?></p>
                </div>

                <div style="display: grid; gap: 5px;">
                    <h5>📍 Where</h5>
                    <p style="word-break: break-word;"><?= htmlspecialchars($event['venue']) ?></p>
                </div>

                <?php if (!empty($event['description'])): ?>
                    <div style="margin-top: 5px;">
                        <p style="opacity: 0.95; line-height: 1.6; word-break: break-word;">
                            <?= nl2br(htmlspecialchars($event['description'])) ?>
                        </p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($event['dress_code']) || !empty($event['registry']) || !empty($event['special_notes'])): ?>
                    <div style="margin-top: 15px; border-top: 1px dashed rgba(0,0,0,0.1); padding-top: 25px; display: grid; gap: 20px; text-align: left; box-sizing: border-box;">
                        
                        <?php if (!empty($event['dress_code'])): ?>
                            <div style="display: grid; gap: 4px;">
                                <h5 style="text-align: center;">👗 Dress Code</h5>
                                <p style="text-align: center; opacity: 0.85; word-break: break-word;"><?= htmlspecialchars($event['dress_code']) ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($event['registry'])): ?>
                            <div style="display: grid; gap: 4px; text-align: center;">
                                <h5>🎁 Gift Registry</h5>
                                <p>
                                    <a href="<?= htmlspecialchars($event['registry']) ?>" target="_blank" class="btn btn-theme" style="display: inline-block; padding: 8px 18px; font-size: clamp(0.75rem, 2.5vw, 0.85rem); text-decoration: none; font-weight:600; text-transform: none; max-width: 100%; box-sizing: border-box; white-space: normal; word-break: break-all;">
                                        View Registry Destination &rarr;
                                    </a>
                                </p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($event['special_notes'])): ?>
                            <div style="display: grid; gap: 4px; background: rgba(0,0,0,0.02); padding: 15px; border-radius: 8px; box-sizing: border-box;">
                                <h5 style="color: currentColor; opacity: 0.7;">⚠️ Important Notes</h5>
                                <p style="line-height: 1.5; font-style: italic; opacity: 0.85; word-break: break-word;">
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
        <div class="card">
            <div class="card-body">
                <h3 style="text-align: center; font-weight: 700; margin-bottom: 30px; letter-spacing: -0.5px; text-transform: uppercase; opacity: 0.8;">
                    Confirm Attendance
                </h3>
                
                <form action="view.php?id=<?= htmlspecialchars($token) ?>" method="POST">
                    <div class="form-group">
                        <label class="form-label" style="font-size: clamp(0.85rem, 2.5vw, 1rem);">Your Guest Name</label>
                        <input type="text" name="guest_name" class="form-control" placeholder="Enter full name parameter space" required autocomplete="off" style="font-size: clamp(0.85rem, 2.5vw, 1rem);">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" style="font-size: clamp(0.85rem, 2.5vw, 1rem);">Response Track Signature</label>
                        <div class="rsvp-selection-grid">
                            <div>
                                <input type="radio" class="btn-check" name="status" id="status_attending" value="Attending" checked>
                                <label class="rsvp-check-label" for="status_attending">Accepts With Pleasure</label>
                            </div>
                            <div>
                                <input type="radio" class="btn-check" name="status" id="status_declined" value="Declined">
                                <label class="rsvp-check-label" for="status_declined">Declines With Regret</label>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" name="submit_rsvp" class="btn btn-theme" style="width: 100%; font-size: clamp(0.85rem, 2.5vw, 1rem);">
                         click to confirm
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
    
</div>

</body>
</html>