<?php
// ============================================================
// VIEW: Event Detail — UniEvent IIUM
// Route: /pages/event_detail.php?id=X
// ============================================================
require_once '../includes/db.php';
require_once '../models/EventModel.php';
requireLogin();

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: events.php'); exit(); }

$event = EventModel::getEventById($id);
if (!$event) { header('Location: events.php'); exit(); }

$isStudent   = $_SESSION['user_role'] === 'student';
$isOrganizer = $_SESSION['user_role'] === 'organizer' && $event['organizer_id'] == $_SESSION['user_id'];
$joined      = $isStudent && EventModel::isJoined($id, $_SESSION['user_id']);
$pct = $event['max_participants'] > 0 ? min(100, round(($event['participant_count']/$event['max_participants'])*100)) : 0;
$full = $event['participant_count'] >= $event['max_participants'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($event['title']) ?> — UniEvent IIUM</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="container page-body">
  <?php include '../includes/flash.php'; ?>
  <a href="events.php" class="btn btn-outline btn-sm mb-2">← Back to Events</a>

  <!-- EVENT HERO -->
  <div class="event-detail-hero">
    <?php if ($event['poster'] && !in_array($event['poster'], ['default_event.png',''])): ?>
      <img src="../images/<?= htmlspecialchars($event['poster']) ?>" alt="Event Poster">
    <?php else: ?>
      <div class="event-detail-hero-placeholder">
        <?= ['Workshop'=>'🔧','Seminar'=>'🎤','Sports'=>'⚽','Cultural'=>'🎭','Academic'=>'📚','Social'=>'🎉'][$event['category']] ?? '📅' ?>
      </div>
    <?php endif; ?>
    <div class="event-detail-overlay">
      <span class="badge badge-<?= strtolower($event['category']) ?>" style="margin-bottom:0.5rem"><?= $event['category'] ?></span>
      <h1 class="event-detail-title"><?= htmlspecialchars($event['title']) ?></h1>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 340px;gap:2rem;align-items:flex-start">

    <!-- LEFT: Details -->
    <div>
      <div class="card mb-2">
        <div class="card-body">
          <h2 style="font-family:var(--font-head);font-size:1.2rem;margin-bottom:1rem;color:var(--green)">📋 Event Details</h2>
          <ul class="info-list">
            <li><span class="info-icon">📅</span><div><div class="info-label">Date</div><?= date('l, d F Y', strtotime($event['date'])) ?></div></li>
            <li><span class="info-icon">🕐</span><div><div class="info-label">Time</div><?= date('H:i', strtotime($event['time'])) ?> (Malaysia Time)</div></li>
            <li><span class="info-icon">📍</span><div><div class="info-label">Venue</div><?= htmlspecialchars($event['venue']) ?></div></li>
            <li><span class="info-icon">🏷️</span><div><div class="info-label">Category</div><?= $event['category'] ?></div></li>
            <li><span class="info-icon">👤</span><div><div class="info-label">Organizer</div><?= htmlspecialchars($event['organizer_name']) ?> — <?= htmlspecialchars($event['organizer_email']) ?></div></li>
            <li><span class="info-icon">👥</span>
              <div style="width:100%">
                <div class="info-label">Participants</div>
                <?= $event['participant_count'] ?> / <?= $event['max_participants'] ?> registered
                <div class="progress mt-1"><div class="progress-bar" data-width="<?= $pct ?>" style="width:0"></div></div>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <h2 style="font-family:var(--font-head);font-size:1.2rem;margin-bottom:0.8rem;color:var(--green)">📝 About This Event</h2>
          <p style="line-height:1.8;color:var(--text)"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
        </div>
      </div>
    </div>

    <!-- RIGHT: Action Card -->
    <div>
      <div class="card" style="position:sticky;top:90px">
        <div class="card-body">
          <?php if ($isStudent): ?>
            <?php if ($joined): ?>
              <div class="flash flash-success">✅ You are registered for this event!</div>
              <a href="my_events.php" class="btn btn-outline btn-block mb-2">📌 View My Events</a>
              <form method="POST" action="../controllers/EventController.php">
                <input type="hidden" name="action" value="leave">
                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm btn-block" data-confirm="Leave this event?">↩ Leave Event</button>
              </form>
            <?php elseif ($full): ?>
              <div class="flash flash-error">❌ This event is fully booked.</div>
              <button class="btn btn-danger btn-block" disabled>Event Full</button>
            <?php else: ?>
              <p style="font-size:0.88rem;color:var(--text-muted);margin-bottom:1rem">
                <?= $event['max_participants'] - $event['participant_count'] ?> spots remaining.
              </p>
              <form method="POST" action="../controllers/EventController.php">
                <input type="hidden" name="action" value="join">
                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                <button type="submit" class="btn btn-primary btn-block btn-lg">🎫 Join This Event</button>
              </form>
            <?php endif; ?>

          <?php elseif ($isOrganizer): ?>
            <div class="flash flash-info">👋 You are the organizer of this event.</div>
            <a href="edit_event.php?id=<?= $event['id'] ?>" class="btn btn-gold btn-block mb-1">✏️ Edit Event</a>
            <a href="participants.php?id=<?= $event['id'] ?>" class="btn btn-outline btn-block mb-1">👥 View Participants</a>
            <form method="POST" action="../controllers/EventController.php">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
              <button class="btn btn-danger btn-sm btn-block" data-confirm="Delete this event permanently?">🗑 Delete Event</button>
            </form>

          <?php else: ?>
            <p class="text-muted text-center">Login as a student to join this event.</p>
          <?php endif; ?>

          <hr style="margin:1.2rem 0;border-color:var(--border)">
          <div style="font-size:0.82rem;color:var(--text-muted)">
            <div style="margin-bottom:0.4rem">📊 <strong><?= $pct ?>%</strong> capacity filled</div>
            <div>🗓 Posted: <?= date('d M Y', strtotime($event['created_at'])) ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<footer><p>&copy; <?= date('Y') ?> <strong>UniEvent IIUM</strong></p></footer>
<script src="../js/app.js"></script>
<style>
@media (max-width: 768px) {
  .container .page-body > div[style*="grid"] { grid-template-columns: 1fr !important; }
}
</style>
</body>
</html>
