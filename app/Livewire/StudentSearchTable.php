<?php

/**
 * StudentSearchTable Livewire Component
 *
 * This Livewire component provides a real-time searchable, filterable,
 * and sortable table for displaying students.
 *
 * Features:
 * - Real-time search by name or student ID (no page refresh)
 * - Filter by grade level, section, and status
 * - Sortable columns (click column header to sort)
 * - Pagination (15 students per page)
 * - Action buttons (View, Edit, Delete)
 *
 * How Livewire Works:
 * - Livewire components have a PHP class (this file) and a Blade view
 * - Properties with #[Url] are synced with the URL query string
 * - When properties change, the component re-renders automatically
 * - wire:model binds form inputs to properties
 * - wire:click calls methods on the component
 *
 * @see resources/views/livewire/student-search-table.blade.php
 * @see App\Models\Student
 */

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Student;

class StudentSearchTable extends Component
{
    /**
     * Use WithPagination trait for Livewire-compatible pagination.
     *
     * This trait modifies how pagination works to be compatible with
     * Livewire's re-rendering system.
     */
    use WithPagination;

    // =========================================================================
    // COMPONENT PROPERTIES
    // =========================================================================

    /**
     * Search term for filtering students.
     *
     * Searches across: first_name, last_name, middle_name, student_id
     * The #[Url] attribute syncs this with the URL query string (?search=...)
     *
     * @var string
     */
    #[Url(history: true)]
    public string $search = '';

    /**
     * Filter by grade level (1-6 or empty for all).
     *
     * @var string
     */
    #[Url(history: true)]
    public string $gradeLevel = '';

    /**
     * Filter by section name (or empty for all).
     *
     * @var string
     */
    #[Url(history: true)]
    public string $section = '';

    /**
     * Filter by status (active/inactive/graduated or empty for all).
     *
     * @var string
     */
    #[Url(history: true)]
    public string $status = '';

    /**
     * Column to sort by.
     *
     * @var string
     */
    #[Url(history: true)]
    public string $sortField = 'last_name';

    /**
     * Sort direction (asc or desc).
     *
     * @var string
     */
    #[Url(history: true)]
    public string $sortDirection = 'asc';

    /**
     * Number of records per page.
     *
     * @var int
     */
    public int $perPage = 15;

    // =========================================================================
    // LIFECYCLE HOOKS
    // =========================================================================

    /**
     * Reset pagination when search or filters change.
     *
     * This hook is called when any of the specified properties change.
     * We reset to page 1 to avoid showing empty pages after filtering.
     *
     * For example: If you're on page 5 and then filter, the new results
     * might only have 2 pages, so we need to go back to page 1.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedGradeLevel(): void
    {
        $this->resetPage();
    }

    public function updatedSection(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    // =========================================================================
    // PUBLIC METHODS
    // =========================================================================

    /**
     * Sort the table by a column.
     *
     * If clicking the same column, toggle direction (asc <-> desc).
     * If clicking a different column, sort ascending.
     *
     * Called from the view: wire:click="sortBy('last_name')"
     *
     * @param string $field The column name to sort by
     * @return void
     */
    public function sortBy(string $field): void
    {
        // If clicking the same column, toggle direction
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // Different column, start with ascending
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Clear all filters and reset to default view.
     *
     * Called from the view: wire:click="clearFilters"
     *
     * @return void
     */
    public function clearFilters(): void
    {
        $this->search = '';
        $this->gradeLevel = '';
        $this->section = '';
        $this->status = '';
        $this->resetPage();
    }

    /**
     * Delete a student (soft delete).
     *
     * Called from the view: wire:click="deleteStudent(1)"
     *
     * @param int $studentId The ID of the student to delete
     * @return void
     */
    public function deleteStudent(int $studentId): void
    {
        $student = Student::find($studentId);

        if (!$student) {
            session()->flash('error', 'Student not found.');
            return;
        }

        // Check if student has unreturned books
        if ($student->currentBorrowedBooks()->count() > 0) {
            session()->flash('error', "Cannot delete '{$student->full_name}'. Student has unreturned books.");
            return;
        }

        $studentName = $student->full_name;
        $student->delete();

        session()->flash('success', "Student '{$studentName}' has been deleted successfully.");
    }

    /**
     * Get unique sections for the filter dropdown.
     *
     * Returns a list of all unique sections currently in the database.
     *
     * @return \Illuminate\Support\Collection Collection of section names
     */
    public function getSectionsProperty()
    {
        return Student::select('section')
            ->distinct()
            ->orderBy('section')
            ->pluck('section');
    }

    // =========================================================================
    // RENDER METHOD
    // =========================================================================

    /**
     * Render the component.
     *
     * This method is called every time the component needs to re-render.
     * It builds the query based on current filters and returns the view.
     *
     * @return \Illuminate\View\View The component view
     */
    public function render()
    {
        // Start building the query
        $query = Student::query();

        // Apply search filter
        // Searches across multiple columns using the search scope
        if ($this->search) {
            $query->search($this->search);
        }

        // Apply grade level filter
        if ($this->gradeLevel) {
            $query->where('grade_level', $this->gradeLevel);
        }

        // Apply section filter
        if ($this->section) {
            $query->where('section', $this->section);
        }

        // Apply status filter
        if ($this->status) {
            $query->where('status', $this->status);
        }

        // Apply sorting
        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['student_id', 'last_name', 'first_name', 'grade_level', 'section', 'status'];
        $sortField = in_array($this->sortField, $allowedSortFields) ? $this->sortField : 'last_name';
        $query->orderBy($sortField, $this->sortDirection);

        // Get paginated results
        $students = $query->paginate($this->perPage);

        // Get sections for filter dropdown
        $sections = $this->sections;

        // Return the view with data
        return view('livewire.student-search-table', [
            'students' => $students,
            'sections' => $sections,
        ]);
    }
}
