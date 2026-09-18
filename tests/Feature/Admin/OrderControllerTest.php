<?php

use App\Models\Book;
use App\Models\Order;
use App\Models\User;

test('guests cannot access the admin orders area', function () {
    $response = $this->get(route('admin.orders.index'));

    $response->assertRedirect(route('login'));
});

test('non-admin authenticated users cannot access the admin orders area', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('admin.orders.index'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.orders.create'))->assertForbidden();
});

test('admins can list orders', function () {
    $user = User::factory()->admin()->create();
    Order::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.orders.index'));

    $response->assertOk();
});

test('an order can be logged manually', function () {
    $user = User::factory()->admin()->create();
    $book = Book::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.orders.store'), [
        'book_id' => $book->id,
        'customer_name' => 'Jane Reader',
        'customer_email' => 'jane@example.com',
        'source' => 'amazon',
        'quantity' => 1,
        'amount' => '12.99',
        'currency' => 'usd',
        'status' => 'paid',
        'delivery_method' => 'external',
        'delivery_status' => 'not_required',
    ]);

    $order = Order::where('customer_name', 'Jane Reader')->firstOrFail();

    $response->assertRedirect(route('admin.orders.edit', $order));
    expect($order->amount_cents)->toBe(1299);
    expect($order->book_id)->toBe($book->id);
});

test('an order status of fulfilled sets a fulfilled_at timestamp', function () {
    $user = User::factory()->admin()->create();
    $order = Order::factory()->create(['status' => 'paid', 'fulfilled_at' => null]);

    $this->actingAs($user)->put(route('admin.orders.update', $order), [
        'customer_name' => $order->customer_name,
        'source' => $order->source,
        'quantity' => $order->quantity,
        'currency' => $order->currency,
        'status' => 'fulfilled',
        'delivery_method' => $order->delivery_method,
        'delivery_status' => 'delivered',
    ]);

    expect($order->refresh()->fulfilled_at)->not->toBeNull();
});

test('an order can be deleted', function () {
    $user = User::factory()->admin()->create();
    $order = Order::factory()->create();

    $response = $this->actingAs($user)->delete(route('admin.orders.destroy', $order));

    $response->assertRedirect(route('admin.orders.index'));
    expect(Order::find($order->id))->toBeNull();
});
