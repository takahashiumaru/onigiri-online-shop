<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_number' => 'ORD-' . strtoupper($this->faker->unique()->bothify('####????')),
            'subtotal' => 20000,
            'shipping_cost' => 5000,
            'total' => 25000,
            'payment_status' => 'paid',
            'status' => 'delivered',
            'shipping_name' => $this->faker->name(),
            'shipping_phone' => '08123456789',
            'shipping_address' => $this->faker->address(),
        ];
    }
}
