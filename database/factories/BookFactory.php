<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'subtitle' => fake()->sentence(6),
            'description' => fake()->paragraphs(3, true),
            'author_name' => fake()->name(),
            'author_bio' => fake()->paragraph(),
            'excerpts' => [fake()->sentence(20), fake()->sentence(20)],
            'retailer_links' => [
                'amazon' => 'https://amazon.com/',
                'barnes_noble' => 'https://barnesandnoble.com/',
                'bookshop' => 'https://bookshop.org/',
                'apple_books' => 'https://books.apple.com/',
            ],
            'price' => 1999,
            'currency' => 'USD',
            'purchase_type' => 'link',
            'is_featured' => false,
            'published_at' => now(),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
