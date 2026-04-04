-- Sanctuary Student Daily Focus System
-- MariaDB Schema
-- Run this once to set up the database

CREATE DATABASE IF NOT EXISTS sanctuary CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sanctuary;

-- Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    class_id INT DEFAULT NULL,
    role ENUM('admin','user') DEFAULT 'user',
    avatar_seed VARCHAR(50) DEFAULT NULL,
    avatar_style VARCHAR(40) NOT NULL DEFAULT 'avataaars',
    avatar_text VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Classes
CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
);

-- Assignments
CREATE TABLE IF NOT EXISTS assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subject VARCHAR(100),
    deadline DATETIME NOT NULL,
    type ENUM('assignment','presentation','project') DEFAULT 'assignment',
    link TEXT DEFAULT NULL,
    visibility ENUM('public','class') DEFAULT 'class',
    class_id INT DEFAULT NULL,
    created_by INT NOT NULL,
    status ENUM('pending','completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL
);

-- Assignment Statuses (per user)
CREATE TABLE IF NOT EXISTS assignment_statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    user_id INT NOT NULL,
    status ENUM('pending','completed') DEFAULT 'pending',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY assignment_user (assignment_id, user_id),
    FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Todos
CREATE TABLE IF NOT EXISTS todos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    is_completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Timetable
CREATE TABLE IF NOT EXISTS timetable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,
    subject VARCHAR(100) NOT NULL,
    room VARCHAR(50),
    day_of_week ENUM('Mon','Tue','Wed','Thu','Fri','Sat','Sun') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
);

-- Mess Menu
CREATE TABLE IF NOT EXISTS mess_menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    meal_type ENUM('breakfast','lunch','lunch_international','snacks','dinner') NOT NULL,
    items TEXT NOT NULL,
    UNIQUE KEY unique_meal (date, meal_type)
);

-- Mess Reactions
CREATE TABLE IF NOT EXISTS mess_reactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    mess_id INT NOT NULL,
    reaction ENUM('like','dislike') NOT NULL,
    UNIQUE KEY unique_reaction (user_id, mess_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (mess_id) REFERENCES mess_menu(id) ON DELETE CASCADE
);

-- Habits
CREATE TABLE IF NOT EXISTS habits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Habit Logs
CREATE TABLE IF NOT EXISTS habit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    habit_id INT NOT NULL,
    date DATE NOT NULL,
    completed BOOLEAN DEFAULT TRUE,
    UNIQUE KEY unique_log (habit_id, date),
    FOREIGN KEY (habit_id) REFERENCES habits(id) ON DELETE CASCADE
);

-- Home Hero Cards
CREATE TABLE IF NOT EXISTS hero_cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    subtitle VARCHAR(255) NOT NULL,
    link TEXT DEFAULT NULL,
    image_url TEXT NOT NULL,
    sort_order INT DEFAULT 1,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -----------------------------------------------
-- Seed data
-- -----------------------------------------------

INSERT IGNORE INTO classes (name) VALUES ('CSE-A'), ('CSE-B'), ('ECE-A'), ('MECH-A');

-- Admin user  (password: admin123)
INSERT IGNORE INTO users (name, email, password, class_id, role, avatar_seed, avatar_style, avatar_text)
VALUES ('Admin', 'admin@sanctuary.dev', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uye29MVSW', 1, 'admin', 'Admin', 'avataaars', 'Admin');

-- Demo student (password: student123)
INSERT IGNORE INTO users (name, email, password, class_id, role, avatar_seed, avatar_style, avatar_text)
VALUES ('Ayush', 'ayush@sanctuary.dev', '$2y$10$TKh8H1.PFfHiYMDC6pUa.uXsNYCEWfUf8opF5e8a/y1WH3.D1kPpC', 1, 'user', 'Ayush', 'avataaars', 'Ayush');

-- Seed timetable for CSE-A
INSERT IGNORE INTO timetable (class_id, subject, room, day_of_week, start_time, end_time) VALUES
(1, 'Data Structures', 'Room 302', 'Mon', '10:30:00', '12:00:00'),
(1, 'Organic Chemistry', 'Lab 101', 'Mon', '13:00:00', '14:30:00'),
(1, 'Business Management', 'Hall B', 'Mon', '15:00:00', '16:30:00'),
(1, 'Algorithm Analysis', 'Room 201', 'Tue', '09:00:00', '10:30:00'),
(1, 'Linear Algebra', 'Room 105', 'Tue', '11:00:00', '12:30:00'),
(1, 'Data Structures', 'Room 302', 'Wed', '10:30:00', '12:00:00'),
(1, 'Business Management', 'Hall B', 'Thu', '09:00:00', '10:30:00'),
(1, 'Algorithm Analysis', 'Room 201', 'Thu', '14:00:00', '15:30:00'),
(1, 'Linear Algebra', 'Room 105', 'Fri', '11:00:00', '12:30:00'),
(1, 'Organic Chemistry', 'Lab 101', 'Fri', '14:00:00', '15:30:00');

-- Seed today's mess menu
INSERT IGNORE INTO mess_menu (date, meal_type, items) VALUES
(CURDATE(), 'breakfast', 'Masala Dosa, Sambar, Coconut Chutney, Seasonal Fruit & Milk'),
(CURDATE(), 'lunch', 'Paneer Butter Masala, Jeera Rice, Dal Tadka, Butter Naan & Roasted Papad'),
(CURDATE(), 'lunch_international', 'Spaghetti Aglio e Olio, Garlic Bread, Fresh Garden Salad'),
(CURDATE(), 'snacks', 'Samosa, Mint Chutney, Masala Chai'),
(CURDATE(), 'dinner', 'Home-style Chicken Curry, Veg Pulao, Chapati, Fresh Green Salad & Gulab Jamun');

-- Seed hero cards
INSERT IGNORE INTO hero_cards (id, title, subtitle, image_url, sort_order) VALUES
    (1, "This Week's Focus", 'Lock in your priorities and stay on schedule.', 'https://images.unsplash.com/photo-1529070538774-1843cb3265df?auto=format&fit=crop&w=1200&q=80', 1),
    (2, 'Keep Your Momentum', 'Plan your next deep work block and protect it.', 'https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=1200&q=80', 2);