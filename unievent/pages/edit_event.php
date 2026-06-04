<?php
// ============================================================
// VIEW: Edit Event — UniEvent IIUM
// Route: /pages/edit_event.php?id=X
// ============================================================
require_once '../includes/db.php';
require_once '../models/EventModel.php';
requireLogin();
if ($_SESSION['user_role'] !== 'organizer') { header('Location: ../index.php'); exit(); }

$id = intval($_GET['id'] ?? 0);
$event = EventModel::getEventById($id);
if (!$event || $event['organizer_id'] != $_SESSION['user_id']) {
    header('Location: dashboard_organizer.php'); exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Event — UniEvent IIUM</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="page-hero">
  <div class="hero-badge">Organizer</div>
  <h1>Edit Event ✏️</h1>
  <p>Update your event details below.</p>
</div>
<div class="container page-body">
  <?php include '../includes/flash.php'; ?>
  <div style="max-width:780px;margin:0 auto">
    <div class="card">
      <div class="card-body" style="padding:2.5rem">
        <form method="POST" action="../controllers/EventController.php" enctype="multipart/form-data" data-validate>
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="event_id" value="<?= $event['id'] ?>">

          <div class="form-group">
            <label class="form-label">📌 Event Title *</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($event['title']) ?>" required>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
            <div class="form-group">
              <label class="form-label">📅 Date *</label>
              <input type="date" name="date" class="form-control" value="<?= $event['date'] ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">🕐 Time *</label>
              <input type="time" name="time" class="form-control" value="<?= $event['time'] ?>" required>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">📍 Venue *</label>
            <input type="text" name="venue" class="form-control" value="<?= htmlspecialchars($event['venue']) ?>" required>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
            <div class="form-group">
              <label class="form-label">🏷️ Category</label>
              <select name="category" class="form-control">
                <?php foreach (['Workshop','Seminar','Sports','Cultural','Academic','Social','Other'] as $cat): ?>
                <option value="<?= $cat ?>" <?= $event['category']===$cat?'selected':'' ?>><?= $cat ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">👥 Max Participants</label>
              <input type="number" name="max_participants" class="form-control" value="<?= $event['max_participants'] ?>" min="1">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">📝 Description</label>
            <textarea name="description" class="form-control"><?= htmlspecialchars($event['description']) ?></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">🖼️ Replace Poster (Optional)</label>
            <input type="file" id="poster" name="poster" class="form-control" accept="image/*">
            <?php if ($event['poster'] && !in_array($event['poster'], ['default_event.png',''])): ?>
            <div class="mt-1"><img src="../images/<?= htmlspecialchars($event['poster']) ?>" style="max-height:120px;border-radius:8px;border:2px solid var(--border)"></div>
            <?php endif; ?>
            <img id="poster-preview" src="#" style="display:none;max-width:100%;max-height:200px;border-radius:10px;margin-top:0.8rem">
          </div>
          <div style="display:flex;gap:1rem">
            <button type="submit" class="btn btn-primary btn-lg">💾 Save Changes</button>
            <a href="event_detail.php?id=<?= $event['id'] ?>" class="btn btn-outline btn-lg">Cancel</a>
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
