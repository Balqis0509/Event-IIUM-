<?php
// ============================================================
// VIEW: Admin Dashboard — UniEvent IIUM
// Route: /pages/dashboard_admin.php
// ============================================================
require_once '../includes/db.php';
require_once '../models/UserModel.php';
require_once '../models/EventModel.php';
requireLogin();
if ($_SESSION['user_role'] !== 'admin') { header('Location: ../index.php'); exit(); }

$allUsers  = UserModel::getAllUsers();
$students  = array_filter($allUsers, fn($u) => $u['role'] === 'student');
$organizers= array_filter($allUsers, fn($u) => $u['role'] === 'organizer');
$allEvents = EventModel::getAllEvents();
$totalRegs = EventModel::getTotalRegistrations();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard — UniEvent IIUM</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="page-hero">
  <div class="hero-badge">Admin Control Panel</div>
  <h1>System Dashboard 🛠️</h1>
  <p>Manage users, events, and system-wide data.</p>
</div>

<div class="container page-body">
  <?php include '../includes/flash.php'; ?>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon">👨‍🎓</div>
      <div><div class="stat-value"><?= count($students) ?></div><div class="stat-label">Students</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">🧑‍💼</div>
      <div><div class="stat-value"><?= count($organizers) ?></div><div class="stat-label">Organizers</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">📅</div>
      <div><div class="stat-value"><?= count($allEvents) ?></div><div class="stat-label">Active Events</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">📋</div>
      <div><div class="stat-value"><?= $totalRegs ?></div><div class="stat-label">Total Registrations</div></div>
    </div>
  </div>

  <!-- USERS TABLE -->
  <div class="section-header">
    <h2 class="section-title">All <span>Users</span></h2>
    <input type="text" id="live-search" class="form-control" placeholder="🔍 Search users..." style="max-width:260px">
  </div>
  <div class="table-wrapper mb-3">
    <table>
      <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Matric No</th><th>Joined</th><th>Action</th></tr></thead>
      <tbody>
        <?php foreach ($allUsers as $u): if ($u['role'] === 'admin') continue; ?>
        <tr data-searchable>
          <td><?= $u['id'] ?></td>
          <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td>
            <span class="badge <?= $u['role'] === 'organizer' ? 'badge-gold' : 'badge-green' ?>">
              <?= ucfirst($u['role']) ?>
            </span>
          </td>
          <td><?= htmlspecialchars($u['matric_no'] ?: '—') ?></td>
          <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
          <td>
            <form method="POST" action="../controllers/UserController.php" style="margin:0">
              <input type="hidden" name="action" value="delete_user">
              <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
              <button class="btn btn-danger btn-sm" data-confirm="Remove user <?= htmlspecialchars($u['name']) ?>?">🗑 Remove</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- EVENTS TABLE -->
  <div class="section-header mt-3">
    <h2 class="section-title">All <span>Events</span></h2>
  </div>
  <div class="table-wrapper">
    <table>
      <thead><tr><th>Title</th><th>Organizer</th><th>Date</th><th>Category</th><th>Participants</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($allEvents as $ev): ?>
        <tr>
          <td><a href="event_detail.php?id=<?= $ev['id'] ?>" style="color:var(--green);font-weight:600"><?= htmlspecialchars($ev['title']) ?></a></td>
          <td><?= htmlspecialchars($ev['organizer_name']) ?></td>
          <td><?= date('d M Y', strtotime($ev['date'])) ?></td>
          <td><span class="badge badge-<?= strtolower($ev['category']) ?>"><?= $ev['category'] ?></span></td>
          <td><?= $ev['participant_count'] ?>/<?= $ev['max_participants'] ?></td>
          <td><span class="badge badge-green"><?= ucfirst($ev['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<footer><p>&copy; <?= date('Y') ?> <strong>UniEvent IIUM</strong></p></footer>
<script src="../js/app.js"></script>
</body>
</html>
