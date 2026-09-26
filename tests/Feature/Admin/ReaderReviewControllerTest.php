<?php

use App\Models\ReaderReview;
use App\Models\User;

test('guests cannot manage reader reviews', function () {
    $this->get(route('admin.reader-reviews.index'))->assertRedirect(route('login'));
});

test('non-admin users cannot manage reader reviews', function () {
    $user = User::factory()->create();
    $review = ReaderReview::factory()->create();

    $this->actingAs($user)->get(route('admin.reader-reviews.index'))->assertForbidden();
    $this->actingAs($user)->post(route('admin.reader-reviews.store'), ['quote' => 'x', 'author' => 'y'])->assertForbidden();
    $this->actingAs($user)->patch(route('admin.reader-reviews.update', $review), ['is_featured' => false])->assertForbidden();
    $this->actingAs($user)->delete(route('admin.reader-reviews.destroy', $review))->assertForbidden();
});

test('admins can list, add, edit and delete reader reviews', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('admin.reader-reviews.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.reader-reviews.create'))->assertOk();

    $this->actingAs($admin)->post(route('admin.reader-reviews.store'), [
        'quote' => 'It gave me words for something I had felt for years.',
        'author' => 'Sam',
        'context' => 'Sponsor',
        'is_published' => true,
        'is_featured' => true,
    ])->assertRedirect(route('admin.reader-reviews.index'));

    $review = ReaderReview::sole();
    expect($review->author)->toBe('Sam')
        ->and($review->is_featured)->toBeTrue()
        ->and($review->sort_order)->toBe(0);

    $this->actingAs($admin)->get(route('admin.reader-reviews.edit', $review))->assertOk();

    $this->actingAs($admin)->put(route('admin.reader-reviews.update', $review), [
        'quote' => 'Updated.',
        'author' => 'Sam R.',
        'context' => '',
        'sort_order' => 2,
        'is_published' => true,
        'is_featured' => true,
    ])->assertRedirect();

    expect($review->fresh())
        ->quote->toBe('Updated.')
        ->context->toBeNull()
        ->sort_order->toBe(2);

    $this->actingAs($admin)->delete(route('admin.reader-reviews.destroy', $review))
        ->assertRedirect(route('admin.reader-reviews.index'));

    expect(ReaderReview::count())->toBe(0);
});

test('a single checkbox can be flipped from the list', function () {
    $admin = User::factory()->admin()->create();
    $review = ReaderReview::factory()->create();

    $this->actingAs($admin)->patch(route('admin.reader-reviews.update', $review), [
        'is_featured' => false,
    ])->assertRedirect();

    expect($review->fresh())
        ->is_featured->toBeFalse()
        ->quote->toBe($review->quote);
});
