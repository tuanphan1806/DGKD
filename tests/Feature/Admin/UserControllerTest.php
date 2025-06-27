<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateAsAdmin()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');
        return $admin;
    }

    public function test_admin_can_view_users_list()
    {
        $this->authenticateAsAdmin();

        User::factory()->create(['name' => 'Nguyen Van A']);

        $response = $this->get('/admin/users');

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.users');
        $response->assertViewHas('users');
    }

    public function test_admin_can_update_user_status()
    {
        $this->authenticateAsAdmin();

        $user = User::factory()->create(['status' => 1]);

        $response = $this->postJson('/admin/update-user-status', [
            'user_id' => $user->id,
            'status' => 'Active',
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 0,
            'user_id' => $user->id
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 0
        ]);
    }
    public function it_registers_user_successfully_via_ajax()
    {
        $response = $this->postJson('/user/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'mobile' => '0912345678',
            'password' => 'password123',
            'accept' => 'on',
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertJson(['type' => 'success']);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);
    }

    /** @test */
    public function it_rejects_registration_if_validation_fails()
    {
        $response = $this->postJson('/user/register', [
            'name' => '',
            'email' => 'invalid-email',
            'mobile' => '123',
            'password' => '',
            'accept' => ''
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertJson(['type' => 'error']);
        $response->assertJsonStructure(['errors']);
    }

    /** @test */
    public function it_updates_user_account_via_ajax()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/user/account', [
            'name' => 'New Name',
            'address' => '123 Street',
            'city' => 'Hanoi',
            'state' => 'HN',
            'country' => 'Vietnam',
            'zipcode' => '100000',
            'mobile' => '0912345678'
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertJson(['type' => 'success']);
        $this->assertDatabaseHas('users', ['name' => 'New Name']);
    }

    /** @test */
    public function it_updates_user_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpass')
        ]);

        $this->actingAs($user);

        $response = $this->postJson('/user/update-password', [
            'current_password' => 'oldpass',
            'new_password' => 'newpassword123',
            'confirm_password' => 'newpassword123'
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertJson(['type' => 'success']);
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    /** @test */
    public function it_fails_password_update_with_incorrect_current_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('correctpass')
        ]);

        $this->actingAs($user);

        $response = $this->postJson('/user/update-password', [
            'current_password' => 'wrongpass',
            'new_password' => 'newpassword123',
            'confirm_password' => 'newpassword123'
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertJson(['type' => 'incorrect']);
    }

    /** @test */
    public function it_logs_in_user_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
            'status' => 1
        ]);

        $response = $this->postJson('/user/login', [
            'email' => 'user@example.com',
            'password' => 'password123'
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertJson(['type' => 'success']);
    }

    /** @test */
    public function it_blocks_login_if_user_is_inactive()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
            'status' => 0
        ]);

        $response = $this->postJson('/user/login', [
            'email' => 'user@example.com',
            'password' => 'password123'
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertJson(['type' => 'inactive']);
    }
}
