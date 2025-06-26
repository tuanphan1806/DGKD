<?php

namespace Tests\Feature\Admin;

use App\Models\Banner;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class BannersControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure public directory for banner images exists
        $path = public_path('front/images/banner_images');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        chmod($path, 0777);
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

    public function test_banners_view_displays_list()
    {
        Banner::factory()->count(3)->create();
        $response = $this->actingAsAdmin()->get('/admin/banners');
        $response->assertStatus(200);
        $response->assertViewHas('banners');
        $this->assertCount(3, $response->viewData('banners'));
    }

    public function test_update_banner_status_ajax_toggles_status()
    {
        $banner = Banner::factory()->create(['status' => 1]);
        $response = $this->actingAsAdmin()
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->postJson('/admin/update-banner-status', [
                'status' => 'Active',
                'banner_id' => $banner->id
            ]);

        $response->assertJson(['status' => 0, 'banner_id' => $banner->id]);
        $this->assertEquals(0, $banner->fresh()->status);
    }

    public function test_delete_banner_removes_file_and_record()
    {
        $path = public_path('front/images/banner_images');
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        // Tạo file giả
        $filename = 'test.jpg';
        $filePath = $path . '/' . $filename;
        file_put_contents($filePath, 'dummy');

        // Đảm bảo file thực sự được tạo
        $this->assertTrue(file_exists($filePath), "File ảnh không được tạo");

        // Tạo banner gắn với ảnh
        $banner = Banner::factory()->create(['image' => $filename]);

        // Gửi request xóa
        $response = $this->actingAsAdmin()->get('/admin/delete-banner/' . $banner->id);
        $response->assertRedirect('/admin/banners');
        $response->assertSessionHas('success_message');

        // Kiểm tra file đã bị xoá và DB không còn bản ghi
        clearstatcache(); // reset cache file_exists
        $this->assertFalse(file_exists($filePath), "File ảnh vẫn tồn tại sau khi xóa");
        $this->assertDatabaseMissing('banners', ['id' => $banner->id]);
    }


    public function test_add_new_banner_with_image()
    {
        $response = $this->actingAsAdmin()->get('/admin/add-edit-banner');
        $response->assertStatus(200);
        $response->assertViewHasAll(['title', 'banner']);

        $image = UploadedFile::fake()->image('banner.jpg');
        $data = [
            'type' => 'Slider',
            'link' => 'http://example.com',
            'title' => 'New Banner',
            'alt' => 'Alt text',
            'image' => $image
        ];
        $post = $this->actingAsAdmin()->post('/admin/add-edit-banner', $data);
        $post->assertRedirect('/admin/banners');
        $post->assertSessionHas('success_message');

        $this->assertDatabaseHas('banners', [
            'title' => 'New Banner',
            'type' => 'Slider'
        ]);
    }

    public function test_edit_existing_banner_updates_fields_and_image()
    {
        $banner = Banner::factory()->create([
            'type' => 'Fix',
            'link' => 'http://old.com',
            'title' => 'Old Title',
            'alt' => 'Old alt',
            'status' => 1,
        ]);

        $response = $this->actingAsAdmin()->get('/admin/add-edit-banner/' . $banner->id);
        $response->assertStatus(200);
        $response->assertViewHasAll(['title', 'banner']);

        $image = UploadedFile::fake()->image('new.jpg');
        $data = [
            'type' => 'Fix',
            'link' => 'http://new.com',
            'title' => 'Updated Title',
            'alt' => 'Updated alt',
            'image' => $image
        ];
        $post = $this->actingAsAdmin()->post('/admin/add-edit-banner/' . $banner->id, $data);
        $post->assertRedirect('/admin/banners');
        $post->assertSessionHas('success_message');

        $this->assertDatabaseHas('banners', [
            'id' => $banner->id,
            'link' => 'http://new.com',
            'title' => 'Updated Title'
        ]);
    }
}
