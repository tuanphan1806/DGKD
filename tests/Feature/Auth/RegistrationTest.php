<?php

namespace Tests\Feature\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $user = User::factory()->make();
        $userData = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'password123!',
            'password_confirmation' => 'password123!',
            'address' => $user->address,
            'city' => $user->city,
            'state' => $user->state,
            'country' => $user->country,
            'zipcode' => $user->zipcode,
            'mobile' => $user->mobile,
            'status' => $user->status,
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect(RouteServiceProvider::HOME);
        $this->assertAuthenticated();
    }
}
