<?php

use App\Models\User;

test('guests cannot access the admin users area', function () {
    $response = $this->get(route('admin.users.index'));

    $response->assertRedirect(route('login'));
});

test('non-admin authenticated users cannot access the admin users area', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
});

test('admins can list and search users', function () {
    $admin = User::factory()->admin()->create();
    $match = User::factory()->create(['name' => 'Jane Reader', 'email' => 'jane@example.com']);
    User::factory()->create(['name' => 'No Match', 'email' => 'nomatch@example.com']);

    $response = $this->actingAs($admin)->get(route('admin.users.index', ['search' => 'jane']));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('users.data', 1)
        ->where('users.data.0.id', $match->id)
    );
});

test('an admin can promote another user to admin', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($admin)->patch(route('admin.users.update', $user), [
        'is_admin' => true,
    ]);

    $response->assertRedirect();
    expect($user->refresh()->is_admin)->toBeTrue();
});

test('an admin cannot remove their own admin access', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->patch(route('admin.users.update', $admin), [
        'is_admin' => false,
    ]);

    expect($admin->refresh()->is_admin)->toBeTrue();
});
