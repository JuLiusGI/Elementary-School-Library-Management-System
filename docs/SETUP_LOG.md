# Project Setup Log

This document tracks the setup and development progress of the Library Management System.

---

## Phase 1.1: Project Initialization & Setup

**Date:** January 2026
**Status:** Completed

### Tasks Completed

#### 1. Laravel Project Initialization
- Created Laravel 12.48.1 project (latest version)
- Project location: `C:\xampp\htdocs\bes_library_sys`

#### 2. Authentication Setup
- Installed Laravel Breeze v2.3.8
- Configured Blade stack for authentication views
- Login, Register, Password Reset pages created

#### 3. Package Installation

| Package | Version | Status | Notes |
|---------|---------|--------|-------|
| `laravel/breeze` | 2.3.8 | Installed | Authentication scaffolding |
| `livewire/livewire` | 4.0.2 | Installed | Dynamic UI components |
| `barryvdh/laravel-dompdf` | 3.1.1 | Installed | PDF generation |
| `intervention/image-laravel` | 1.5.6 | Installed | Image handling |
| `maatwebsite/excel` | - | **Skipped** | Not compatible with Laravel 12 yet |

**Note on Excel Package:** The `maatwebsite/excel` package does not yet support Laravel 12. Alternative options:
- Wait for package update
- Use native PHP CSV export
- Use `openspout/openspout` as alternative

#### 4. Tailwind CSS Configuration

Custom colors added to `tailwind.config.js`:

```javascript
colors: {
    primary: {
        500: '#3B82F6',  // Blue - main actions
        // Full shade range 50-950
    },
    success: {
        500: '#10B981',  // Green - available, success
    },
    warning: {
        500: '#F59E0B',  // Yellow - due soon
    },
    danger: {
        500: '#EF4444',  // Red - overdue, errors
    },
}
```

#### 5. Environment Configuration

`.env` configured for:
- App name: "Library Management System"
- Database: MySQL (`library_management`)
- Filesystem: Public disk for file uploads
- URL: `http://localhost/bes_library_sys/public`

#### 6. Folder Structure Created

```
app/
├── Services/
│   ├── BorrowingService.php      # Book borrowing/return logic
│   ├── FineCalculationService.php # Fine calculation
│   └── ReportService.php         # Report generation
└── Http/
    └── Requests/                  # Form validation (exists from Breeze)
```

#### 7. Layout Templates Updated

**Main Layout (`resources/views/layouts/app.blade.php`):**
- Sidebar navigation with sections
- School branding (Bobon B Elementary School)
- User dropdown menu
- Flash message display
- Responsive mobile menu
- Footer with copyright

**Guest Layout (`resources/views/layouts/guest.blade.php`):**
- School logo and name
- Gradient background using primary colors
- Clean, centered card layout

#### 8. Storage Configuration
- Created public storage symbolic link
- Configured for book cover image uploads

#### 9. Assets Built
- Compiled CSS with custom Tailwind colors
- Production build created

---

## Files Created/Modified

### New Files Created

| File | Purpose |
|------|---------|
| `app/Services/BorrowingService.php` | Book borrowing and return business logic |
| `app/Services/FineCalculationService.php` | Fine calculation logic |
| `app/Services/ReportService.php` | Report generation logic |
| `docs/SETUP_LOG.md` | This file - setup documentation |
| `README.md` | Project documentation (replaced Laravel default) |

### Files Modified

| File | Changes |
|------|---------|
| `tailwind.config.js` | Added custom color palette |
| `.env` | Database and app configuration |
| `resources/views/layouts/app.blade.php` | Complete redesign with sidebar |
| `resources/views/layouts/guest.blade.php` | School branding added |

---

## Service Classes Documentation

### BorrowingService.php
Handles all book borrowing and returning operations:
- `canBorrow(Student)` - Check if student can borrow
- `borrowBook(Student, Book, User)` - Process borrowing
- `returnBook(Transaction)` - Process return
- `getCurrentBorrowedBooks(Student)` - Get student's borrowed books
- `getRemainingBorrowingCapacity(Student)` - Check how many more books can be borrowed

### FineCalculationService.php
Handles fine calculation:
- `calculateFine(Transaction)` - Calculate fine amount
- `getDaysOverdue(Transaction)` - Get days overdue
- `isOverdue(Transaction)` - Check if overdue
- `getFinePolicy()` - Get current fine settings
- `markFinePaid(Transaction)` - Mark fine as paid
- `waiveFine(Transaction, reason)` - Waive fine (admin)

### ReportService.php
Generates various reports:
- `getDailyTransactions(date)` - Daily transaction report
- `getOverdueBooks()` - Overdue books report
- `getStudentBorrowingHistory(Student)` - Student history
- `getMostBorrowedBooks(limit)` - Popular books
- `getBooksByCategory()` - Category distribution
- `getStudentsWithFines()` - Students with unpaid fines
- `getInventoryReport()` - Inventory statistics
- `getCirculationStatistics(start, end)` - Circulation stats
- `getDashboardStatistics()` - Dashboard overview

