<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'parent_id' => 0, // hoặc: $this->faker->numberBetween(0, 10),
            'section_id' => $this->faker->numberBetween(1, 20),
            'category_name' => $this->faker->unique()->word(),
            'category_image' => $this->faker->image('public/front/images/category_images', 640, 480, null, false),

            'category_discount' => $this->faker->randomFloat(2, 0, 50),
            'description' => $this->faker->sentence(),
            'url' => $this->faker->slug(),
            'meta_title' => $this->faker->words(3, true),
            'meta_description' => $this->faker->sentence(),
            'meta_keywords' => implode(', ', $this->faker->words(5)),
            'status' => $this->faker->numberBetween(0, 1),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
