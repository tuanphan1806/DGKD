<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    public function definition(): array
    {
        return [
            'session_id' => $this->faker->uuid,
            'user_id' => $this->faker->optional()->numberBetween(1, 100), // Có thể là null
            'product_id' => $this->faker->numberBetween(1, 50),
            'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL', '128GB', '256GB', '11', '42']),
            'quantity' => $this->faker->numberBetween(1, 5),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
