<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'section_id' => 1,
            'category_id' => $this->faker->numberBetween(16, 18),
            'brand_id' => $this->faker->numberBetween(2, 14),
            'vendor_id' => 0,
            // 'admin_id' => 1,
            'admin_type' => 'admin',
            'product_name' => $this->faker->unique()->words(4, true),
            'product_code' => $this->faker->unique()->bothify('??###'),
            'product_color' => $this->faker->safeColorName(),
            'product_price' => $this->faker->numberBetween(50000, 40000),
            'product_discount' => $this->faker->randomElement([0, 5, 10]),
            'product_weight' => 1500,
            'product_image' => $this->faker->randomElement([
                '17498.webp', '91247.webp', '46875.webp', '35904.webp',
                '15017.webp', '75816.webp', '49243.webp', '86701.webp',
            ]),
            'product_video' => $this->faker->randomElement([
                'https://www.youtube.com/watch?v=example1',
                'https://www.youtube.com/watch?v=example2',
            ]),
            // 'group_code' => $this->faker->randomElement(['100', '120']),
            'description' => $this->faker->paragraph(2),
            // 'dai' => null,
            // 'card' => null,
            // 'ram' => null,
            // 'fabric' => null,
            'meta_title' => $this->faker->sentence(4),
            'meta_description' => $this->faker->sentence(6),
            'meta_keywords' => $this->faker->words(3, true),
            'is_featured' => $this->faker->randomElement(['Yes', 'No']),
            'is_bestseller' => $this->faker->randomElement(['Yes', 'No']),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
