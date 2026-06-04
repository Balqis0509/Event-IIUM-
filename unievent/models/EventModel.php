<?php
// ============================================================
// MODEL: Event — UniEvent IIUM
// Handles all event-related database operations
// ============================================================
require_once __DIR__ . '/../includes/db.php';

class EventModel {

    // Get all events with optional filter
    public static function getAllEvents($category = null, $search = null) {
        $db = getDB();
        $sql = "SELECT e.*, u.name AS organizer_name,
                (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) AS participant_count
                FROM events e
                JOIN users u ON e.organizer_id = u.id
                WHERE e.status = 'active'";
        if ($category) {
            $cat = $db->real_escape_string(clean($category));
            $sql .= " AND e.category = '$cat'";
        }
        if ($search) {
            $s = $db->real_escape_string(clean($search));
            $sql .= " AND (e.title LIKE '%$s%' OR e.venue LIKE '%$s%')";
        }
        $sql .= " ORDER BY e.date ASC";
        $result = $db->query($sql);
        $events = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $db->close();
        return $events;
    }

    // Get single event
    public static function getEventById($id) {
        $db = getDB();
        $id = intval($id);
        $result = $db->query("SELECT e.*, u.name AS organizer_name, u.email AS organizer_email,
                (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) AS participant_count
                FROM events e JOIN users u ON e.organizer_id = u.id
                WHERE e.id = $id LIMIT 1");
        $event = $result ? $result->fetch_assoc() : null;
        $db->close();
        return $event;
    }

    // Get events by organizer
    public static function getEventsByOrganizer($organizer_id) {
        $db = getDB();
        $id = intval($organizer_id);
        $result = $db->query("SELECT e.*,
                (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) AS participant_count
                FROM events e WHERE e.organizer_id = $id ORDER BY e.date ASC");
        $events = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $db->close();
        return $events;
    }

    // Create event
    public static function createEvent($data, $organizer_id) {
        $db = getDB();
        $title   = clean($data['title']);
        $desc    = clean($data['description']);
        $date    = clean($data['date']);
        $time    = clean($data['time']);
        $venue   = clean($data['venue']);
        $cat     = clean($data['category']);
        $max     = intval($data['max_participants']);
        $poster  = clean($data['poster'] ?? 'default_event.png');
        $oid     = intval($organizer_id);

        $stmt = $db->prepare("INSERT INTO events (organizer_id,title,description,date,time,venue,category,poster,max_participants)
                              VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("isssssssi", $oid, $title, $desc, $date, $time, $venue, $cat, $poster, $max);
        $ok = $stmt->execute();
        $new_id = $db->insert_id;
        $stmt->close();
        $db->close();
        return $ok ? ['success' => true, 'event_id' => $new_id] : ['success' => false, 'message' => 'Failed to create event.'];
    }

    // Update event
    public static function updateEvent($id, $data, $organizer_id) {
        $db = getDB();
        $id    = intval($id);
        $oid   = intval($organizer_id);
        $title = clean($data['title']);
        $desc  = clean($data['description']);
        $date  = clean($data['date']);
        $time  = clean($data['time']);
        $venue = clean($data['venue']);
        $cat   = clean($data['category']);
        $max   = intval($data['max_participants']);
        $poster = clean($data['poster'] ?? '');

        if ($poster) {
            $stmt = $db->prepare("UPDATE events SET title=?,description=?,date=?,time=?,venue=?,category=?,max_participants=?,poster=? WHERE id=? AND organizer_id=?");
            $stmt->bind_param("ssssssisii", $title,$desc,$date,$time,$venue,$cat,$max,$poster,$id,$oid);
        } else {
            $stmt = $db->prepare("UPDATE events SET title=?,description=?,date=?,time=?,venue=?,category=?,max_participants=? WHERE id=? AND organizer_id=?");
            $stmt->bind_param("ssssssiiii", $title,$desc,$date,$time,$venue,$cat,$max,$id,$oid);
        }
        $ok = $stmt->execute();
        $stmt->close();
        $db->close();
        return $ok;
    }

    // Delete event
    public static function deleteEvent($id, $organizer_id) {
        $db = getDB();
        $id  = intval($id);
        $oid = intval($organizer_id);
        $ok = $db->query("DELETE FROM events WHERE id=$id AND organizer_id=$oid");
        $db->close();
        return $ok;
    }

    // Join event (student)
    public static function joinEvent($event_id, $student_id) {
        $db = getDB();
        $eid = intval($event_id);
        $sid = intval($student_id);

        // Check capacity
        $result = $db->query("SELECT max_participants, (SELECT COUNT(*) FROM registrations WHERE event_id=$eid) AS count FROM events WHERE id=$eid");
        $row = $result->fetch_assoc();
        if ($row['count'] >= $row['max_participants']) {
            $db->close();
            return ['success' => false, 'message' => 'Event is fully booked.'];
        }

        $stmt = $db->prepare("INSERT IGNORE INTO registrations (event_id, student_id) VALUES (?,?)");
        $stmt->bind_param("ii", $eid, $sid);
        $ok = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        $db->close();
        if ($affected === 0) return ['success' => false, 'message' => 'You have already joined this event.'];
        return ['success' => true, 'message' => 'Successfully registered!'];
    }

    // Leave event
    public static function leaveEvent($event_id, $student_id) {
        $db = getDB();
        $eid = intval($event_id);
        $sid = intval($student_id);
        $ok = $db->query("DELETE FROM registrations WHERE event_id=$eid AND student_id=$sid");
        $db->close();
        return $ok;
    }

    // Get events joined by student
    public static function getStudentEvents($student_id) {
        $db = getDB();
        $sid = intval($student_id);
        $result = $db->query("SELECT e.*, r.status AS reg_status, r.registered_at,
                u.name AS organizer_name
                FROM registrations r
                JOIN events e ON r.event_id = e.id
                JOIN users u ON e.organizer_id = u.id
                WHERE r.student_id = $sid
                ORDER BY e.date ASC");
        $events = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $db->close();
        return $events;
    }

    // Get participants of an event
    public static function getParticipants($event_id) {
        $db = getDB();
        $eid = intval($event_id);
        $result = $db->query("SELECT u.id, u.name, u.email, u.matric_no, r.status, r.registered_at
                FROM registrations r JOIN users u ON r.student_id = u.id
                WHERE r.event_id = $eid ORDER BY r.registered_at ASC");
        $participants = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $db->close();
        return $participants;
    }

    // Check if student joined
    public static function isJoined($event_id, $student_id) {
        $db = getDB();
        $eid = intval($event_id); $sid = intval($student_id);
        $result = $db->query("SELECT id FROM registrations WHERE event_id=$eid AND student_id=$sid LIMIT 1");
        $joined = $result && $result->num_rows > 0;
        $db->close();
        return $joined;
    }

    // Get upcoming events count for dashboard
    public static function getUpcomingCount() {
        $db = getDB();
        $result = $db->query("SELECT COUNT(*) as c FROM events WHERE date >= CURDATE() AND status='active'");
        $row = $result->fetch_assoc();
        $db->close();
        return $row['c'] ?? 0;
    }

    // Total registrations
    public static function getTotalRegistrations() {
        $db = getDB();
        $result = $db->query("SELECT COUNT(*) as c FROM registrations");
        $row = $result->fetch_assoc();
        $db->close();
        return $row['c'] ?? 0;
    }
}
?>
