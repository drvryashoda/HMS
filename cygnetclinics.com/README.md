# Cygnet Clinics CMS

A PHP and MySQL powered clinic management system featuring a dynamic marketing website with administrative control and dedicated portals for doctors, receptionists, laboratory technicians and pharmacy teams.

## Requirements

- PHP 8.0+
- MySQL 5.7+/MariaDB 10+
- Web server capable of running PHP applications (Apache/Nginx)

## Installation

1. Upload the `cygnetclinics.com` folder to your web root (e.g. alongside `public_html`).
2. Create a MySQL database named `cygnetclinics` (or update `config.php` with your chosen name).
3. Import the `database.sql` file into the database.
4. Ensure the database credentials in `config.php` match your hosting provider. The default configuration uses:
   - Host: `sdb-w.hosting.stackcp.net`
   - Username: `cygnetclinics-323133c8ff`
   - Password: `Db@b360800`
   - Database: `cygnetclinics`
5. Visit `/cygnetclinics.com/login.php` and sign in with the seeded administrator account:
   - Email: `admin@cygnetclinics.com`
   - Password: `Admin@123`
6. From the admin dashboard you can configure the homepage, add services and create team accounts for the clinical portals.

## Features

- Dynamic marketing site with banners, services, blogs, and contact enquiries managed from the admin panel.
- Admin portal for updating site content, managing staff accounts and tracking key metrics.
- Doctor portal for reviewing appointments, generating prescriptions, and viewing issued prescriptions.
- Reception portal for registering patients and managing appointments.
- Lab portal for processing diagnostic tests and recording results.
- Pharmacy portal for tracking prescription dispensing status.
- Contact form submissions stored in the database for follow up.

## Optional configuration

If the application is deployed in a different directory, update references in navigation links and redirects accordingly.

For production deployments consider:

- Enabling HTTPS across the domain.
- Configuring environment variables for database credentials.
- Restricting direct access to the `database.sql` file after installation.
- Creating regular database backups.
