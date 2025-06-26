<?php

namespace Database\Factories;

use App\Models\Rating;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class RatingFactory extends Factory
{
    protected $model = Rating::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->value('id') ?? 1,
            'product_id' => Product::inRandomOrder()->value('id') ?? 1,
            'review' => $this->faker->sentence(),
            'rating' => $this->faker->numberBetween(1, 5),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
