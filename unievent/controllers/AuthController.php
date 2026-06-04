<?php
// ============================================================
// CONTROLLER: Auth — UniEvent IIUM
// Routes: /controllers/AuthController.php
// Handles: login, register, logout POST actions
// ============================================================
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../models/UserModel.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

    // ── REGISTER ────────────────────────────────────────────
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('index.php'); break; }
        $name     = $_POST['name'] ?? '';
        $email    = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role     = $_POST['role'] ?? 'student';
        $matric   = $_POST['matric_no'] ?? '';

        if (!$name || !$email || !$password) {
            setFlash('error', 'All fields are required.');
            redirect('pages/register.php'); break;
        }
        if (strlen($password) < 6) {
            setFlash('error', 'Password must be at least 6 characters.');
            redirect('pages/register.php'); break;
        }

        $result = UserModel::register($name, $email, $password, $role, $matric);
        if ($result['success']) {
            // Auto login after register
            UserModel::login($email, $password);
            redirect(getDashboardRoute($_SESSION['user_role']));
        } else {
            setFlash('error', $result['message']);
            redirect('pages/register.php');
        }
        break;

    // ── LOGIN ────────────────────────────────────────────────
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('index.php'); break; }
        $email    = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            setFlash('error', 'Please enter email and password.');
            redirect('index.php'); break;
        }

        $result = UserModel::login($email, $password);
        if ($result['success']) {
            redirect(getDashboardRoute($result['role']));
        } else {
            setFlash('error', $result['message']);
            redirect('index.php');
        }
        break;

    // ── LOGOUT ───────────────────────────────────────────────
    case 'logout':
        session_unset();
        session_destroy();
        header('Location: ../index.php');
        exit();

    default:
        redirect('index.php');
}

// ─── Helpers ─────────────────────────────────────────────────
function setFlash($type, $msg) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $msg];
}

function redirect($path) {
    // Work out relative path from controllers folder
    $base = dirname(dirname($_SERVER['PHP_SELF']));
    if (strpos($path, 'http') === 0) {
        header("Location: $path");
    } else {
        header("Location: /{$path}");
    }
    exit();
}

function getDashboardRoute($role) {
    switch ($role) {
        case 'admin':     return 'pages/dashboard_admin.php';
        case 'organizer': return 'pages/dashboard_organizer.php';
        default:          return 'pages/dashboard_student.php';
    }
}
?>
