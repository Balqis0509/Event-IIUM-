-- ============================================================
-- UniEvent IIUM — Database Schema
-- Run this in phpMyAdmin or MySQL CLI before starting the app
-- ============================================================

CREATE DATABASE IF NOT EXISTS unievent_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE unievent_db;

-- USERS TABLE
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student','organizer','admin') DEFAULT 'student',
    matric_no VARCHAR(20) NULL,
    profile_pic VARCHAR(255) DEFAULT 'default.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- EVENTS TABLE
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organizer_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    time TIME NOT NULL,
    venue VARCHAR(200) NOT NULL,
    category ENUM('Workshop','Seminar','Sports','Cultural','Academic','Social','Other') DEFAULT 'Other',
    poster VARCHAR(255) DEFAULT 'default_event.png',
    max_participants INT DEFAULT 100,
    status ENUM('active','cancelled','completed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- REGISTRATIONS TABLE
CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    student_id INT NOT NULL,
    status ENUM('registered','approved','completed','cancelled') DEFAULT 'registered',
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_reg (event_id, student_id)
);

-- ANNOUNCEMENTS TABLE
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    author_id INT NOT NULL,
    event_id INT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
-- SAMPLE DATA
-- ============================================================

-- Admin account (password: admin123)
INSERT INTO users (name, email, password, role) VALUES
('Admin IIUM', 'admin@iium.edu.my', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Sample organizer (password: organizer123)
INSERT INTO users (name, email, password, role, matric_no) VALUES
('Ahmad Faiz', 'faiz@iium.edu.my', '$2y$10$TKh8H1.PkCe4D5oG7f5ObeG/bXlckQvA7LBlP7yTvDdXvFpLQRrW2', 'organizer', 'S2110234'),
('Nurul Aina', 'aina@iium.edu.my', '$2y$10$TKh8H1.PkCe4D5oG7f5ObeG/bXlckQvA7LBlP7yTvDdXvFpLQRrW2', 'student', 'S2210456');

-- Sample events
INSERT INTO events (organizer_id, title, description, date, time, venue, category, max_participants) VALUES
(2, 'Python for Data Science Workshop', 'Hands-on workshop covering Python basics, pandas, and data visualisation for beginners and intermediate learners.', DATE_ADD(CURDATE(), INTERVAL 7 DAY), '09:00:00', 'KICT Lab 1, IIUM', 'Workshop', 50),
(2, 'IIUM Futsal Championship 2025', 'Annual inter-kulliyyah futsal tournament. Register your team and compete for the championship trophy!', DATE_ADD(CURDATE(), INTERVAL 14 DAY), '08:00:00', 'IIUM Sports Complex', 'Sports', 200),
(2, 'Entrepreneurship Seminar: Start Your Halal Business', 'Inspiring seminar featuring successful Muslim entrepreneurs sharing their journey and practical tips.', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '14:00:00', 'Auditorium KENMS, IIUM', 'Seminar', 150),
(2, 'Cultural Night: Colours of Malaysia', 'Celebrate Malaysian diversity with cultural performances, traditional food, and art exhibitions.', DATE_ADD(CURDATE(), INTERVAL 21 DAY), '19:00:00', 'IIUM Main Hall', 'Cultural', 300),
(2, 'Academic Writing Masterclass', 'Improve your academic writing skills with guidance from experienced IIUM faculty members.', DATE_ADD(CURDATE(), INTERVAL 5 DAY), '10:00:00', 'Library Seminar Room, IIUM', 'Academic', 80);
