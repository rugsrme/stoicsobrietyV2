<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Book::query()->updateOrCreate(
            ['slug' => 'architecture-of-surrender'],
            [
                'title' => 'Architecture of Surrender',
                'subtitle' => 'A stoic path through recovery',
                'description' => 'Architecture of Surrender is a meditation on what it means to let go without giving up — drawing on Stoic philosophy to build a durable, honest framework for recovery.',
                'author_name' => 'Stoic Recovery',
                'author_bio' => 'The author writes at the intersection of Stoic philosophy and recovery, drawing on lived experience to make old ideas useful again.',
                'excerpts' => [
                    'Surrender is not defeat. It is the first honest structure you build.',
                    "You do not control the wave. You control whether you're still standing when it passes.",
                    'Discipline is not punishment. It is the architecture that keeps the roof up.',
                ],
                'retailer_links' => [
                    'amazon' => 'https://www.amazon.com/',
                    'barnes_noble' => 'https://www.barnesandnoble.com/',
                    'bookshop' => 'https://bookshop.org/',
                    'apple_books' => 'https://books.apple.com/',
                ],
                'price' => 1899,
                'currency' => 'USD',
                'purchase_type' => 'link',
                'is_featured' => true,
                'published_at' => now(),
            ],
        );
    }
}
