# 🎓 UniEvent — IIUM Event Management System

A full-stack web application built with **PHP, HTML, CSS, and JavaScript** for managing university events at IIUM (International Islamic University Malaysia).

---

## 📁 Project Structure

```
unievent/
├── index.php                    ← Login page (Route: /)
├── database.sql                 ← Database schema + sample data
├── css/
│   └── style.css               ← Main stylesheet (IIUM green/gold theme)
├── js/
│   └── app.js                  ← JavaScript (validation, calendar, filters)
├── images/
│   └── uploads/                ← Uploaded event posters (auto-created)
├── includes/
│   ├── db.php                  ← Database config + session helpers
│   ├── navbar.php              ← Reusable navigation bar
│   └── flash.php               ← Flash message display
├── models/
│   ├── UserModel.php           ← User CRUD (register, login, profile)
│   └── EventModel.php         ← Event CRUD (create, join, participants)
├── controllers/
│   ├── AuthController.php     ← Login/Register/Logout routes
│   ├── EventController.php    ← Event action routes
│   └── UserController.php     ← Profile/password routes
└── pages/
    ├── register.php            ← Registration page
    ├── dashboard_student.php   ← Student dashboard
    ├── dashboard_organizer.php ← Organizer dashboard
    ├── dashboard_admin.php     ← Admin control panel
    ├── events.php              ← Event listing + search/filter
    ├── event_detail.php        ← Event detail + Join button
    ├── create_event.php        ← Create event form (organizer)
    ├── edit_event.php          ← Edit event form (organizer)
    ├── participants.php        ← Participant management + CSV export
    ├── my_events.php           ← Student's registered events
    ├── announcements.php       ← Announcements board
    ├── calendar.php            ← Interactive monthly calendar
    └── profile.php             ← User profile + change password
```

---

## ⚙️ Setup Instructions (XAMPP / WAMP / MAMP)

### Step 1 — Move Project Files
Copy the entire `unievent/` folder to your web server root:
- **XAMPP (Windows):** `C:\xampp\htdocs\unievent`
- **XAMPP (Mac):** `/Applications/XAMPP/htdocs/unievent`
- **WAMP:** `C:\wamp64\www\unievent`

### Step 2 — Create the Database
1. Open **phpMyAdmin** → `http://localhost/phpmyadmin`
2. Click **Import** → Choose file → select `unievent/database.sql`
3. Click **Go**

OR run via MySQL CLI:
```bash
mysql -u root -p < database.sql
```

### Step 3 — Configure Database (if needed)
Edit `includes/db.php` if your MySQL credentials differ:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');     // your MySQL username
define('DB_PASS', '');         // your MySQL password
define('DB_NAME', 'unievent_db');
```

### Step 4 — Run the App
Open your browser: **`http://localhost/unievent/`**

### Step 5 — Create uploads folder (if not auto-created)
```bash
mkdir unievent/images/uploads
chmod 755 unievent/images/uploads
```

---

## 🔐 Demo Accounts

| Role       | Email                    | Password      |
|------------|--------------------------|---------------|
| Admin      | admin@iium.edu.my        | password      |
| Organizer  | faiz@iium.edu.my         | organizer123  |
| Student    | aina@iium.edu.my         | organizer123  |

> ⚠️ The sample data uses hashed passwords via `password_hash()`. The actual password string for admin is `password` and organizer/student is `organizer123`.

---

## 🌐 Pages & Routes

| URL                              | Description                    | Access         |
|----------------------------------|--------------------------------|----------------|
| `/index.php`                     | Login page                     | Public         |
| `/pages/register.php`            | Registration page              | Public         |
| `/pages/dashboard_student.php`   | Student home                   | Student        |
| `/pages/dashboard_organizer.php` | Organizer home                 | Organizer      |
| `/pages/dashboard_admin.php`     | Admin control panel            | Admin          |
| `/pages/events.php`              | Browse all events              | All users      |
| `/pages/event_detail.php?id=X`   | Event detail + join            | All users      |
| `/pages/create_event.php`        | Create new event               | Organizer      |
| `/pages/edit_event.php?id=X`     | Edit existing event            | Organizer      |
| `/pages/participants.php?id=X`   | View + export participants     | Organizer/Admin|
| `/pages/my_events.php`           | Student's joined events        | Student        |
| `/pages/announcements.php`       | Announcements board            | All users      |
| `/pages/calendar.php`            | Monthly event calendar         | All users      |
| `/pages/profile.php`             | Profile + password settings    | All users      |
| `/controllers/AuthController.php`| Login/Register/Logout handler  | POST only      |
| `/controllers/EventController.php`| Event action handler          | POST only      |
| `/controllers/UserController.php`| Profile action handler         | POST only      |

---

## 🧩 Marking Criteria Coverage

| Criteria                    | Implementation                                              | Marks |
|-----------------------------|-------------------------------------------------------------|-------|
| **Routes**                  | Controllers route all POST/GET actions                      | 10    |
| **Controllers**             | AuthController, EventController, UserController             | 10    |
| **Views**                   | 13 PHP view pages with full HTML templates                  | 10    |
| **Models**                  | UserModel.php, EventModel.php with full CRUD operations     | 10    |
| **User Authentication**     | Session-based login/register, role-based access, bcrypt     | 10    |
| **Media Usage**             | Emoji icons, event poster uploads, calendar, CSS animations | 20    |
| **Design/Colour/Layout**    | IIUM green/gold theme, Google Fonts, responsive grid        | 20    |
| **Navigation/Links**        | Role-based navbar, breadcrumbs, internal linking            | 10    |

---

## 🛠️ Technologies Used

| Layer      | Technology                        |
|------------|-----------------------------------|
| Backend    | PHP 8+ (procedural + OOP models)  |
| Database   | MySQL / MariaDB                   |
| Frontend   | HTML5, CSS3, Vanilla JavaScript   |
| Fonts      | Google Fonts (Playfair Display + DM Sans) |
| Server     | Apache (via XAMPP/WAMP)           |

---

## 🔒 Security Features
- Passwords hashed with `password_hash()` (bcrypt)
- SQL injection prevention via prepared statements (`mysqli`)
- XSS prevention via `htmlspecialchars()` on all output
- Session-based authentication with role checks on every protected page
- File upload validation (type & extension checking)

---

## 📸 Key Features
- ✅ Role-based login (Student / Organizer / Admin)
- ✅ Event listing with search and category filters
- ✅ Join/Leave event with capacity tracking & progress bars
- ✅ Event poster image upload
- ✅ Participant list with CSV export
- ✅ Interactive monthly calendar with clickable event dates
- ✅ Announcements board (post + read)
- ✅ Profile management + password change
- ✅ Admin user management (view + delete users)
- ✅ Fully responsive (mobile-friendly)

---

*Developed for IIUM — Digital Web Development Assignment*
