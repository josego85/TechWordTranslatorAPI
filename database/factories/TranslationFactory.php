<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Translation;
use App\Models\Word;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Translation>
 */
class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'word_id' => Word::factory(),
            'language' => fake()->randomElement(['en', 'es', 'de', 'fr', 'it', 'pt']),
            'translation' => fake()->word(),
        ];
    }

    /**
     * Indicate that the translation is in English.
     */
    public function english(): static
    {
        return $this->state(fn (array $attributes) => [
            'language' => 'en',
        ]);
    }

    /**
     * Indicate that the translation is in Spanish.
     */
    public function spanish(): static
    {
        return $this->state(fn (array $attributes) => [
            'language' => 'es',
        ]);
    }

    /**
     * Indicate that the translation is in German.
     */
    public function german(): static
    {
        return $this->state(fn (array $attributes) => [
            'language' => 'de',
        ]);
    }

    /**
     * Set a specific word for the translation.
     */
    public function forWord(Word $word): static
    {
        return $this->state(fn (array $attributes) => [
            'word_id' => $word->id,
        ]);
    }
}