---

## Phase 1.2: Database Migrations

**Date:** January 2026
**Status:** Completed

### Migrations Created

| Migration File | Table | Description |
|----------------|-------|-------------|
| `add_role_to_users_table` | users | Adds `role` enum column (admin/librarian) |
| `create_students_table` | students | Student records with grade levels |
| `create_categories_table` | categories | Book categories |
| `create_books_table` | books | Book catalog with inventory tracking |
| `create_transactions_table` | transactions | Borrowing records with fines |
| `create_settings_table` | settings | Key-value system configuration |

### Database Schema Summary

#### users (modified)
- Added: `role` enum('admin', 'librarian') default 'librarian'

#### students
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| student_id | varchar(50) | Unique school ID |
| first_name | varchar(100) | Required |
| last_name | varchar(100) | Required |
| middle_name | varchar(100) | Nullable |
| grade_level | enum(1-6) | Grade 1 to 6 |
| section | varchar(50) | Class section |
| status | enum | active/inactive/graduated |
| contact_number | varchar(20) | Nullable |
| guardian_name | varchar(255) | Nullable |
| guardian_contact | varchar(20) | Nullable |
| timestamps | | created_at, updated_at |

**Indexes:** student_id, grade_level, status (composite)

#### categories
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | varchar(100) | Unique category name |
| description | text | Nullable |
| timestamps | | created_at, updated_at |

#### books
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| accession_number | varchar(50) | Unique library ID |
| isbn | varchar(13) | Nullable |
| title | varchar(255) | Required |
| author | varchar(255) | Required |
| publisher | varchar(255) | Nullable |
| publication_year | year | Nullable |
| category_id | foreignId | FK to categories |
| edition | varchar(50) | Nullable |
| pages | int | Nullable |
| copies_total | int | Default 1 |
| copies_available | int | Default 1 |
| location | varchar(100) | Nullable |
| condition | enum | excellent/good/fair/poor |
| description | text | Nullable |
| cover_image | varchar(255) | Nullable |
| status | enum | available/unavailable |
| timestamps | | created_at, updated_at |

**Indexes:** accession_number, isbn, category_id, status (composite)

#### transactions
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| student_id | foreignId | FK to students |
| book_id | foreignId | FK to books |
| librarian_id | foreignId | FK to users |
| borrowed_date | date | Required |
| due_date | date | Required |
| returned_date | date | Nullable |
| status | enum | borrowed/returned/overdue |
| notes | text | Nullable |
| fine_amount | decimal(8,2) | Default 0.00 |
| fine_paid | boolean | Default false |
| timestamps | | created_at, updated_at |

**Indexes:** student_id, book_id, status, due_date, (student_id, status), (student_id, book_id, status, due_date)

#### settings
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| key | varchar(100) | Unique setting key |
| value | text | Setting value |
| description | varchar(255) | Nullable |
| timestamps | | created_at, updated_at |

### To Run Migrations

