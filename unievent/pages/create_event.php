<?php
// ============================================================
// VIEW: Create Event — UniEvent IIUM
// Route: /pages/create_event.php
// ============================================================
require_once '../includes/db.php';
requireLogin();
if ($_SESSION['user_role'] !== 'organizer') { header('Location: ../index.php'); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Event — UniEvent IIUM</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="page-hero">
  <div class="hero-badge">Organizer</div>
  <h1>Create New Event ➕</h1>
  <p>Fill in the details to publish your event on UniEvent.</p>
</div>

<div class="container page-body">
  <?php include '../includes/flash.php'; ?>
  <div style="max-width:780px;margin:0 auto">
    <div class="card">
      <div class="card-body" style="padding:2.5rem">
        <form method="POST" action="../controllers/EventController.php" enctype="multipart/form-data" data-validate>
          <input type="hidden" name="action" value="create">

          <div class="form-group">
            <label class="form-label">📌 Event Title *</label>
            <input type="text" name="title" class="form-control" placeholder="e.g. Python for Data Science Workshop" required maxlength="200">
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
            <div class="form-group">
              <label class="form-label">📅 Date *</label>
              <input type="date" name="date" class="form-control" min="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">🕐 Time *</label>
              <input type="time" name="time" class="form-control" required>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">📍 Venue *</label>
            <input type="text" name="venue" class="form-control" placeholder="e.g. KICT Lab 1, IIUM Gombak" required>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
            <div class="form-group">
              <label class="form-label">🏷️ Category</label>
              <select name="category" class="form-control">
                <option value="Workshop">Workshop</option>
                <option value="Seminar">Seminar</option>
                <option value="Sports">Sports</option>
                <option value="Cultural">Cultural</option>
                <option value="Academic">Academic</option>
                <option value="Social">Social</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">👥 Max Participants</label>
              <input type="number" name="max_participants" class="form-control" value="50" min="1" max="10000">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">📝 Description</label>
            <textarea name="description" class="form-control" placeholder="Describe your event — what will attendees learn or experience?"></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">🖼️ Event Poster (Optional)</label>
            <input type="file" id="poster" name="poster" class="form-control" accept="image/*">
            <div class="form-hint">JPG, PNG, or GIF. Max 5MB. Recommended: 800×400px.</div>
            <img id="poster-preview" src="#" alt="Preview" style="display:none;max-width:100%;max-height:200px;border-radius:10px;margin-top:0.8rem;border:2px solid var(--border)">
          </div>

          <div style="display:flex;gap:1rem;margin-top:0.5rem">
            <button type="submit" class="btn btn-primary btn-lg">🚀 Publish Event</button>
            <a href="dashboard_organizer.php" class="btn btn-outline btn-lg">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<footer><p>&copy; <?= date('Y') ?> <strong>UniEvent IIUM</strong></p></footer>
<script src="../js/app.js"></script>
</body>
</html>
