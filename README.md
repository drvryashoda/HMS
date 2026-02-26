# HMS - Doctor Portfolio CMS

Installable PHP/MySQL doctor portfolio website with:
- Modern premium-minimal UI and UX for patient-facing pages
- Home hero banner + latest and featured articles sections
- About, contact, terms, and privacy pages
- Appointment booking with date-first slot picker and doctor email notification
- Recurring appointment schedule generator in admin panel (range + weekdays + interval)
- Admin panel for login, article/category/tag management, and slot management
- Article featured images (upload or URL) fully manageable via admin
- Built-in SEO meta structure (title/description/keywords/canonical + OpenGraph/Twitter)

## Installation (Shared Hosting Friendly)
1. Upload all files to your hosting root directory.
2. Open your domain URL.
3. You will be redirected to `/install/`.
4. Fill database + website + admin details.
5. Installer will create database tables and first admin account.
6. Login from `/login.php` and manage content.

## Requirements
- PHP 8+
- MySQL / MariaDB
- `PDO` extension enabled
- `mail()` configured on server for appointment notifications
- Write permission for `/uploads` directory for image uploads
