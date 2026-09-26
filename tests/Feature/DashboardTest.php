<?php

use App\Models\Post;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('the dashboard shows the latest reflection and the latest book review', function () {
    $user = User::factory()->create();
    Post::factory()->create(['published_at' => now()->subDays(2)]);
    $reflection = Post::factory()->create(['published_at' => now()->subDay()]);
    $review = Post::factory()->review()->create(['published_at' => now()->subDay()]);
    Post::factory()->review()->draft()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertInertia(fn ($page) => $page
            ->where('latestReflection.id', $reflection->id)
            ->where('latestReview.id', $review->id)
            ->has('latestReview.summary')
            ->missing('latestReview.body')
        );
});
