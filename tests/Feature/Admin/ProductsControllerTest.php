<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Section;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Intervention\Image\Facades\Image;

class ProductsControllerTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        // Ensure public directory for banner images exists
        $path = public_path('front/images/product_images');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        chmod($path, 0777);
        // Mock Intervention Image
        Image::shouldReceive('make')->andReturnSelf();
        Image::shouldReceive('resize')->andReturnSelf();
        Image::shouldReceive('save')->andReturn(true);
    }
    protected function authenticateAsAdmin()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');
        return $admin;
    }

    public function test_admin_can_view_products_list()
    {
        $this->authenticateAsAdmin();

        $response = $this->get('/admin/products');

        $response->assertStatus(200);
    }

    public function test_admin_can_add_product()
    {
        $this->authenticateAsAdmin();

        $section = Section::factory()->create();
        $category = Category::factory()->create(['section_id' => $section->id]);
        $brand = Brand::factory()->create();

        // Tạo thư mục và giả lập hình ảnh
        Storage::fake('public');
        $image = UploadedFile::fake()->image('product.jpg');

        $response = $this->post('/admin/add-edit-product', [
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'group_code' => 'group01',
            'product_name' => 'Sản phẩm test',
            'product_code' => 'SP01',
            'product_color' => 'Đỏ',
            'product_price' => 120000,
            'product_discount' => 10000,
            'product_weight' => 500,
            'description' => 'Mô tả sản phẩm',
            'meta_title' => 'Meta title',
            'meta_description' => 'Meta description',
            'meta_keywords' => 'từ khóa',
            'is_featured' => 'Yes',
            'is_bestseller' => 'No',
            'product_image' => $image,
        ]);

        $response->assertRedirect('/admin/products');
        $this->assertDatabaseHas('products', [
            'product_code' => 'SP01',
            'product_color' => 'Đỏ'
        ]);
    }

    public function test_admin_can_delete_product()
    {
        $this->authenticateAsAdmin();

        $product = Product::factory()->create();

        $response = $this->get('/admin/delete-product/' . $product->id);

        $response->assertRedirect();
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_admin_can_update_product_status()
    {
        $this->authenticateAsAdmin();

        $product = Product::factory()->create(['status' => 1]);

        $response = $this->postJson('/admin/update-product-status', [
            'product_id' => $product->id,
            'status' => 'Active',
        ]);

        $response->assertJson(['status' => 0]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => 0,
        ]);
    }
}
