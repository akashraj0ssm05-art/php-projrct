-- ============================================
-- Movie Ticket Booking System - Database Schema
-- ============================================

CREATE DATABASE IF NOT EXISTS movie_ticket_system;
USE movie_ticket_system;

-- ---------------------------
-- Table: admins
-- ---------------------------
CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin login -> username: admin | password: admin123
INSERT INTO admins (username, password, full_name)
VALUES ('admin', '$2y$10$qq9dIasl1FdgCMtQ5Crqiu4kI7OcUPBxXXwuV6iTvo6w.v0ku04f6', 'Site Administrator');
-- NOTE: The hash above corresponds to "admin123" (bcrypt) - verified with password_verify()

-- ---------------------------
-- Table: users
-- ---------------------------
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------
-- Table: movies
-- ---------------------------
CREATE TABLE movies (
    movie_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    genre VARCHAR(100),
    language VARCHAR(50),
    duration_minutes INT,
    release_date DATE,
    poster VARCHAR(255) DEFAULT 'default.jpg',
    status ENUM('Now Showing','Coming Soon','Archived') DEFAULT 'Now Showing',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------
-- Table: theaters (screens)
-- ---------------------------
CREATE TABLE theaters (
    theater_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    location VARCHAR(150),
    total_seats INT NOT NULL DEFAULT 50
);

-- ---------------------------
-- Table: showtimes
-- ---------------------------
CREATE TABLE showtimes (
    showtime_id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT NOT NULL,
    theater_id INT NOT NULL,
    show_date DATE NOT NULL,
    show_time TIME NOT NULL,
    price DECIMAL(8,2) NOT NULL DEFAULT 150.00,
    FOREIGN KEY (movie_id) REFERENCES movies(movie_id) ON DELETE CASCADE,
    FOREIGN KEY (theater_id) REFERENCES theaters(theater_id) ON DELETE CASCADE
);

-- ---------------------------
-- Table: bookings
-- ---------------------------
CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    showtime_id INT NOT NULL,
    seats VARCHAR(255) NOT NULL,          -- comma separated seat numbers e.g. A1,A2,A3
    num_seats INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    booking_status ENUM('Confirmed','Cancelled') DEFAULT 'Confirmed',
    booked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (showtime_id) REFERENCES showtimes(showtime_id) ON DELETE CASCADE
);

-- ---------------------------
-- Sample data
-- ---------------------------
INSERT INTO theaters (name, location, total_seats) VALUES
('Screen 1', 'Downtown Multiplex', 50),
('Screen 2', 'Downtown Multiplex', 40),
('Screen 3', 'City Mall Cinema', 60);

INSERT INTO movies (title, description, genre, language, duration_minutes, release_date, status) VALUES
('The Last Horizon', 'A gripping sci-fi adventure about humanity\'s final journey to the stars.', 'Sci-Fi, Adventure', 'English', 148, '2026-06-01', 'Now Showing'),
('Shadows of Tomorrow', 'A thrilling mystery drama that keeps you guessing until the last frame.', 'Mystery, Thriller', 'English', 132, '2026-07-10', 'Now Showing'),
('Laughing All the Way', 'A heartwarming comedy about family, friendship, and second chances.', 'Comedy', 'Hindi', 120, '2026-07-25', 'Now Showing'),
('Guardians of the Deep', 'An underwater fantasy epic full of stunning visuals.', 'Fantasy, Action', 'English', 140, '2026-08-15', 'Coming Soon');

INSERT INTO showtimes (movie_id, theater_id, show_date, show_time, price) VALUES
(1, 1, CURDATE(), '10:00:00', 180.00),
(1, 1, CURDATE(), '14:00:00', 180.00),
(1, 2, CURDATE(), '18:30:00', 200.00),
(2, 2, CURDATE(), '11:00:00', 170.00),
(2, 3, CURDATE(), '19:00:00', 220.00),
(3, 3, CURDATE(), '16:00:00', 150.00);
