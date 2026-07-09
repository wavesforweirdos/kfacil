@php
    $columns = match($block->data['columns'] ?? 3) {
        2 => 'grid-cols-1 md:grid-cols-2',
        3 => 'grid-cols-1 md:grid-cols-3',
        4 => 'grid-cols-1 md:grid-cols-4',
        default => 'grid-cols-1 md:grid-cols-3'
    };
    
    $images = is_array($block->data['images'] ?? []) ? $block->data['images'] : [];
@endphp

<section class="py-20 px-4 bg-white">
    <div class="max-w-7xl mx-auto">
        @if($block->data['title'] ?? null)
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 font-playfair">
                    {{ $block->data['title'] }}
                </h2>
            </div>
        @endif

        @if(count($images) > 0)
            <div class="grid {{ $columns }} gap-6">
                @foreach($images as $image)
                    <div class="group overflow-hidden rounded-lg shadow-lg hover:shadow-xl transition">
                        <img 
                            src="{{ $image }}" 
                            alt="Galería"
                            class="w-full h-64 object-cover group-hover:scale-110 transition duration-300"
                        />
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-lg">
                <p class="text-gray-500">No hay imágenes configuradas para esta galería</p>
            </div>
        @endif
    </div>
</section>
