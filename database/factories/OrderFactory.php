<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'source' => fake()->randomElement(Order::SOURCES),
            'quantity' => 1,
            'amount_cents' => fake()->numberBetween(999, 2999),
            'currency' => 'usd',
            'status' => fake()->randomElement(Order::STATUSES),
            'delivery_method' => 'external',
            'delivery_status' => 'not_required',
            'ordered_at' => now(),
        ];
    }
}
