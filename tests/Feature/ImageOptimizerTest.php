<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Support\ImageOptimizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageOptimizerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! function_exists('imagewebp')) {
            $this->markTestSkipped('GD sin soporte WebP');
        }

        Storage::fake('public');
    }

    private function widthOf(string $path): int
    {
        return getimagesizefromstring(Storage::disk('public')->get($path))[0];
    }

    public function test_a_large_photo_is_saved_as_a_smaller_webp_with_a_thumbnail(): void
    {
        $path = ImageOptimizer::store(UploadedFile::fake()->image('casa.jpg', 3200, 2000), 'properties');

        $this->assertStringEndsWith('.webp', $path);
        $this->assertSame(1600, $this->widthOf($path));
        $this->assertSame(ImageOptimizer::THUMB_WIDTH, $this->widthOf(ImageOptimizer::thumbPath($path)));
    }

    public function test_small_images_are_never_enlarged(): void
    {
        $path = ImageOptimizer::store(UploadedFile::fake()->image('pequena.png', 400, 300), 'properties');

        $this->assertSame(400, $this->widthOf($path));
        $this->assertSame(400, $this->widthOf(ImageOptimizer::thumbPath($path)));
    }

    public function test_floor_plans_can_keep_a_larger_width(): void
    {
        $path = ImageOptimizer::store(UploadedFile::fake()->image('plano.jpg', 3000, 2000), 'properties', 2000);

        $this->assertSame(2000, $this->widthOf($path));
    }

    public function test_delete_removes_the_image_and_its_thumbnail_and_ignores_remote_urls(): void
    {
        $path = ImageOptimizer::store(UploadedFile::fake()->image('a.jpg', 800, 600), 'properties');

        ImageOptimizer::delete($path);
        ImageOptimizer::delete('https://example.test/foto.jpg');
        ImageOptimizer::delete(null);

        Storage::disk('public')->assertMissing($path);
        Storage::disk('public')->assertMissing(ImageOptimizer::thumbPath($path));
    }

    public function test_duplicate_creates_an_independent_copy_with_its_own_thumbnail(): void
    {
        $path = ImageOptimizer::store(UploadedFile::fake()->image('a.jpg', 800, 600), 'properties');

        $copy = ImageOptimizer::duplicate($path);

        $this->assertNotSame($path, $copy);
        Storage::disk('public')->assertExists($copy);
        Storage::disk('public')->assertExists(ImageOptimizer::thumbPath($copy));

        ImageOptimizer::delete($path);
        Storage::disk('public')->assertExists($copy);
    }

    public function test_listings_use_the_thumbnail_when_it_exists_and_the_original_otherwise(): void
    {
        $optimized = ImageOptimizer::store(UploadedFile::fake()->image('a.jpg', 1200, 800), 'properties');
        $legacy = UploadedFile::fake()->image('vieja.jpg')->store('properties', 'public');

        $withThumb = Property::factory()->create(['main_image' => $optimized]);
        $withoutThumb = Property::factory()->create(['main_image' => $legacy]);

        $this->assertStringContainsString('/thumbs/', $withThumb->mainImageThumbUrl());
        $this->assertStringNotContainsString('/thumbs/', $withThumb->mainImageUrl());
        $this->assertSame($withoutThumb->mainImageUrl(), $withoutThumb->mainImageThumbUrl());
    }
}
