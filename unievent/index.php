<?php
// ============================================================
// VIEW: Login Page — UniEvent IIUM (index.php)
// Route: /index.php
// ============================================================
require_once 'includes/db.php';

// Already logged in — redirect to dashboard
if (isLoggedIn()) {
    $role = $_SESSION['user_role'];
    if ($role === 'admin')     header('Location: pages/dashboard_admin.php');
    elseif ($role === 'organizer') header('Location: pages/dashboard_organizer.php');
    else header('Location: pages/dashboard_student.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UniEvent IIUM — Login</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .login-split { display: grid; grid-template-columns: 1fr 1fr; min-height: 100vh; }
    .login-left {
      background: linear-gradient(145deg, #004d35 0%, #006747 50%, #1a8a5a 100%);
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      padding: 3rem; color: white; position: relative; overflow: hidden;
    }
    .login-left::before {
      content: '';
      position: absolute; width: 500px; height: 500px;
      background: rgba(255,255,255,0.04);
      border-radius: 50%; top: -100px; right: -100px;
    }
    .login-left::after {
      content: '';
      position: absolute; width: 300px; height: 300px;
      background: rgba(255,255,255,0.04);
      border-radius: 50%; bottom: -80px; left: -50px;
    }
    .login-right { display: flex; align-items: center; justify-content: center; padding: 2rem; background: #f4f7f4; }
    .hero-text { text-align: center; position: relative; z-index: 1; }
    .hero-icon { font-size: 5rem; margin-bottom: 1rem; }
    .hero-title { font-family: 'Playfair Display', serif; font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem; }
    .hero-sub { font-size: 1rem; opacity: 0.8; margin-bottom: 2rem; }
    .feature-list { list-style: none; text-align: left; }
    .feature-list li { padding: 0.5rem 0; display: flex; align-items: center; gap: 0.6rem; font-size: 0.92rem; opacity: 0.9; }
    .login-card { background: white; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,103,71,0.12); padding: 2.5rem; width: 100%; max-width: 420px; }
    .login-card-title { font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 700; color: #1a2e22; margin-bottom: 0.3rem; }
    .login-card-sub { color: #5a7a66; font-size: 0.9rem; margin-bottom: 1.8rem; }
    .demo-accounts { background: #e8f5ee; border-radius: 10px; padding: 1rem; margin-top: 1.5rem; font-size: 0.82rem; }
    .demo-accounts strong { color: #006747; display: block; margin-bottom: 0.4rem; }
    .demo-row { display: flex; justify-content: space-between; padding: 0.2rem 0; color: #5a7a66; }
    @media (max-width: 768px) {
      .login-split { grid-template-columns: 1fr; }
      .login-left { display: none; }
    }
  </style>
</head>
<body>
<div class="login-split">
  <!-- LEFT PANEL -->
  <div class="login-left">
    <div class="hero-text">
      <div class="hero-icon">🎓</div>
      <h1 class="hero-title">UniEvent</h1>
      <p class="hero-sub">IIUM Event Management System</p>
      <ul class="feature-list">
        <li>✅ Browse & join campus events</li>
        <li>✅ Create and manage your events</li>
        <li>✅ Track participant registrations</li>
        <li>✅ Stay updated with announcements</li>
        <li>✅ View events on a calendar</li>
      </ul>
    </div>
  </div>

  <!-- RIGHT PANEL -->
  <div class="login-right">
    <div class="login-card">
      <h2 class="login-card-title">Welcome Back 👋</h2>
      <p class="login-card-sub">Sign in to your UniEvent account</p>

      <?php include 'includes/flash.php'; ?>

      <form method="POST" action="controllers/AuthController.php" data-validate>
        <input type="hidden" name="action" value="login">
        <div class="form-group">
          <label class="form-label" for="email">📧 Email Address</label>
          <div class="input-group">
            <input type="email" id="email" name="email" class="form-control" placeholder="student@iium.edu.my" required autocomplete="email">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="password">🔒 Password</label>
          <div class="input-group" style="position:relative">
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
            <button type="button" class="pw-toggle" data-target="#password"
              style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:1.1rem">👁</button>
          </div>
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:0.5rem">
          🔐 Sign In
        </button>
      </form>

      <div class="auth-footer">
        Don't have an account? <a href="pages/register.php">Register here</a>
      </div>

      <div class="demo-accounts">
        <strong>🧪 Demo Accounts</strong>
        <div class="demo-row"><span>Admin:</span><span>admin@iium.edu.my / password</span></div>
        <div class="demo-row"><span>Organizer:</span><span>faiz@iium.edu.my / organizer123</span></div>
        <div class="demo-row"><span>Student:</span><span>aina@iium.edu.my / organizer123</span></div>
      </div>
    </div>
  </div>
</div>
<script src="js/app.js"></script>
</body>
</html>
