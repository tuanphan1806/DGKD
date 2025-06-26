<?php

namespace Database\Factories;

use App\Models\OrderItemStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemStatusFactory extends Factory
{
    protected $model = OrderItemStatus::class;

    public function definition()
    {
        return [
            'name'       => $this->faker->unique()->randomElement([
                'Pending', 'In Process', 'Shipped', 'Delivered', 'Cancelled', 'Returned'
            ]),
            'status'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
