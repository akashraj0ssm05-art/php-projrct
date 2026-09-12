# CineBook — Movie Ticket Booking System

A complete movie ticket booking web application built with **PHP, MySQL, HTML & CSS**, featuring separate **User** and **Admin** panels.

## Features

### User Side
- Register / Login / Logout
- Browse movies (Now Showing / Coming Soon / All)
- View movie details and available showtimes
- Interactive seat selection (visual seat map, click to select)
- Book tickets and see instant confirmation
- View booking history ("My Bookings") and cancel upcoming bookings

### Admin Side
- Secure admin login
- Dashboard with stats (total movies, showtimes, bookings, users, revenue)
- Manage Movies (add / edit / delete, poster upload)
- Manage Theaters/Screens (add / delete, set seat capacity)
- Manage Showtimes (schedule a movie in a theater with date, time, price)
- View & manage all Bookings (cancel any booking)
- View registered Users

## Tech Stack
- **Backend:** PHP (procedural, mysqli with prepared statements)
- **Database:** MySQL
- **Frontend:** HTML5, CSS3 (custom, no frameworks), vanilla JavaScript for seat selection
- **Security:** Password hashing (bcrypt), prepared statements (SQL-injection safe), input sanitization (XSS safe)

## Folder Structure
```
movie-ticket-system/
├── admin/
│   ├── includes/
│   │   ├── admin_header.php
│   │   └── admin_footer.php
│   ├── login.php          (Admin login)
│   ├── logout.php
│   ├── dashboard.php      (Stats overview)
│   ├── movies.php         (List/delete movies)
│   ├── add_movie.php
│   ├── edit_movie.php
│   ├── theaters.php       (Manage screens)
│   ├── showtimes.php      (Schedule shows)
│   ├── bookings.php       (View/cancel all bookings)
│   └── users.php          (View registered users)
├── assets/
│   └── uploads/           (Movie poster images)
├── config/
│   └── db.php             (Database connection settings)
├── css/
│   ├── style.css          (User site styling)
│   └── admin.css          (Admin panel styling)
├── includes/
│   ├── functions.php      (Shared helper functions)
│   ├── user_header.php
│   └── user_footer.php
├── database.sql           (Database schema + sample data)
├── index.php              (Home page / movie listing)
├── login.php              (User login)
├── register.php           (User registration)
├── logout.php
├── movie_details.php      (Movie info + showtimes)
├── book.php                (Seat selection + booking)
├── booking_success.php
├── my_bookings.php        (User's booking history)
└── cancel_booking.php
```

## Setup Instructions

### 1. Requirements
- PHP 7.4+ (works with PHP 8.x)
- MySQL 5.7+ / MariaDB
- A local server stack such as **XAMPP**, **WAMP**, **MAMP**, or `php -S`

### 2. Install
1. Copy the `movie-ticket-system` folder into your server's web root
   (e.g. `htdocs/` for XAMPP, `www/` for WAMP).
2. Create the database by importing `database.sql`:
   - Open **phpMyAdmin** → Create a new query → paste the contents of `database.sql` → Run.
   - Or via terminal:
     ```
     mysql -u root -p < database.sql
     ```
3. Open `config/db.php` and update credentials if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'movie_ticket_system');
   ```
4. Make sure `assets/uploads/` is writable (for poster image uploads).

### 3. Run
- Visit `http://localhost/movie-ticket-system/` for the **User site**.
- Visit `http://localhost/movie-ticket-system/admin/login.php` for the **Admin panel**.

### 4. Default Admin Login
```
Username: admin
Password: admin123
```
(Change this password after first login by updating the `admins` table with a new bcrypt hash, or add a "change password" feature.)

### 5. Quick Test with PHP Built-in Server (no XAMPP needed)
```bash
cd movie-ticket-system
php -S localhost:8000
```
Then visit `http://localhost:8000/`. (You still need a MySQL server running and the database imported.)

## Notes
- All passwords are hashed using PHP's `password_hash()` (bcrypt).
- All SQL queries use prepared statements to prevent SQL injection.
- All output is escaped with `htmlspecialchars()` to prevent XSS.
- Seat availability is checked both visually (blocked seats shown in gray) and again on the server before confirming a booking, to prevent race conditions/double-booking.
- Currency is displayed in ₹ (INR) — change the `formatPrice()` function in `includes/functions.php` if you need a different currency symbol.

## Possible Enhancements
- Payment gateway integration (Stripe/Razorpay)
- Email/SMS booking confirmations
- QR-code e-tickets
- Multi-admin roles & permissions
- Movie ratings/reviews from users
