<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        return [
            'order_id'        => $this->faker->numberBetween(1, 100),
            'user_id'         => $this->faker->numberBetween(1, 50),
            'payment_id'      => strtoupper('PAY-' . $this->faker->unique()->bothify('######')),
            'payer_id'        => strtoupper('PAYER-' . $this->faker->unique()->bothify('#####')),
            'payer_email'     => $this->faker->unique()->safeEmail,
            'amount'          => $this->faker->randomFloat(2, 10, 10000), // từ 10 đến 10,000
            'currency'        => $this->faker->randomElement(['USD', 'EUR', 'VND']),
            'payment_status'  => $this->faker->randomElement(['Completed', 'Pending', 'Failed']),
            'created_at'      => now(),
            'updated_at'      => now(),
        ];
    }
}
