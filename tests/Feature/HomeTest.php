<?php

use App\Models\Book;
use App\Models\Post;
use App\Models\ReaderReview;
use Inertia\Testing\AssertableInertia as Assert;

test('home page renders the featured book', function () {
    $book = Book::factory()->featured()->create();

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Welcome')
        ->where('book.id', $book->id)
    );
});

test('home page renders without a book', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
});

test('home page shows posts ticked for the home page', function () {
    $featured = Post::factory()->create(['is_featured' => true]);
    $featuredReview = Post::factory()->review()->create(['is_featured' => true]);
    Post::factory()->create();
    Post::factory()->draft()->create(['is_featured' => true]);
    Post::factory()->journal()->create(['is_featured' => true]);

    $response = $this->get(route('home'));

    $response->assertInertia(fn (Assert $page) => $page
        ->has('posts', 2)
        ->where('posts', fn ($posts) => collect($posts)->pluck('id')->sort()->values()->all()
            === collect([$featured->id, $featuredReview->id])->sort()->values()->all())
    );
});

test('home page falls back to the latest posts when none are ticked', function () {
    Post::factory()->count(4)->create();
    Post::factory()->journal()->create();

    $response = $this->get(route('home'));

    $response->assertInertia(fn (Assert $page) => $page
        ->has('posts', 3)
        ->where('posts.0.category', fn ($category) => $category !== 'journal')
    );
});

test('home page carousel only shows published reader reviews ticked for it', function () {
    $shown = ReaderReview::factory()->create();
    ReaderReview::factory()->hidden()->create();
    ReaderReview::factory()->draft()->create();

    $response = $this->get(route('home'));

    $response->assertInertia(fn (Assert $page) => $page
        ->has('readerReviews', 1)
        ->where('readerReviews.0.id', $shown->id)
    );
});
