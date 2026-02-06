# Bobon B Elementary School - Library Management System

A web-based library management system designed specifically for **Bobon B Elementary School** in Southern Leyte, Philippines. This system streamlines book inventory management, student borrowing, returns, fine tracking, and reporting for librarians and administrators.

![Laravel](https://img.shields.io/badge/Laravel-12.x-red)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue)
![Livewire](https://img.shields.io/badge/Livewire-4.x-purple)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-teal)
![License](https://img.shields.io/badge/License-Proprietary-green)

---

## Table of Contents

- [Features](#features)
- [Screenshots](#screenshots)
- [Tech Stack](#tech-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Default Credentials](#default-credentials)
- [Configuration](#configuration)
- [Usage](#usage)
- [Custom Artisan Commands](#custom-artisan-commands)
- [Project Structure](#project-structure)
- [Development](#development)
- [Deployment](#deployment)
- [Troubleshooting](#troubleshooting)
- [Documentation](#documentation)
- [License](#license)
- [Changelog](#changelog)

---

## Features

### Core Features
- **Dashboard** - Real-time statistics, charts, overdue alerts, and quick actions
- **Student Management** - Full CRUD with search, filtering, grade/section management
- **Book Catalog** - Inventory management with categories, cover images, grid/list views
- **Borrowing System** - Multi-step checkout with eligibility checks and due date tracking
- **Return System** - Book returns with automatic fine calculation and condition tracking
- **Fine Management** - Overdue fines with payment recording, waivers, and tracking
- **Reports** - Daily transactions, overdue, most borrowed, inventory, and monthly statistics
- **PDF/CSV Export** - Export reports to PDF and CSV formats
- **System Settings** - Configurable borrowing rules, school info, and system preferences

### UI Features
- **Dark Mode** - Toggle with localStorage persistence
- **Responsive Design** - Mobile-friendly layout with sidebar navigation
- **Real-time Search** - Livewire-powered search and filtering
- **Charts** - Chart.js visualizations (line, bar, doughnut charts)
- **Keyboard Shortcuts** - Ctrl+K for search, Escape for modals
- **Print-friendly** - Optimized print styles for reports
- **Loading States** - Spinners, skeleton tables, and loading indicators
- **Custom Error Pages** - Styled 404, 403, 500, and 503 pages

### User Roles

| Role | Access Level |
|------|-------------|
| **Admin** | Full system access: students, books, transactions, reports, settings, user management |
| **Librarian** | Student/book management, borrowing/returns, fine management, reports |

### Configurable Business Rules

| Setting | Default | Description |
|---------|---------|-------------|
| Max Books per Student | 3 | Maximum books a student can borrow simultaneously |
| Borrowing Period | 7 days | Number of days before a book is due |
| Fine per Day | ₱5.00 | Daily overdue fine amount |
| Grace Period | 1 day | Days before fines start accumulating |
| Max Fine Amount | ₱100.00 | Maximum fine cap per transaction |

---

## Screenshots

> **Note:** Screenshots will be added after deployment.

| Screen | Description |
|--------|-------------|
| Dashboard | Statistics cards, charts, alerts, and quick actions |
| Student Management | Student list with search, filter, and CRUD operations |
| Book Catalog | Grid/list view with cover images and availability status |
| Borrow Book | Multi-step form with student/book search and eligibility checks |
| Return Book | Return processing with fine preview and calculation |
| Reports | Various reports with PDF/CSV export and chart visualizations |
| Settings | System configuration with grouped settings panels |
| Dark Mode | Full dark mode support across all pages |

---

## Tech Stack

| Technology | Version | Purpose |
|------------|---------|---------|
| **Laravel** | 12.x | PHP Framework (MVC Architecture) |
| **PHP** | 8.2+ | Server-side programming language |
| **MySQL** | 8.0+ | Relational database |
| **Blade** | - | Server-side templating engine |
| **Livewire** | 4.x | Dynamic UI components without writing JavaScript |
| **Tailwind CSS** | 3.x | Utility-first CSS framework |
| **Alpine.js** | 3.x | Lightweight JavaScript framework (via Livewire) |
| **Chart.js** | 4.x | Chart visualizations |
| **Vite** | 6.x | Frontend build tool |

### Key Packages

| Package | Purpose |
|---------|---------|
| `laravel/breeze` | Authentication scaffolding (login, register, password reset) |
| `livewire/livewire` | Dynamic components without full JavaScript |
| `barryvdh/laravel-dompdf` | PDF generation for reports |
| `intervention/image-laravel` | Image handling for book covers |

---

## Requirements

### Server Requirements
- PHP 8.2 or higher
- MySQL 8.0 or higher (or MariaDB 10.3+)
- Composer 2.x
- Node.js 18+ and NPM
- Apache or Nginx web server

### Required PHP Extensions
- BCMath, Ctype, Fileinfo, JSON
- Mbstring, OpenSSL, PDO, PDO_MySQL
- Tokenizer, XML, GD (for image processing)

### Recommended Development Environment
- **XAMPP** (Windows) - Includes Apache, MySQL, PHP
- **Laravel Valet** (macOS)
- **Laravel Sail** (Docker)

---

## Installation

### Quick Start (5 minutes)

```bash
# 1. Clone the repository
git clone <repository-url> bes_library_sys
cd bes_library_sys

# 2. Install PHP dependencies
composer install

# 3. Install Node.js dependencies
npm install

# 4. Configure environment
copy .env.example .env          # Windows
# cp .env.example .env          # Linux/macOS

# 5. Generate application key
php artisan key:generate

# 6. Edit .env file with your database credentials (see Database Setup below)

# 7. Run migrations and seed data
php artisan migrate --seed

# 8. Create storage link (for book cover uploads)
php artisan storage:link

# 9. Build frontend assets
npm run build

# 10. Start the server
php artisan serve
# Or access via XAMPP: http://localhost/bes_library_sys/public
```

### Detailed Installation Steps

#### Step 1: Clone or Download

```bash
# Using git
git clone <repository-url> bes_library_sys
cd bes_library_sys

# Or extract the downloaded zip to your web server directory
# For XAMPP: C:\xampp\htdocs\bes_library_sys
```

#### Step 2: Install Dependencies

```bash
# PHP dependencies
composer install

# Node.js dependencies
npm install
```

#### Step 3: Environment Configuration

```bash
# Copy the example environment file
copy .env.example .env          # Windows
cp .env.example .env            # Linux/macOS

# Generate application key
php artisan key:generate
```

Edit the `.env` file with your database settings:

```env
APP_NAME="Library Management System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost/bes_library_sys/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=library_management
DB_USERNAME=root
DB_PASSWORD=
```

#### Step 4: Create the Database

Using phpMyAdmin or MySQL command line:

```sql
CREATE DATABASE library_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### Step 5: Run Migrations and Seed Data

```bash
# Run all migrations to create database tables
php artisan migrate

# Seed initial data (users, categories, sample data)
php artisan db:seed
```

Or do both in one command:

```bash
php artisan migrate --seed
```

#### Step 6: Create Storage Link

```bash
php artisan storage:link
```

#### Step 7: Build Frontend Assets

```bash
# For development (with hot reload)
npm run dev

# For production (minified)
npm run build
```

#### Step 8: Start the Server

```bash
# Option 1: Laravel's built-in server
php artisan serve

# Option 2: Access via XAMPP
# Navigate to: http://localhost/bes_library_sys/public
```

---

## Database Setup

### Fresh Installation (Recommended)

This creates all tables and populates with sample data:

```bash
php artisan migrate:fresh --seed
```

### What Gets Seeded

| Data | Count | Details |
|------|-------|---------|
| Users | 3 | 1 admin + 2 librarians |
| Categories | 11 | Fiction, Science, Filipino Literature, etc. |
| Students | 50 | Grades 1-6, Sections A/B/C, Filipino names |
| Books | 100 | Distributed across all categories |
| Transactions | 85 | 30 active + 50 returned + 5 overdue |
| Settings | 15 | System configuration defaults |

### Running Individual Seeders

```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=StudentSeeder
php artisan db:seed --class=BookSeeder
php artisan db:seed --class=TransactionSeeder
```

---

## Default Credentials

After seeding the database, use these credentials to log in:

| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@bobon.edu.ph | password |
| **Librarian 1** | librarian1@bobon.edu.ph | password |
| **Librarian 2** | librarian2@bobon.edu.ph | password |

> **IMPORTANT:** Change these passwords immediately after first login in a production environment!

---

## Configuration

### Library Settings (Admin Panel)

These settings can be changed via the admin settings page:

| Setting | Default | Description |
|---------|---------|-------------|
| `school_name` | Bobon B Elementary School | School name displayed in reports |
| `library_name` | School Library | Library name |
| `library_hours` | 7:00 AM - 5:00 PM | Operating hours |
| `max_books_per_student` | 3 | Max books a student can borrow |
| `borrowing_period` | 7 | Days until book is due |
| `fine_per_day` | 5.00 | Fine per overdue day (₱) |
| `grace_period` | 1 | Grace days before fines start |
| `max_fine_amount` | 100.00 | Maximum fine cap (₱) |
| `items_per_page` | 15 | Items shown per page in lists |

### Tailwind CSS Custom Colors

| Color | Hex | Usage |
|-------|-----|-------|
| Primary (Blue) | `#3B82F6` | Actions, navigation, buttons |
| Success (Green) | `#10B981` | Success messages, available status |
| Warning (Yellow) | `#F59E0B` | Warnings, due soon alerts |
| Danger (Red) | `#EF4444` | Errors, overdue status, delete actions |

---

## Usage

### For Librarians

1. **Log in** with your credentials at the login page
2. **Dashboard** - View statistics, overdue alerts, and quick actions
3. **Students** - Add, edit, search, and manage student records
4. **Books** - Manage book catalog, categories, and cover images
5. **Borrow Book** - Process student book checkouts with eligibility checks
6. **Return Book** - Process returns, view fine previews, update book condition
7. **Fines** - View unpaid fines, record payments
8. **Reports** - Generate and export reports (PDF/CSV)

### For Administrators

All librarian features plus:
- **Settings** - Configure borrowing rules, school info, system preferences
- **Users** - Manage librarian accounts (future feature)

---

## Custom Artisan Commands

| Command | Description |
|---------|-------------|
| `php artisan library:check-overdue` | Check and update overdue book transactions |
| `php artisan library:check-overdue --dry-run` | Preview overdue changes without updating |
| `php artisan library:backup-db` | Create a MySQL database backup |
| `php artisan library:clear-cache` | Clear all application caches at once |

---

## Project Structure

```
bes_library_sys/
├── app/
│   ├── Console/Commands/          # Custom artisan commands
│   ├── Http/
│   │   ├── Controllers/           # Request handlers
│   │   ├── Middleware/             # Request filters (admin, auth)
│   │   └── Requests/              # Form validation classes
│   ├── Livewire/                  # Livewire components
│   ├── Models/                    # Eloquent models (User, Student, Book, etc.)
│   └── Services/                  # Business logic
│       ├── BorrowingService.php   # Borrowing/return logic
│       ├── FineCalculationService.php  # Fine calculations
│       └── ReportService.php      # Report generation
├── database/
│   ├── factories/                 # Model factories (Student, Book, Transaction)
│   ├── migrations/                # Database schema
│   └── seeders/                   # Seed data (Users, Categories, Students, etc.)
├── resources/views/
│   ├── layouts/                   # Page layouts (app, guest)
│   ├── components/                # Reusable Blade components
│   ├── auth/                      # Login/register pages
│   ├── dashboard/                 # Dashboard views
│   ├── students/                  # Student management views
│   ├── books/                     # Book management views
│   ├── transactions/              # Borrowing/return views
│   ├── reports/                   # Report views
│   ├── settings/                  # Settings views
│   └── errors/                    # Custom error pages (404, 403, 500, 503)
├── routes/web.php                 # All web routes
├── docs/                          # Documentation
│   ├── SETUP_LOG.md               # Development progress log
│   ├── USER_GUIDE.md              # User guide for librarians
│   ├── DEPLOYMENT.md              # Deployment checklist
│   ├── TESTING_CHECKLIST.md       # Testing checklist
│   └── TECHNICAL_SPEC.md          # Technical specification
└── tests/                         # Automated tests
```

---

## Development

### Running Development Server

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server (for hot reload)
npm run dev
```

### Building for Production

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Running Tests

```bash
php artisan test
```

### Code Style

This project follows:
- **PSR-12** coding standards
- **Laravel conventions** for naming and structure
- **Comprehensive commenting** for beginner developers

---

## Deployment

For detailed deployment instructions, see [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md).

### Quick Deployment Checklist

```bash
# 1. Set environment to production
# Edit .env: APP_ENV=production, APP_DEBUG=false

# 2. Install dependencies (no dev packages)
composer install --optimize-autoloader --no-dev

# 3. Build assets
npm run build

# 4. Run migrations
php artisan migrate --force

# 5. Seed initial data (first time only)
php artisan db:seed --force

# 6. Create storage link
php artisan storage:link

# 7. Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Set proper permissions (Linux/macOS)
chmod -R 775 storage bootstrap/cache
```

---

## Troubleshooting

### Common Issues

**1. "Class not found" errors**
```bash
composer dump-autoload
php artisan cache:clear
```

**2. Database connection refused**
- Ensure MySQL is running (check XAMPP Control Panel)
- Verify `.env` database credentials
- Check that `library_management` database exists

**3. Storage/permission issues**
```bash
# Linux/macOS
chmod -R 775 storage bootstrap/cache

# Windows: Run command prompt as Administrator
php artisan storage:link
```

**4. Assets not loading (blank page or unstyled)**
```bash
npm run build
php artisan cache:clear
php artisan view:clear
```

**5. Session/CSRF token mismatch**
```bash
php artisan cache:clear
php artisan config:clear
php artisan session:table   # If using database sessions
php artisan migrate
```

**6. Livewire components not loading**
```bash
php artisan livewire:publish --assets
php artisan view:clear
```

**7. Book cover images not showing**
```bash
php artisan storage:link
# Ensure storage/app/public directory exists
```

**8. PDF export not working**
```bash
composer require barryvdh/laravel-dompdf
php artisan cache:clear
```

---

## Documentation

| Document | Description |
|----------|-------------|
| [User Guide](docs/USER_GUIDE.md) | Step-by-step guide for librarians and admins |
| [Deployment Guide](docs/DEPLOYMENT.md) | Production deployment checklist |
| [Testing Checklist](docs/TESTING_CHECKLIST.md) | QA testing checklist |
| [Technical Spec](docs/TECHNICAL_SPEC.md) | Full technical specification |
| [Setup Log](docs/SETUP_LOG.md) | Development progress log |

---

## Support

For issues and questions:
1. Check the [Troubleshooting](#troubleshooting) section above
2. Review the [User Guide](docs/USER_GUIDE.md)
3. Check the [Setup Log](docs/SETUP_LOG.md) for development details
4. Contact the system administrator

---

## License

This project is developed exclusively for **Bobon B Elementary School**, Southern Leyte, Philippines. All rights reserved.

This software is proprietary and confidential. Unauthorized copying, distribution, or modification of this software is strictly prohibited.

---

## Changelog

### Version 1.0.0

#### Phase 1 - Project Setup (January 2026)
- Laravel 12 project initialization with authentication
- Database migrations for all tables (users, students, books, categories, transactions, settings)
- Eloquent models with relationships, scopes, and helper methods
- Custom Tailwind CSS color scheme and layout templates

#### Phase 2 - Student & Book Management (January 2026)
- Student CRUD with Livewire search, filtering, and statistics
- Book catalog with grid/list view, categories, and cover image upload
- Auto-generated accession numbers and color-coded availability

#### Phase 3 - Borrowing & Returns (January 2026)
- Multi-step borrowing form with eligibility checking
- Return processing with automatic fine calculation
- Transaction history with filtering and fine management
- Payment recording and fine waiver functionality

#### Phase 4 - Reports & Settings (January 2026)
- Reports dashboard with 5 report types (daily, overdue, most borrowed, inventory, monthly)
- PDF and CSV export functionality with Chart.js visualizations
- System settings management with grouped configuration panels

#### Phase 5 - Dashboard & UI Polish (January 2026)
- Main dashboard with statistics, charts, alerts, and quick actions
- Dark mode, reusable components, custom error pages, keyboard shortcuts
- Print-friendly styles and database performance indexes

#### Phase 6 - Testing & Documentation (February 2026)
- Model factories (StudentFactory, BookFactory, TransactionFactory)
- Comprehensive database seeders (3 users, 11 categories, 50 students, 100 books, 85 transactions)
- Custom artisan commands (check-overdue, backup-db, clear-cache)
- Complete documentation (README, User Guide, Deployment, Testing Checklist)
- Production optimization and security audit

---

*Last Updated: February 2026*
