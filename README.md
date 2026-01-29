# Bobon B Elementary School Library Management System

A web-based library management system designed specifically for Bobon B Elementary School in Southern Leyte, Philippines. This system manages book inventory, student borrowing, returns, and provides reporting for librarians and administrators.

---

## Project Information

| Item | Details |
|------|---------|
| **Project Name** | Bobon B Elementary School Library Management System |
| **Version** | 1.0.0 |
| **Status** | In Development |
| **Location** | Southern Leyte, Philippines |

---

## Tech Stack

| Technology | Version | Purpose |
|------------|---------|---------|
| **Laravel** | 12.x | PHP Framework (MVC Architecture) |
| **PHP** | 8.2+ | Server-side Language |
| **MySQL** | 8.0+ | Database |
| **Blade** | - | Templating Engine |
| **Livewire** | 4.x | Dynamic UI Components |
| **Tailwind CSS** | 3.x | Styling Framework |
| **Alpine.js** | 3.x | JavaScript Framework (via Livewire) |

### Installed Packages

| Package | Purpose |
|---------|---------|
| `laravel/breeze` | Authentication scaffolding |
| `livewire/livewire` | Dynamic components without JavaScript |
| `barryvdh/laravel-dompdf` | PDF generation for reports |
| `intervention/image-laravel` | Image handling for book covers |

---

## Features

### Core Features
- **Student Management**: Register, update, and track student records
- **Book Catalog**: Manage book inventory with categories
- **Borrowing System**: Process book checkouts with due date tracking
- **Return System**: Handle book returns with automatic fine calculation
- **Fine Management**: Calculate and track overdue fines
- **Reports**: Generate various library reports (PDF export)

### User Roles
| Role | Access Level |
|------|--------------|
| **Admin** | Full system access, user management, settings |
| **Librarian** | Student/book management, transactions, reports |

### Business Rules (Configurable)
- Maximum books per student: **3**
- Borrowing period: **7 days**
- Fine per overdue day: **₱5.00**
- Grace period: **1 day**

---

## Requirements

### Server Requirements
- PHP 8.2 or higher
- MySQL 8.0 or higher
- Composer 2.x
- Node.js 18+ & NPM
- Apache/Nginx web server

### PHP Extensions Required
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- GD (for image processing)

### Recommended Development Environment
- **XAMPP** (Windows) - Includes Apache, MySQL, PHP
- **Laravel Valet** (macOS)
- **Laravel Sail** (Docker)

---

## Installation Guide

### Step 1: Clone or Download the Project

```bash
# If using git
git clone <repository-url> bes_library_sys
cd bes_library_sys

# Or extract the downloaded zip to your web server directory
# For XAMPP: C:\xampp\htdocs\bes_library_sys
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Install Node.js Dependencies

```bash
npm install
```

### Step 4: Environment Configuration

Copy the example environment file and configure it:

```bash
# Linux/macOS
cp .env.example .env

# Windows
copy .env.example .env
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

### Step 5: Generate Application Key

```bash
php artisan key:generate
```

### Step 6: Create the Database

Using phpMyAdmin or MySQL command line:

```sql
CREATE DATABASE library_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 7: Run Database Migrations

```bash
php artisan migrate
```

### Step 8: Seed Initial Data (Optional)

```bash
php artisan db:seed
```

### Step 9: Create Storage Link

```bash
php artisan storage:link
```

### Step 10: Build Frontend Assets

```bash
# For development (with hot reload)
npm run dev

# For production
npm run build
```

### Step 11: Start the Development Server

```bash
# Option 1: Laravel's built-in server
php artisan serve

# Option 2: Access via XAMPP
# Navigate to: http://localhost/bes_library_sys/public
```

---

## Default Credentials

After seeding the database, use these credentials to log in:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@bobon.edu.ph | password |
| Librarian | librarian1@bobon.edu.ph | password |
| Librarian | librarian2@bobon.edu.ph | password |

> **Important**: Change these passwords immediately after first login in a production environment!

---

## Project Structure

```
bes_library_sys/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Request handlers
│   │   ├── Middleware/         # Request filters
│   │   └── Requests/           # Form validation classes
│   ├── Models/                 # Eloquent models
│   ├── Services/               # Business logic classes
│   │   ├── BorrowingService.php
│   │   ├── FineCalculationService.php
│   │   └── ReportService.php
│   └── Livewire/               # Livewire components
├── database/
│   ├── migrations/             # Database schema
│   ├── seeders/                # Initial data
│   └── factories/              # Test data generators
├── resources/
│   ├── views/
│   │   ├── layouts/            # Page layouts
│   │   ├── auth/               # Login/register pages
│   │   ├── dashboard/          # Dashboard views
│   │   ├── students/           # Student management views
│   │   ├── books/              # Book management views
│   │   ├── transactions/       # Borrowing/return views
│   │   ├── reports/            # Report views
│   │   └── settings/           # System settings views
│   ├── css/
│   └── js/
├── routes/
│   └── web.php                 # Web routes
├── public/
│   ├── build/                  # Compiled assets
│   └── storage/                # Public file uploads
├── storage/
│   └── app/public/             # Uploaded files (book covers)
├── docs/
│   └── SETUP_LOG.md            # Development progress log
└── tests/                      # Automated tests
```

---

## Configuration

### Tailwind CSS Custom Colors

The application uses a custom color scheme defined in `tailwind.config.js`:

| Color | Hex Code | Usage |
|-------|----------|-------|
| Primary (Blue) | `#3B82F6` | Main actions, navigation, buttons |
| Success (Green) | `#10B981` | Success messages, available status |
| Warning (Yellow) | `#F59E0B` | Warnings, due soon alerts |
| Danger (Red) | `#EF4444` | Errors, overdue status, delete actions |

