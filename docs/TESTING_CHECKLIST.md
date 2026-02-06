# Testing Checklist

## Bobon B Elementary School - Library Management System

Use this checklist before each deployment to ensure all features work correctly.

---

## How to Use This Checklist

1. Start with a fresh database: `php artisan migrate:fresh --seed`
2. Work through each section in order
3. Mark each item as you test it
4. Note any issues in the "Notes" column
5. All items must pass before deployment

---

## 1. Authentication

| # | Test Case | Steps | Expected Result | Pass? | Notes |
|---|-----------|-------|-----------------|-------|-------|
| 1.1 | Admin login | Login with admin@bobon.edu.ph / password | Redirected to dashboard | [ ] | |
| 1.2 | Librarian login | Login with librarian1@bobon.edu.ph / password | Redirected to dashboard | [ ] | |
| 1.3 | Invalid credentials | Login with wrong email/password | Error message shown | [ ] | |
| 1.4 | Logout | Click logout from dropdown | Redirected to login page | [ ] | |
| 1.5 | Protected routes | Access /dashboard without login | Redirected to login page | [ ] | |
| 1.6 | Admin-only routes | Access /settings as librarian | Access denied (403) or redirected | [ ] | |

---

## 2. Dashboard

| # | Test Case | Steps | Expected Result | Pass? | Notes |
|---|-----------|-------|-----------------|-------|-------|
| 2.1 | Statistics cards | View dashboard | Shows total books, available, students, borrowed counts | [ ] | |
| 2.2 | Charts load | View dashboard | Weekly activity, category, top books charts render | [ ] | |
| 2.3 | Overdue alerts | Have overdue transactions | Overdue books section shows alerts | [ ] | |
| 2.4 | Recent transactions | View dashboard | Latest transactions displayed | [ ] | |
| 2.5 | Quick actions | Click quick action buttons | Navigate to correct pages | [ ] | |
| 2.6 | Auto-refresh | Wait 60 seconds | Dashboard data refreshes automatically | [ ] | |

---

## 3. Student Management (CRUD)

| # | Test Case | Steps | Expected Result | Pass? | Notes |
|---|-----------|-------|-----------------|-------|-------|
| 3.1 | List students | Click Students in sidebar | Student list loads with pagination | [ ] | |
| 3.2 | Search students | Type name in search box | Results filter in real-time | [ ] | |
| 3.3 | Filter by grade | Select grade from dropdown | Only matching students shown | [ ] | |
| 3.4 | Add student | Fill form, click Save | Student created, success message | [ ] | |
| 3.5 | Add student - validation | Submit empty form | Validation errors shown | [ ] | |
| 3.6 | Add student - duplicate ID | Use existing student ID | Error: ID already exists | [ ] | |
| 3.7 | Edit student | Click Edit, change data, Save | Student updated, success message | [ ] | |
| 3.8 | View student | Click View button | Student details and history shown | [ ] | |
| 3.9 | Delete student | Click Delete, confirm | Student removed, success message | [ ] | |
| 3.10 | Delete student with loans | Delete student with active borrowings | Error: cannot delete | [ ] | |

---

## 4. Book Management (CRUD)

| # | Test Case | Steps | Expected Result | Pass? | Notes |
|---|-----------|-------|-----------------|-------|-------|
| 4.1 | List books | Click Books in sidebar | Book list/grid loads | [ ] | |
| 4.2 | Grid/List toggle | Click view toggle buttons | View switches between grid and list | [ ] | |
| 4.3 | Search books | Type title in search box | Results filter in real-time | [ ] | |
| 4.4 | Filter by category | Select category from dropdown | Only matching books shown | [ ] | |
| 4.5 | Add book | Fill form, click Save | Book created with auto accession # | [ ] | |
| 4.6 | Add book - validation | Submit empty required fields | Validation errors shown | [ ] | |
| 4.7 | Upload cover image | Add JPG/PNG image (< 2MB) | Image uploaded and displayed | [ ] | |
| 4.8 | Upload oversized image | Add image > 2MB | Error: file too large | [ ] | |
| 4.9 | Edit book | Click Edit, change data, Save | Book updated, success message | [ ] | |
| 4.10 | View book | Click View button | Book details and borrowing history shown | [ ] | |
| 4.11 | Delete book | Click Delete, confirm | Book removed, success message | [ ] | |
| 4.12 | Delete book with loans | Delete book with active borrowings | Error: cannot delete | [ ] | |

---

## 5. Category Management

