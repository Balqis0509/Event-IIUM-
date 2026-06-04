<?php
// ============================================================
// VIEW: My Events (Student) — UniEvent IIUM
// Route: /pages/my_events.php
// ============================================================
require_once '../includes/db.php';
require_once '../models/EventModel.php';
requireLogin();
if ($_SESSION['user_role'] !== 'student') { header('Location: events.php'); exit(); }

$events = EventModel::getStudentEvents($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Events — UniEvent IIUM</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="page-hero">
  <div class="hero-badge">My Activity</div>
  <h1>My Events 📌</h1>
  <p>Track all the events you've registered for.</p>
</div>

<div class="container page-body">
  <?php include '../includes/flash.php'; ?>

  <!-- STATS -->
  <div class="stats-grid mb-3">
    <div class="stat-card">
      <div class="stat-icon">📋</div>
      <div><div class="stat-value"><?= count($events) ?></div><div class="stat-label">Total Joined</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">⏳</div>
      <div><div class="stat-value"><?= count(array_filter($events, fn($e) => $e['reg_status']==='registered')) ?></div><div class="stat-label">Active</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">✅</div>
      <div><div class="stat-value"><?= count(array_filter($events, fn($e) => $e['reg_status']==='completed')) ?></div><div class="stat-label">Completed</div></div>
    </div>
  </div>

  <?php if (empty($events)): ?>
  <div class="empty-state">
    <div class="empty-state-icon">📭</div>
    <h3>You haven't joined any events yet</h3>
    <p>Explore upcoming events and join one today!</p>
    <a href="events.php" class="btn btn-primary mt-2">Browse Events</a>
  </div>
  <?php else: ?>
  <div class="events-grid">
    <?php foreach ($events as $ev):
      $icons = ['Workshop'=>'🔧','Seminar'=>'🎤','Sports'=>'⚽','Cultural'=>'🎭','Academic'=>'📚','Social'=>'🎉','Other'=>'📌'];
      $icon = $icons[$ev['category']] ?? '📌';
      $isPast = $ev['date'] < date('Y-m-d');
    ?>
    <div class="card" style="<?= $isPast ? 'opacity:0.75' : '' ?>">
      <?php if ($ev['poster'] && !in_array($ev['poster'],['default_event.png',''])): ?>
        <img src="../images/<?= htmlspecialchars($ev['poster']) ?>" class="card-img" alt="Poster">
      <?php else: ?>
        <div class="card-img-placeholder"><?= $icon ?></div>
      <?php endif; ?>
      <div class="card-body">
        <div style="display:flex;gap:0.4rem;margin-bottom:0.5rem;flex-wrap:wrap">
          <span class="badge badge-<?= strtolower($ev['category']) ?>"><?= $ev['category'] ?></span>
          <span class="badge <?= $ev['reg_status']==='registered'?'badge-green':'badge-gold' ?>">
            <?= ucfirst($ev['reg_status']) ?>
          </span>
          <?php if ($isPast): ?><span class="badge" style="background:#f5f5f5;color:#777">Past</span><?php endif; ?>
        </div>
        <h3 class="event-card-title"><?= htmlspecialchars($ev['title']) ?></h3>
        <div class="event-card-meta">
          <span class="meta-item">📅 <?= date('d M Y', strtotime($ev['date'])) ?></span>
          <span class="meta-item">🕐 <?= date('H:i', strtotime($ev['time'])) ?></span>
        </div>
        <div class="meta-item mb-1">📍 <?= htmlspecialchars($ev['venue']) ?></div>
        <div class="meta-item mb-2" style="font-size:0.78rem;color:var(--text-muted)">
          Registered: <?= date('d M Y', strtotime($ev['registered_at'])) ?>
        </div>
        <div style="display:flex;gap:0.5rem">
          <a href="event_detail.php?id=<?= $ev['id'] ?>" class="btn btn-outline btn-sm" style="flex:1;justify-content:center">👁 View</a>
          <?php if (!$isPast): ?>
          <form method="POST" action="../controllers/EventController.php" style="flex:1">
            <input type="hidden" name="action" value="leave">
            <input type="hidden" name="event_id" value="<?= $ev['id'] ?>">
            <button class="btn btn-danger btn-sm btn-block" data-confirm="Leave this event?">↩ Leave</button>
          </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<footer><p>&copy; <?= date('Y') ?> <strong>UniEvent IIUM</strong></p></footer>
<script src="../js/app.js"></script>
</body>
</html>
