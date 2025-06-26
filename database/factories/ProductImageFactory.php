<?php

namespace Database\Factories;

use App\Models\ProductsImage;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    protected $model = ProductsImage::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::inRandomOrder()->value('id') ?? 1, // fallback nếu không có
            'image' => $this->faker->unique()->lexify('image_??????.webp'),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
