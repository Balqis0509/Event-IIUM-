<?php
// ============================================================
// VIEW: Calendar — UniEvent IIUM
// Route: /pages/calendar.php
// ============================================================
require_once '../includes/db.php';
require_once '../models/EventModel.php';
requireLogin();

// Fetch all upcoming event dates for the calendar
$db = getDB();
$result = $db->query("SELECT date, title, category, id FROM events WHERE status='active' ORDER BY date ASC");
$eventsByDate = [];
$allDates = [];
while ($row = $result->fetch_assoc()) {
    $eventsByDate[$row['date']][] = $row;
    $allDates[] = $row['date'];
}
$db->close();
$allDatesJson = json_encode(array_unique($allDates));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Event Calendar — UniEvent IIUM</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    .cal-events-panel {
      background: var(--card);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      border: 1px solid var(--border);
      padding: 1.5rem;
      min-height: 200px;
    }
    .cal-event-item {
      display: flex;
      align-items: flex-start;
      gap: 0.8rem;
      padding: 0.8rem;
      border-radius: var(--radius-sm);
      border: 1px solid var(--border);
      margin-bottom: 0.7rem;
      transition: var(--transition);
      cursor: pointer;
      text-decoration: none;
      color: inherit;
    }
    .cal-event-item:hover { background: var(--green-light); border-color: var(--green); }
    .cal-dot { width: 12px; height: 12px; border-radius: 50%; background: var(--green); flex-shrink: 0; margin-top: 4px; }
    .clickable-day { cursor: pointer; }
    .clickable-day:hover { background: var(--green-light) !important; }
    .selected-day { background: var(--gold-light) !important; border: 2px solid var(--gold) !important; font-weight: 700; }
  </style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="page-hero">
  <div class="hero-badge">Schedule</div>
  <h1>📆 Event Calendar</h1>
  <p>Browse events by date. Click any highlighted date to view events.</p>
</div>

<div class="container page-body">
  <?php include '../includes/flash.php'; ?>

  <div style="display:grid;grid-template-columns:1fr 360px;gap:2rem;align-items:flex-start">

    <!-- CALENDAR -->
    <div>
      <div id="calendar-container" data-events='<?= $allDatesJson ?>'></div>

      <!-- Event count summary -->
      <div style="margin-top:1.2rem;display:flex;gap:1rem;flex-wrap:wrap">
        <div style="display:flex;align-items:center;gap:0.4rem;font-size:0.85rem;color:var(--text-muted)">
          <div style="width:12px;height:12px;border-radius:50%;background:var(--gold)"></div> Has events
        </div>
        <div style="display:flex;align-items:center;gap:0.4rem;font-size:0.85rem;color:var(--text-muted)">
          <div style="width:12px;height:12px;border-radius:50%;background:var(--green)"></div> Today
        </div>
      </div>
    </div>

    <!-- EVENTS PANEL -->
    <div>
      <div class="cal-events-panel" id="events-panel">
        <h3 style="font-family:var(--font-head);color:var(--green);margin-bottom:1rem" id="panel-title">All Upcoming Events</h3>
        <div id="panel-content">
          <?php if (empty($eventsByDate)): ?>
            <div class="empty-state" style="padding:2rem 0">
              <div class="empty-state-icon">📭</div>
              <p>No upcoming events.</p>
            </div>
          <?php else: ?>
            <?php foreach ($eventsByDate as $date => $evs): ?>
              <?php if ($date >= date('Y-m-d')): ?>
              <div style="font-size:0.78rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;margin:0.8rem 0 0.4rem">
                <?= date('l, d F Y', strtotime($date)) ?>
              </div>
              <?php foreach ($evs as $ev): ?>
              <a href="event_detail.php?id=<?= $ev['id'] ?>" class="cal-event-item">
                <div class="cal-dot"></div>
                <div>
                  <div style="font-weight:600;font-size:0.9rem"><?= htmlspecialchars($ev['title']) ?></div>
                  <span class="badge badge-<?= strtolower($ev['category']) ?>" style="margin-top:3px"><?= $ev['category'] ?></span>
                </div>
              </a>
              <?php endforeach; ?>
              <?php endif; ?>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<footer><p>&copy; <?= date('Y') ?> <strong>UniEvent IIUM</strong></p></footer>
<script src="../js/app.js"></script>
<script>
// Event data from PHP
const eventsByDate = <?= json_encode($eventsByDate) ?>;

// After calendar renders, attach click handlers to days with events
function attachDayClicks() {
  document.querySelectorAll('.cal-day.has-event').forEach(day => {
    day.classList.add('clickable-day');
    day.addEventListener('click', function() {
      // Get date from month/year context
      const calHeader = document.querySelector('.calendar-header span');
      const [monthName, year] = calHeader.textContent.split(' ');
      const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
      const month = String(months.indexOf(monthName) + 1).padStart(2,'0');
      const d = String(parseInt(this.textContent)).padStart(2,'0');
      const dateStr = `${year}-${month}-${d}`;

      // Highlight selected day
      document.querySelectorAll('.cal-day').forEach(c => c.classList.remove('selected-day'));
      this.classList.add('selected-day');

      // Update panel
      const panel = document.getElementById('panel-content');
      const title = document.getElementById('panel-title');
      const events = eventsByDate[dateStr];

      if (events && events.length > 0) {
        title.textContent = new Date(dateStr + 'T00:00:00').toLocaleDateString('en-MY', {weekday:'long', day:'numeric', month:'long', year:'numeric'});
        panel.innerHTML = events.map(ev => `
          <a href="event_detail.php?id=${ev.id}" class="cal-event-item">
            <div class="cal-dot"></div>
            <div>
              <div style="font-weight:600;font-size:0.9rem">${ev.title}</div>
              <span class="badge badge-${ev.category.toLowerCase()}" style="margin-top:3px">${ev.category}</span>
            </div>
          </a>
        `).join('');
      }
    });
  });
}

// Override calPrev/calNext to re-attach after re-render
const origPrev = window.calPrev;
const origNext = window.calNext;
window.calPrev = () => { origPrev(); setTimeout(attachDayClicks, 50); };
window.calNext = () => { origNext(); setTimeout(attachDayClicks, 50); };
setTimeout(attachDayClicks, 100);
</script>
</body>
</html>
