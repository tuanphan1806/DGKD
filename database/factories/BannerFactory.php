<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BannerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'image' => $this->faker->image('public/front/images/banner_images', 800, 600, null, false), // tạo ảnh giả
            'type' => $this->faker->randomElement(['Slider', 'Fix']),
            'link' => $this->faker->slug,
            'title' => $this->faker->words(2, true),
            'alt' => $this->faker->words(2, true),
            'status' => $this->faker->numberBetween(0, 1),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