### Library Settings

These settings can be configured in the admin panel:

| Setting | Default | Description |
|---------|---------|-------------|
| `max_books_per_student` | 3 | Maximum books a student can borrow |
| `borrowing_period` | 7 | Days until book is due |
| `fine_per_day` | 5.00 | Fine amount in PHP per overdue day |
| `grace_period` | 1 | Days before fines start |

---

## Usage

### For Librarians

1. **Log in** with your librarian credentials
2. **Dashboard** shows overview statistics and alerts
3. **Students** - Add/edit student records
4. **Books** - Manage book catalog and categories
5. **Borrow Book** - Process student book checkouts
6. **Return Book** - Process returns and calculate fines
7. **Reports** - Generate and export reports

### For Administrators

All librarian features plus:
- **Users** - Manage librarian accounts
- **Settings** - Configure system parameters

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

## Troubleshooting

### Common Issues

**1. "Class not found" errors**
```bash
composer dump-autoload
```

**2. Storage permission issues**
```bash
# Linux/macOS
chmod -R 775 storage bootstrap/cache

# Windows: Run as Administrator
```

**3. Database connection refused**
- Ensure MySQL is running (check XAMPP Control Panel)
- Verify `.env` database credentials

**4. Assets not loading**
```bash
npm run build
php artisan cache:clear
```

**5. Session/CSRF token mismatch**
```bash
php artisan cache:clear
php artisan config:clear
```

---

## Documentation

| Document | Description |
|----------|-------------|
| [Setup Log](docs/SETUP_LOG.md) | Development progress and setup documentation |

---

## Support

For issues and questions:
1. Check the [Troubleshooting](#troubleshooting) section
2. Review the [Setup Log](docs/SETUP_LOG.md) for development details
3. Contact the system administrator

---

## License

This project is developed for Bobon B Elementary School. All rights reserved.

---

## Changelog

### Version 1.0.0 (In Development)

#### Phase 1 - Completed (January 2026)
- Project initialization with Laravel 12
- Database migrations for all tables (users, students, books, categories, transactions, settings)
- Eloquent models with relationships and helper methods
- Custom Tailwind CSS color scheme
- Layout templates with sidebar navigation

#### Phase 2.1 - Completed (January 2026)
- Student CRUD management
- Livewire-powered search table with real-time filtering
- Student statistics dashboard
- Soft delete support for students
- Form validation with custom error messages

#### Phase 2.2 - Completed (January 2026)
- Book catalog with grid/list view toggle
- Category management with inline editing
- Cover image upload (max 2MB)
- Auto-generated accession numbers (YEAR-#### format)
- Color-coded availability indicators
- Book statistics dashboard
- Borrowing history on book detail pages

#### Phase 3.1 - Completed (January 2026)
- Multi-step book borrowing form (Livewire)
- Student eligibility checking (max books, overdue, unpaid fines)
- Book return processing with condition updates
- Automatic fine calculation (grace period + daily rate)
- Transaction history with filtering and pagination
- Fine payment and waiver functionality
- Real-time search for borrowed books

#### Phase 3.2 - Completed (January 2026)
- Fine management dashboard with statistics
- Detailed fine calculation breakdown display
- Payment recording with method tracking (cash, GCash, Maya, bank)
- Fine waiver functionality (admin only)
- Scheduled task for automatic overdue updates
- Enhanced return interface with fine preview
- Sidebar navigation updated with Fines link

#### Phase 4.1 - Completed (January 2026)
- Reports dashboard with quick statistics
- Daily transactions report with date navigation
- Overdue books report with sortable columns
- Most borrowed books report with date range filtering
- Inventory report with utilization statistics
- Monthly statistics report with charts
- PDF export functionality (DomPDF)
- CSV export functionality
- Chart.js visualizations (bar, pie, line charts)

#### Phase 4.2 - Completed (January 2026)
- System settings management interface
- Grouped settings (School, Circulation, Fines, System)
- Admin-only access with middleware
- Form validation with error messages
- Reset to defaults functionality
- SettingService with caching support
- SettingSeeder for default values

#### Phase 5.1 - Completed (January 2026)
- Main dashboard with statistics cards
- Real-time auto-refresh (60 seconds) with Livewire
- Weekly activity line chart (borrowed/returned)
- Books by category doughnut chart
- Top borrowed books bar chart
- Overdue books alert section
- Low stock books warning
- Recent transactions table
- Quick action buttons and navigation cards
- Welcome message with user name and current date

#### Phase 5.2 - Completed (January 2026)
- Dark mode toggle with localStorage persistence
- Reusable Blade components (Alert, Breadcrumb, Confirm Modal, Tooltip, Loading Spinner, Skeleton Table)
- Custom error pages (404, 403, 500, 503)
- Print-friendly styles for reports
- Global Livewire loading indicator
- Keyboard shortcuts (Ctrl+K for search, Escape for modals)
- Database performance indexes migration
- Improved flash message animations
- Mobile responsiveness improvements

#### Phase 6.1 - Completed (January 2026)
- Model factories: StudentFactory, BookFactory, TransactionFactory
- UserSeeder: 1 admin (admin@bobon.edu.ph) + 2 librarians
- CategorySeeder: 11 categories (Fiction, Science, Filipino Literature, etc.)
- StudentSeeder: 50 students using factory (Grades 1-6, Sections A/B/C)
- BookSeeder: 100 books using factory (distributed across categories)
- TransactionSeeder: 85 transactions (30 active, 50 returned, 5 overdue)
- DatabaseSeeder orchestrating all seeders in correct order

#### Phase 6.2 - Pending
- User guide documentation
- Final testing and review

---

*Last Updated: January 2026*
