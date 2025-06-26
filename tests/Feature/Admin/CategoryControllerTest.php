<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Intervention\Image\Facades\Image;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();

        $imagePath = public_path('front/images/category_images');
        if (!file_exists($imagePath)) {
            mkdir($imagePath, 0777, true);
        }
        chmod($imagePath, 0777);
        // Mock Intervention Image
        Image::shouldReceive('make')->andReturnSelf();
        Image::shouldReceive('resize')->andReturnSelf();
        Image::shouldReceive('save')->andReturn(true);
    }

    private function actingAsAdmin()
    {
        $admin = Admin::factory()->create();
        return $this->actingAs($admin, 'admin');
    }

    public function test_view_categories_page()
    {
        $this->actingAsAdmin();
        $response = $this->get('/admin/categories');
        $response->assertStatus(200);
        $response->assertViewHas('categories');
    }

    public function test_update_category_status()
    {
        $category = Category::factory()->create(['status' => 1]);

        $response = $this->actingAsAdmin()->post('/admin/update-category-status', [
            'status' => 'Active',
            'category_id' => $category->id
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertJson([
            'status' => 0,
            'category_id' => $category->id
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'status' => 0
        ]);
    }

    public function test_add_category()
    {
        Storage::fake('public');
        $section = Section::factory()->create();

        $data = [
            'section_id' => $section->id,
            'parent_id' => 0,
            'category_name' => 'Thể thao',
            'category_discount' => '5',
            'description' => 'Mô tả',
            'url' => 'the-thao',
            'meta_title' => 'Meta title',
            'meta_description' => 'Meta desc',
            'meta_keywords' => 'key1,key2',
            'category_image' => UploadedFile::fake()->image('category.jpg')
        ];

        $response = $this->actingAsAdmin()->post('/admin/add-edit-category', $data);
        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('categories', ['category_name' => 'Thể thao']);
    }

    public function test_edit_category()
    {
        $category = Category::factory()->create([
            'category_name' => 'Cũ',
            'category_discount' => 0,
        ]);

        $data = [
            'section_id' => $category->section_id,
            'parent_id' => 0,
            'category_name' => 'Mới',
            'category_discount' => 10,
            'description' => 'Cập nhật',
            'url' => 'moi',
            'meta_title' => 'Apple',
            'meta_description' => 'Apple',
            'meta_keywords' => 'Apple',
        ];

        $response = $this->actingAsAdmin()->post('/admin/add-edit-category/' . $category->id, $data);
        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'category_name' => 'Mới']);
    }

    public function test_delete_category()
    {
        $category = Category::factory()->create();
        $response = $this->actingAsAdmin()->get('/admin/delete-category/' . $category->id);
        $response->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_delete_category_image()
    {
        // Đảm bảo thư mục tồn tại
        $path = public_path('front/images/category_images');
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        // Tạo file giả
        $filename = 'test.jpg';
        $filePath = $path . '/' . $filename;
        file_put_contents($filePath, 'dummy');

        // Tạo bản ghi category
        $category = Category::factory()->create(['category_image' => $filename]);

        // Gửi request xoá
        $response = $this->actingAsAdmin()->from('/admin/categories')->get('/admin/delete-category-image/' . $category->id);

        // Kiểm tra redirect về lại đúng trang ban đầu
        $response->assertRedirect('/admin/categories');
        $response->assertSessionHas('success_message', 'Xóa hình thành công!');

        clearstatcache(); // reset cache file_exists cache
        $this->assertFalse(file_exists($filePath), "File ảnh vẫn tồn tại sau khi xóa");

        // Kiểm tra DB cũng được cập nhật
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'category_image' => ''
        ]);
    }


    public function test_append_category_level_ajax()
    {
        $section = \App\Models\Section::factory()->create();
        $category = \App\Models\Category::factory()->create([
            'section_id' => $section->id,
            'parent_id' => 0,
            'category_name' => 'Thể thao'
        ]);

        $response = $this->actingAsAdmin()->get(
            '/admin/append-categories-level?section_id=' . $section->id,
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        $response->assertStatus(200);
        $response->assertSee('<option', false); // kiểm tra HTML có tag <option>
        $response->assertSee('Thể thao');
    }
}
