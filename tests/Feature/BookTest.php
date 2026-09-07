<?php

use App\Models\Book;

test('published books are listed', function () {
    $published = Book::factory()->create(['published_at' => now()]);
    $draft = Book::factory()->create(['published_at' => null]);

    $response = $this->get(route('books.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('books/Index')
        ->has('books', 1)
        ->where('books.0.id', $published->id)
    );

    expect($draft->exists)->toBeTrue();
});

test('a published book can be viewed', function () {
    $book = Book::factory()->create(['published_at' => now()]);

    $response = $this->get(route('books.show', $book));

    $response->assertOk();
});

test('an unpublished book returns 404', function () {
    $book = Book::factory()->create(['published_at' => null]);

    $response = $this->get(route('books.show', $book));

    $response->assertNotFound();
});

test('downloading a sample 404s when none exists', function () {
    $book = Book::factory()->create(['published_at' => now(), 'sample_path' => null]);

    $response = $this->get(route('books.sample', $book));

    $response->assertNotFound();
});
