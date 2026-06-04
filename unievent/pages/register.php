<?php
// ============================================================
// VIEW: Register Page — UniEvent IIUM
// Route: /pages/register.php
// ============================================================
require_once '../includes/db.php';
if (isLoggedIn()) { header('Location: ../index.php'); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register — UniEvent IIUM</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="auth-wrapper">
  <div class="auth-card" style="max-width:520px">
    <div class="auth-header">
      <div class="auth-logo">🎓</div>
      <h1 class="auth-title">Create Account</h1>
      <p class="auth-subtitle">Join UniEvent IIUM today</p>
    </div>
    <div class="auth-body">
      <?php include '../includes/flash.php'; ?>
      <form method="POST" action="../controllers/AuthController.php" data-validate>
        <input type="hidden" name="action" value="register">

        <div class="form-group">
          <label class="form-label" for="name">Full Name</label>
          <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Ahmad Faiz bin Abdullah" required>
          <div class="form-error"></div>
        </div>

        <div class="form-group">
          <label class="form-label" for="email">IIUM Email</label>
          <input type="email" id="email" name="email" class="form-control" placeholder="student@iium.edu.my" required>
          <div class="form-error"></div>
        </div>

        <div class="form-group">
          <label class="form-label" for="matric_no">Matric Number (Optional)</label>
          <input type="text" id="matric_no" name="matric_no" class="form-control" placeholder="e.g. S2110234">
        </div>

        <div class="form-group">
          <label class="form-label">Register As</label>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.8rem">
            <label style="cursor:pointer">
              <input type="radio" name="role" value="student" checked style="display:none" class="role-radio">
              <div class="role-option" data-val="student" style="border:2px solid #006747;border-radius:10px;padding:1rem;text-align:center;background:#e8f5ee">
                <div style="font-size:2rem">👨‍🎓</div>
                <div style="font-weight:600;color:#006747">Student</div>
                <div style="font-size:0.75rem;color:#5a7a66">Browse & join events</div>
              </div>
            </label>
            <label style="cursor:pointer">
              <input type="radio" name="role" value="organizer" style="display:none" class="role-radio">
              <div class="role-option" data-val="organizer" style="border:2px solid #ddd;border-radius:10px;padding:1rem;text-align:center">
                <div style="font-size:2rem">🧑‍💼</div>
                <div style="font-weight:600;color:#555">Organizer</div>
                <div style="font-size:0.75rem;color:#888">Create & manage events</div>
              </div>
            </label>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div style="position:relative">
            <input type="password" id="password" name="password" class="form-control" placeholder="Min. 6 characters" required minlength="6">
            <button type="button" class="pw-toggle" data-target="#password" style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer">👁</button>
          </div>
          <div class="form-error"></div>
        </div>

        <div class="form-group">
          <label class="form-label" for="confirm_password">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Re-enter password" required>
          <div class="form-error"></div>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg">🚀 Create Account</button>
      </form>
      <div class="auth-footer">
        Already have an account? <a href="../index.php">Sign in</a>
      </div>
    </div>
  </div>
</div>
<script src="../js/app.js"></script>
<script>
// Role selector UI
document.querySelectorAll('.role-radio').forEach(radio => {
  radio.addEventListener('change', function() {
    document.querySelectorAll('.role-option').forEach(opt => {
      opt.style.border = '2px solid #ddd';
      opt.style.background = '';
      opt.querySelector('div').style.color = '#555';
    });
    const opt = this.nextElementSibling;
    opt.style.border = '2px solid #006747';
    opt.style.background = '#e8f5ee';
    opt.querySelector('div').style.color = '#006747';
  });
});
</script>
</body>
</html>