| # | Test Case | Steps | Expected Result | Pass? | Notes |
|---|-----------|-------|-----------------|-------|-------|
| 5.1 | List categories | Click Categories | All categories shown with book counts | [ ] | |
| 5.2 | Add category | Click Add, fill form, Save | Category created | [ ] | |
| 5.3 | Edit category | Click Edit, change name, Save | Category updated | [ ] | |
| 5.4 | Delete empty category | Delete category with no books | Category removed | [ ] | |
| 5.5 | Delete category with books | Delete category that has books | Error: cannot delete | [ ] | |

---

## 6. Borrowing Flow

| # | Test Case | Steps | Expected Result | Pass? | Notes |
|---|-----------|-------|-----------------|-------|-------|
| 6.1 | Borrow book | Select student, select book, confirm | Transaction created, copies decremented | [ ] | |
| 6.2 | Due date calculation | Process a borrowing | Due date = borrowed date + borrowing period | [ ] | |
| 6.3 | Eligibility - max books | Student with 3 active loans | Error: max books reached | [ ] | |
| 6.4 | Eligibility - overdue | Student with overdue books | Error: has overdue books | [ ] | |
| 6.5 | Eligibility - unpaid fines | Student with unpaid fines | Error: has unpaid fines | [ ] | |
| 6.6 | Eligibility - inactive | Select inactive student | Error: student not active | [ ] | |
| 6.7 | Book unavailable | Select book with 0 copies | Error: no copies available | [ ] | |
| 6.8 | Copies decrement | Borrow a book | copies_available decreases by 1 | [ ] | |

---

## 7. Return Flow

| # | Test Case | Steps | Expected Result | Pass? | Notes |
|---|-----------|-------|-----------------|-------|-------|
| 7.1 | Return on time | Return book before due date | Status = returned, no fine | [ ] | |
| 7.2 | Return overdue | Return book past due date | Fine calculated and shown | [ ] | |
| 7.3 | Fine calculation | Return 5 days late (1 grace, ₱5/day) | Fine = (5-1) x ₱5 = ₱20.00 | [ ] | |
| 7.4 | Copies increment | Return a book | copies_available increases by 1 | [ ] | |
| 7.5 | Search borrowed books | Search by student/book on return page | Correct results shown | [ ] | |

---

## 8. Fine Management

| # | Test Case | Steps | Expected Result | Pass? | Notes |
|---|-----------|-------|-----------------|-------|-------|
| 8.1 | View fines list | Click Fines in sidebar | All fines listed with statuses | [ ] | |
| 8.2 | Fine summary cards | View fines page | Total, paid, unpaid amounts shown | [ ] | |
| 8.3 | Record payment | Click Record Payment, select method | Fine marked as paid | [ ] | |
| 8.4 | Waive fine (admin) | As admin, click Waive Fine | Fine set to ₱0 and marked waived | [ ] | |
| 8.5 | Waive fine (librarian) | As librarian, try to waive fine | Not allowed or button not shown | [ ] | |

---

## 9. Reports

| # | Test Case | Steps | Expected Result | Pass? | Notes |
|---|-----------|-------|-----------------|-------|-------|
| 9.1 | Reports dashboard | Click Reports | Dashboard with statistics loads | [ ] | |
| 9.2 | Daily transactions | Click Daily Transactions | Report shows today's transactions | [ ] | |
| 9.3 | Date navigation | Change date on daily report | Data updates for selected date | [ ] | |
| 9.4 | Overdue report | Click Overdue Books | Shows all overdue transactions | [ ] | |
| 9.5 | Most borrowed | Click Most Borrowed | Shows popular books with chart | [ ] | |
| 9.6 | Inventory report | Click Inventory | Shows all books with utilization | [ ] | |
| 9.7 | Monthly statistics | Click Monthly Stats | Shows monthly charts and data | [ ] | |
| 9.8 | PDF export | Click Export PDF on any report | PDF file downloads | [ ] | |
| 9.9 | CSV export | Click Export CSV on any report | CSV file downloads | [ ] | |

---

## 10. System Settings (Admin Only)

| # | Test Case | Steps | Expected Result | Pass? | Notes |
|---|-----------|-------|-----------------|-------|-------|
| 10.1 | View settings | Click Settings (as admin) | Settings page with groups loads | [ ] | |
| 10.2 | Update setting | Change borrowing period, Save | Setting updated, success message | [ ] | |
| 10.3 | Reset defaults | Click Reset to Defaults | All settings restored to defaults | [ ] | |
| 10.4 | Access as librarian | Try to access /settings as librarian | Redirected or access denied | [ ] | |

---

## 11. User Permissions

