<?php
// ============================================================
// VIEW: Student Dashboard — UniEvent IIUM
// Route: /pages/dashboard_student.php
// ============================================================
require_once '../includes/db.php';
require_once '../models/EventModel.php';
requireLogin();
if ($_SESSION['user_role'] !== 'student') { header('Location: ../index.php'); exit(); }

$upcomingEvents = EventModel::getAllEvents();
$upcomingEvents = array_slice($upcomingEvents, 0, 6);
$myEvents = EventModel::getStudentEvents($_SESSION['user_id']);
$totalUpcoming = EventModel::getUpcomingCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard — UniEvent IIUM</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="page-hero">
  <div class="hero-badge">Student Portal</div>
  <h1>Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>! 👋</h1>
  <p>Discover events, join activities, and make the most of your IIUM experience.</p>
</div>

<div class="container page-body">
  <?php include '../includes/flash.php'; ?>

  <!-- STATS -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon">📅</div>
      <div>
        <div class="stat-value"><?= $totalUpcoming ?></div>
        <div class="stat-label">Upcoming Events</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">📌</div>
      <div>
        <div class="stat-value"><?= count($myEvents) ?></div>
        <div class="stat-label">Events Joined</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">✅</div>
      <div>
        <div class="stat-value"><?= count(array_filter($myEvents, fn($e) => $e['reg_status'] === 'completed')) ?></div>
        <div class="stat-label">Completed Events</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">🏆</div>
      <div>
        <div class="stat-value"><?= count(array_filter($myEvents, fn($e) => $e['reg_status'] === 'registered')) ?></div>
        <div class="stat-label">Active Registrations</div>
      </div>
    </div>
  </div>

  <!-- UPCOMING EVENTS -->
  <div class="section-header">
    <h2 class="section-title">Upcoming <span>Events</span></h2>
    <a href="events.php" class="btn btn-outline">View All Events →</a>
  </div>

  <?php if (empty($upcomingEvents)): ?>
    <div class="empty-state">
      <div class="empty-state-icon">📭</div>
      <h3>No events yet</h3>
      <p>Check back soon for upcoming events!</p>
    </div>
  <?php else: ?>
  <div class="events-grid">
    <?php foreach ($upcomingEvents as $ev):
      $pct = $ev['max_participants'] > 0 ? min(100, round(($ev['participant_count'] / $ev['max_participants']) * 100)) : 0;
      $badgeClass = 'badge-' . strtolower($ev['category']);
      $full = $ev['participant_count'] >= $ev['max_participants'];
    ?>
    <div class="card" data-card-category="<?= $ev['category'] ?>">
      <?php if ($ev['poster'] && $ev['poster'] !== 'default_event.png'): ?>
        <img src="../images/<?= htmlspecialchars($ev['poster']) ?>" class="card-img" alt="Event poster">
      <?php else: ?>
        <div class="card-img-placeholder">
          <?= $ev['category'] === 'Workshop' ? '🔧' : ($ev['category'] === 'Sports' ? '⚽' : ($ev['category'] === 'Cultural' ? '🎭' : ($ev['category'] === 'Seminar' ? '🎤' : '📚'))) ?>
        </div>
      <?php endif; ?>
      <div class="card-body">
        <span class="badge <?= $badgeClass ?>"><?= $ev['category'] ?></span>
        <h3 class="event-card-title mt-1"><?= htmlspecialchars($ev['title']) ?></h3>
        <div class="event-card-meta">
          <span class="meta-item">📅 <?= date('d M Y', strtotime($ev['date'])) ?></span>
          <span class="meta-item">🕐 <?= date('H:i', strtotime($ev['time'])) ?></span>
          <span class="meta-item">📍 <?= htmlspecialchars($ev['venue']) ?></span>
        </div>
        <div style="margin: 0.8rem 0 0.4rem; font-size:0.8rem; color:var(--text-muted); display:flex; justify-content:space-between">
          <span>👥 <?= $ev['participant_count'] ?>/<?= $ev['max_participants'] ?> participants</span>
          <span><?= $pct ?>%</span>
        </div>
        <div class="progress mb-2"><div class="progress-bar" data-width="<?= $pct ?>" style="width:0"></div></div>
        <a href="event_detail.php?id=<?= $ev['id'] ?>" class="btn btn-primary btn-sm <?= $full ? 'btn-danger' : '' ?>" style="width:100%;justify-content:center">
          <?= $full ? '🔴 Full' : '🎫 View & Join' ?>
        </a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- MY RECENT EVENTS -->
  <?php if (!empty($myEvents)): ?>
  <div class="section-header mt-3">
    <h2 class="section-title">My Recent <span>Registrations</span></h2>
    <a href="my_events.php" class="btn btn-outline">View All →</a>
  </div>
  <div class="table-wrapper">
    <table>
      <thead><tr><th>Event</th><th>Date</th><th>Venue</th><th>Status</th><th>Action</th></tr></thead>
      <tbody>
        <?php foreach (array_slice($myEvents, 0, 5) as $ev): ?>
        <tr>
          <td><strong><?= htmlspecialchars($ev['title']) ?></strong></td>
          <td><?= date('d M Y', strtotime($ev['date'])) ?></td>
          <td><?= htmlspecialchars($ev['venue']) ?></td>
          <td>
            <span class="badge <?= $ev['reg_status'] === 'registered' ? 'badge-green' : 'badge-gold' ?>">
              <?= ucfirst($ev['reg_status']) ?>
            </span>
          </td>
          <td><a href="event_detail.php?id=<?= $ev['id'] ?>" class="btn btn-outline btn-sm">View</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<footer><p>&copy; <?= date('Y') ?> <strong>UniEvent IIUM</strong> — International Islamic University Malaysia</p></footer>
<script src="../js/app.js"></script>
</body>
</html>
