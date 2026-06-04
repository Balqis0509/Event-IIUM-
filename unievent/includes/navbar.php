<?php
// ============================================================
// INCLUDE: Navbar — UniEvent IIUM
// ============================================================
$role = $_SESSION['user_role'] ?? '';
$name = $_SESSION['user_name'] ?? '';
$initial = strtoupper(substr($name, 0, 1));

// Determine active page
$current = basename($_SERVER['PHP_SELF']);
$base = (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../' : '';
?>
<nav class="navbar">
  <a href="<?= $base ?>index.php" class="navbar-brand">
    <div class="navbar-logo-icon">🎓</div>
    <div>
      <div class="navbar-brand-text">UniEvent</div>
      <div class="navbar-brand-sub">IIUM Event System</div>
    </div>
  </a>
  <div class="navbar-spacer"></div>

  <ul class="navbar-nav" id="main-nav">
    <?php if ($role === 'student'): ?>
      <li><a href="<?= $base ?>pages/events.php" class="<?= $current==='events.php'?'active':'' ?>">📅 Events</a></li>
      <li><a href="<?= $base ?>pages/my_events.php" class="<?= $current==='my_events.php'?'active':'' ?>">📌 My Events</a></li>
      <li><a href="<?= $base ?>pages/announcements.php" class="<?= $current==='announcements.php'?'active':'' ?>">📢 Announcements</a></li>
      <li><a href="<?= $base ?>pages/dashboard_student.php" class="<?= $current==='dashboard_student.php'?'active':'' ?>">🏠 Dashboard</a></li>
    <?php elseif ($role === 'organizer'): ?>
      <li><a href="<?= $base ?>pages/events.php" class="<?= $current==='events.php'?'active':'' ?>">📅 Events</a></li>
      <li><a href="<?= $base ?>pages/create_event.php" class="<?= $current==='create_event.php'?'active':'' ?>">➕ Create Event</a></li>
      <li><a href="<?= $base ?>pages/announcements.php" class="<?= $current==='announcements.php'?'active':'' ?>">📢 Announcements</a></li>
      <li><a href="<?= $base ?>pages/dashboard_organizer.php" class="<?= $current==='dashboard_organizer.php'?'active':'' ?>">🏠 Dashboard</a></li>
    <?php elseif ($role === 'admin'): ?>
      <li><a href="<?= $base ?>pages/events.php" class="<?= $current==='events.php'?'active':'' ?>">📅 Events</a></li>
      <li><a href="<?= $base ?>pages/dashboard_admin.php" class="<?= $current==='dashboard_admin.php'?'active':'' ?>">🛠 Admin</a></li>
    <?php else: ?>
      <li><a href="<?= $base ?>index.php">🔐 Login</a></li>
      <li><a href="<?= $base ?>pages/register.php">📝 Register</a></li>
    <?php endif; ?>
  </ul>

  <?php if ($role): ?>
  <div class="navbar-user">
    <div class="navbar-avatar"><?= $initial ?></div>
    <a href="<?= $base ?>pages/profile.php" class="navbar-username" style="text-decoration:none"><?= htmlspecialchars($name) ?></a>
    <form method="POST" action="<?= $base ?>controllers/AuthController.php" style="margin:0">
      <input type="hidden" name="action" value="logout">
      <button type="submit" class="btn-logout">Logout</button>
    </form>
  </div>
  <?php endif; ?>

  <button class="hamburger" id="hamburger">☰</button>
</nav>
