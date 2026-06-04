<?php
// ============================================================
// MODEL: User — UniEvent IIUM
// Handles all user-related database operations
// ============================================================
require_once __DIR__ . '/../includes/db.php';

class UserModel {

    // Register a new user
    public static function register($name, $email, $password, $role, $matric_no = '') {
        $db = getDB();
        $name     = clean($name);
        $email    = clean($email);
        $role     = in_array($role, ['student','organizer']) ? $role : 'student';
        $matric   = clean($matric_no);
        $hashed   = password_hash($password, PASSWORD_BCRYPT);

        // Check if email already exists
        $check = $db->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $db->close();
            return ['success' => false, 'message' => 'Email already registered.'];
        }
        $check->close();

        $stmt = $db->prepare("INSERT INTO users (name, email, password, role, matric_no) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $name, $email, $hashed, $role, $matric);
        $ok = $stmt->execute();
        $id = $db->insert_id;
        $stmt->close();
        $db->close();

        if ($ok) return ['success' => true, 'user_id' => $id, 'role' => $role];
        return ['success' => false, 'message' => 'Registration failed. Try again.'];
    }

    // Login user
    public static function login($email, $password) {
        $db = getDB();
        $email = clean($email);
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        $db->close();

        if (!$user) return ['success' => false, 'message' => 'Email not found.'];
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Incorrect password.'];
        }

        // Set session
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        return ['success' => true, 'role' => $user['role']];
    }

    // Get all users (admin)
    public static function getAllUsers($role = null) {
        $db = getDB();
        if ($role) {
            $r = clean($role);
            $result = $db->query("SELECT id, name, email, role, matric_no, created_at FROM users WHERE role='$r' ORDER BY created_at DESC");
        } else {
            $result = $db->query("SELECT id, name, email, role, matric_no, created_at FROM users ORDER BY created_at DESC");
        }
        $users = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $db->close();
        return $users;
    }

    // Delete user
    public static function deleteUser($id) {
        $db = getDB();
        $id = intval($id);
        $ok = $db->query("DELETE FROM users WHERE id = $id AND role != 'admin'");
        $db->close();
        return $ok;
    }

    // Update profile
    public static function updateProfile($id, $name, $matric_no) {
        $db = getDB();
        $id     = intval($id);
        $name   = clean($name);
        $matric = clean($matric_no);
        $stmt = $db->prepare("UPDATE users SET name=?, matric_no=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $matric, $id);
        $ok = $stmt->execute();
        $stmt->close();
        $db->close();
        if ($ok) { $_SESSION['user_name'] = $name; }
        return $ok;
    }

    // Change password
    public static function changePassword($id, $old, $new) {
        $db = getDB();
        $id = intval($id);
        $result = $db->query("SELECT password FROM users WHERE id=$id LIMIT 1");
        $user = $result->fetch_assoc();
        if (!password_verify($old, $user['password'])) {
            $db->close();
            return ['success' => false, 'message' => 'Current password is incorrect.'];
        }
        $hashed = password_hash($new, PASSWORD_BCRYPT);
        $stmt = $db->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("si", $hashed, $id);
        $ok = $stmt->execute();
        $stmt->close();
        $db->close();
        return ['success' => $ok, 'message' => $ok ? 'Password updated.' : 'Update failed.'];
    }
}
?>
