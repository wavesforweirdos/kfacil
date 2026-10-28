<?php

namespace App\Livewire\PageBuilder;

use App\Models\PageBlock;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;

class BlockEditor extends Component
{
    use WithFileUploads;

    public PageBlock $block;

    public array $data = [];

    public array $imageUploads = [];

    public ?string $lastSavedAt = null;

    public function mount(PageBlock $block)
    {
        $this->block = $block;
        $this->data = $block->data;
    }

    public function updateData($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function updatedData()
    {
        $this->persist();
    }

    public function updatedImageUploads($file, $field)
    {
        if (! $file) {
            return;
        }

        // Solo imágenes: sin SVG (puede llevar scripts) ni otros tipos de archivo.
        $validator = Validator::make(
            ['file' => $file],
            ['file' => ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120']],
            [
                'file.image' => 'El archivo tiene que ser una imagen.',
                'file.mimes' => 'Solo se admiten imágenes JPG, PNG, WEBP o GIF.',
                'file.max' => 'La imagen no puede pesar más de 5 MB.',
            ],
        );

        if ($validator->fails()) {
            unset($this->imageUploads[$field]);
            $file->delete();
            $this->addError("imageUploads.{$field}", $validator->errors()->first('file'));

            return;
        }

        $path = $file->store('page-builder', 'public');
        $this->data[$field] = Storage::url($path);

        $this->persist();
    }

    protected function persist()
    {
        $this->block->update(['data' => $this->data]);
        $this->lastSavedAt = now()->format('H:i:s');
        $this->dispatch('block-saved');
    }

    public function save()
    {
        $this->persist();
        session()->flash('success', 'Bloque actualizado correctamente');
    }

    public function render()
    {
        $schema = PageBlock::getComponentSchema($this->block->component);

        return view('livewire.page-builder.block-editor', [
            'schema' => $schema,
        ]);
    }
}
