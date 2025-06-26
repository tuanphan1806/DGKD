<?php

namespace Database\Factories;

use App\Models\ProductsAttribute;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductAttributeFactory extends Factory
{
    protected $model = ProductsAttribute::class;

    public function definition(): array
    {
        $sizes = ['128GB', '256GB', '512GB', '64GB', '11', '12', '1'];

        return [
            'product_id' => Product::factory(), // hoặc ->random()->id nếu đã có data
            'size' => $this->faker->randomElement($sizes),
            'price' => $this->faker->randomFloat(2, 10000, 50000000),
            'stock' => $this->faker->numberBetween(0, 5000),
            'sku' => strtoupper($this->faker->bothify('???###')),
            'status' => $this->faker->randomElement([0, 1]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
