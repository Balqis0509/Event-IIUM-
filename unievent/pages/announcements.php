<?php
// ============================================================
// VIEW: Announcements — UniEvent IIUM
// Route: /pages/announcements.php
// ============================================================
require_once '../includes/db.php';
requireLogin();

$role = $_SESSION['user_role'];

// Handle POST: create announcement (organizer/admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($role, ['organizer','admin'])) {
    $title   = clean($_POST['title'] ?? '');
    $content = clean($_POST['content'] ?? '');
    $author  = intval($_SESSION['user_id']);
    if ($title && $content) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO announcements (author_id, title, content) VALUES (?,?,?)");
        $stmt->bind_param("iss", $author, $title, $content);
        $stmt->execute();
        $stmt->close();
        $db->close();
        $_SESSION['flash'] = ['type'=>'success','message'=>'Announcement posted!'];
    }
    header('Location: announcements.php'); exit();
}

// Fetch all announcements
$db = getDB();
$result = $db->query("SELECT a.*, u.name AS author_name, u.role AS author_role
    FROM announcements a JOIN users u ON a.author_id = u.id
    ORDER BY a.created_at DESC");
$announcements = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Announcements — UniEvent IIUM</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="page-hero">
  <div class="hero-badge">Updates</div>
  <h1>📢 Announcements</h1>
  <p>Stay up-to-date with the latest news and event updates.</p>
</div>

<div class="container page-body">
  <?php include '../includes/flash.php'; ?>

  <!-- POST FORM (organizer/admin only) -->
  <?php if (in_array($role, ['organizer','admin'])): ?>
  <div class="card mb-3">
    <div class="card-body">
      <h3 style="font-family:var(--font-head);color:var(--green);margin-bottom:1rem">📝 Post New Announcement</h3>
      <form method="POST" data-validate>
        <div class="form-group">
          <label class="form-label">Title *</label>
          <input type="text" name="title" class="form-control" placeholder="Announcement title..." required>
        </div>
        <div class="form-group">
          <label class="form-label">Message *</label>
          <textarea name="content" class="form-control" placeholder="Write your announcement here..." required style="min-height:100px"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">📣 Post Announcement</button>
      </form>
    </div>
  </div>
  <?php endif; ?>

  <!-- ANNOUNCEMENTS LIST -->
  <?php if (empty($announcements)): ?>
  <div class="empty-state">
    <div class="empty-state-icon">📭</div>
    <h3>No announcements yet</h3>
    <p>Check back later for updates.</p>
  </div>
  <?php else: ?>
  <div style="display:flex;flex-direction:column;gap:1rem">
    <?php foreach ($announcements as $a):
      $isNew = strtotime($a['created_at']) > time() - 86400 * 3; // within 3 days
    ?>
    <div class="card" style="border-left:4px solid <?= $a['author_role']==='admin' ? '#C9A84C' : 'var(--green)' ?>">
      <div class="card-body" style="padding:1.5rem">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:0.5rem;margin-bottom:0.8rem">
          <h3 style="font-family:var(--font-head);font-size:1.15rem;color:var(--text)">
            <?= htmlspecialchars($a['title']) ?>
            <?php if ($isNew): ?><span class="badge badge-green" style="font-size:0.7rem;margin-left:0.5rem">NEW</span><?php endif; ?>
          </h3>
          <div style="font-size:0.8rem;color:var(--text-muted);white-space:nowrap">
            <?= date('d M Y, H:i', strtotime($a['created_at'])) ?>
          </div>
        </div>
        <p style="color:var(--text);line-height:1.75;margin-bottom:0.8rem"><?= nl2br(htmlspecialchars($a['content'])) ?></p>
        <div style="display:flex;align-items:center;gap:0.5rem;font-size:0.82rem;color:var(--text-muted)">
          <div style="width:28px;height:28px;background:var(--green-light);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--green)">
            <?= strtoupper(substr($a['author_name'],0,1)) ?>
          </div>
          <span><strong><?= htmlspecialchars($a['author_name']) ?></strong> &mdash;
            <span class="badge badge-<?= $a['author_role']==='admin'?'gold':'green' ?>" style="font-size:0.7rem"><?= ucfirst($a['author_role']) ?></span>
          </span>
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
