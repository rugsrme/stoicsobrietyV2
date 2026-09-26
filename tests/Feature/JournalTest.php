<?php

use App\Models\Post;
use App\Models\User;

test('guests cannot see the journal', function () {
    $post = Post::factory()->journal()->create();

    $this->get(route('journal.index'))->assertRedirect(route('login'));
    $this->get(route('journal.show', $post))->assertRedirect(route('login'));
});

test('subscribers who are not admins cannot see the journal', function () {
    $user = User::factory()->create();
    $post = Post::factory()->journal()->create();

    $this->actingAs($user)->get(route('journal.index'))->assertForbidden();
    $this->actingAs($user)->get(route('journal.show', $post))->assertForbidden();
});

test('admins can read the journal', function () {
    $user = User::factory()->admin()->create();
    $entry = Post::factory()->journal()->create(['published_at' => now()->subDay()]);
    Post::factory()->create(['published_at' => now()->subDay()]);

    $this->actingAs($user)
        ->get(route('journal.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('posts/Index')
            ->where('section', 'journal')
            ->has('posts.data', 1)
            ->where('posts.data.0.id', $entry->id)
        );

    $this->actingAs($user)->get(route('journal.show', $entry))->assertOk();
});

test('journal entries are never reachable through public addresses', function () {
    $entry = Post::factory()->journal()->create(['published_at' => now()->subDay()]);

    $this->get(route('reflections.show', $entry))->assertNotFound();
    $this->get(route('reviews.show', $entry))->assertNotFound();
    $this->get('/blog/'.$entry->slug)->assertRedirect();
    $this->get(route('reviews.index'))->assertInertia(fn ($page) => $page->has('posts.data', 0));
});

test('journal entries stay off the subscriber dashboard', function () {
    $user = User::factory()->create();
    Post::factory()->journal()->create(['published_at' => now()]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertInertia(fn ($page) => $page->where('latestReflection', null));
});
