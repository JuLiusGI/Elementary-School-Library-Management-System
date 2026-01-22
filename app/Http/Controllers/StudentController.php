<?php

/**
 * StudentController
 *
 * This controller handles all student management operations including
 * listing, creating, viewing, editing, and deleting student records.
 *
 * Features:
 * - CRUD operations for students
 * - Student statistics for dashboard
 * - Soft delete support (students are archived, not permanently deleted)
 * - Flash messages for user feedback
 *
 * Routes handled (defined in routes/web.php):
 * - GET    /students           -> index()   List all students
 * - GET    /students/create    -> create()  Show create form
 * - POST   /students           -> store()   Save new student
 * - GET    /students/{id}      -> show()    View student details
 * - GET    /students/{id}/edit -> edit()    Show edit form
 * - PUT    /students/{id}      -> update()  Update student
 * - DELETE /students/{id}      -> destroy() Delete student (soft delete)
 *
 * @see App\Models\Student
 * @see App\Http\Requests\StudentRequest
 */

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Transaction;
use App\Http\Requests\StudentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Display a listing of students with statistics.
     *
     * This is the main students page showing:
     * - Statistics cards (total, active, with borrowed books, with fines)
     * - Livewire component for searchable/filterable student table
     *
     * The actual student listing is handled by the StudentSearchTable
     * Livewire component for real-time search and filtering.
     *
     * @return \Illuminate\View\View The students index view
     */
    public function index(): View
    {
        // Calculate statistics for the dashboard cards
        // These provide quick overview of student status
        $statistics = $this->getStudentStatistics();

        // Return the view with statistics
        // The Livewire component handles the actual student list
        return view('students.index', compact('statistics'));
    }

    /**
     * Show the form for creating a new student.
     *
     * Displays an empty form for entering new student details.
     * Grade levels and status options are passed for dropdown menus.
     *
     * @return \Illuminate\View\View The student create form view
     */
    public function create(): View
    {
        // Grade levels for the dropdown (1-6 for elementary)
        $gradeLevels = $this->getGradeLevels();

        // Status options for the dropdown
        $statusOptions = $this->getStatusOptions();

        return view('students.create', compact('gradeLevels', 'statusOptions'));
    }

    /**
     * Store a newly created student in the database.
     *
     * Receives validated data from StudentRequest and creates
     * a new student record. Redirects to index with success message.
     *
     * @param \App\Http\Requests\StudentRequest $request Validated request data
     * @return \Illuminate\Http\RedirectResponse Redirect to students index
     */
    public function store(StudentRequest $request): RedirectResponse
    {
        // Get validated data from the form request
        // StudentRequest handles all validation automatically
        $validated = $request->validated();

        // Set default status to 'active' if not provided
        $validated['status'] = $validated['status'] ?? 'active';

        // Create the student record
        $student = Student::create($validated);

        // Redirect with success message
        // The session flash message will be displayed on the next page
        return redirect()
            ->route('students.index')
            ->with('success', "Student '{$student->full_name}' has been registered successfully.");
    }

    /**
     * Display the specified student's details and borrowing history.
     *
     * Shows complete student information including:
     * - Personal details
     * - Guardian information
     * - Current borrowed books
     * - Borrowing history
     * - Fine information
     *
     * @param \App\Models\Student $student The student to display (route model binding)
     * @return \Illuminate\View\View The student details view
     */
    public function show(Student $student): View
    {
        // Eager load transactions with related book and librarian data
        // This prevents N+1 query problems when displaying history
        $student->load([
            'transactions' => function ($query) {
                $query->with(['book', 'librarian'])
                      ->orderBy('created_at', 'desc');
            }
        ]);

        // Get current borrowed books (not yet returned)
        $currentBorrowedBooks = $student->currentBorrowedBooks()
            ->with('book')
            ->get();

        // Get borrowing history (all transactions)
        $borrowingHistory = $student->transactions()
            ->with(['book', 'librarian'])
            ->orderBy('borrowed_date', 'desc')
            ->paginate(10);

        // Calculate total fines (unpaid)
        $totalFines = $student->totalFines();

        return view('students.show', compact(
            'student',
            'currentBorrowedBooks',
            'borrowingHistory',
            'totalFines'
        ));
    }

    /**
     * Show the form for editing a student.
     *
     * Displays a form pre-filled with the student's current data.
     *
     * @param \App\Models\Student $student The student to edit (route model binding)
     * @return \Illuminate\View\View The student edit form view
     */
    public function edit(Student $student): View
    {
        // Grade levels for the dropdown
        $gradeLevels = $this->getGradeLevels();

        // Status options for the dropdown
        $statusOptions = $this->getStatusOptions();

        return view('students.edit', compact('student', 'gradeLevels', 'statusOptions'));
    }

    /**
     * Update the specified student in the database.
     *
     * Receives validated data from StudentRequest and updates
     * the student record. Redirects to show page with success message.
     *
     * @param \App\Http\Requests\StudentRequest $request Validated request data
     * @param \App\Models\Student $student The student to update
     * @return \Illuminate\Http\RedirectResponse Redirect to student show page
     */
    public function update(StudentRequest $request, Student $student): RedirectResponse
    {
        // Get validated data from the form request
        $validated = $request->validated();

        // Update the student record
        $student->update($validated);

        // Redirect to student details page with success message
        return redirect()
            ->route('students.show', $student)
            ->with('success', "Student '{$student->full_name}' has been updated successfully.");
    }

    /**
     * Remove the specified student from the database (soft delete).
     *
     * Students are soft-deleted to preserve borrowing history.
     * Soft-deleted students can be restored if needed.
     *
     * Before deletion, checks if student has unreturned books.
     *
     * @param \App\Models\Student $student The student to delete
     * @return \Illuminate\Http\RedirectResponse Redirect to students index
     */
    public function destroy(Student $student): RedirectResponse
    {
        // Check if student has unreturned books
        // Don't allow deletion if they have books out
        if ($student->currentBorrowedBooks()->count() > 0) {
            return redirect()
                ->route('students.index')
                ->with('error', "Cannot delete '{$student->full_name}'. Student has unreturned books.");
        }

        // Store name for message before deletion
        $studentName = $student->full_name;

        // Soft delete the student
        // This sets deleted_at timestamp instead of removing the record
        $student->delete();

        // Redirect with success message
        return redirect()
            ->route('students.index')
            ->with('success', "Student '{$studentName}' has been deleted successfully.");
    }

    // =========================================================================
    // PRIVATE HELPER METHODS
    // =========================================================================

    /**
     * Get student statistics for the index page cards.
     *
     * Calculates various statistics about students:
     * - Total registered students
     * - Active students who can borrow
     * - Students currently borrowing books
     * - Students with unpaid fines
     *
     * @return array Statistics array with counts
     */
    private function getStudentStatistics(): array
    {
        return [
            // Total number of students (excluding soft-deleted)
            'total' => Student::count(),

            // Students with 'active' status
            'active' => Student::where('status', 'active')->count(),

            // Students who currently have borrowed books
            // Uses a subquery to find students with active transactions
            'with_borrowed_books' => Student::whereHas('transactions', function ($query) {
                $query->whereIn('status', ['borrowed', 'overdue']);
            })->count(),

            // Students who have unpaid fines
            'with_fines' => Student::whereHas('transactions', function ($query) {
                $query->where('fine_amount', '>', 0)
                      ->where('fine_paid', false);
            })->count(),
        ];
    }

    /**
     * Get array of grade levels for form dropdowns.
     *
     * @return array Grade levels with value => label pairs
     */
    private function getGradeLevels(): array
    {
        return [
            '1' => 'Grade 1',
            '2' => 'Grade 2',
            '3' => 'Grade 3',
            '4' => 'Grade 4',
            '5' => 'Grade 5',
            '6' => 'Grade 6',
        ];
    }

    /**
     * Get array of status options for form dropdowns.
     *
     * @return array Status options with value => label pairs
     */
    private function getStatusOptions(): array
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'graduated' => 'Graduated',
        ];
    }
}
