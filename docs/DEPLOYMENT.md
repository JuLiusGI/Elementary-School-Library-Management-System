# Deployment Guide

## Bobon B Elementary School - Library Management System

This document provides step-by-step instructions for deploying the application to a production environment.

---

## Table of Contents

1. [Pre-Deployment Checklist](#1-pre-deployment-checklist)
2. [Environment Configuration](#2-environment-configuration)
3. [Server Setup](#3-server-setup)
4. [Application Deployment](#4-application-deployment)
5. [Database Setup](#5-database-setup)
6. [Post-Deployment](#6-post-deployment)
7. [Optimization](#7-optimization)
8. [Backup Procedures](#8-backup-procedures)
9. [Maintenance](#9-maintenance)
10. [Security Checklist](#10-security-checklist)
11. [Rollback Procedure](#11-rollback-procedure)

---

## 1. Pre-Deployment Checklist

Before deploying, ensure the following:

- [ ] All code changes are committed and pushed to the repository
- [ ] All tests pass (`php artisan test`)
- [ ] Frontend assets are built (`npm run build`)
- [ ] `.env.example` is up to date with all required variables
- [ ] Database migrations are tested on a clean database
- [ ] Default credentials are documented
- [ ] Backup of current production database (if updating)

---

## 2. Environment Configuration

### Production .env Settings

```env
# Application
APP_NAME="Library Management System"
APP_ENV=production
APP_KEY=                          # Generate with: php artisan key:generate
APP_DEBUG=false                   # MUST be false in production
APP_URL=https://your-domain.com   # Your actual domain

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=library_management
DB_USERNAME=library_user          # Use a dedicated database user
DB_PASSWORD=strong_password_here  # Use a strong password

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true              # Enable in production

# Cache
CACHE_STORE=file                  # Or 'database' for shared environments

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error                   # Only log errors in production

# File Storage
FILESYSTEM_DISK=public

# Mail (optional, for notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=library@bobon.edu.ph
MAIL_FROM_NAME="${APP_NAME}"
```

### Critical Settings

| Setting | Development | Production |
|---------|-------------|------------|
| `APP_ENV` | local | **production** |
| `APP_DEBUG` | true | **false** |
| `APP_URL` | http://localhost/... | **https://your-domain.com** |
| `LOG_LEVEL` | debug | **error** |
| `SESSION_ENCRYPT` | false | **true** |
| `DB_PASSWORD` | (empty) | **strong password** |

---

## 3. Server Setup

### Server Requirements

- PHP 8.2+ with required extensions
- MySQL 8.0+ or MariaDB 10.3+
- Apache 2.4+ or Nginx
- Composer 2.x
- Node.js 18+ and NPM (for building assets)
- SSL certificate (recommended)

### PHP Extensions

Verify all required extensions are installed:

```bash
php -m | grep -E "bcmath|ctype|fileinfo|json|mbstring|openssl|pdo|pdo_mysql|tokenizer|xml|gd"
```

### Apache Configuration

If using Apache, ensure `mod_rewrite` is enabled:

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/bes_library_sys/public

    <Directory /var/www/bes_library_sys/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/bes_library_sys/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### File Permissions (Linux/macOS)

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/bes_library_sys

# Set directory permissions
sudo find /var/www/bes_library_sys -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/bes_library_sys -type f -exec chmod 644 {} \;

# Set writable directories
sudo chmod -R 775 storage bootstrap/cache
```

---

## 4. Application Deployment

### Step-by-Step Deployment

```bash
# 1. Navigate to project directory
cd /var/www/bes_library_sys

# 2. Pull latest code (if using git)
git pull origin main

# 3. Install PHP dependencies (no dev packages)
composer install --optimize-autoloader --no-dev

# 4. Install Node.js dependencies and build assets
npm ci
npm run build

# 5. Copy environment file (first time only)
cp .env.example .env
# Then edit .env with production values

# 6. Generate application key (first time only)
php artisan key:generate

# 7. Run database migrations
php artisan migrate --force

# 8. Seed initial data (first time only)
php artisan db:seed --force

# 9. Create storage symlink
php artisan storage:link

# 10. Clear and rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 11. Set permissions
chmod -R 775 storage bootstrap/cache
```

### For XAMPP (Windows) Deployment

```cmd
REM 1. Copy project to htdocs
xcopy /E /I project_folder C:\xampp\htdocs\bes_library_sys

REM 2. Navigate to project
cd C:\xampp\htdocs\bes_library_sys

REM 3. Install dependencies
composer install --optimize-autoloader --no-dev
npm ci
npm run build

REM 4. Configure environment
copy .env.example .env
REM Edit .env with your settings

REM 5. Setup database
php artisan key:generate
php artisan migrate --seed
php artisan storage:link

REM 6. Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 5. Database Setup

### First-Time Setup

```bash
# Create the database
mysql -u root -p -e "CREATE DATABASE library_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Create a dedicated database user
mysql -u root -p -e "CREATE USER 'library_user'@'localhost' IDENTIFIED BY 'strong_password';"
mysql -u root -p -e "GRANT ALL PRIVILEGES ON library_management.* TO 'library_user'@'localhost';"
mysql -u root -p -e "FLUSH PRIVILEGES;"

# Run migrations
php artisan migrate --force

# Seed initial data
php artisan db:seed --force
```

### Updating Existing Database

```bash
# Run new migrations only
php artisan migrate --force

# If you need to reseed specific data
php artisan db:seed --class=SettingSeeder --force
```

---

## 6. Post-Deployment

### Verify the Deployment

- [ ] Application loads without errors at the URL
- [ ] Login page is accessible
- [ ] Admin can log in with default credentials
- [ ] Dashboard loads with statistics
- [ ] All sidebar navigation links work
- [ ] File uploads work (book cover images)
- [ ] PDF export generates correctly
- [ ] Dark mode toggle works

### Change Default Passwords

Immediately change the default passwords:

1. Log in as admin (admin@bobon.edu.ph / password)
2. Go to Profile > Update Password
3. Set a strong password
4. Repeat for all librarian accounts

---

## 7. Optimization

### Cache Configuration

```bash
# Cache config files (faster boot)
php artisan config:cache

# Cache routes (faster routing)
php artisan route:cache

# Cache views (faster rendering)
php artisan view:cache
```

### Autoloader Optimization

```bash
composer install --optimize-autoloader --no-dev
```

### Frontend Asset Optimization

```bash
# Build minified assets
npm run build
```

### Database Optimization

The application includes performance indexes. Verify they exist:

```bash
php artisan migrate --force
```

---

## 8. Backup Procedures

### Database Backup

Use the built-in command:

```bash
# Create a backup
php artisan library:backup-db

# Backup to a specific directory
php artisan library:backup-db --path=/path/to/backups
```

### Manual Database Backup

```bash
# Using mysqldump
mysqldump -u root -p library_management > backup_$(date +%Y%m%d_%H%M%S).sql
```

### File Backup

```bash
# Backup uploaded files (book covers)
tar -czf uploads_backup_$(date +%Y%m%d).tar.gz storage/app/public/

# Backup the entire application
tar -czf app_backup_$(date +%Y%m%d).tar.gz --exclude=node_modules --exclude=vendor .
```

### Backup Schedule

| Backup Type | Frequency | Retention |
|-------------|-----------|-----------|
| Database | Daily | 30 days |
| Uploaded files | Weekly | 12 weeks |
| Full application | Before updates | 3 versions |

### Restore from Backup

```bash
# Restore database
mysql -u root -p library_management < backup_file.sql

# Restore uploaded files
tar -xzf uploads_backup.tar.gz -C /path/to/project/
```

---

## 9. Maintenance

### Daily Tasks

```bash
# Check for overdue books and update status
php artisan library:check-overdue
```

### Weekly Tasks

```bash
# Create database backup
php artisan library:backup-db

# Clear old log files (optional)
truncate -s 0 storage/logs/laravel.log
```

### When Updating the Application

```bash
# 1. Enable maintenance mode
php artisan down

# 2. Backup database
php artisan library:backup-db

# 3. Pull updates
git pull origin main

# 4. Install dependencies
composer install --optimize-autoloader --no-dev
npm ci && npm run build

# 5. Run migrations
php artisan migrate --force

# 6. Clear and rebuild caches
php artisan library:clear-cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Disable maintenance mode
php artisan up
```

### Clearing Caches

```bash
# Clear all caches at once
php artisan library:clear-cache

# Or individually:
php artisan cache:clear     # Application cache
php artisan config:clear    # Configuration cache
php artisan route:clear     # Route cache
php artisan view:clear      # Compiled views
```

---

## 10. Security Checklist

### Application Security

- [ ] `APP_DEBUG=false` in production `.env`
- [ ] `APP_ENV=production` in `.env`
- [ ] Strong `APP_KEY` generated
- [ ] Strong database password set
- [ ] Default user passwords changed after first login
- [ ] `.env` file is NOT in version control (in `.gitignore`)
- [ ] `storage/` and `bootstrap/cache/` are writable but not web-accessible

### Route Protection

- [ ] All routes require authentication (`auth` middleware)
- [ ] Admin-only routes use `admin` middleware
- [ ] Settings routes are admin-only
- [ ] No sensitive data exposed in public routes

### Data Security

- [ ] CSRF protection enabled on all forms (Laravel default)
- [ ] SQL injection prevented (Eloquent ORM, parameterized queries)
- [ ] XSS protection (Blade `{{ }}` auto-escaping)
- [ ] File upload validation (type, size limits)
- [ ] Password hashing (bcrypt via Laravel)
- [ ] Session encryption enabled in production

### Server Security

- [ ] SSL/HTTPS enabled (recommended)
- [ ] Directory listing disabled
- [ ] `.env` file not accessible via web
- [ ] `storage/` directory not accessible via web
- [ ] Regular security updates applied

---

## 11. Rollback Procedure

If a deployment goes wrong:

```bash
# 1. Enable maintenance mode
php artisan down

# 2. Revert code to previous version
git checkout <previous-commit-hash>

# 3. Restore database backup
mysql -u root -p library_management < /path/to/backup.sql

# 4. Reinstall dependencies
composer install --optimize-autoloader --no-dev
npm ci && npm run build

# 5. Clear caches
php artisan library:clear-cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Disable maintenance mode
php artisan up
```

---

*Deployment Guide - Bobon B Elementary School Library Management System*
*Last Updated: February 2026*
