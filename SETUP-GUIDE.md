# Portfolio Database Setup Guide (XAMPP / WAMP / MAMP / Linux)

This guide helps you fix **"Database connection failed"** for this project.

---

## 1) Quick Start (Recommended)

1. Start your web server + MySQL service.
2. Open:
   - `http://localhost/portfolio/check-db.php`
   - `http://localhost/portfolio/setup/install.php`
3. In installer, test credentials and run setup.
4. Open homepage:
   - `http://localhost/portfolio/`

---

## 2) XAMPP Setup (Step-by-Step)

### Windows
1. Install XAMPP from https://www.apachefriends.org
2. Copy project folder to:
   - `C:\xampp\htdocs\portfolio`
3. Open **XAMPP Control Panel** and start:
   - Apache
   - MySQL
4. Visit installer:
   - `http://localhost/portfolio/setup/install.php`

### macOS
1. Install XAMPP.
2. Copy project to:
   - `/Applications/XAMPP/xamppfiles/htdocs/portfolio`
3. Start Apache + MySQL from XAMPP Manager.
4. Open:
   - `http://localhost/portfolio/setup/install.php`

### Linux
1. Install XAMPP or use native Apache + MySQL/MariaDB.
2. Place project in web root (example):
   - `/opt/lampp/htdocs/portfolio` (XAMPP)
   - `/var/www/html/portfolio` (native Apache)
3. Start services and run installer URL.

---

## 3) Common Credentials to Try

- `root` + *(empty password)*
- `root` + `root`

> Installer auto-tests common combinations and shows PASS/FAIL.

---

## 4) Manual Terminal Commands (if needed)

### Check MySQL status

#### Linux (systemd)
```bash
sudo systemctl status mysql
# or
sudo systemctl status mariadb
```

#### XAMPP (Linux)
```bash
sudo /opt/lampp/lampp status
sudo /opt/lampp/lampp startmysql
```

#### macOS (Homebrew MySQL)
```bash
brew services list | grep mysql
brew services start mysql
```

### Connect to MySQL manually
```bash
mysql -u root -p
# or (empty password)
mysql -u root
```

### Create database manually
```sql
CREATE DATABASE IF NOT EXISTS portfolio_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

### Import schema/data manually
```bash
mysql -u root -p portfolio_db < /path/to/portfolio/database/portfolio.sql
# or with empty password:
mysql -u root portfolio_db < /path/to/portfolio/database/portfolio.sql
```

---

## 5) Troubleshooting

### Error: `Connection refused`
- MySQL is not running.
- Start MySQL service from XAMPP/WAMP/MAMP or OS service manager.
- Verify host/port (`127.0.0.1:3306`).

### Error: `Access denied for user 'root'`
- Wrong password.
- Try `root` with empty password, then `root/root`.
- Reset root password if needed.

### Error: `Unknown database 'portfolio_db'`
- Database has not been created.
- Run installer or create/import manually.

### Error still persists
- Open `check-db.php` and verify diagnostics.
- Update `includes/db.php` (or environment vars `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`).

---

## 6) Screenshot Placeholders

- `[Screenshot 1: XAMPP Control Panel with Apache/MySQL running]`
- `[Screenshot 2: check-db.php PASS results]`
- `[Screenshot 3: setup/install.php success message]`
- `[Screenshot 4: phpMyAdmin database list showing portfolio_db]`

---

## 7) After Setup

1. Visit `http://localhost/portfolio/`
2. Test login page and project listing.
3. If needed, clear browser cache and re-run `check-db.php`.
