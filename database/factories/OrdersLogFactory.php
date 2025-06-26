<?php

namespace Database\Factories;

use App\Models\OrdersLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrdersLogFactory extends Factory
{
    protected $model = OrdersLog::class;

    public function definition()
    {
        $statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled', 'Returned'];

        return [
            'order_id'      => $this->faker->numberBetween(1, 50), // Đảm bảo tồn tại trong bảng orders
            'order_item_id' => $this->faker->numberBetween(1, 100),
            'order_status'  => $this->faker->randomElement($statuses),
            'created_at'    => now(),
            'updated_at'    => now(),
        ];
    }
}
