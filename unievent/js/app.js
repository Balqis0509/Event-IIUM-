// ============================================================
// UniEvent IIUM — Main JavaScript
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

  // ── FLASH MESSAGE AUTO-DISMISS ──────────────────────────
  const flash = document.querySelector('.flash');
  if (flash) {
    setTimeout(() => {
      flash.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
      flash.style.opacity = '0';
      flash.style.transform = 'translateY(-10px)';
      setTimeout(() => flash.remove(), 500);
    }, 4000);
  }

  // ── MOBILE HAMBURGER MENU ───────────────────────────────
  const hamburger = document.querySelector('.hamburger');
  const navbarNav = document.querySelector('.navbar-nav');
  if (hamburger && navbarNav) {
    hamburger.addEventListener('click', () => {
      navbarNav.classList.toggle('open');
      hamburger.textContent = navbarNav.classList.contains('open') ? '✕' : '☰';
    });
    document.addEventListener('click', (e) => {
      if (!hamburger.contains(e.target) && !navbarNav.contains(e.target)) {
        navbarNav.classList.remove('open');
        hamburger.textContent = '☰';
      }
    });
  }

  // ── DELETE CONFIRM DIALOGS ──────────────────────────────
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', function (e) {
      const msg = this.dataset.confirm || 'Are you sure?';
      if (!confirm(msg)) e.preventDefault();
    });
  });

  // ── FORM VALIDATION ─────────────────────────────────────
  document.querySelectorAll('form[data-validate]').forEach(form => {
    form.addEventListener('submit', function (e) {
      let valid = true;
      this.querySelectorAll('[required]').forEach(field => {
        const errEl = field.parentElement.querySelector('.form-error');
        if (!field.value.trim()) {
          valid = false;
          field.style.borderColor = '#dc3545';
          if (errEl) errEl.textContent = 'This field is required.';
        } else {
          field.style.borderColor = '';
          if (errEl) errEl.textContent = '';
        }
      });
      // Password confirm check
      const pw = this.querySelector('#password');
      const pw2 = this.querySelector('#confirm_password');
      if (pw && pw2 && pw.value !== pw2.value) {
        valid = false;
        pw2.style.borderColor = '#dc3545';
        const errEl = pw2.parentElement.querySelector('.form-error');
        if (errEl) errEl.textContent = 'Passwords do not match.';
      }
      if (!valid) e.preventDefault();
    });
  });

  // ── PASSWORD TOGGLE ──────────────────────────────────────
  document.querySelectorAll('.pw-toggle').forEach(btn => {
    btn.addEventListener('click', function () {
      const input = document.querySelector(this.dataset.target);
      if (!input) return;
      input.type = input.type === 'password' ? 'text' : 'password';
      this.textContent = input.type === 'password' ? '👁' : '🙈';
    });
  });

  // ── SEARCH FILTER (live) ─────────────────────────────────
  const searchInput = document.getElementById('live-search');
  if (searchInput) {
    searchInput.addEventListener('input', function () {
      const q = this.value.toLowerCase();
      document.querySelectorAll('[data-searchable]').forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(q) ? '' : 'none';
      });
    });
  }

  // ── PARTICIPANT PROGRESS BARS ANIMATE ────────────────────
  document.querySelectorAll('.progress-bar[data-width]').forEach(bar => {
    setTimeout(() => { bar.style.width = bar.dataset.width + '%'; }, 300);
  });

  // ── CALENDAR ────────────────────────────────────────────
  const calContainer = document.getElementById('calendar-container');
  if (calContainer) {
    buildCalendar(calContainer);
  }

  // ── IMAGE PREVIEW FOR FILE UPLOAD ───────────────────────
  const posterInput = document.getElementById('poster');
  const posterPreview = document.getElementById('poster-preview');
  if (posterInput && posterPreview) {
    posterInput.addEventListener('change', function () {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = e => {
          posterPreview.src = e.target.result;
          posterPreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // ── CATEGORY CHIPS FILTER ───────────────────────────────
  document.querySelectorAll('.chip[data-category]').forEach(chip => {
    chip.addEventListener('click', function () {
      const cat = this.dataset.category;
      document.querySelectorAll('.chip[data-category]').forEach(c => c.classList.remove('active'));
      this.classList.add('active');
      document.querySelectorAll('[data-card-category]').forEach(card => {
        if (cat === 'all' || card.dataset.cardCategory === cat) {
          card.closest('.card-wrapper') ? card.closest('.card-wrapper').style.display = '' : card.style.display = '';
        } else {
          card.closest('.card-wrapper') ? card.closest('.card-wrapper').style.display = 'none' : card.style.display = 'none';
        }
      });
    });
  });

  // ── NOTIFICATION BELL ───────────────────────────────────
  const bell = document.getElementById('notif-bell');
  const notifPanel = document.getElementById('notif-panel');
  if (bell && notifPanel) {
    bell.addEventListener('click', (e) => {
      e.stopPropagation();
      notifPanel.classList.toggle('hidden');
    });
    document.addEventListener('click', () => notifPanel.classList.add('hidden'));
  }
});

// ── CALENDAR BUILD ─────────────────────────────────────────
function buildCalendar(container) {
  const eventDates = JSON.parse(container.dataset.events || '[]');
  const now = new Date();
  let year = now.getFullYear(), month = now.getMonth();

  function render() {
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const dayNames = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

    let html = `<div class="calendar">
      <div class="calendar-header">
        <button onclick="calPrev()" style="background:none;border:none;color:white;font-size:1.3rem;cursor:pointer">&#8249;</button>
        <span style="font-family:var(--font-head);font-size:1.1rem;font-weight:700">${monthNames[month]} ${year}</span>
        <button onclick="calNext()" style="background:none;border:none;color:white;font-size:1.3rem;cursor:pointer">&#8250;</button>
      </div>
      <div class="calendar-grid">`;

    dayNames.forEach(d => html += `<div class="cal-day-name">${d}</div>`);

    for (let i = 0; i < firstDay; i++) html += `<div class="cal-day other-month"></div>`;

    for (let d = 1; d <= daysInMonth; d++) {
      const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
      const isToday = d === now.getDate() && month === now.getMonth() && year === now.getFullYear();
      const hasEvent = eventDates.includes(dateStr);
      html += `<div class="cal-day ${isToday?'today':''} ${hasEvent?'has-event':''}"
               title="${hasEvent?'Event on this day':''}">${d}</div>`;
    }
    html += '</div></div>';
    container.innerHTML = html;
  }

  window.calPrev = () => { if (--month < 0) { month = 11; year--; } render(); };
  window.calNext = () => { if (++month > 11) { month = 0; year++; } render(); };
  render();
}

// ── CONFIRM MODAL ──────────────────────────────────────────
function confirmAction(msg, formId) {
  if (confirm(msg)) document.getElementById(formId).submit();
}

// ── COPY TO CLIPBOARD ──────────────────────────────────────
function copyText(text) {
  navigator.clipboard.writeText(text).then(() => showToast('Copied!'));
}

function showToast(msg) {
  const t = document.createElement('div');
  t.textContent = msg;
  t.style.cssText = 'position:fixed;bottom:20px;right:20px;background:#006747;color:white;padding:0.7rem 1.3rem;border-radius:8px;font-size:0.9rem;z-index:9999;box-shadow:0 4px 12px rgba(0,0,0,0.2);animation:slideDown 0.3s ease';
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 2500);
}
