<?php

namespace Database\Factories;

use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

class SectionFactory extends Factory
{
    protected $model = Section::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'status' => $this->faker->randomElement([0, 1]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
