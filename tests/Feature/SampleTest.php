<?php

use App\Models\User;

test('the sample starts at the opening of the book', function () {
    $this->get(route('sample.index'))
        ->assertRedirect(route('sample.chapter', 'front-matter'));
});

test('guests can read the opening and first three chapters', function (string $slug) {
    $this->get(route('sample.chapter', $slug))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('sample/Chapter')
            ->where('chapter.slug', $slug)
            ->has('chapter.blocks')
        );
})->with(['front-matter', 'chapter-1', 'chapter-2', 'chapter-3']);

test('the rest of the book is for subscribers', function () {
    $this->get(route('sample.chapter', 'chapter-4'))
        ->assertRedirect(route('library.chapter', 'chapter-4'));

    $this->get(route('library.chapter', 'chapter-4'))
        ->assertRedirect(route('login'));
});

test('the last free chapter points at a locked next chapter', function () {
    $this->get(route('sample.chapter', 'chapter-3'))
        ->assertInertia(fn ($page) => $page
            ->where('next.slug', 'chapter-4')
            ->where('next.public', false)
        );
});

test('unknown chapters return 404', function () {
    $this->get(route('sample.chapter', 'chapter-99'))->assertNotFound();
});

test('subscribers can read every chapter, including the sources', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('library.chapter', 'sources-and-notes'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('chapter.title', 'Sources and Notes')
        );
});
