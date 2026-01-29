<?php

/**
 * CategorySeeder
 *
 * Seeds the default book categories for the library management system.
 * Categories help organize books and make them easier to find.
 *
 * Categories are designed for an elementary school library and include:
 * - Literature categories (Fiction, Non-Fiction, Filipino/English Literature)
 * - Subject-based categories (Science, Mathematics, History)
 * - Skill-based categories (Arts & Music, Health & PE, Computer & Technology)
 *
 * @package Database\Seeders
 * @see App\Models\Category
 */

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Default categories for elementary school library.
     *
     * Each category has:
     * - name: The category title displayed in the UI
     * - description: A helpful description for librarians and students
     *
     * @var array<int, array<string, string>>
     */
    protected array $categories = [
        [
            'name' => 'Fiction',
            'description' => 'Imaginative stories including novels, fairy tales, fantasy, and adventure books.',
        ],
        [
            'name' => 'Non-Fiction',
            'description' => 'Factual books about real events, people, places, and informational topics.',
        ],
        [
            'name' => 'Reference',
            'description' => 'Dictionaries, encyclopedias, atlases, and other reference materials.',
        ],
        [
            'name' => 'Science',
            'description' => 'Books about nature, animals, plants, experiments, and scientific discoveries.',
        ],
        [
            'name' => 'Mathematics',
            'description' => 'Math workbooks, number games, puzzles, and mathematical concepts.',
        ],
        [
            'name' => 'Filipino Literature',
            'description' => 'Books written in Filipino/Tagalog including local stories, legends, and cultural materials.',
        ],
        [
            'name' => 'English Literature',
            'description' => 'Classic and contemporary English novels, short stories, and literary works.',
        ],
        [
            'name' => 'History',
            'description' => 'Historical events, world cultures, Philippine history, and social studies.',
        ],
        [
            'name' => 'Arts & Music',
            'description' => 'Books about drawing, painting, crafts, music appreciation, and creative expression.',
        ],
        [
            'name' => 'Health & PE',
            'description' => 'Books about health, hygiene, sports, physical education, and wellness.',
        ],
        [
            'name' => 'Computer & Technology',
            'description' => 'Books about computers, coding, internet safety, and digital literacy.',
        ],
    ];

    /**
     * Run the database seeds.
     *
     * Uses updateOrCreate to avoid duplicates if seeder is run multiple times.
     * This allows safe re-running of the seeder without data duplication.
     *
     * @return void
     */
    public function run(): void
    {
        foreach ($this->categories as $category) {
            Category::updateOrCreate(
                // Search criteria: match by name
                ['name' => $category['name']],
                // Values to set/update
                ['description' => $category['description']]
            );
        }

        $this->command->info('Default categories seeded successfully! (' . count($this->categories) . ' categories)');
    }
}
