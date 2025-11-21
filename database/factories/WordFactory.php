<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Word;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Word>
 */
class WordFactory extends Factory
{
    protected $model = Word::class;

    /**
     * Technical terms for seeding
     */
    private static array $techTerms = [
        'Computer', 'Algorithm', 'Database', 'Network', 'Server',
        'Protocol', 'Interface', 'Framework', 'Repository', 'API',
        'Cache', 'Container', 'Deployment', 'Middleware', 'Queue',
        'Authentication', 'Authorization', 'Encryption', 'Firewall', 'Gateway',
        'Router', 'Switch', 'Bandwidth', 'Latency', 'Throughput',
        'Virtual Machine', 'Cloud Computing', 'Microservice', 'Scalability', 'Load Balancer',
    ];

    private static int $termIndex = 0;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Use predefined tech terms if available, otherwise generate random
        if (self::$termIndex < count(self::$techTerms)) {
            $word = self::$techTerms[self::$termIndex];
            self::$termIndex++;
        } else {
            $word = ucfirst(fake()->unique()->word()) . ' ' . ucfirst(fake()->word());
        }

        return [
            'english_word' => $word,
        ];
    }

    /**
     * Reset the term index (useful for testing)
     */
    public static function resetTermIndex(): void
    {
        self::$termIndex = 0;
    }
}
