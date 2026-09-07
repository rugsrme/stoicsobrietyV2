<?php

use App\Models\Post;

test('only published posts are listed', function () {
    $published = Post::factory()->create(['published_at' => now()->subDay()]);
    Post::factory()->create(['published_at' => null]);
    Post::factory()->create(['published_at' => now()->addDay()]);

    $response = $this->get(route('blog.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('blog/Index')
        ->has('posts.data', 1)
        ->where('posts.data.0.id', $published->id)
    );
});

test('a published post can be viewed', function () {
    $post = Post::factory()->create(['published_at' => now()->subDay()]);

    $response = $this->get(route('blog.show', $post));

    $response->assertOk();
});

test('an unpublished post returns 404', function () {
    $post = Post::factory()->create(['published_at' => null]);

    $response = $this->get(route('blog.show', $post));

    $response->assertNotFound();
});

test('a scheduled post returns 404 before its publish date', function () {
    $post = Post::factory()->create(['published_at' => now()->addDay()]);

    $response = $this->get(route('blog.show', $post));

    $response->assertNotFound();
});
