<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProductsFiltersValue;
use App\Models\ProductsFilter;

class ProductsFiltersValueFactory extends Factory
{
    protected $model = ProductsFiltersValue::class;

    public function definition(): array
    {
        return [
            'filter_id' => ProductsFilter::inRandomOrder()->value('id') ?? 1, // fallback nếu chưa có dữ liệu
            'filter_value' => $this->faker->unique()->word,
            'status' => $this->faker->boolean(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
