<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company,
            'status' => 1, // hoặc $this->faker->numberBetween(0, 1) nếu muốn ngẫu nhiên
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
