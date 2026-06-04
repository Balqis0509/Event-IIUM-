<?php
// ============================================================
// VIEW: Event Listing — UniEvent IIUM
// Route: /pages/events.php
// ============================================================
require_once '../includes/db.php';
require_once '../models/EventModel.php';
requireLogin();

$category = $_GET['category'] ?? null;
$search   = $_GET['search'] ?? null;
$events   = EventModel::getAllEvents($category, $search);
$categories = ['Workshop','Seminar','Sports','Cultural','Academic','Social','Other'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Events — UniEvent IIUM</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="page-hero">
  <div class="hero-badge">Discover</div>
  <h1>Campus Events 📅</h1>
  <p>Find and join exciting events happening at IIUM.</p>
</div>

<div class="container page-body">
  <?php include '../includes/flash.php'; ?>

  <!-- FILTER BAR -->
  <form method="GET" class="filter-bar">
    <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>"
           class="form-control" placeholder="🔍 Search events, venues...">
    <div class="filter-chips">
      <a href="events.php" class="chip <?= !$category ? 'active' : '' ?>">All</a>
      <?php foreach ($categories as $cat): ?>
      <a href="?category=<?= urlencode($cat) ?><?= $search ? '&search='.urlencode($search) : '' ?>"
         class="chip <?= $category === $cat ? 'active' : '' ?>"><?= $cat ?></a>
      <?php endforeach; ?>
    </div>
    <button type="submit" class="btn btn-primary">Search</button>
    <?php if ($search || $category): ?>
    <a href="events.php" class="btn btn-outline">✕ Clear</a>
    <?php endif; ?>
  </form>

  <!-- RESULTS COUNT -->
  <div style="margin-bottom:1.5rem; color:var(--text-muted); font-size:0.9rem">
    <?= count($events) ?> event<?= count($events) !== 1 ? 's' : '' ?> found
    <?= $category ? " in <strong>$category</strong>" : '' ?>
    <?= $search ? " matching \"<strong>" . htmlspecialchars($search) . "</strong>\"" : '' ?>
  </div>

  <?php if (empty($events)): ?>
  <div class="empty-state">
    <div class="empty-state-icon">📭</div>
    <h3>No events found</h3>
    <p>Try a different search or category filter.</p>
    <a href="events.php" class="btn btn-primary mt-2">Clear filters</a>
  </div>
  <?php else: ?>
  <div class="events-grid">
    <?php foreach ($events as $ev):
      $pct = $ev['max_participants'] > 0 ? min(100, round(($ev['participant_count']/$ev['max_participants'])*100)) : 0;
      $full = $ev['participant_count'] >= $ev['max_participants'];
      $icons = ['Workshop'=>'🔧','Seminar'=>'🎤','Sports'=>'⚽','Cultural'=>'🎭','Academic'=>'📚','Social'=>'🎉','Other'=>'📌'];
      $icon = $icons[$ev['category']] ?? '📌';
    ?>
    <div class="card">
      <?php if ($ev['poster'] && !in_array($ev['poster'], ['default_event.png',''])): ?>
        <img src="../images/<?= htmlspecialchars($ev['poster']) ?>" class="card-img" alt="Poster">
      <?php else: ?>
        <div class="card-img-placeholder"><?= $icon ?></div>
      <?php endif; ?>
      <div class="card-body">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.4rem">
          <span class="badge badge-<?= strtolower($ev['category']) ?>"><?= $ev['category'] ?></span>
          <?php if ($full): ?><span class="badge" style="background:#fde8e8;color:#dc3545">Full</span><?php endif; ?>
        </div>
        <h3 class="event-card-title"><?= htmlspecialchars($ev['title']) ?></h3>
        <div class="event-card-meta">
          <span class="meta-item">📅 <?= date('d M Y', strtotime($ev['date'])) ?></span>
          <span class="meta-item">🕐 <?= date('H:i', strtotime($ev['time'])) ?></span>
        </div>
        <div class="meta-item mb-1">📍 <?= htmlspecialchars($ev['venue']) ?></div>
        <div class="meta-item mb-2">👤 Organised by <?= htmlspecialchars($ev['organizer_name']) ?></div>
        <div style="display:flex;justify-content:space-between;font-size:0.78rem;color:var(--text-muted);margin-bottom:4px">
          <span>👥 <?= $ev['participant_count'] ?>/<?= $ev['max_participants'] ?></span>
          <span><?= $pct ?>% full</span>
        </div>
        <div class="progress mb-2"><div class="progress-bar" data-width="<?= $pct ?>" style="width:0"></div></div>
        <a href="event_detail.php?id=<?= $ev['id'] ?>" class="btn <?= $full ? 'btn-outline' : 'btn-primary' ?> btn-sm btn-block">
          <?= $full ? '👁 View Details' : '🎫 View & Join' ?>
        </a>
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
