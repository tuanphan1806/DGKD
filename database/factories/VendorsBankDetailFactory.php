<?php

namespace Database\Factories;

use App\Models\VendorsBankDetail;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorsBankDetailFactory extends Factory
{
    protected $model = VendorsBankDetail::class;

    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory(), // hoặc random trong DB nếu đã có Vendor
            'account_holder_name' => $this->faker->name(),
            'bank_name' => $this->faker->randomElement(['Vietcombank', 'Techcombank', 'MB Bank', 'VPBank']),
            'account_number' => $this->faker->bankAccountNumber(),
            'bank_ifsc_code' => strtoupper($this->faker->bothify('???######')),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
