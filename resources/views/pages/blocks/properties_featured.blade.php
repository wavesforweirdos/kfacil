@php
    $limit = $block->data['limit'] ?? 6;
    $columns = match($block->data['columns'] ?? 3) {
        2 => 'grid-cols-1 md:grid-cols-2',
        3 => 'grid-cols-1 md:grid-cols-3',
        4 => 'grid-cols-1 md:grid-cols-4',
        default => 'grid-cols-1 md:grid-cols-3'
    };
    
    $properties = \App\Models\Property::query()
        ->latest()
        ->limit($limit)
        ->get();
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

        @if($properties->count() > 0)
            <div class="grid {{ $columns }} gap-8">
                @foreach($properties as $property)
                    <a href="{{ route('public.properties.show', $property) }}" class="group overflow-hidden rounded-lg shadow-lg hover:shadow-xl transition">
                        <div class="relative overflow-hidden bg-gray-200 h-64">
                            @if($property->mainImageUrl())
                                <img 
                                    src="{{ $property->mainImageUrl() }}" 
                                    alt="{{ $property->title }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition duration-300"
                                />
                            @endif
                            
                            <div class="absolute top-4 right-4 bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                {{ number_format($property->price, 0, ',', '.') }}€
                            </div>
                        </div>

                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition">
                                {{ $property->title }}
                            </h3>

                            <p class="text-gray-600 text-sm mb-4">
                                <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                                {{ $property->locality }}, {{ $property->country }}
                            </p>

                            <div class="flex justify-between text-gray-600 text-sm">
                                <span><i class="fas fa-door-open mr-1 text-blue-600"></i> {{ $property->rooms }} Hab.</span>
                                <span><i class="fas fa-bath mr-1 text-blue-600"></i> {{ $property->bathrooms }} Baños</span>
                                <span><i class="fas fa-ruler-combined mr-1 text-blue-600"></i> {{ $property->surface }} m²</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a 
                    href="{{ route('public.properties.index') }}"
                    class="inline-block px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition"
                >
                    Ver todas las propiedades
                </a>
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-lg">
                <p class="text-gray-500">No hay propiedades destacadas disponibles</p>
            </div>
        @endif
    </div>
</section>
