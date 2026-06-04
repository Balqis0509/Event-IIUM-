<?php
// ============================================================
// VIEW: Participants — UniEvent IIUM
// Route: /pages/participants.php?id=X
// ============================================================
require_once '../includes/db.php';
require_once '../models/EventModel.php';
requireLogin();
if (!in_array($_SESSION['user_role'], ['organizer','admin'])) { header('Location: ../index.php'); exit(); }

$id = intval($_GET['id'] ?? 0);
$event = EventModel::getEventById($id);
if (!$event) { header('Location: dashboard_organizer.php'); exit(); }

$participants = EventModel::getParticipants($id);
$pct = $event['max_participants'] > 0 ? min(100, round(($event['participant_count']/$event['max_participants'])*100)) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Participants — <?= htmlspecialchars($event['title']) ?></title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="page-hero">
  <div class="hero-badge">Participant Management</div>
  <h1>👥 Participants</h1>
  <p><?= htmlspecialchars($event['title']) ?></p>
</div>

<div class="container page-body">
  <?php include '../includes/flash.php'; ?>

  <!-- Event Summary -->
  <div class="card mb-3">
    <div class="card-body">
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;align-items:center">
        <div>
          <div style="font-size:0.78rem;color:var(--text-muted);text-transform:uppercase;font-weight:700">Event</div>
          <div style="font-weight:700;font-family:var(--font-head)"><?= htmlspecialchars($event['title']) ?></div>
        </div>
        <div>
          <div style="font-size:0.78rem;color:var(--text-muted);text-transform:uppercase;font-weight:700">Date</div>
          <div><?= date('d M Y', strtotime($event['date'])) ?></div>
        </div>
        <div>
          <div style="font-size:0.78rem;color:var(--text-muted);text-transform:uppercase;font-weight:700">Registered</div>
          <div><strong><?= count($participants) ?></strong> / <?= $event['max_participants'] ?></div>
        </div>
        <div style="width:100%">
          <div style="font-size:0.78rem;color:var(--text-muted);margin-bottom:4px"><?= $pct ?>% full</div>
          <div class="progress"><div class="progress-bar" data-width="<?= $pct ?>" style="width:0"></div></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Participant Search + Export -->
  <div class="section-header">
    <h2 class="section-title">Registered <span>Students</span></h2>
    <div style="display:flex;gap:0.8rem;align-items:center">
      <input type="text" id="live-search" class="form-control" placeholder="🔍 Search participants..." style="width:220px">
      <button onclick="exportCSV()" class="btn btn-gold btn-sm">📥 Export CSV</button>
    </div>
  </div>

  <?php if (empty($participants)): ?>
  <div class="empty-state">
    <div class="empty-state-icon">👤</div>
    <h3>No participants yet</h3>
    <p>Share your event to attract participants!</p>
  </div>
  <?php else: ?>
  <div class="table-wrapper" id="participants-table">
    <table>
      <thead>
        <tr>
          <th>#</th><th>Name</th><th>Email</th><th>Matric No</th><th>Registered</th><th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($participants as $i => $p): ?>
        <tr data-searchable>
          <td><?= $i + 1 ?></td>
          <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
          <td><?= htmlspecialchars($p['email']) ?></td>
          <td><?= htmlspecialchars($p['matric_no'] ?: '—') ?></td>
          <td><?= date('d M Y H:i', strtotime($p['registered_at'])) ?></td>
          <td><span class="badge badge-green"><?= ucfirst($p['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <div class="mt-2">
    <a href="event_detail.php?id=<?= $event['id'] ?>" class="btn btn-outline">← Back to Event</a>
  </div>
</div>

<footer><p>&copy; <?= date('Y') ?> <strong>UniEvent IIUM</strong></p></footer>
<script src="../js/app.js"></script>
<script>
function exportCSV() {
  const rows = [['#','Name','Email','Matric No','Registered','Status']];
  document.querySelectorAll('#participants-table tbody tr').forEach((row, i) => {
    const cells = [...row.querySelectorAll('td')].map(c => c.textContent.trim());
    rows.push(cells);
  });
  const csv = rows.map(r => r.map(c => `"${c.replace(/"/g,'""')}"`).join(',')).join('\n');
  const a = document.createElement('a');
  a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
  a.download = 'participants_<?= $event['id'] ?>.csv';
  a.click();
}
</script>
</body>
</html>
