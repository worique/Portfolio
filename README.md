# Full-Stack Developer Portfolio (PHP + MySQL)

A complete, production-ready developer portfolio built for academic grading in an **Internet & Web Programming** course. The application includes a modern public-facing portfolio and a secure admin dashboard for content management.

## Project Owner
- **Name:** Ilyes Bensaid
- **Title:** Full-Stack Web Developer
- **Email:** ilyes.bensaid@outlook.com
- https://ilyes-portfolio.free.nf/index.php
- https://ilyes-portfolio.free.nf/index.php

## Features

### Public Website
- Home page with hero, intro, skills, and featured projects
- About page with bio, education timeline, and skills grid
- Projects page loading dynamic projects from MySQL
- Contact page with JavaScript validation + AJAX submission (no refresh)
- Responsive navigation with mobile hamburger menu
- Dark/light mode toggle persisted in `localStorage`

### Admin Panel
- Secure login with sessions and remember-me cookie support
- Dashboard with:
  - Total projects count
  - Total contact messages count
  - Projects table with Edit/Delete actions
  - Contact messages table
- Full CRUD for projects:
  - Add project
  - Edit project
  - Delete project
- Logout with session and cookie cleanup

### Security
- PDO prepared statements for all DB operations
- Password hashing compatibility with `password_verify()`
- Session ID regeneration on login
- CSRF token checks on sensitive forms/actions
- XSS prevention using `htmlspecialchars()` output escaping
- Input validation + sanitization (server-side + client-side)

## Technologies Used
- PHP 8+
- MySQL / MariaDB
- HTML5
- CSS3 (Grid, Flexbox, CSS Variables)
- JavaScript (Fetch API, DOM APIs, localStorage)

## Project Structure

```bash
portfolio/
├── index.php
├── about.php
├── projects.php
├── contact.php
├── login.php
├── dashboard/
│   ├── index.php
│   ├── add-project.php
│   ├── edit-project.php
│   ├── delete-project.php
│   └── logout.php
├── ajax/
│   ├── fetch-projects.php
│   └── save-contact.php
├── includes/
│   ├── db.php
│   ├── auth.php
│   ├── header.php
│   └── footer.php
├── assets/
│   ├── css/style.css
│   ├── js/app.js
│   ├── js/validation.js
│   └── images/
├── database/
│   └── portfolio.sql
├── README.md
└── .gitignore
```

## Installation (XAMPP)

1. Copy the `portfolio` folder into your XAMPP `htdocs` directory.
2. Start **Apache** and **MySQL** from XAMPP Control Panel.
3. Open phpMyAdmin and import:
   - `database/portfolio.sql`
4. Update database credentials in `includes/db.php` if needed:
   - host, db name, username, password
5. Open in browser:
   - `http://localhost/portfolio/index.php`

## Default Admin Credentials
- **Username:** `bensaidissou`
- **Password:** `b7ae8522158`
- **Email:** `ilyes.bensaid@outlook.com`

## Database Setup Notes
- Database name: `portfolio_db`
- Tables:
  - `users`
  - `projects`
  - `contacts`
- Sample data includes:
  - 1 admin user
  - 4 sample projects
  - 2 sample contact messages

## Deployment Guide

### InfinityFree / 000WebHost
1. Upload all files to `htdocs`/`public_html`.
2. Create a MySQL database from hosting panel.
3. Import `database/portfolio.sql`.
4. Edit `includes/db.php` with host, database, user, password provided by host.
5. Ensure PHP version is 8.0+.
6. Visit your deployed domain and test login/contact/project pages.

## Screenshots (Placeholders)
- Home page screenshot
- Projects page screenshot
- Contact form screenshot
- Admin dashboard screenshot

## Academic Notes
This project follows full-stack best practices expected for university evaluation:
- clean architecture
- reusable components
- modern UI/UX
- secure coding patterns

## License
This project is provided for educational use. You may customize and extend it for your own portfolio.