| # | Test Case | Steps | Expected Result | Pass? | Notes |
|---|-----------|-------|-----------------|-------|-------|
| 11.1 | Admin sees all menus | Login as admin | Settings, Users links visible | [ ] | |
| 11.2 | Librarian restricted | Login as librarian | Settings link not visible | [ ] | |
| 11.3 | Profile edit | Edit own profile | Name and email updated | [ ] | |
| 11.4 | Password change | Change password in profile | Password updated, can login with new | [ ] | |

---

## 12. UI & Responsiveness

| # | Test Case | Steps | Expected Result | Pass? | Notes |
|---|-----------|-------|-----------------|-------|-------|
| 12.1 | Dark mode toggle | Click dark mode button | Theme switches correctly | [ ] | |
| 12.2 | Dark mode persistence | Toggle dark mode, refresh page | Theme stays the same | [ ] | |
| 12.3 | Mobile sidebar | Resize to mobile width | Sidebar collapses to hamburger menu | [ ] | |
| 12.4 | Mobile tables | View tables on mobile | Tables scroll horizontally | [ ] | |
| 12.5 | Print styles | Print a report page (Ctrl+P) | Sidebar and nav hidden, content clean | [ ] | |
| 12.6 | Error page 404 | Visit non-existent URL | Custom 404 page shown | [ ] | |
| 12.7 | Loading states | Trigger Livewire actions | Loading indicators shown | [ ] | |
| 12.8 | Keyboard shortcut | Press Ctrl+K | Search input focused | [ ] | |

---

## 13. Validation & Error Handling

| # | Test Case | Steps | Expected Result | Pass? | Notes |
|---|-----------|-------|-----------------|-------|-------|
| 13.1 | Required fields | Submit forms with empty required fields | Validation errors shown | [ ] | |
| 13.2 | Email format | Enter invalid email format | Validation error shown | [ ] | |
| 13.3 | Unique constraints | Create duplicate student ID | Error: already exists | [ ] | |
| 13.4 | Flash messages | Perform CRUD operation | Success/error flash message shown | [ ] | |
| 13.5 | CSRF protection | Submit form without CSRF token | 419 error (page expired) | [ ] | |

---

## 14. Custom Commands

| # | Test Case | Steps | Expected Result | Pass? | Notes |
|---|-----------|-------|-----------------|-------|-------|
| 14.1 | Check overdue | Run `php artisan library:check-overdue` | Overdue transactions updated | [ ] | |
| 14.2 | Check overdue (dry run) | Run with `--dry-run` flag | Preview shown, no changes made | [ ] | |
| 14.3 | Backup database | Run `php artisan library:backup-db` | SQL file created in storage/app/backups | [ ] | |
| 14.4 | Clear cache | Run `php artisan library:clear-cache` | All caches cleared successfully | [ ] | |

---

## 15. Security

| # | Test Case | Steps | Expected Result | Pass? | Notes |
|---|-----------|-------|-----------------|-------|-------|
| 15.1 | Routes protected | Access any route without auth | Redirected to login | [ ] | |
| 15.2 | CSRF on forms | All forms include @csrf | CSRF token present in HTML | [ ] | |
| 15.3 | XSS prevention | Enter `<script>alert('xss')</script>` in form | Script not executed, text escaped | [ ] | |
| 15.4 | SQL injection | Enter `' OR 1=1 --` in search | No SQL error, proper filtering | [ ] | |
| 15.5 | File upload security | Upload .php file as cover image | Rejected, only images accepted | [ ] | |
| 15.6 | Admin middleware | Access /settings as librarian via URL | Access denied | [ ] | |
| 15.7 | .env not accessible | Navigate to /.env in browser | 404 or 403, not file contents | [ ] | |

---

## Test Summary

| Section | Total Tests | Passed | Failed | Notes |
|---------|-------------|--------|--------|-------|
| 1. Authentication | 6 | | | |
| 2. Dashboard | 6 | | | |
| 3. Student CRUD | 10 | | | |
| 4. Book CRUD | 12 | | | |
| 5. Categories | 5 | | | |
| 6. Borrowing Flow | 8 | | | |
| 7. Return Flow | 5 | | | |
| 8. Fine Management | 5 | | | |
| 9. Reports | 9 | | | |
| 10. Settings | 4 | | | |
| 11. Permissions | 4 | | | |
| 12. UI & Responsiveness | 8 | | | |
| 13. Validation | 5 | | | |
| 14. Custom Commands | 4 | | | |
| 15. Security | 7 | | | |
| **TOTAL** | **98** | | | |

---

**Tested by:** _______________
**Date:** _______________
**Environment:** _______________
**Browser:** _______________
**Result:** PASS / FAIL

---

*Testing Checklist - Bobon B Elementary School Library Management System*
*Last Updated: February 2026*