1. Start MySQL in XAMPP Control Panel
2. Create database:
   ```sql
   CREATE DATABASE library_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Run migrations:
   ```bash
   php artisan migrate
   ```

---

## Phase 1.3: Eloquent Models

**Date:** January 2026
**Status:** Completed

### Models Created/Updated

| Model | File | Description |
|-------|------|-------------|
| User | `app/Models/User.php` | Updated with role, relationships, and helper methods |
| Student | `app/Models/Student.php` | Student records with borrowing logic |
| Category | `app/Models/Category.php` | Book categories |
| Book | `app/Models/Book.php` | Book catalog with availability tracking |
| Transaction | `app/Models/Transaction.php` | Borrowing records with fine calculation |
| Setting | `app/Models/Setting.php` | Key-value system configuration |

### Model Features Summary

#### User Model
- **Fillable:** name, email, password, role
- **Relationships:** `hasMany(Transaction::class, 'librarian_id')`
- **Methods:**
  - `isAdmin()` - Check if user is admin
  - `isLibrarian()` - Check if user is librarian
  - `canManageLibrary()` - Check if user can manage library

#### Student Model
- **Fillable:** student_id, first_name, last_name, middle_name, grade_level, section, status, contact_number, guardian_name, guardian_contact
- **Casts:** grade_level (string), status (string)
- **Relationships:** `hasMany(Transaction::class)`
- **Accessors:**
  - `full_name` - "Last, First Middle" format
  - `display_name` - "First Last" format
  - `grade_display` - "Grade X" format
- **Scopes:**
  - `scopeActive()` - Filter active students
  - `scopeInGrade($grade)` - Filter by grade level
  - `scopeSearch($term)` - Search by name or ID
- **Methods:**
  - `canBorrow()` - Check if student can borrow more books
  - `currentBorrowedBooks()` - Get currently borrowed books
  - `remainingBorrowingCapacity()` - Get remaining borrowing slots
  - `totalFines()` - Calculate total unpaid fines
  - `hasUnpaidFines()` - Check for unpaid fines
  - `hasOverdueBooks()` - Check for overdue books

#### Category Model
- **Fillable:** name, description
- **Relationships:** `hasMany(Book::class)`
- **Methods:**
  - `bookCount()` - Count books in category
  - `availableBookCount()` - Count available books
  - `hasBooks()` - Check if category has books

#### Book Model
- **Fillable:** accession_number, isbn, title, author, publisher, publication_year, category_id, edition, pages, copies_total, copies_available, location, condition, description, cover_image, status
- **Casts:** publication_year (integer), copies_total (integer), copies_available (integer), pages (integer)
- **Relationships:**
  - `belongsTo(Category::class)`
  - `hasMany(Transaction::class)`
- **Scopes:**
  - `scopeAvailable()` - Filter available books
  - `scopeUnavailable()` - Filter unavailable books
  - `scopeInCategory($id)` - Filter by category
  - `scopeSearch($term)` - Search by title, author, ISBN
  - `scopeInCondition($condition)` - Filter by condition
- **Methods:**
  - `isAvailable()` - Check availability
  - `decrementCopy()` - Decrease available copies (when borrowed)
  - `incrementCopy()` - Increase available copies (when returned)
  - `copiesBorrowed()` - Get count of borrowed copies
- **Accessors:**
  - `cover_image_url` - Full URL to cover image
  - `publication_info` - Formatted publisher and year

#### Transaction Model
- **Fillable:** student_id, book_id, librarian_id, borrowed_date, due_date, returned_date, status, notes, fine_amount, fine_paid
- **Casts:** borrowed_date (date), due_date (date), returned_date (date), fine_amount (decimal:2), fine_paid (boolean)
- **Relationships:**
  - `belongsTo(Student::class)`
  - `belongsTo(Book::class)`
  - `belongsTo(User::class, 'librarian_id')`
- **Scopes:**
  - `scopeBorrowed()` - Currently borrowed
  - `scopeReturned()` - Returned transactions
  - `scopeOverdue()` - Overdue transactions
  - `scopeActive()` - Not yet returned (borrowed or overdue)
  - `scopeWithUnpaidFines()` - Has unpaid fines
  - `scopeDueOn($date)` - Due on specific date
  - `scopeDueBefore($date)` - Due before date
- **Methods:**
  - `isOverdue()` - Check if overdue
  - `daysOverdue()` - Calculate days overdue
  - `calculateFine()` - Calculate fine amount
  - `markAsReturned($date)` - Mark as returned
  - `markFinePaid()` - Mark fine as paid
  - `hasUnpaidFine()` - Check for unpaid fine
- **Accessors:**
  - `status_label` - Human-readable status
  - `status_color` - CSS class for status badge
  - `formatted_fine` - Fine with PHP currency symbol
  - `days_until_due` - Days until/since due date

#### Setting Model
- **Fillable:** key, value, description
- **Static Methods:**
  - `get($key, $default)` - Get setting value (with caching)
  - `set($key, $value, $description)` - Set setting value
  - `has($key)` - Check if setting exists
  - `remove($key)` - Delete setting
  - `getAll()` - Get all settings as array
  - `getMany($keys, $defaults)` - Get multiple settings
  - `clearCache()` - Clear settings cache
- **Type-Specific Getters:**
  - `getInt($key, $default)` - Get as integer
  - `getFloat($key, $default)` - Get as float
  - `getBool($key, $default)` - Get as boolean
- **Library Convenience Methods:**
  - `maxBooksPerStudent()` - Get max books setting
  - `borrowingPeriod()` - Get borrowing period setting
  - `finePerDay()` - Get fine per day setting
  - `gracePeriod()` - Get grace period setting
  - `schoolName()` - Get school name setting

---

## Phase 1 Complete

All Phase 1 tasks have been completed:
- [x] Phase 1.1: Project Initialization & Setup
- [x] Phase 1.2: Database Migrations
- [x] Phase 1.3: Eloquent Models

### To Run the Application

1. Start MySQL and Apache in XAMPP Control Panel
2. Create the database:
   ```sql
   CREATE DATABASE library_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Run migrations:
   ```bash
   php artisan migrate
   ```
4. Access the application: `http://localhost/bes_library_sys/public`

---

## Next Steps

### Phase 2: Core Functionality
- [ ] Create database seeders for initial data
- [ ] Build Book Management (CRUD)
- [ ] Build Student Management (CRUD)
- [ ] Build Category Management (CRUD)
- [ ] Implement Borrowing System
- [ ] Implement Return System

---

*Log Updated: January 2026*
