<?php

/**
 * BookFactory
 *
 * Factory for generating realistic book data for testing.
 * Creates books with authentic titles and authors appropriate
 * for an elementary school library.
 *
 * Usage:
 * Book::factory()->create();                       // Create one book
 * Book::factory()->count(100)->create();           // Create 100 books
 * Book::factory()->available()->create();          // Create available book
 * Book::factory()->inCategory($categoryId)->create(); // Specific category
 *
 * @package Database\Factories
 * @see App\Models\Book
 */

namespace Database\Factories;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Book::class;

    /**
     * Counter for generating unique accession numbers.
     *
     * @var int
     */
    protected static int $bookCounter = 0;

    /**
     * Sample book titles organized by category type.
     *
     * @var array<string, array<int, array<string, string>>>
     */
    protected static array $booksByCategory = [
        'Fiction' => [
            ['title' => 'The Little Prince', 'author' => 'Antoine de Saint-Exupéry'],
            ['title' => 'Charlotte\'s Web', 'author' => 'E.B. White'],
            ['title' => 'Matilda', 'author' => 'Roald Dahl'],
            ['title' => 'The Giving Tree', 'author' => 'Shel Silverstein'],
            ['title' => 'James and the Giant Peach', 'author' => 'Roald Dahl'],
            ['title' => 'Charlie and the Chocolate Factory', 'author' => 'Roald Dahl'],
            ['title' => 'The BFG', 'author' => 'Roald Dahl'],
            ['title' => 'Where the Wild Things Are', 'author' => 'Maurice Sendak'],
            ['title' => 'The Cat in the Hat', 'author' => 'Dr. Seuss'],
            ['title' => 'Green Eggs and Ham', 'author' => 'Dr. Seuss'],
            ['title' => 'Goodnight Moon', 'author' => 'Margaret Wise Brown'],
            ['title' => 'The Very Hungry Caterpillar', 'author' => 'Eric Carle'],
        ],
        'Non-Fiction' => [
            ['title' => 'National Geographic Kids Encyclopedia', 'author' => 'National Geographic'],
            ['title' => 'The Magic School Bus: Inside the Human Body', 'author' => 'Joanna Cole'],
            ['title' => 'What Do You Do With an Idea?', 'author' => 'Kobi Yamada'],
            ['title' => 'Hidden Figures (Young Readers Edition)', 'author' => 'Margot Lee Shetterly'],
            ['title' => 'I Am Malala (Young Readers Edition)', 'author' => 'Malala Yousafzai'],
            ['title' => 'Who Was Albert Einstein?', 'author' => 'Jess Brallier'],
            ['title' => 'Who Was Abraham Lincoln?', 'author' => 'Janet Pascal'],
            ['title' => 'The Story of Ruby Bridges', 'author' => 'Robert Coles'],
        ],
        'Reference' => [
            ['title' => 'Merriam-Webster\'s Elementary Dictionary', 'author' => 'Merriam-Webster'],
            ['title' => 'World Atlas for Kids', 'author' => 'National Geographic'],
            ['title' => 'Children\'s Encyclopedia', 'author' => 'DK Publishing'],
            ['title' => 'First Encyclopedia of Science', 'author' => 'Rachel Firth'],
            ['title' => 'Filipino-English Dictionary', 'author' => 'Various Authors'],
            ['title' => 'Thesaurus for Kids', 'author' => 'Scholastic'],
        ],
        'Science' => [
            ['title' => 'The Way Things Work Now', 'author' => 'David Macaulay'],
            ['title' => 'There\'s No Place Like Space', 'author' => 'Tish Rabe'],
            ['title' => 'The Big Book of Bugs', 'author' => 'Yuval Zommer'],
            ['title' => 'National Geographic Little Kids First Big Book of Dinosaurs', 'author' => 'Catherine Hughes'],
            ['title' => 'The Human Body Book', 'author' => 'Steve Parker'],
            ['title' => 'Weather and Climate', 'author' => 'Scholastic'],
            ['title' => 'Plants and Animals', 'author' => 'DK Publishing'],
            ['title' => 'Our Solar System', 'author' => 'Seymour Simon'],
        ],
        'Mathematics' => [
            ['title' => 'Math Curse', 'author' => 'Jon Scieszka'],
            ['title' => 'Sir Cumference and the First Round Table', 'author' => 'Cindy Neuschwander'],
            ['title' => 'The Grapes of Math', 'author' => 'Greg Tang'],
            ['title' => 'Fractions in Disguise', 'author' => 'Edward Einhorn'],
            ['title' => 'One Grain of Rice', 'author' => 'Demi'],
            ['title' => 'Math for All Seasons', 'author' => 'Greg Tang'],
            ['title' => 'The Doorbell Rang', 'author' => 'Pat Hutchins'],
        ],
        'Filipino Literature' => [
            ['title' => 'Noli Me Tangere (Abridged for Children)', 'author' => 'Jose Rizal (Adapted)'],
            ['title' => 'Mga Kuwento ni Lola Basyang', 'author' => 'Severino Reyes'],
            ['title' => 'Si Janus Silang at ang Tiyanak ng Tabon', 'author' => 'Edgar Calabia Samar'],
            ['title' => 'Ibong Adarna', 'author' => 'Jose de la Cruz'],
            ['title' => 'Alamat ng Pinya', 'author' => 'Traditional'],
            ['title' => 'Alamat ng Sampaguita', 'author' => 'Traditional'],
            ['title' => 'Ang Mahiwagang Biyulin', 'author' => 'Rene Villanueva'],
            ['title' => 'Si Pilandok', 'author' => 'Traditional Filipino'],
            ['title' => 'Ang Alamat ng Ampalaya', 'author' => 'Traditional'],
            ['title' => 'Florante at Laura (Simplified)', 'author' => 'Francisco Balagtas'],
        ],
        'English Literature' => [
            ['title' => 'Tales of a Fourth Grade Nothing', 'author' => 'Judy Blume'],
            ['title' => 'Diary of a Wimpy Kid', 'author' => 'Jeff Kinney'],
            ['title' => 'Percy Jackson: The Lightning Thief', 'author' => 'Rick Riordan'],
            ['title' => 'Harry Potter and the Sorcerer\'s Stone', 'author' => 'J.K. Rowling'],
            ['title' => 'The Chronicles of Narnia: The Lion, the Witch and the Wardrobe', 'author' => 'C.S. Lewis'],
            ['title' => 'A Wrinkle in Time', 'author' => 'Madeleine L\'Engle'],
            ['title' => 'Bridge to Terabithia', 'author' => 'Katherine Paterson'],
            ['title' => 'The Giver', 'author' => 'Lois Lowry'],
        ],
        'History' => [
            ['title' => 'Who Was Jose Rizal?', 'author' => 'Various Authors'],
            ['title' => 'The Philippines: A Visual History', 'author' => 'Various Authors'],
            ['title' => 'History of the Philippines for Kids', 'author' => 'Rex Bookstore'],
            ['title' => 'World History for Kids', 'author' => 'DK Publishing'],
            ['title' => 'Ancient Civilizations', 'author' => 'Scholastic'],
            ['title' => 'The Story of Lapu-Lapu', 'author' => 'Tahanan Books'],
            ['title' => 'Andres Bonifacio: Hero of the Masses', 'author' => 'Adarna House'],
        ],
        'Arts & Music' => [
            ['title' => 'Art Lab for Kids', 'author' => 'Susan Schwake'],
            ['title' => 'The Story of the Orchestra', 'author' => 'Robert Levine'],
            ['title' => 'Drawing for Kids', 'author' => 'Various Authors'],
            ['title' => 'Music Theory for Kids', 'author' => 'Scholastic'],
            ['title' => 'Origami for Beginners', 'author' => 'Various Authors'],
            ['title' => 'Painting and Drawing', 'author' => 'DK Publishing'],
        ],
        'Health & PE' => [
            ['title' => 'The Care and Keeping of You', 'author' => 'American Girl'],
            ['title' => 'My Body Belongs to Me', 'author' => 'Jill Starishevsky'],
            ['title' => 'Sports Illustrated Kids', 'author' => 'Various Authors'],
            ['title' => 'Healthy Eating for Kids', 'author' => 'Various Authors'],
            ['title' => 'First Aid for Kids', 'author' => 'Red Cross'],
            ['title' => 'Exercise and Fitness', 'author' => 'Scholastic'],
        ],
        'Computer & Technology' => [
            ['title' => 'Coding for Kids', 'author' => 'Various Authors'],
            ['title' => 'Scratch Programming for Beginners', 'author' => 'DK Publishing'],
            ['title' => 'How Computers Work', 'author' => 'Ron White'],
            ['title' => 'Internet Safety for Kids', 'author' => 'Various Authors'],
            ['title' => 'Robots and AI for Kids', 'author' => 'DK Publishing'],
            ['title' => 'Digital Citizenship', 'author' => 'Various Authors'],
        ],
    ];

    /**
     * Publisher names.
     *
     * @var array<int, string>
     */
    protected static array $publishers = [
        'Scholastic', 'Penguin Random House', 'HarperCollins', 'Simon & Schuster',
        'Hachette Book Group', 'Rex Bookstore', 'Adarna House', 'Anvil Publishing',
        'National Geographic', 'DK Publishing', 'Tahanan Books', 'OMF Literature',
    ];

    /**
     * Shelf locations.
     *
     * @var array<int, string>
     */
    protected static array $locations = [
        'Shelf A-1', 'Shelf A-2', 'Shelf A-3', 'Shelf B-1', 'Shelf B-2', 'Shelf B-3',
        'Shelf C-1', 'Shelf C-2', 'Shelf C-3', 'Shelf D-1', 'Shelf D-2', 'Shelf D-3',
        'Reference Section', 'Reading Corner', 'New Arrivals',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        self::$bookCounter++;
        $currentYear = date('Y');

        // Generate accession number: YEAR-#### format
        $accessionNumber = $currentYear . '-' . str_pad(self::$bookCounter, 4, '0', STR_PAD_LEFT);

        // Get a random category
        $category = Category::inRandomOrder()->first();
        $categoryName = $category?->name ?? 'Fiction';

        // Get book data based on category
        $bookData = $this->getBookDataForCategory($categoryName);

        // Random number of copies (1-5)
        $copiesTotal = fake()->numberBetween(1, 5);

        // 85% chance all copies are available
        $copiesAvailable = fake()->boolean(85)
            ? $copiesTotal
            : fake()->numberBetween(0, $copiesTotal);

        // Book condition distribution
        $condition = fake()->randomElement([
            'excellent', 'excellent',
            'good', 'good', 'good', 'good',
            'fair', 'fair',
            'poor',
        ]);

        // Status based on availability
        $status = $copiesAvailable > 0 ? 'available' : 'unavailable';

        return [
            'accession_number' => $accessionNumber,
            'isbn' => fake()->optional(0.7)->isbn13(),
            'title' => $bookData['title'],
            'author' => $bookData['author'],
            'publisher' => fake()->randomElement(self::$publishers),
            'publication_year' => fake()->numberBetween(1990, 2024),
            'category_id' => $category?->id ?? 1,
            'edition' => fake()->optional(0.3)->randomElement(['1st Edition', '2nd Edition', '3rd Edition', 'Revised Edition']),
            'pages' => fake()->numberBetween(20, 400),
            'copies_total' => $copiesTotal,
            'copies_available' => $copiesAvailable,
            'location' => fake()->randomElement(self::$locations),
            'condition' => $condition,
            'description' => fake()->optional(0.6)->sentence(15),
            'cover_image' => null,
            'status' => $status,
        ];
    }

    /**
     * Get book data for a specific category.
     *
     * @param string $categoryName
     * @return array<string, string>
     */
    protected function getBookDataForCategory(string $categoryName): array
    {
        // Check if we have books for this category
        if (isset(self::$booksByCategory[$categoryName])) {
            return fake()->randomElement(self::$booksByCategory[$categoryName]);
        }

        // Default to Fiction if category not found
        return fake()->randomElement(self::$booksByCategory['Fiction']);
    }

    /**
     * Set the book as available.
     *
     * @return static
     */
    public function available(): static
    {
        return $this->state(function (array $attributes) {
            $copiesTotal = $attributes['copies_total'] ?? fake()->numberBetween(1, 5);
            return [
                'copies_total' => $copiesTotal,
                'copies_available' => $copiesTotal,
                'status' => 'available',
            ];
        });
    }

    /**
     * Set the book as unavailable.
     *
     * @return static
     */
    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'copies_available' => 0,
            'status' => 'unavailable',
        ]);
    }

    /**
     * Set the book's category.
     *
     * @param int $categoryId
     * @return static
     */
    public function inCategory(int $categoryId): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $categoryId,
        ]);
    }

    /**
     * Set the book's condition.
     *
     * @param string $condition
     * @return static
     */
    public function inCondition(string $condition): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => $condition,
        ]);
    }

    /**
     * Reset the book counter (useful for testing).
     *
     * @return void
     */
    public static function resetCounter(): void
    {
        self::$bookCounter = 0;
    }
}
