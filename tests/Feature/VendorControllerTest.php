<?php

namespace Tests\Feature\Front;

use App\Models\Admin;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_vendor_register_form_displays_correctly()
    {
        $response = $this->get('/vendor/login-register');
        $response->assertStatus(200);
        $response->assertViewIs('front.vendors.login_register');
    }

    public function test_vendor_can_register_with_valid_data()
    {
        $response = $this->post('/vendor/register', [
            'name' => 'Nguyen Van A',
            'email' => 'vendor@example.com',
            'mobile' => '0912345678',
            'password' => '12345678',
            'accept' => 'on',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success_message', 'Cảm ơn vì đã trở thành nhà cung cấp. Chúng tôi sẽ xác nhận bằng email khi tài khoản được phê duyệt.');

        $this->assertDatabaseHas('vendors', ['email' => 'vendor@example.com']);
        $this->assertDatabaseHas('admins', ['email' => 'vendor@example.com', 'type' => 'vendor']);
    }

    public function test_register_fails_with_missing_required_fields()
    {
        $response = $this->post('/vendor/register', []);
        $response->assertSessionHasErrors(['name', 'email', 'mobile', 'accept']);
    }

    public function test_register_fails_with_duplicate_email_or_mobile()
    {
        Vendor::factory()->create([
            'email' => 'vendor@exist.com',
            'mobile' => '0911222333',
        ]);

        $response = $this->post('/vendor/register', [
            'name' => 'Dup',
            'email' => 'vendor@exist.com',
            'mobile' => '0911222333',
            'password' => 'password',
            'accept' => 'on',
        ]);

        $response->assertSessionHasErrors(['email', 'mobile']);
    }

    public function test_register_fails_without_password()
    {
        $response = $this->post('/vendor/register', [
            'name' => 'No Pass',
            'email' => 'nopass@example.com',
            'mobile' => '0912345678',
            'accept' => 'on',
        ]);

        // Laravel không validate password nhưng sẽ lỗi khi `bcrypt(null)`
        $response->assertSessionHasErrors();
    }

    public function test_register_fails_without_accepting_terms()
    {
        $response = $this->post('/vendor/register', [
            'name' => 'No Accept',
            'email' => 'noaccept@example.com',
            'mobile' => '0912345678',
            'password' => '12345678',
        ]);

        $response->assertSessionHasErrors(['accept']);
    }
}
