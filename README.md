# Complaint Desk: Online Complaint Management System
PHP + MySQL + HTML/CSS/JS, neo-brutalist UI.

## Setup (XAMPP)
1. Start Apache and MySQL in the XAMPP Control Panel.
2. Copy this folder to `C:\xampp\htdocs\complaint-system\`
3. Open http://localhost/phpmyadmin, SQL tab, paste `database.sql`, click Go.
4. Open http://localhost/complaint-system/create_admin.php ONCE, then DELETE that file.
5. Open http://localhost/complaint-system/

Admin login: admin@complaint.com / Admin@123 (change it after first login).

## File guide
| File | Purpose |
|---|---|
| database.sql | Creates the database and tables |
| config.php | DB connection, session, helper functions, category list |
| includes/header.php, footer.php | Shared top navbar and footer |
| index.php | Home page |
| register.php / login.php / logout.php | Authentication |
| submit_complaint.php | New complaint form |
| dashboard.php | User's complaints and status counts |
| view_complaint.php | One complaint with admin reply |
| admin/dashboard.php | Admin list, stats, filters, search |
| admin/respond.php | Admin changes status and writes a reply |
| create_admin.php | One-time admin creator (delete after use) |
| assets/css/style.css | Whole design |
| assets/js/script.js | Validation, counter, confirm popup |
