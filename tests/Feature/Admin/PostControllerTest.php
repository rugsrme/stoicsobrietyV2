<?php

use App\Models\Post;
use App\Models\User;

test('guests cannot access the admin posts area', function () {
    $response = $this->get(route('admin.posts.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can list posts', function () {
    $user = User::factory()->create();
    Post::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.posts.index'));

    $response->assertOk();
});

test('a post can be created as a draft', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.posts.store'), [
        'title' => 'A New Post',
        'slug' => 'a-new-post',
        'excerpt' => 'A short summary',
        'body' => 'The full body of the post.',
        'published' => false,
    ]);

    $response->assertRedirect(route('admin.posts.index'));

    $post = Post::where('slug', 'a-new-post')->firstOrFail();

    expect($post->author_id)->toBe($user->id);
    expect($post->published_at)->toBeNull();
});

test('a post can be created published', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('admin.posts.store'), [
        'title' => 'A Published Post',
        'slug' => 'a-published-post',
        'body' => 'The full body of the post.',
        'published' => true,
    ]);

    $post = Post::where('slug', 'a-published-post')->firstOrFail();

    expect($post->published_at)->not->toBeNull();
});

test('a post slug must be unique', function () {
    $user = User::factory()->create();
    Post::factory()->create(['slug' => 'taken']);

    $response = $this->actingAs($user)->post(route('admin.posts.store'), [
        'title' => 'Another Post',
        'slug' => 'taken',
        'body' => 'Body',
        'published' => false,
    ]);

    $response->assertSessionHasErrors('slug');
});

test('a post can be updated and published', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['published_at' => null]);

    $response = $this->actingAs($user)->put(route('admin.posts.update', $post), [
        'title' => 'Updated Title',
        'slug' => $post->slug,
        'body' => 'Updated body',
        'published' => true,
    ]);

    $response->assertRedirect(route('admin.posts.index'));

    $post->refresh();

    expect($post->title)->toBe('Updated Title');
    expect($post->published_at)->not->toBeNull();
});

test('a post can be unpublished', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['published_at' => now()]);

    $this->actingAs($user)->put(route('admin.posts.update', $post), [
        'title' => $post->title,
        'slug' => $post->slug,
        'body' => $post->body,
        'published' => false,
    ]);

    expect($post->refresh()->published_at)->toBeNull();
});

test('a post can be deleted', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $response = $this->actingAs($user)->delete(route('admin.posts.destroy', $post));

    $response->assertRedirect(route('admin.posts.index'));
    expect(Post::find($post->id))->toBeNull();
});
