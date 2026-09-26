<?php

namespace Database\Factories;

use App\Enums\PostCategory;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'author_id' => User::factory(),
            'category' => PostCategory::Reflection,
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->sentence(20),
            'body' => collect(range(1, 5))->map(fn () => '<p>'.fake()->paragraph().'</p>')->implode(''),
            'published_at' => now(),
        ];
    }

    public function review(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => PostCategory::BookReview,
            'reviewed_book_title' => fake()->sentence(3),
            'reviewed_book_author' => fake()->name(),
            'rating' => fake()->numberBetween(3, 5),
            'affiliate_links' => [
                ['label' => 'Amazon', 'url' => 'https://www.amazon.com/dp/'.fake()->bothify('##########')],
            ],
        ]);
    }

    public function journal(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => PostCategory::Journal,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => null,
        ]);
    }
}
