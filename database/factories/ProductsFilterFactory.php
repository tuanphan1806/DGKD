<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProductsFilter;

class ProductsFilterFactory extends Factory
{
    protected $model = ProductsFilter::class;

    public function definition(): array
    {
        return [
            'cat_ids' => implode(',', $this->faker->randomElements([1, 2, 3, 4, 5], rand(1, 3))),
            'filter_name' => $this->faker->word,
            'filter_column' => $this->faker->unique()->word,
            'status' => $this->faker->boolean(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
