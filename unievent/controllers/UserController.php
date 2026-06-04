<?php
// ============================================================
// CONTROLLER: User — UniEvent IIUM
// Routes: /controllers/UserController.php
// Handles: profile update, password change, admin user management
// ============================================================
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../models/UserModel.php';

requireLogin();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

    // ── UPDATE PROFILE ────────────────────────────────────────
    case 'update_profile':
        $name   = $_POST['name'] ?? '';
        $matric = $_POST['matric_no'] ?? '';
        if (!$name) {
            setFlash('error', 'Name is required.');
        } else {
            UserModel::updateProfile($_SESSION['user_id'], $name, $matric);
            setFlash('success', 'Profile updated successfully!');
        }
        header('Location: ../pages/profile.php'); exit();

    // ── CHANGE PASSWORD ───────────────────────────────────────
    case 'change_password':
        $old = $_POST['old_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if ($new !== $confirm) {
            setFlash('error', 'New passwords do not match.');
        } elseif (strlen($new) < 6) {
            setFlash('error', 'Password must be at least 6 characters.');
        } else {
            $result = UserModel::changePassword($_SESSION['user_id'], $old, $new);
            setFlash($result['success'] ? 'success' : 'error', $result['message']);
        }
        header('Location: ../pages/profile.php'); exit();

    // ── ADMIN: DELETE USER ────────────────────────────────────
    case 'delete_user':
        if ($_SESSION['user_role'] !== 'admin') {
            setFlash('error', 'Unauthorized.'); header('Location: ../pages/dashboard_admin.php'); exit();
        }
        $user_id = intval($_POST['user_id'] ?? 0);
        UserModel::deleteUser($user_id);
        setFlash('success', 'User removed.');
        header('Location: ../pages/dashboard_admin.php'); exit();

    default:
        header('Location: ../pages/profile.php'); exit();
}

function setFlash($type, $msg) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $msg];
}
?>
