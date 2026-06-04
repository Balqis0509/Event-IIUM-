<?php
// ============================================================
// CONTROLLER: Event — UniEvent IIUM
// Routes: /controllers/EventController.php
// Handles: create, edit, delete, join, leave events
// ============================================================
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../models/EventModel.php';

requireLogin();

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

    // ── CREATE EVENT ─────────────────────────────────────────
    case 'create':
        if ($_SESSION['user_role'] !== 'organizer') {
            setFlash('error', 'Access denied.');
            header('Location: ../pages/events.php'); exit();
        }
        $poster = handlePosterUpload();
        $data = [
            'title'           => $_POST['title'] ?? '',
            'description'     => $_POST['description'] ?? '',
            'date'            => $_POST['date'] ?? '',
            'time'            => $_POST['time'] ?? '',
            'venue'           => $_POST['venue'] ?? '',
            'category'        => $_POST['category'] ?? 'Other',
            'max_participants' => $_POST['max_participants'] ?? 100,
            'poster'          => $poster,
        ];
        if (!$data['title'] || !$data['date'] || !$data['venue']) {
            setFlash('error', 'Title, date and venue are required.');
            header('Location: ../pages/create_event.php'); exit();
        }
        $result = EventModel::createEvent($data, $_SESSION['user_id']);
        if ($result['success']) {
            setFlash('success', 'Event created successfully!');
            header('Location: ../pages/event_detail.php?id=' . $result['event_id']); exit();
        } else {
            setFlash('error', $result['message']);
            header('Location: ../pages/create_event.php'); exit();
        }
        break;

    // ── UPDATE EVENT ─────────────────────────────────────────
    case 'update':
        $event_id = intval($_POST['event_id'] ?? 0);
        $poster = handlePosterUpload();
        $data = [
            'title'           => $_POST['title'] ?? '',
            'description'     => $_POST['description'] ?? '',
            'date'            => $_POST['date'] ?? '',
            'time'            => $_POST['time'] ?? '',
            'venue'           => $_POST['venue'] ?? '',
            'category'        => $_POST['category'] ?? 'Other',
            'max_participants' => $_POST['max_participants'] ?? 100,
            'poster'          => $poster,
        ];
        $ok = EventModel::updateEvent($event_id, $data, $_SESSION['user_id']);
        if ($ok) {
            setFlash('success', 'Event updated successfully!');
        } else {
            setFlash('error', 'Failed to update event.');
        }
        header('Location: ../pages/event_detail.php?id=' . $event_id); exit();

    // ── DELETE EVENT ─────────────────────────────────────────
    case 'delete':
        $event_id = intval($_POST['event_id'] ?? $_GET['id'] ?? 0);
        $ok = EventModel::deleteEvent($event_id, $_SESSION['user_id']);
        setFlash($ok ? 'success' : 'error', $ok ? 'Event deleted.' : 'Failed to delete event.');
        header('Location: ../pages/dashboard_organizer.php'); exit();

    // ── JOIN EVENT ────────────────────────────────────────────
    case 'join':
        $event_id = intval($_POST['event_id'] ?? 0);
        $result = EventModel::joinEvent($event_id, $_SESSION['user_id']);
        setFlash($result['success'] ? 'success' : 'error', $result['message']);
        header('Location: ../pages/event_detail.php?id=' . $event_id); exit();

    // ── LEAVE EVENT ───────────────────────────────────────────
    case 'leave':
        $event_id = intval($_POST['event_id'] ?? 0);
        EventModel::leaveEvent($event_id, $_SESSION['user_id']);
        setFlash('success', 'You have left the event.');
        header('Location: ../pages/my_events.php'); exit();

    default:
        header('Location: ../pages/events.php'); exit();
}

// ─── Helpers ─────────────────────────────────────────────────
function handlePosterUpload() {
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === 0) {
        $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
        if (!in_array($_FILES['poster']['type'], $allowed)) return 'default_event.png';
        $ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
        $filename = 'event_' . time() . '_' . rand(100,999) . '.' . $ext;
        $dest = __DIR__ . '/../images/uploads/' . $filename;
        if (!is_dir(dirname($dest))) mkdir(dirname($dest), 0755, true);
        if (move_uploaded_file($_FILES['poster']['tmp_name'], $dest)) return 'uploads/' . $filename;
    }
    return '';
}

function setFlash($type, $msg) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $msg];
}
?>
