<?php

namespace App\Livewire\PageBuilder;

use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;

class PageEditor extends Component
{
    public Page $page;

    public array $blocks = [];

    public ?string $selectedBlockId = null;

    public int $previewVersion = 1;

    public string $previewDevice = 'desktop';

    public array $availableComponents = [
        'home' => [
            'hero' => 'Hero',
            'gallery' => 'Galería',
            'testimonials' => 'Testimonios',
            'cta' => 'Llamada a la acción',
            'properties_featured' => 'Propiedades destacadas',
        ],
        'servicios' => [
            'hero' => 'Hero',
            'services' => 'Servicios',
            'cta' => 'Llamada a la acción',
        ],
        'contacto' => [
            'hero' => 'Hero',
            'contact_form' => 'Formulario de contacto',
            'cta' => 'Llamada a la acción',
        ],
        'propiedades' => [
            'hero' => 'Hero',
            'properties_search' => 'Buscador de propiedades',
        ],
    ];

    public function mount(Page $page)
    {
        Gate::authorize('edit-page', $page);
        $this->page = $page;
        $this->loadBlocks();
    }

    public function loadBlocks()
    {
        $this->blocks = $this->page->blocks()
            ->get()
            ->map(fn ($block) => [
                'id' => $block->id,
                'component' => $block->component,
                'order' => $block->order,
                'data' => $block->data,
            ])
            ->toArray();
    }

    public function addBlock($component)
    {
        $maxOrder = PageBlock::where('page_id', $this->page->id)->max('order') ?? -1;

        $block = PageBlock::create([
            'page_id' => $this->page->id,
            'component' => $component,
            'order' => $maxOrder + 1,
            'data' => [],
        ]);

        $this->loadBlocks();
        $this->selectedBlockId = (string) $block->id;
        $this->refreshPreview();
        $this->dispatch('block-added', $block->id);
    }

    public function deleteBlock($blockId)
    {
        $block = PageBlock::find($blockId);

        if ($block && $block->page_id === $this->page->id) {
            $block->delete();
            $this->loadBlocks();
            $this->selectedBlockId = null;
            $this->refreshPreview();
        }
    }

    public function updateBlockOrder($blockIds)
    {
        DB::transaction(function () use ($blockIds) {
            foreach ($blockIds as $index => $blockId) {
                PageBlock::where('id', $blockId)
                    ->where('page_id', $this->page->id)
                    ->update(['order' => $index]);
            }
        });

        $this->loadBlocks();
        $this->refreshPreview();
    }

    public function updateBlockData($blockId, $data)
    {
        $block = PageBlock::find($blockId);

        if ($block && $block->page_id === $this->page->id) {
            $block->update(['data' => $data]);
            $this->loadBlocks();
            $this->refreshPreview();
            $this->dispatch('block-updated');
        }
    }

    public function updateQuickField($blockId, $field, $value)
    {
        $block = PageBlock::find($blockId);

        if (! $block || $block->page_id !== $this->page->id) {
            return;
        }

        $allowedFields = [
            'title',
            'subtitle',
            'description',
            'brand',
            'cta_text',
            'cta_url',
            'button_text',
            'button_url',
            'image',
        ];

        if (! in_array($field, $allowedFields, true)) {
            return;
        }

        $data = is_array($block->data) ? $block->data : [];
        $data[$field] = $value;

        $block->update(['data' => $data]);

        $this->loadBlocks();
        $this->refreshPreview();
    }

    public function updateBlockOption($blockId, $field, $value)
    {
        $block = PageBlock::find($blockId);

        if (! $block || $block->page_id !== $this->page->id) {
            return;
        }

        $schema = PageBlock::getComponentSchema($block->component);

        if (! array_key_exists($field, $schema)) {
            return;
        }

        if ($schema[$field] === 'array') {
            return;
        }

        $data = is_array($block->data) ? $block->data : [];

        if ($schema[$field] === 'boolean') {
            $data[$field] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        } else {
            $data[$field] = $value;
        }

        $block->update(['data' => $data]);

        $this->loadBlocks();
        $this->refreshPreview();
    }

    public function selectBlock($blockId)
    {
        $this->selectedBlockId = (string) $blockId;
    }

    public function duplicateBlock($blockId)
    {
        $original = PageBlock::find($blockId);

        if ($original && $original->page_id === $this->page->id) {
            $maxOrder = PageBlock::where('page_id', $this->page->id)->max('order') ?? -1;

            PageBlock::create([
                'page_id' => $this->page->id,
                'component' => $original->component,
                'order' => $maxOrder + 1,
                'data' => $original->data,
            ]);

            $this->loadBlocks();
            $this->refreshPreview();
            $this->dispatch('block-duplicated');
        }
    }

    #[On('block-saved')]
    public function handleBlockSaved()
    {
        $this->loadBlocks();
        $this->refreshPreview();
    }

    public function refreshPreview()
    {
        $this->previewVersion++;
    }

    public function setPreviewDevice(string $device)
    {
        if (! in_array($device, ['desktop', 'tablet', 'mobile'], true)) {
            return;
        }

        $this->previewDevice = $device;
    }

    public function render()
    {
        return view('livewire.page-builder.page-editor', [
            'pageComponents' => $this->availableComponents[$this->page->type] ?? [],
        ]);
    }
}
