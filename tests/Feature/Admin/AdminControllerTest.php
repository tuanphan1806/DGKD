<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Vendor;
use App\Models\VendorsBankDetail;
use App\Models\VendorsBusinessDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        // parent::setUp();
        // Storage::fake('public');
        parent::setUp();
        Artisan::call('storage:link');
        // Ensure directories exist for direct image saves
        if (!file_exists(public_path('admin/images/photos'))) {
            mkdir(public_path('admin/images/photos'), 0777, true);
        }
        if (!file_exists(public_path('admin/images/proofs'))) {
            mkdir(public_path('admin/images/proofs'), 0777, true);
        }
        // Mock Intervention Image operations
        \Intervention\Image\Facades\Image::shouldReceive('make')->andReturnSelf();
        \Intervention\Image\Facades\Image::shouldReceive('save')->andReturn(true);
    }

    public function test_admin_can_login()
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('123456'),
            'status' => 1,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => '123456'
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated('admin');
    }

    public function test_admin_dashboard_counts_are_displayed()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertViewHasAll([
            'sectionsCount', 'categoriesCount', 'productsCount', 'ordersCount',
            'couponsCount', 'brandsCount', 'usersCount', 'vendorsCount'
        ]);
    }

    public function test_update_admin_password_success()
    {
        $admin = Admin::factory()->create([
            'password' => bcrypt('oldpass')
        ]);

        $this->actingAs($admin, 'admin');

        $response = $this->post('/admin/update-admin-password', [
            'current_password' => 'oldpass',
            'new_password' => 'newpass123',
            'confirm_password' => 'newpass123'
        ]);

        $response->assertSessionHas('success_message');
        $this->assertTrue(Hash::check('newpass123', $admin->fresh()->password));
    }

    public function test_update_admin_password_wrong_current()
    {
        $admin = Admin::factory()->create([
            'password' => bcrypt('oldpass')
        ]);
        $this->actingAs($admin, 'admin');

        $response = $this->post('/admin/update-admin-password', [
            'current_password' => 'wrongpass',
            'new_password' => 'newpass123',
            'confirm_password' => 'newpass123'
        ]);

        $response->assertSessionHas('error_message');
    }

    public function test_check_admin_password_valid()
    {
        $admin = Admin::factory()->create([
            'password' => bcrypt('test123')
        ]);

        $this->actingAs($admin, 'admin');
        $response = $this->post('/admin/check-admin-password', [
            'current_password' => 'test123'
        ]);

        $response->assertSee('true');
    }

    public function test_update_admin_details_with_image()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $image = UploadedFile::fake()->image('admin.jpg');
        $response = $this->post('/admin/update-admin-details', [
            'admin_name' => 'Nguyen Van A',
            'admin_mobile' => '0901234567',
            'admin_image' => $image
        ]);

        $response->assertSessionHas('success_message');
    }

    public function test_view_vendor_details()
    {
        $vendor = Vendor::factory()->create();
        $admin = Admin::factory()->create([
            'vendor_id' => $vendor->id
        ]);

        $this->actingAs($admin, 'admin');
        $response = $this->get('/admin/view-vendor-details/' . $admin->id);

        $response->assertStatus(200);
        $response->assertViewHas('vendorDetails');
    }

    public function test_admin_status_toggle()
    {
        $admin = Admin::factory()->create(['status' => 1]);
        $vendor = Vendor::factory()->create(['id' => $admin->vendor_id, 'status' => 1]);

        $this->actingAs($admin, 'admin');

        $response = $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
        ])->postJson('/admin/update-admin-status', [
            'status'   => 'Active',
            'admin_id' => $admin->id,
        ]);

        $response->assertJson(['status' => 0]);
        $this->assertEquals(0, $admin->fresh()->status);
    }

    public function test_admin_logout()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $response = $this->get('/admin/logout');
        $response->assertRedirect('/admin/login');
        $this->assertGuest('admin');
    }
    public function test_update_vendor_details_personal()
    {
        $vendor = Vendor::factory()->create();
        $admin = Admin::factory()->create(['vendor_id' => $vendor->id]);

        $this->actingAs($admin, 'admin');

        $image = UploadedFile::fake()->image('vendor.jpg');

        $response = $this->post('/admin/update-vendor-details/personal', [
            'vendor_name' => 'Vendor Name',
            'vendor_city' => 'Hanoi',
            'vendor_mobile' => '0911223344',
            'vendor_address' => '123 Street',
            'vendor_state' => 'State A',
            'vendor_country' => 'Vietnam',
            'vendor_zipcode' => '10000',
            'vendor_image' => $image
        ]);

        $response->assertSessionHas('success_message');
        $this->assertDatabaseHas('vendors', ['id' => $vendor->id, 'name' => 'Vendor Name']);
    }

    public function test_update_vendor_details_business()
    {
        $vendor = Vendor::factory()->create();
        $admin = Admin::factory()->create(['vendor_id' => $vendor->id]);

        VendorsBusinessDetail::factory()->create(['vendor_id' => $vendor->id, 'shop_name' => 'Old Shop']);

        $this->actingAs($admin, 'admin');
        $proofImage = UploadedFile::fake()->image('proof.jpg');

        $response = $this->post('/admin/update-vendor-details/business', [
            'shop_name' => 'Shop ABC',
            'shop_mobile' => '0911223344',
            'shop_address' => '456 Road',
            'shop_city' => 'HCM',
            'shop_state' => 'State B',
            'shop_country' => 'Vietnam',
            'shop_zipcode' => '70000',
            'business_license_number' => '12345678',
            'gst_number' => 'GST123',
            'pan-number' => 'PAN789',
            'address_proof' => 'utility_bill',
            'address_proof_image' => $proofImage
        ]);

        $response->assertSessionHas('success_message');
        $this->assertDatabaseHas('vendors_business_details', [
            'vendor_id' => $vendor->id,
            'shop_name' => 'Shop ABC'
        ]);
    }

    public function test_update_vendor_details_bank()
    {
        $vendor = Vendor::factory()->create();
        $admin = Admin::factory()->create(['vendor_id' => $vendor->id]);

        $this->actingAs($admin, 'admin');

        $response = $this->post('/admin/update-vendor-details/bank', [
            'bank_name' => 'Vietcombank',
            'account_holder_name' => 'Nguyen A',
            'account_number' => '123456789',
            'bank_ifsc_code' => 'VCB123456'
        ]);

        $response->assertSessionHas('success_message');
        $this->assertDatabaseHas('vendors_bank_details', [
            'vendor_id' => $vendor->id,
            'bank_name' => 'Vietcombank',
            'account_holder_name' => 'Nguyen A',
        ]);
    }
}
