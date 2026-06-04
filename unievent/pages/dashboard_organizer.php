<?php
// ============================================================
// VIEW: Organizer Dashboard — UniEvent IIUM
// Route: /pages/dashboard_organizer.php
// ============================================================
require_once '../includes/db.php';
require_once '../models/EventModel.php';
requireLogin();
if ($_SESSION['user_role'] !== 'organizer') { header('Location: ../index.php'); exit(); }

$myEvents = EventModel::getEventsByOrganizer($_SESSION['user_id']);
$totalParticipants = array_sum(array_column($myEvents, 'participant_count'));
$upcoming = array_filter($myEvents, fn($e) => $e['date'] >= date('Y-m-d') && $e['status'] === 'active');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Organizer Dashboard — UniEvent IIUM</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="page-hero">
  <div class="hero-badge">Organizer Portal</div>
  <h1>Organizer Dashboard 🧑‍💼</h1>
  <p>Manage your events and track participant registrations.</p>
</div>

<div class="container page-body">
  <?php include '../includes/flash.php'; ?>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon">📅</div>
      <div><div class="stat-value"><?= count($myEvents) ?></div><div class="stat-label">Total Events</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">🚀</div>
      <div><div class="stat-value"><?= count($upcoming) ?></div><div class="stat-label">Active Events</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">👥</div>
      <div><div class="stat-value"><?= $totalParticipants ?></div><div class="stat-label">Total Registrations</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">✅</div>
      <div><div class="stat-value"><?= count(array_filter($myEvents, fn($e) => $e['status'] === 'completed')) ?></div><div class="stat-label">Completed</div></div>
    </div>
  </div>

  <div class="section-header">
    <h2 class="section-title">My <span>Events</span></h2>
    <a href="create_event.php" class="btn btn-gold">➕ Create New Event</a>
  </div>

  <?php if (empty($myEvents)): ?>
  <div class="empty-state">
    <div class="empty-state-icon">📭</div>
    <h3>No events yet</h3>
    <p>Start by creating your first event!</p>
    <a href="create_event.php" class="btn btn-primary mt-2">➕ Create Event</a>
  </div>
  <?php else: ?>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Event Title</th><th>Date</th><th>Category</th>
          <th>Participants</th><th>Status</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($myEvents as $ev):
          $pct = $ev['max_participants'] > 0 ? min(100, round(($ev['participant_count']/$ev['max_participants'])*100)) : 0;
        ?>
        <tr>
          <td><strong><?= htmlspecialchars($ev['title']) ?></strong></td>
          <td><?= date('d M Y', strtotime($ev['date'])) ?></td>
          <td><span class="badge badge-<?= strtolower($ev['category']) ?>"><?= $ev['category'] ?></span></td>
          <td>
            <div style="font-size:0.82rem; margin-bottom:4px">
              <?= $ev['participant_count'] ?>/<?= $ev['max_participants'] ?>
              <span style="color:var(--text-muted)">(<?= $pct ?>%)</span>
            </div>
            <div class="progress" style="height:5px;width:120px"><div class="progress-bar" data-width="<?= $pct ?>" style="width:0"></div></div>
          </td>
          <td><span class="badge badge-<?= $ev['status'] === 'active' ? 'green' : 'gold' ?>"><?= ucfirst($ev['status']) ?></span></td>
          <td>
            <div class="d-flex gap-1">
              <a href="event_detail.php?id=<?= $ev['id'] ?>" class="btn btn-outline btn-sm">👁 View</a>
              <a href="edit_event.php?id=<?= $ev['id'] ?>" class="btn btn-gold btn-sm">✏️ Edit</a>
              <a href="participants.php?id=<?= $ev['id'] ?>" class="btn btn-outline btn-sm">👥</a>
              <form method="POST" action="../controllers/EventController.php" style="margin:0">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="event_id" value="<?= $ev['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm" data-confirm="Delete '<?= htmlspecialchars($ev['title']) ?>'? This cannot be undone.">🗑</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<footer><p>&copy; <?= date('Y') ?> <strong>UniEvent IIUM</strong></p></footer>
<script src="../js/app.js"></script>
</body>
</html>
