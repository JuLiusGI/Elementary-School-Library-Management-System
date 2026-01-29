<?php

/**
 * TransactionFactory
 *
 * Factory for generating realistic borrowing transaction data for testing.
 * Creates transactions with various statuses: borrowed, returned, and overdue.
 *
 * Usage:
 * Transaction::factory()->create();                    // Create one transaction
 * Transaction::factory()->count(30)->create();         // Create 30 transactions
 * Transaction::factory()->borrowed()->create();        // Active borrowing
 * Transaction::factory()->returned()->create();        // Returned book
 * Transaction::factory()->overdue()->create();         // Overdue with fine
 *
 * @package Database\Factories
 * @see App\Models\Transaction
 */

namespace Database\Factories;

use App\Models\Book;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * Creates a currently borrowed transaction (not returned yet).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get random student, book, and librarian
        $student = Student::active()->inRandomOrder()->first()
            ?? Student::inRandomOrder()->first();
        $book = Book::where('copies_available', '>', 0)->inRandomOrder()->first()
            ?? Book::inRandomOrder()->first();
        $librarian = User::whereIn('role', ['admin', 'librarian'])->inRandomOrder()->first();

        // Get borrowing period from settings (default 7 days)
        $borrowingPeriod = (int) Setting::get('borrowing_period', 7);

        // Borrowed 1-5 days ago (still on time)
        $borrowedDaysAgo = fake()->numberBetween(1, 5);
        $borrowedDate = Carbon::now()->subDays($borrowedDaysAgo);
        $dueDate = $borrowedDate->copy()->addDays($borrowingPeriod);

        return [
            'student_id' => $student?->id ?? 1,
            'book_id' => $book?->id ?? 1,
            'librarian_id' => $librarian?->id ?? 1,
            'borrowed_date' => $borrowedDate,
            'due_date' => $dueDate,
            'returned_date' => null,
            'status' => 'borrowed',
            'notes' => fake()->optional(0.3)->sentence(),
            'fine_amount' => 0,
            'fine_paid' => false,
        ];
    }

    /**
     * Create a currently borrowed transaction.
     *
     * @return static
     */
    public function borrowed(): static
    {
        return $this->state(function (array $attributes) {
            $borrowingPeriod = (int) Setting::get('borrowing_period', 7);
            $borrowedDaysAgo = fake()->numberBetween(1, 5);
            $borrowedDate = Carbon::now()->subDays($borrowedDaysAgo);
            $dueDate = $borrowedDate->copy()->addDays($borrowingPeriod);

            return [
                'borrowed_date' => $borrowedDate,
                'due_date' => $dueDate,
                'returned_date' => null,
                'status' => 'borrowed',
                'fine_amount' => 0,
                'fine_paid' => false,
                'notes' => 'Currently borrowed',
            ];
        });
    }

    /**
     * Create a returned transaction (on time, no fine).
     *
     * @return static
     */
    public function returned(): static
    {
        return $this->state(function (array $attributes) {
            $borrowingPeriod = (int) Setting::get('borrowing_period', 7);

            // Borrowed 15-30 days ago
            $borrowedDaysAgo = fake()->numberBetween(15, 30);
            $borrowedDate = Carbon::now()->subDays($borrowedDaysAgo);
            $dueDate = $borrowedDate->copy()->addDays($borrowingPeriod);

            // Returned 1-3 days before due date (on time)
            $returnedDate = $dueDate->copy()->subDays(fake()->numberBetween(1, 3));

            return [
                'borrowed_date' => $borrowedDate,
                'due_date' => $dueDate,
                'returned_date' => $returnedDate,
                'status' => 'returned',
                'fine_amount' => 0,
                'fine_paid' => false,
                'notes' => 'Returned on time',
            ];
        });
    }

    /**
     * Create an overdue transaction with fine.
     *
     * @return static
     */
    public function overdue(): static
    {
        return $this->state(function (array $attributes) {
            $borrowingPeriod = (int) Setting::get('borrowing_period', 7);
            $finePerDay = (float) Setting::get('fine_per_day', 5.00);
            $gracePeriod = (int) Setting::get('grace_period', 1);

            // Borrowed 15-25 days ago (past due date)
            $borrowedDaysAgo = fake()->numberBetween(15, 25);
            $borrowedDate = Carbon::now()->subDays($borrowedDaysAgo);
            $dueDate = $borrowedDate->copy()->addDays($borrowingPeriod);

            // Calculate days overdue
            $daysOverdue = Carbon::now()->diffInDays($dueDate);
            $chargeableDays = max(0, $daysOverdue - $gracePeriod);
            $fineAmount = $chargeableDays * $finePerDay;

            return [
                'borrowed_date' => $borrowedDate,
                'due_date' => $dueDate,
                'returned_date' => null,
                'status' => 'overdue',
                'fine_amount' => $fineAmount,
                'fine_paid' => false,
                'notes' => "Overdue by {$daysOverdue} days",
            ];
        });
    }

    /**
     * Create a returned late transaction with paid fine.
     *
     * @return static
     */
    public function returnedLateWithPaidFine(): static
    {
        return $this->state(function (array $attributes) {
            $borrowingPeriod = (int) Setting::get('borrowing_period', 7);
            $finePerDay = (float) Setting::get('fine_per_day', 5.00);
            $gracePeriod = (int) Setting::get('grace_period', 1);

            // Borrowed 20-40 days ago
            $borrowedDaysAgo = fake()->numberBetween(20, 40);
            $borrowedDate = Carbon::now()->subDays($borrowedDaysAgo);
            $dueDate = $borrowedDate->copy()->addDays($borrowingPeriod);

            // Returned 3-7 days after due date
            $daysLate = fake()->numberBetween(3, 7);
            $returnedDate = $dueDate->copy()->addDays($daysLate);

            // Calculate fine
            $chargeableDays = max(0, $daysLate - $gracePeriod);
            $fineAmount = $chargeableDays * $finePerDay;

            return [
                'borrowed_date' => $borrowedDate,
                'due_date' => $dueDate,
                'returned_date' => $returnedDate,
                'status' => 'returned',
                'fine_amount' => $fineAmount,
                'fine_paid' => true,
                'notes' => "Returned {$daysLate} days late, fine paid",
            ];
        });
    }

    /**
     * Create a returned late transaction with unpaid fine.
     *
     * @return static
     */
    public function returnedLateWithUnpaidFine(): static
    {
        return $this->state(function (array $attributes) {
            $borrowingPeriod = (int) Setting::get('borrowing_period', 7);
            $finePerDay = (float) Setting::get('fine_per_day', 5.00);
            $gracePeriod = (int) Setting::get('grace_period', 1);

            // Borrowed 15-25 days ago
            $borrowedDaysAgo = fake()->numberBetween(15, 25);
            $borrowedDate = Carbon::now()->subDays($borrowedDaysAgo);
            $dueDate = $borrowedDate->copy()->addDays($borrowingPeriod);

            // Returned 2-5 days after due date
            $daysLate = fake()->numberBetween(2, 5);
            $returnedDate = $dueDate->copy()->addDays($daysLate);

            // Calculate fine
            $chargeableDays = max(0, $daysLate - $gracePeriod);
            $fineAmount = $chargeableDays * $finePerDay;

            return [
                'borrowed_date' => $borrowedDate,
                'due_date' => $dueDate,
                'returned_date' => $returnedDate,
                'status' => 'returned',
                'fine_amount' => $fineAmount,
                'fine_paid' => false,
                'notes' => "Returned {$daysLate} days late, FINE UNPAID",
            ];
        });
    }

    /**
     * Set specific student for the transaction.
     *
     * @param int $studentId
     * @return static
     */
    public function forStudent(int $studentId): static
    {
        return $this->state(fn (array $attributes) => [
            'student_id' => $studentId,
        ]);
    }

    /**
     * Set specific book for the transaction.
     *
     * @param int $bookId
     * @return static
     */
    public function forBook(int $bookId): static
    {
        return $this->state(fn (array $attributes) => [
            'book_id' => $bookId,
        ]);
    }

    /**
     * Set specific librarian for the transaction.
     *
     * @param int $librarianId
     * @return static
     */
    public function byLibrarian(int $librarianId): static
    {
        return $this->state(fn (array $attributes) => [
            'librarian_id' => $librarianId,
        ]);
    }
}
