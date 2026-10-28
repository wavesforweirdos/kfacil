<?php

namespace Tests\Feature;

use App\Livewire\PageBuilder\BlockEditor;
use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class BlockEditorUploadTest extends TestCase
{
    use RefreshDatabase;

    private function block(): PageBlock
    {
        return PageBlock::create(['page_id' => Page::factory()->create()->id, 'component' => 'hero', 'order' => 1, 'data' => []]);
    }

    public function test_an_image_is_stored_and_its_url_saved_in_the_block(): void
    {
        Storage::fake('public');
        $block = $this->block();

        Livewire::test(BlockEditor::class, ['block' => $block])
            ->set('imageUploads.background', UploadedFile::fake()->image('fondo.jpg'))
            ->assertHasNoErrors();

        $this->assertNotEmpty($block->fresh()->data['background'] ?? null);
        $this->assertCount(1, Storage::disk('public')->files('page-builder'));
    }

    public function test_files_that_are_not_images_are_rejected_and_not_stored(): void
    {
        Storage::fake('public');
        $block = $this->block();

        foreach (['shell.php', 'pagina.html', 'dibujo.svg'] as $name) {
            Livewire::test(BlockEditor::class, ['block' => $block])
                ->set('imageUploads.background', UploadedFile::fake()->create($name, 5, $name === 'dibujo.svg' ? 'image/svg+xml' : 'text/plain'))
                ->assertHasErrors('imageUploads.background');
        }

        $this->assertSame([], Storage::disk('public')->files('page-builder'));
        $this->assertArrayNotHasKey('background', $block->fresh()->data);
    }
}
