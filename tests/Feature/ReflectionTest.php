<?php

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
