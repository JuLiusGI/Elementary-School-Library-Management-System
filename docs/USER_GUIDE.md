# User Guide - Library Management System

## Bobon B Elementary School

This guide explains how to use the Library Management System step by step. It is written for librarians and administrators who will use the system daily.

---

## Table of Contents

1. [Getting Started](#1-getting-started)
2. [Dashboard Overview](#2-dashboard-overview)
3. [Managing Students](#3-managing-students)
4. [Managing Books](#4-managing-books)
5. [Managing Categories](#5-managing-categories)
6. [Processing Borrowing](#6-processing-borrowing)
7. [Processing Returns](#7-processing-returns)
8. [Managing Fines](#8-managing-fines)
9. [Generating Reports](#9-generating-reports)
10. [System Settings (Admin Only)](#10-system-settings-admin-only)
11. [Your Profile](#11-your-profile)
12. [Dark Mode](#12-dark-mode)
13. [Keyboard Shortcuts](#13-keyboard-shortcuts)
14. [Troubleshooting](#14-troubleshooting)

---

## 1. Getting Started

### How to Log In

1. Open your web browser (Chrome, Firefox, or Edge recommended)
2. Navigate to the system URL (e.g., `http://localhost/bes_library_sys/public`)
3. Enter your **email** and **password**
4. Click **"Log in"**

### Default Login Accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@bobon.edu.ph | password |
| Librarian 1 | librarian1@bobon.edu.ph | password |
| Librarian 2 | librarian2@bobon.edu.ph | password |

> **Tip:** Change your password after the first login by going to your Profile page.

### How to Log Out

1. Click your **name** in the top-right corner of the page
2. Click **"Log Out"** from the dropdown menu

---

## 2. Dashboard Overview

The dashboard is the first page you see after logging in. It provides a quick overview of the library.

### What You'll See

- **Statistics Cards** - Total books, available books, total students, active borrowings
- **Charts** - Weekly activity, books by category, top borrowed books
- **Overdue Alerts** - Books that are past their due date
- **Low Stock Warnings** - Books with few available copies
- **Recent Transactions** - Latest borrowing and return activities
- **Quick Actions** - Shortcut buttons to common tasks

### Auto-Refresh

The dashboard automatically refreshes every 60 seconds to show the latest data.

---

## 3. Managing Students

### Viewing All Students

1. Click **"Students"** in the left sidebar
2. You'll see a list of all students with search and filter options

### Searching for a Student

1. Type the student's **name**, **student ID**, or **grade** in the search box
2. Results update automatically as you type
3. Use the **Grade** and **Section** filters to narrow results

### Adding a New Student

1. Click **"Students"** in the sidebar
2. Click the **"Add Student"** button (top-right)
3. Fill in the required information:
   - **Student ID** - Unique ID number (format: YYYY-####)
   - **First Name** - Student's first name
   - **Last Name** - Student's last name
   - **Middle Name** - (Optional)
   - **Grade Level** - Select 1 through 6
   - **Section** - Student's section (A, B, C, etc.)
   - **Status** - Active, Inactive, or Graduated
   - **Guardian Name** - Parent or guardian's name
   - **Guardian Contact** - Phone number
4. Click **"Save Student"**

### Editing a Student

1. Find the student in the list
2. Click the **"Edit"** button (pencil icon)
3. Update the necessary information
4. Click **"Update Student"**

### Viewing Student Details

1. Click the student's **name** or the **"View"** button (eye icon)
2. You'll see their full information and borrowing history

### Deleting a Student

1. Find the student in the list
2. Click the **"Delete"** button (trash icon)
3. Confirm the deletion in the popup dialog

> **Note:** Students with active borrowings cannot be deleted. Return all books first.

---

## 4. Managing Books

### Viewing All Books

1. Click **"Books"** in the left sidebar
2. Toggle between **Grid View** (cards) and **List View** (table)
3. Filter by category, status, or search by title/author

### Adding a New Book

1. Click **"Books"** in the sidebar
2. Click the **"Add Book"** button
3. Fill in the book information:
   - **Title** - Full book title (required)
   - **Author** - Author name(s) (required)
   - **Category** - Select from dropdown (required)
   - **Accession Number** - Auto-generated (YYYY-#### format)
   - **ISBN** - International Standard Book Number (optional)
   - **Publisher** - Publishing company (optional)
   - **Publication Year** - Year published (optional)
   - **Edition** - Book edition (optional)
   - **Pages** - Number of pages (optional)
   - **Total Copies** - How many copies the library owns
   - **Location** - Shelf location (e.g., "Shelf A-1")
   - **Condition** - Excellent, Good, Fair, or Poor
   - **Cover Image** - Upload a cover photo (max 2MB, JPG/PNG)
   - **Description** - Brief description of the book
4. Click **"Save Book"**

### Editing a Book

1. Find the book in the catalog
2. Click **"Edit"**
3. Update the information
4. Click **"Update Book"**

### Viewing Book Details

1. Click the book's **title** or **"View"** button
2. You'll see full details, availability, and borrowing history

### Removing a Book Cover

1. Go to the book's edit page
2. Click **"Remove Cover"** below the current image
3. Confirm the removal

---

## 5. Managing Categories

### Viewing Categories

1. Click **"Categories"** in the left sidebar
2. You'll see all book categories with book counts

### Adding a New Category

1. Click **"Categories"** in the sidebar
2. Click **"Add Category"**
3. Enter the **Category Name** and **Description**
4. Click **"Save"**

### Editing a Category

1. Click **"Edit"** next to the category
2. Update the name or description
3. Click **"Update"**

### Deleting a Category

1. Click **"Delete"** next to the category
2. Confirm the deletion

> **Note:** Categories with books assigned to them cannot be deleted. Reassign the books first.

---

## 6. Processing Borrowing

### How to Borrow a Book for a Student

1. Click **"Borrow Book"** in the left sidebar
2. **Step 1 - Select Student:**
   - Type the student's name or ID in the search box
   - Select the student from the results
   - The system checks if the student is eligible:
     - Student must be **active**
     - Student must not have reached the **maximum book limit** (default: 3)
     - Student must not have **overdue books**
     - Student must not have **unpaid fines**
3. **Step 2 - Select Book:**
   - Type the book title or accession number in the search box
   - Select the book from the results
   - The system checks if the book is available (has copies)
4. **Step 3 - Confirm:**
   - Review the borrowing details
   - The **due date** is automatically calculated (default: 7 days)
   - Add optional notes
   - Click **"Process Borrowing"**
5. A success message confirms the transaction

### Eligibility Requirements

A student **cannot** borrow if:
- They already have 3 books borrowed (configurable in Settings)
- They have overdue books
- They have unpaid fines
- Their status is not "active"

---

## 7. Processing Returns

### How to Return a Book

1. Click **"Return Book"** in the left sidebar
2. **Search for the borrowed book:**
   - Type the student's name, book title, or accession number
   - Select the active transaction from the results
3. **Review the return:**
   - See the book details and borrowing information
   - If the book is **overdue**, the system shows:
     - Days overdue
     - Calculated fine amount
     - Fine breakdown (days x rate - grace period)
4. **Update book condition** (optional):
   - Select the book's condition after return
5. Click **"Process Return"**
6. The system automatically:
   - Updates the transaction status to "returned"
   - Calculates any overdue fine
   - Increases the book's available copies
   - Records the return date

### Fine Calculation

Fines are calculated as:
```
Fine = (Days Overdue - Grace Period) x Fine Per Day
```

Example: Book is 5 days overdue with 1-day grace period and ₱5.00/day rate:
```
Fine = (5 - 1) x ₱5.00 = ₱20.00
```

---

## 8. Managing Fines

### Viewing Fines

1. Click **"Fines"** in the left sidebar
2. You'll see:
   - **Summary cards** - Total fines, paid, unpaid amounts
   - **List of all fines** - With student info, amounts, and status

### Recording a Payment

1. Find the unpaid fine in the list
2. Click **"Record Payment"**
3. Select the payment method:
   - Cash
   - GCash
   - Maya
   - Bank Transfer
4. Click **"Confirm Payment"**
5. The fine is marked as paid

### Waiving a Fine (Admin Only)

1. Find the unpaid fine in the list
2. Click **"Waive Fine"**
3. Enter the reason for waiving
4. Click **"Confirm Waiver"**
5. The fine is marked as waived (set to ₱0.00)

> **Note:** Only administrators can waive fines. Librarians can only record payments.

---

## 9. Generating Reports

### Accessing Reports

1. Click **"Reports"** in the left sidebar
2. You'll see the reports dashboard with quick statistics

### Available Reports

#### Daily Transactions Report
- Shows all borrowing and return transactions for a specific date
- Use the date picker to navigate between days
- Export to PDF or CSV

#### Overdue Books Report
- Lists all currently overdue books
- Shows student info, book title, days overdue, and estimated fine
- Sortable by any column
- Export to PDF or CSV

#### Most Borrowed Books Report
- Shows the most popular books in a date range
- Use the date range picker to filter
- Includes borrow count and category
- Visualized with a bar chart

#### Inventory Report
- Complete book inventory with utilization statistics
- Shows total copies, available copies, and utilization rate
- Filter by category
- Export to PDF or CSV

#### Monthly Statistics Report
- Monthly overview with charts
- Line chart: borrowed vs returned books over time
- Category distribution chart
- Summary statistics

### Exporting Reports

1. Navigate to the desired report
2. Click the **"Export PDF"** button for a PDF document
3. Click the **"Export CSV"** button for a spreadsheet file
4. The file will download automatically

### Printing Reports

1. Navigate to the report you want to print
2. Press **Ctrl+P** (or Cmd+P on Mac)
3. The page is optimized for printing (sidebar and navigation are hidden)

---

## 10. System Settings (Admin Only)

> **Note:** Only administrators can access the settings page.

### Accessing Settings

1. Click **"Settings"** in the left sidebar (only visible to admins)
2. Settings are organized into groups:

### School Information
- **School Name** - Displayed in reports and headers
- **School Address** - School location
- **Library Name** - Library name
- **Library Email** - Contact email
- **Library Phone** - Contact phone number
- **Library Hours** - Operating hours

### Circulation Rules
- **Max Books per Student** - Maximum books a student can borrow (default: 3)
- **Borrowing Period** - Number of days before a book is due (default: 7)
- **Allow Renewals** - Whether students can renew books
- **Max Renewals** - Maximum number of renewals allowed

### Fine Configuration
- **Fine per Day** - Daily fine amount for overdue books (default: ₱5.00)
- **Grace Period** - Days before fines start (default: 1)
- **Max Fine Amount** - Maximum fine cap per transaction (default: ₱100.00)

### System Preferences
- **Date Format** - How dates are displayed
- **Items per Page** - Number of items in lists
- **Email Notifications** - Enable/disable email notifications

### Saving Settings

1. Make your changes in any settings group
2. Click **"Save Settings"** at the bottom of the group
3. A success message confirms the changes

### Resetting to Defaults

1. Click the **"Reset to Defaults"** button
2. Confirm the reset in the popup dialog
3. All settings will be restored to their original values

---

## 11. Your Profile

### Editing Your Profile

1. Click your **name** in the top-right corner
2. Click **"Profile"**
3. You can update:
   - Your name
   - Your email address
4. Click **"Save"**

### Changing Your Password

1. Go to your Profile page
2. Scroll to the **"Update Password"** section
3. Enter your **current password**
4. Enter your **new password** (minimum 8 characters)
5. Confirm your new password
6. Click **"Save"**

---

## 12. Dark Mode

### Toggling Dark Mode

1. Click the **moon/sun icon** in the top navigation bar
2. The entire application switches between light and dark themes
3. Your preference is saved and remembered for future visits

---

## 13. Keyboard Shortcuts

| Shortcut | Action |
|----------|--------|
| **Ctrl+K** (or Cmd+K) | Focus the search input |
| **Escape** | Close open modals and dialogs |

---

## 14. Troubleshooting

### Common Issues and Solutions

#### "Page not found" (404 Error)
- Check the URL in your browser
- Make sure you're logged in
- Try navigating from the sidebar menu

#### "Access denied" (403 Error)
- You don't have permission to access this page
- Settings and user management are admin-only
- Contact your administrator

#### "Server error" (500 Error)
- Try refreshing the page
- Clear your browser cache
- If the problem persists, contact the system administrator

#### Search is not working
- Make sure you're typing at least 2 characters
- Try clearing the search box and searching again
- Check your internet connection

#### Book shows "unavailable" but should be available
- Check if all copies are currently borrowed
- Go to the book's detail page to see borrowing history
- An administrator can manually update the copy count

#### Student can't borrow a book
Check if the student:
- Has already reached the maximum book limit (default: 3)
- Has overdue books that need to be returned
- Has unpaid fines that need to be settled
- Has an "active" status (not inactive or graduated)

#### Fine amount seems incorrect
- Fines are calculated automatically based on:
  - Number of days overdue
  - Grace period (default: 1 day)
  - Fine rate (default: ₱5.00/day)
- Check the fine breakdown on the return page
- Verify the settings are correct (Admin > Settings)

#### Reports show no data
- Make sure there are transactions in the selected date range
- Try adjusting the date filters
- Check if the database has been seeded with sample data

#### PDF export shows blank page
- Make sure DomPDF is installed (`composer require barryvdh/laravel-dompdf`)
- Try clearing the cache: run `php artisan cache:clear`
- Check that the `storage/` directory is writable

#### Page looks broken or unstyled
- Run `npm run build` to compile the frontend assets
- Clear browser cache (Ctrl+Shift+Delete)
- Try accessing in an incognito/private window

#### Can't upload book cover image
- Image must be **JPG, JPEG, or PNG** format
- Maximum file size is **2MB**
- Make sure the storage link is created: `php artisan storage:link`

### Getting Help

If you encounter an issue not listed here:
1. Note the exact error message
2. Note what you were trying to do when the error occurred
3. Contact the system administrator with this information

---

*User Guide - Bobon B Elementary School Library Management System*
*Last Updated: February 2026*
