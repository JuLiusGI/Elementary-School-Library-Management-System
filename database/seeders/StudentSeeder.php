<?php

/**
 * StudentSeeder
 *
 * Seeds sample student data for the library management system using StudentFactory.
 * Creates 50 realistic student records with Filipino names for testing.
 *
 * Students are distributed across:
 * - Grades 1-6 (elementary school levels)
 * - Sections A, B, C
 * - Mix of active/inactive status
 *
 * Student ID Format: YEAR-#### (e.g., 2026-0001)
 *
 * @package Database\Seeders
 * @see App\Models\Student
 * @see Database\Factories\StudentFactory
 */

namespace Database\Seeders;

use App\Models\Student;
use Database\Factories\StudentFactory;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * The number of students to create.
     *
     * @var int
     */
    protected int $studentCount = 50;

    /**
     * Run the database seeds.
     *
     * Creates 50 students using the StudentFactory.
     * Distributes students across grades 1-6 and sections A, B, C.
     *
     * @return void
     */
    public function run(): void
    {
        // Reset the factory counter to ensure consistent student IDs
        StudentFactory::resetCounter();

        // Calculate students per grade (approximately 8-9 per grade for 50 students)
        $studentsPerGrade = (int) ceil($this->studentCount / 6);

        $totalCreated = 0;

        // Create students for each grade level
        for ($grade = 1; $grade <= 6; $grade++) {
            // Determine how many to create for this grade
            $remaining = $this->studentCount - $totalCreated;
            $toCreate = min($studentsPerGrade, $remaining);

            if ($toCreate <= 0) {
                break;
            }

            // Distribute across sections A, B, C
            $sections = ['A', 'B', 'C'];
            $perSection = (int) ceil($toCreate / 3);

            foreach ($sections as $index => $section) {
                $sectionCount = min($perSection, $toCreate - ($index * $perSection));
                if ($sectionCount <= 0) {
                    break;
                }

                // Create students for this grade and section
                Student::factory()
                    ->count($sectionCount)
                    ->inGrade($grade)
                    ->inSection($section)
                    ->create();

                $totalCreated += $sectionCount;

                if ($totalCreated >= $this->studentCount) {
                    break 2;
                }
            }
        }

        $this->command->info("Sample students seeded successfully! ({$totalCreated} students)");

        // Show distribution summary
        $this->command->table(
            ['Grade', 'Section A', 'Section B', 'Section C', 'Total'],
            $this->getDistributionSummary()
        );
    }

    /**
     * Get the distribution summary of created students.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getDistributionSummary(): array
    {
        $summary = [];

        for ($grade = 1; $grade <= 6; $grade++) {
            $sectionA = Student::where('grade_level', $grade)->where('section', 'A')->count();
            $sectionB = Student::where('grade_level', $grade)->where('section', 'B')->count();
            $sectionC = Student::where('grade_level', $grade)->where('section', 'C')->count();

            $summary[] = [
                'Grade' => "Grade {$grade}",
                'Section A' => $sectionA,
                'Section B' => $sectionB,
                'Section C' => $sectionC,
                'Total' => $sectionA + $sectionB + $sectionC,
            ];
        }

        return $summary;
    }
}
