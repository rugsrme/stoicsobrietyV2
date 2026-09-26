<?php

namespace Database\Factories;

use App\Models\ReaderReview;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReaderReview>
 */
class ReaderReviewFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quote' => fake()->sentence(18),
            'author' => fake()->name(),
            'context' => fake()->randomElement([null, 'Amazon review', 'Sponsor']),
            'is_published' => true,
            'is_featured' => true,
            'sort_order' => 0,
        ];
    }

    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => false,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }
}
