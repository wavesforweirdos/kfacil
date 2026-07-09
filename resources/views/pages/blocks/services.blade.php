@php
    $columns = match($block->data['columns'] ?? 3) {
        2 => 'grid-cols-1 md:grid-cols-2',
        3 => 'grid-cols-1 md:grid-cols-3',
        4 => 'grid-cols-1 md:grid-cols-4',
        default => 'grid-cols-1 md:grid-cols-3'
    };
    
    $services = is_array($block->data['services'] ?? []) ? $block->data['services'] : [];
@endphp

<section class="py-20 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        @if($block->data['title'] ?? null)
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 font-playfair">
                    {{ $block->data['title'] }}
                </h2>
            </div>
        @endif

        @if(count($services) > 0)
            <div class="grid {{ $columns }} gap-8">
                @foreach($services as $service)
                    <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-xl transition">
                        @if($service['icon'] ?? null)
                            <div class="text-4xl text-blue-600 mb-4">
                                {{ $service['icon'] }}
                            </div>
                        @endif

                        <h3 class="text-xl font-bold text-gray-900 mb-3">
                            {{ $service['title'] ?? 'Servicio' }}
                        </h3>

                        <p class="text-gray-600 mb-4">
                            {{ $service['description'] ?? '' }}
                        </p>

                        @if($service['url'] ?? null)
                            <a 
                                href="{{ $service['url'] }}"
                                class="text-blue-600 font-semibold hover:text-blue-800 inline-flex items-center gap-2"
                            >
                                Más info <i class="fas fa-arrow-right text-sm"></i>
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-lg">
                <p class="text-gray-500">No hay servicios configurados</p>
            </div>
        @endif
    </div>
</section>
