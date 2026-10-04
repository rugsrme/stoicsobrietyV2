<?php

use App\Enums\PostCategory;
use App\Models\Post;

test('only published posts are listed', function () {
    $published = Post::factory()->create(['published_at' => now()->subDay()]);
    Post::factory()->create(['published_at' => null]);
    Post::factory()->create(['published_at' => now()->addDay()]);

    $response = $this->get(route('reflections.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('posts/Index')
        ->where('section', 'reflection')
        ->has('posts.data', 1)
        ->where('posts.data.0.id', $published->id)
    );
});

test('a published post can be viewed', function () {
    $post = Post::factory()->create(['published_at' => now()->subDay()]);

    $response = $this->get(route('reflections.show', $post));

    $response->assertOk();
});

test('an unpublished post returns 404', function () {
    $post = Post::factory()->create(['published_at' => null]);

    $response = $this->get(route('reflections.show', $post));

    $response->assertNotFound();
});

test('a scheduled post returns 404 before its publish date', function () {
    $post = Post::factory()->create(['published_at' => now()->addDay()]);

    $response = $this->get(route('reflections.show', $post));

    $response->assertNotFound();
});

test('book reviews and journal entries are not listed in reflections', function () {
    Post::factory()->review()->create(['published_at' => now()->subDay()]);
    Post::factory()->journal()->create(['published_at' => now()->subDay()]);

    $this->get(route('reflections.index'))
        ->assertInertia(fn ($page) => $page->has('posts.data', 0));
});

test('listings include a summary when no excerpt was written', function () {
    Post::factory()->create([
        'excerpt' => null,
        'body' => '<p>First paragraph.</p><p>Second.</p>',
        'published_at' => now()->subDay(),
    ]);

    $this->get(route('reflections.index'))
        ->assertInertia(fn ($page) => $page
            ->where('posts.data.0.summary', 'First paragraph. Second.')
            ->missing('posts.data.0.body')
        );
});

test('a review opened under reflections redirects to the reviews section', function () {
    $post = Post::factory()->review()->create(['published_at' => now()->subDay()]);

    $this->get(route('reflections.show', $post))
        ->assertRedirect(route('reviews.show', $post));
});

test('old blog links redirect to reflections', function () {
    $post = Post::factory()->create(['published_at' => now()->subDay()]);

    $this->get('/blog')->assertRedirect('/reflections')->assertStatus(301);
    $this->get('/blog/'.$post->slug)
        ->assertRedirect(route('reflections.show', $post))
        ->assertStatus(301);
});

test('a post\'s old slug redirects to its new one', function () {
    $post = Post::factory()->create(['slug' => 'old-slug', 'published_at' => now()->subDay()]);

    $post->update(['slug' => 'new-slug']);

    $this->get('/reflections/old-slug')
        ->assertMovedPermanently()
        ->assertRedirect(route('reflections.show', 'new-slug'));
});

test('every earlier slug keeps redirecting after several renames', function () {
    $post = Post::factory()->create(['slug' => 'first', 'published_at' => now()->subDay()]);

    $post->update(['slug' => 'second']);
    $post->update(['slug' => 'third']);

    $this->get('/reflections/first')->assertRedirect(route('reflections.show', 'third'));
    $this->get('/reflections/second')->assertRedirect(route('reflections.show', 'third'));
});

test('renaming a post back to an old slug drops that redirect', function () {
    $post = Post::factory()->create(['slug' => 'original', 'published_at' => now()->subDay()]);

    $post->update(['slug' => 'renamed']);
    $post->update(['slug' => 'original']);

    $this->get('/reflections/original')->assertOk();
    $this->get('/reflections/renamed')->assertRedirect(route('reflections.show', 'original'));
});

test('an old slug follows the post into another section', function () {
    $post = Post::factory()->create(['slug' => 'old-slug', 'published_at' => now()->subDay()]);

    $post->update(['slug' => 'new-slug', 'category' => PostCategory::BookReview]);

    $this->get('/reflections/old-slug')->assertRedirect(route('reviews.show', 'new-slug'));
});

test('an old slug of an unpublished or private post returns 404', function () {
    $unpublished = Post::factory()->create(['slug' => 'draft-old', 'published_at' => now()->subDay()]);
    $unpublished->update(['slug' => 'draft-new', 'published_at' => null]);

    $journal = Post::factory()->create(['slug' => 'journal-old', 'published_at' => now()->subDay()]);
    $journal->update(['slug' => 'journal-new', 'category' => PostCategory::Journal]);

    $this->get('/reflections/draft-old')->assertNotFound();
    $this->get('/reflections/journal-old')->assertNotFound();
});

test('a deleted post\'s old slugs return 404', function () {
    $post = Post::factory()->create(['slug' => 'old-slug', 'published_at' => now()->subDay()]);
    $post->update(['slug' => 'new-slug']);

    $post->delete();

    $this->get('/reflections/old-slug')->assertNotFound();
});

test('facebook click ids are stripped from shared links', function () {
    $post = Post::factory()->create(['slug' => 'shared', 'published_at' => now()->subDay()]);

    $this->get('/reflections/shared?fbclid=IwZXh0bgNhZW0')
        ->assertMovedPermanently()
        ->assertRedirect('/reflections/shared');

    $this->get('/reflections?page=2&fbclid=IwZXh0bgNhZW0')
        ->assertRedirect('/reflections?page=2');
});
