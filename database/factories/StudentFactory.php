<?php

/**
 * StudentFactory
 *
 * Factory for generating realistic Filipino student data for testing.
 * Creates students with authentic Filipino names distributed across
 * elementary school grades 1-6.
 *
 * Usage:
 * Student::factory()->create();                    // Create one student
 * Student::factory()->count(50)->create();         // Create 50 students
 * Student::factory()->active()->create();          // Create active student
 * Student::factory()->inGrade(3)->create();        // Create Grade 3 student
 *
 * @package Database\Factories
 * @see App\Models\Student
 */

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Student::class;

    /**
     * Common Filipino first names (both male and female).
     *
     * @var array<int, string>
     */
    protected static array $firstNames = [
        // Male names
        'Juan', 'Jose', 'Pedro', 'Miguel', 'Antonio', 'Francisco', 'Rafael',
        'Carlos', 'Manuel', 'Roberto', 'Gabriel', 'Luis', 'Mark', 'John',
        'James', 'Kenneth', 'Christian', 'Joshua', 'Daniel', 'Matthew',
        'Jayden', 'Ethan', 'Kyle', 'Ryan', 'Aldrin', 'Arjay', 'CJ', 'EJ',
        'Francis', 'Harvey', 'Jerome', 'Kevin', 'Lloyd', 'Marco', 'Nathan',
        // Female names
        'Maria', 'Ana', 'Rosa', 'Carmen', 'Teresa', 'Luz', 'Elena', 'Gloria',
        'Isabel', 'Sofia', 'Angela', 'Patricia', 'Princess', 'Angel', 'Nicole',
        'Ashley', 'Jasmine', 'Kimberly', 'Kate', 'Grace', 'Faith', 'Hope',
        'Joy', 'Mary', 'Angelica', 'Bianca', 'Daisy', 'Ella', 'Gwen', 'Irish',
        'Julia', 'Krista', 'Liza', 'Michelle', 'Nina', 'Paula', 'Queenie',
    ];

    /**
     * Common Filipino last names.
     *
     * @var array<int, string>
     */
    protected static array $lastNames = [
        'Dela Cruz', 'Santos', 'Reyes', 'Garcia', 'Cruz', 'Flores', 'Lopez',
        'Gonzales', 'Torres', 'Martinez', 'Ramos', 'Mendoza', 'Fernandez',
        'Castillo', 'Villanueva', 'Bautista', 'Rivera', 'Navarro', 'Aquino',
        'Pascual', 'Mercado', 'Dizon', 'Salazar', 'Castro', 'Perez', 'David',
        'Morales', 'Jimenez', 'Hernandez', 'Domingo', 'Manalo', 'Soriano',
        'Tolentino', 'Valencia', 'Aguilar', 'Espiritu', 'Ignacio', 'Santiago',
        'Lim', 'Tan', 'Chua', 'Ong', 'Go', 'Lee', 'Sy', 'Co', 'Yu', 'Ng',
    ];

    /**
     * Section names used in Philippine elementary schools.
     *
     * @var array<string, array<int, string>>
     */
    protected static array $sections = [
        'A' => ['A'],
        'B' => ['B'],
        'C' => ['C'],
    ];

    /**
     * Counter for generating unique student IDs.
     *
     * @var int
     */
    protected static int $studentCounter = 0;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        self::$studentCounter++;
        $currentYear = date('Y');

        // Generate student ID: YEAR-#### format
        $studentId = $currentYear . '-' . str_pad(self::$studentCounter, 4, '0', STR_PAD_LEFT);

        // Random names
        $firstName = self::$firstNames[array_rand(self::$firstNames)];
        $lastName = self::$lastNames[array_rand(self::$lastNames)];

        // 70% chance of having middle name
        $middleName = fake()->boolean(70)
            ? self::$lastNames[array_rand(self::$lastNames)]
            : null;

        // Random grade (1-6) and section (A, B, C)
        $gradeLevel = (string) fake()->numberBetween(1, 6);
        $section = fake()->randomElement(['A', 'B', 'C']);

        // Status distribution: 90% active, 8% inactive, 2% graduated
        $statusRandom = fake()->numberBetween(1, 100);
        if ($statusRandom <= 90) {
            $status = 'active';
        } elseif ($statusRandom <= 98) {
            $status = 'inactive';
        } else {
            $status = 'graduated';
        }

        // Guardian information
        $guardianName = self::$firstNames[array_rand(self::$firstNames)] . ' ' . $lastName;
        $guardianContact = '09' . fake()->numerify('##-###-####');

        return [
            'student_id' => $studentId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'middle_name' => $middleName,
            'grade_level' => $gradeLevel,
            'section' => $section,
            'status' => $status,
            'contact_number' => $guardianContact,
            'guardian_name' => $guardianName,
            'guardian_contact' => $guardianContact,
        ];
    }

    /**
     * Set the student as active.
     *
     * @return static
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Set the student as inactive.
     *
     * @return static
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Set the student as graduated.
     *
     * @return static
     */
    public function graduated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'graduated',
        ]);
    }

    /**
     * Set the student's grade level.
     *
     * @param int|string $grade
     * @return static
     */
    public function inGrade(int|string $grade): static
    {
        return $this->state(fn (array $attributes) => [
            'grade_level' => (string) $grade,
        ]);
    }

    /**
     * Set the student's section.
     *
     * @param string $section
     * @return static
     */
    public function inSection(string $section): static
    {
        return $this->state(fn (array $attributes) => [
            'section' => $section,
        ]);
    }

    /**
     * Reset the student counter (useful for testing).
     *
     * @return void
     */
    public static function resetCounter(): void
    {
        self::$studentCounter = 0;
    }
}
