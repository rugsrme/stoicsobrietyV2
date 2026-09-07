<?php

use App\Models\Book;
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
