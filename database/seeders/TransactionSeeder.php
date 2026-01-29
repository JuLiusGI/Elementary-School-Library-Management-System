<?php

/**
 * TransactionSeeder
 *
 * Seeds sample transaction data for the library management system.
 * Creates realistic borrowing scenarios for testing and demonstration.
 *
 * Transaction Distribution:
 * - 30 active borrowing transactions (currently borrowed)
 * - 50 returned transactions (completed)
 * - 5 overdue transactions with fines
 *
 * This seeder depends on:
 * - UserSeeder (for librarian IDs)
 * - StudentSeeder (for student IDs)
 * - BookSeeder (for book IDs)
 * - SettingSeeder (for borrowing rules)
 *
 * @package Database\Seeders
 * @see App\Models\Transaction
 * @see Database\Factories\TransactionFactory
 */

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Transaction counts by type.
     *
     * @var array<string, int>
     */
    protected array $counts = [
        'active' => 30,
        'returned' => 50,
        'overdue' => 5,
    ];

    /**
     * Run the database seeds.
     *
     * Creates various transaction scenarios for testing.
     *
     * @return void
     */
    public function run(): void
    {
        // Get librarians for processing transactions
        $librarians = User::whereIn('role', ['admin', 'librarian'])->get();

        if ($librarians->isEmpty()) {
            $this->command->error('No librarian or admin users found. Please run UserSeeder first.');
            return;
        }

        // Get active students
        $students = Student::active()->get();

        if ($students->isEmpty()) {
            $this->command->error('No active students found. Please run StudentSeeder first.');
            return;
        }

        // Get available books
        $books = Book::where('copies_available', '>', 0)->get();

        if ($books->isEmpty()) {
            $this->command->error('No available books found. Please run BookSeeder first.');
            return;
        }

        // Get settings
        $borrowingPeriod = (int) Setting::get('borrowing_period', 7);
        $finePerDay = (float) Setting::get('fine_per_day', 5.00);
        $gracePeriod = (int) Setting::get('grace_period', 1);

        $createdCounts = [
            'active' => 0,
            'returned' => 0,
            'overdue' => 0,
        ];

        // =========================================================================
        // ACTIVE BORROWING TRANSACTIONS (30)
        // =========================================================================
        $this->command->info('Creating active borrowing transactions...');

        for ($i = 0; $i < $this->counts['active']; $i++) {
            $student = $students->random();
            $book = $this->getAvailableBook($books);

            if (!$book) {
                $this->command->warn('No more available books for active transactions.');
                break;
            }

            $librarian = $librarians->random();

            // Borrowed 1-5 days ago (still on time)
            $borrowedDaysAgo = rand(1, 5);
            $borrowedDate = Carbon::now()->subDays($borrowedDaysAgo);
            $dueDate = $borrowedDate->copy()->addDays($borrowingPeriod);

            Transaction::create([
                'student_id' => $student->id,
                'book_id' => $book->id,
                'librarian_id' => $librarian->id,
                'borrowed_date' => $borrowedDate,
                'due_date' => $dueDate,
                'status' => 'borrowed',
                'notes' => 'Currently borrowed - on time',
                'fine_amount' => 0,
                'fine_paid' => false,
            ]);

            // Decrease available copies
            $book->decrement('copies_available');
            if ($book->copies_available <= 0) {
                $book->update(['status' => 'unavailable']);
            }

            $createdCounts['active']++;
        }

        // =========================================================================
        // RETURNED TRANSACTIONS (50)
        // =========================================================================
        $this->command->info('Creating returned transactions...');

        for ($i = 0; $i < $this->counts['returned']; $i++) {
            $student = $students->random();
            $book = $books->random();
            $librarian = $librarians->random();

            // Borrowed 15-60 days ago
            $borrowedDaysAgo = rand(15, 60);
            $borrowedDate = Carbon::now()->subDays($borrowedDaysAgo);
            $dueDate = $borrowedDate->copy()->addDays($borrowingPeriod);

            // 80% returned on time, 20% returned late
            $returnedLate = rand(1, 100) <= 20;

            if ($returnedLate) {
                // Returned 1-5 days after due date
                $daysLate = rand(1, 5);
                $returnedDate = $dueDate->copy()->addDays($daysLate);
                $chargeableDays = max(0, $daysLate - $gracePeriod);
                $fineAmount = $chargeableDays * $finePerDay;
                $finePaid = rand(1, 100) <= 70; // 70% paid their fines
                $notes = "Returned {$daysLate} days late" . ($finePaid ? ', fine paid' : ', fine unpaid');
            } else {
                // Returned on time (1-3 days before due)
                $returnedDate = $dueDate->copy()->subDays(rand(1, 3));
                $fineAmount = 0;
                $finePaid = false;
                $notes = 'Returned on time';
            }

            Transaction::create([
                'student_id' => $student->id,
                'book_id' => $book->id,
                'librarian_id' => $librarian->id,
                'borrowed_date' => $borrowedDate,
                'due_date' => $dueDate,
                'returned_date' => $returnedDate,
                'status' => 'returned',
                'notes' => $notes,
                'fine_amount' => $fineAmount,
                'fine_paid' => $finePaid,
            ]);

            $createdCounts['returned']++;
        }

        // =========================================================================
        // OVERDUE TRANSACTIONS WITH FINES (5)
        // =========================================================================
        $this->command->info('Creating overdue transactions with fines...');

        for ($i = 0; $i < $this->counts['overdue']; $i++) {
            $student = $students->random();
            $book = $this->getAvailableBook($books);

            if (!$book) {
                $this->command->warn('No more available books for overdue transactions.');
                break;
            }

            $librarian = $librarians->random();

            // Borrowed 15-25 days ago (past due date)
            $borrowedDaysAgo = rand(15, 25);
            $borrowedDate = Carbon::now()->subDays($borrowedDaysAgo);
            $dueDate = $borrowedDate->copy()->addDays($borrowingPeriod);

            // Calculate days overdue and fine
            $daysOverdue = Carbon::now()->diffInDays($dueDate);
            $chargeableDays = max(0, $daysOverdue - $gracePeriod);
            $fineAmount = $chargeableDays * $finePerDay;

            Transaction::create([
                'student_id' => $student->id,
                'book_id' => $book->id,
                'librarian_id' => $librarian->id,
                'borrowed_date' => $borrowedDate,
                'due_date' => $dueDate,
                'status' => 'overdue',
                'notes' => "Overdue by {$daysOverdue} days - fine accumulating",
                'fine_amount' => $fineAmount,
                'fine_paid' => false,
            ]);

            // Decrease available copies
            $book->decrement('copies_available');
            if ($book->copies_available <= 0) {
                $book->update(['status' => 'unavailable']);
            }

            $createdCounts['overdue']++;
        }

        // =========================================================================
        // SUMMARY
        // =========================================================================
        $totalCreated = array_sum($createdCounts);

        $this->command->info("Sample transactions seeded successfully! ({$totalCreated} transactions)");
        $this->command->table(
            ['Type', 'Count'],
            [
                ['Active (Borrowed)', $createdCounts['active']],
                ['Returned', $createdCounts['returned']],
                ['Overdue with Fines', $createdCounts['overdue']],
            ]
        );

        // Show fine summary
        $totalFines = Transaction::sum('fine_amount');
        $unpaidFines = Transaction::where('fine_paid', false)->where('fine_amount', '>', 0)->sum('fine_amount');
        $this->command->info("Total fines generated: ₱" . number_format($totalFines, 2));
        $this->command->info("Unpaid fines: ₱" . number_format($unpaidFines, 2));
    }

    /**
     * Get an available book (with copies available).
     *
     * @param \Illuminate\Database\Eloquent\Collection $books
     * @return \App\Models\Book|null
     */
    protected function getAvailableBook($books)
    {
        // Refresh and filter for available books
        $availableBooks = $books->filter(function ($book) {
            $book->refresh();
            return $book->copies_available > 0;
        });

        if ($availableBooks->isEmpty()) {
            return null;
        }

        return $availableBooks->random();
    }
}
