<?php

use App\Models\Post;

test('only published book reviews are listed', function () {
    $review = Post::factory()->review()->create(['published_at' => now()->subDay()]);
    Post::factory()->review()->draft()->create();
    Post::factory()->create(['published_at' => now()->subDay()]);

    $this->get(route('reviews.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('reviews/Index')
            ->has('posts.data', 1)
            ->where('posts.data.0.id', $review->id)
        );
});

test('a published review can be viewed with its affiliate links', function () {
    $review = Post::factory()->review()->create(['published_at' => now()->subDay()]);

    $this->get(route('reviews.show', $review))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('reviews/Show')
            ->where('post.affiliate_links.0.label', 'Amazon')
        );
});

test('an unpublished review returns 404', function () {
    $review = Post::factory()->review()->draft()->create();

    $this->get(route('reviews.show', $review))->assertNotFound();
});

test('a reflection opened under reviews redirects to reflections', function () {
    $post = Post::factory()->create(['published_at' => now()->subDay()]);

    $this->get(route('reviews.show', $post))
        ->assertRedirect(route('reflections.show', $post));
});
