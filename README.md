# Hospital Information Management System (HMS)

A complete, installable HMS web application built with **PHP**, **MySQL**, **HTML**, and **CSS** with a modern dashboard UI.

## Features

- Secure authentication (login/logout with hashed password verification)
- Role-aware user badges for staff users
- Dashboard with key hospital metrics
- Patient management
- Doctor management
- Appointment scheduling and completion workflow
- Admission/inpatient tracking
- Lab test ordering and results management
- Pharmacy dispensing records
- Billing and invoice tracking
- Reports with revenue and operational summaries

## Tech Stack

- PHP 8+
- MySQL 8+ (or MariaDB with compatible features)
- PDO for database access
- Vanilla HTML/CSS/JS frontend

## Installation

1. Clone or copy this repository to your web server directory.
2. Create the MySQL schema and tables:
   ```bash
   mysql -u root -p < install.sql
   ```
3. Update DB credentials in `config/config.php`.
4. (Optional) Keep a backup template in `config/config.sample.php`.
5. Run locally:
   ```bash
   php -S 0.0.0.0:8000
   ```
6. Open `http://localhost:8000` in your browser.

## Default Login

- Email: `admin@hospital.com`
- Password: `password123`

## Project Structure

- `config/` - DB and app configuration
- `includes/` - authentication, layout, and utility helpers
- `assets/` - CSS and JS
- `*.php` - module pages (patients, doctors, appointments, etc.)
- `install.sql` - installable database schema + seed data

## Notes

- This project is intentionally framework-free for easier deployment in shared hosting environments.
- For production use, move credentials to environment variables and add strict access control per module.
