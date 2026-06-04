<?php
// ============================================================
// VIEW: Profile Page — UniEvent IIUM
// Route: /pages/profile.php
// ============================================================
require_once '../includes/db.php';
require_once '../models/UserModel.php';
requireLogin();
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile — UniEvent IIUM</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="page-hero">
  <div class="hero-badge">Account</div>
  <h1>My Profile ⚙️</h1>
  <p>Manage your account information and preferences.</p>
</div>

<div class="container page-body">
  <?php include '../includes/flash.php'; ?>

  <div style="display:grid;grid-template-columns:300px 1fr;gap:2rem;align-items:flex-start">

    <!-- LEFT: Avatar Card -->
    <div>
      <div class="card text-center">
        <div class="card-body" style="padding:2rem">
          <div style="width:90px;height:90px;background:var(--green);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2.5rem;font-weight:700;color:white;margin:0 auto 1rem">
            <?= strtoupper(substr($user['name'],0,1)) ?>
          </div>
          <h3 style="font-family:var(--font-head);font-size:1.3rem;margin-bottom:0.3rem"><?= htmlspecialchars($user['name']) ?></h3>
          <span class="badge <?= $user['role']==='organizer'?'badge-gold':($user['role']==='admin'?'badge-workshop':'badge-green') ?>">
            <?= ucfirst($user['role']) ?>
          </span>
          <div style="margin-top:1rem;font-size:0.85rem;color:var(--text-muted)">
            <div style="margin-bottom:0.4rem">📧 <?= htmlspecialchars($user['email']) ?></div>
            <?php if ($user['matric_no']): ?>
            <div>🎓 <?= htmlspecialchars($user['matric_no']) ?></div>
            <?php endif; ?>
            <div style="margin-top:0.6rem">Joined <?= date('M Y', strtotime($user['created_at'])) ?></div>
          </div>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="card mt-2">
        <div class="card-body" style="padding:1.2rem">
          <div style="font-weight:700;font-size:0.85rem;color:var(--text-muted);text-transform:uppercase;margin-bottom:0.8rem">Quick Links</div>
          <?php if ($user['role']==='student'): ?>
            <a href="events.php" class="btn btn-outline btn-sm btn-block mb-1">📅 Browse Events</a>
            <a href="my_events.php" class="btn btn-outline btn-sm btn-block mb-1">📌 My Events</a>
          <?php elseif ($user['role']==='organizer'): ?>
            <a href="dashboard_organizer.php" class="btn btn-outline btn-sm btn-block mb-1">🏠 Dashboard</a>
            <a href="create_event.php" class="btn btn-gold btn-sm btn-block mb-1">➕ Create Event</a>
          <?php endif; ?>
          <form method="POST" action="../controllers/AuthController.php" style="margin:0">
            <input type="hidden" name="action" value="logout">
            <button class="btn btn-danger btn-sm btn-block">🚪 Logout</button>
          </form>
        </div>
      </div>
    </div>

    <!-- RIGHT: Edit Forms -->
    <div>
      <!-- Edit Profile -->
      <div class="card mb-2">
        <div class="card-body" style="padding:2rem">
          <h2 style="font-family:var(--font-head);font-size:1.2rem;color:var(--green);margin-bottom:1.5rem">✏️ Edit Profile</h2>
          <form method="POST" action="../controllers/UserController.php" data-validate>
            <input type="hidden" name="action" value="update_profile">
            <div class="form-group">
              <label class="form-label">Full Name</label>
              <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Email Address</label>
              <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled style="background:#f5f5f5;color:#888">
              <div class="form-hint">Email cannot be changed.</div>
            </div>
            <div class="form-group">
              <label class="form-label">Matric Number</label>
              <input type="text" name="matric_no" class="form-control" value="<?= htmlspecialchars($user['matric_no'] ?? '') ?>" placeholder="e.g. S2110234">
            </div>
            <button type="submit" class="btn btn-primary">💾 Save Profile</button>
          </form>
        </div>
      </div>

      <!-- Change Password -->
      <div class="card">
        <div class="card-body" style="padding:2rem">
          <h2 style="font-family:var(--font-head);font-size:1.2rem;color:var(--green);margin-bottom:1.5rem">🔒 Change Password</h2>
          <form method="POST" action="../controllers/UserController.php" data-validate>
            <input type="hidden" name="action" value="change_password">
            <div class="form-group">
              <label class="form-label">Current Password</label>
              <div style="position:relative">
                <input type="password" name="old_password" id="old_pw" class="form-control" placeholder="Your current password" required>
                <button type="button" class="pw-toggle" data-target="#old_pw" style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer">👁</button>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">New Password</label>
              <input type="password" name="new_password" id="password" class="form-control" placeholder="Min. 6 characters" required minlength="6">
            </div>
            <div class="form-group">
              <label class="form-label">Confirm New Password</label>
              <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Re-enter new password" required>
              <div class="form-error"></div>
            </div>
            <button type="submit" class="btn btn-primary">🔑 Update Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<footer><p>&copy; <?= date('Y') ?> <strong>UniEvent IIUM</strong></p></footer>
<script src="../js/app.js"></script>
<style>
@media (max-width: 768px) {
  .container .page-body > div[style*="300px"] { grid-template-columns: 1fr !important; }
}
</style>
</body>
</html>
