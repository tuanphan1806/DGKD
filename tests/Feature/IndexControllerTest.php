<?php

namespace Tests\Feature\Front;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Banner;
use App\Models\Product;

class IndexControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */

    /** @test */
    public function it_does_not_load_inactive_banners_or_products()
    {
        // Create inactive data
        Banner::factory()->create(['type' => 'Slider', 'status' => 0]);
        Product::factory()->create(['status' => 0]);

        $response = $this->get('/');
        $response->assertStatus(200);

        // Assert that inactive banners and products are not included in the response   
        $response->assertViewHas('sliderBanners', function ($banners) {
            return empty($banners);
        });

        $response->assertViewHas('newProducts', function ($products) {
            return empty($products);
        });
    }
    public function it_loads_active_banners_and_products()
    {
        // Create active data
        $activeBanner = Banner::factory()->create(['type' => 'Slider', 'status' => 1]);
        $activeProduct = Product::factory()->create(['status' => 1]);

        $response = $this->get('/');
        $response->assertStatus(200);

        // Assert that active banners and products are included in the response
        $response->assertViewHas('sliderBanners', function ($banners) use ($activeBanner) {
            return $banners->contains($activeBanner);
        });

        $response->assertViewHas('newProducts', function ($products) use ($activeProduct) {
            return $products->contains($activeProduct);
        });
    }
    public function it_loads_banners_and_products_with_correct_types()
    {
        // Create banners and products with different types
        $sliderBanner = Banner::factory()->create(['type' => 'Slider', 'status' => 1]);
        $categoryBanner = Banner::factory()->create(['type' => 'Category', 'status' => 1]);
        $newProduct = Product::factory()->create(['status' => 1]);

        $response = $this->get('/');
        $response->assertStatus(200);

        // Assert that the correct types are loaded
        $response->assertViewHas('sliderBanners', function ($banners) use ($sliderBanner) {
            return $banners->contains($sliderBanner);
        });

        $response->assertViewHas('categoryBanners', function ($banners) use ($categoryBanner) {
            return $banners->contains($categoryBanner);
        });

        $response->assertViewHas('newProducts', function ($products) use ($newProduct) {
            return $products->contains($newProduct);
        });
    }
}
