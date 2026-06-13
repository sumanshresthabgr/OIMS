<?php
require_once '../includes/auth.php';
include '../includes/header.php';
?>

<div class="card form-grid-width">
    <div class="card-header">
        <h3>Invitation Details</h3>
    </div>
    <div class="card-body">
        <form action="process_event.php" method="POST" id="invitationForm">
            <div class="form-group">
                <label class="form-label">Event Title</label>
                <input type="text" name="title" class="form-control" placeholder="e.g., John & Mary Wedding Celebration" required>
            </div>
            
            <div class="grid-2col">
                <div class="form-group">
                    <label class="form-label">Event Time</label>
                    <input type="datetime-local" name="date_time" id="event_date_time" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Theme</label>
                    <select name="theme" class="form-control" required>
                        <option value="wedding">Romantic Floral Style (Weddings)</option>
                        <option value="birthday">Vibrant Festive Style (Birthdays)</option>
                        <option value="corporate">Modern Minimalist Style (Corporate)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Venue Instructions</label>
                <input type="text" name="venue" class="form-control" placeholder="Full address details" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Event Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="A brief welcome or introductory message for your guests..."></textarea>
            </div>

            <h4 style="margin: 35px 0 15px 0; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color:#718096; border-bottom: 1px solid #edf2f7; padding-bottom: 8px;">
                Other Description (Optional Fields)
            </h4>

            <div class="grid-2col">
                <div class="form-group">
                    <label class="form-label">Dress Code Guidelines</label>
                    <input type="text" name="dress_code" class="form-control" placeholder="e.g., Black Tie Optional, Smart Casual">
                </div>
                <div class="form-group">
                    <label class="form-label">Gift Registry Link Network</label>
                    <input type="url" name="registry" class="form-control" placeholder="https://example.com/registry">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Special Attendance Notes</label>
                <textarea name="special_notes" class="form-control" rows="3" placeholder="e.g., Please arrive 15 minutes early. Valet parking available on-site. Adult-only reception."></textarea>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-top: 40px; border-top: 1px solid #edf2f7; padding-top: 25px;">
                <a href="dashboard.php" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-success" style="padding: 14px 35px;">Trigger Assembly Engine</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const dateTimeInput = document.getElementById("event_date_time");
    const form = document.getElementById("invitationForm");

    function getFormattedCurrentDateTime() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    // Set the minimum selectable date-time to the current exact time
    const currentDateTimeString = getFormattedCurrentDateTime();
    dateTimeInput.min = currentDateTimeString;

    // Hard submission check to block manual keystroke past-date inputs
    form.addEventListener("submit", function(event) {
        const selectedDateTime = new Date(dateTimeInput.value);
        const dynamicNow = new Date();

        if (selectedDateTime < dynamicNow) {
            event.preventDefault();
            alert("⚠️ The event date and time cannot be set in the past. Please choose a future time.");
            dateTimeInput.focus();
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>