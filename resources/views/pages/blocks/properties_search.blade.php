@php
    $viewType = request('view', $block->data['view_type'] ?? 'grid');
    $itemsPerPage = $block->data['items_per_page'] ?? 12;
@endphp

<section class="py-20 px-4 bg-white">
    <div class="max-w-7xl mx-auto">
        @if($block->data['title'] ?? null)
            <div class="mb-12">
                <h1 class="text-4xl font-bold text-gray-900 font-playfair mb-4">
                    {{ $block->data['title'] }}
                </h1>
            </div>
        @endif

        <!-- Search Filter -->
        <div class="mb-8 p-6 bg-gray-50 rounded-lg">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Buscar propiedad..." 
                        value="{{ request('search') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    />
                    
                    <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todos los tipos</option>
                        @foreach(\App\Models\PropertyType::all() as $type)
                            <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>

                    <input 
                        type="number" 
                        name="min_price" 
                        placeholder="Precio mín." 
                        value="{{ request('min_price') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    />

                    <input 
                        type="number" 
                        name="max_price" 
                        placeholder="Precio máx." 
                        value="{{ request('max_price') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    />
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                        Buscar
                    </button>
                    <a href="{{ url()->current() }}" class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg font-medium transition">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        <!-- View Toggle (solo si hay buscador) -->
        <div class="flex items-center justify-between mb-8">
            <p class="text-gray-600">
                Mostrando propiedades
            </p>
            <div class="flex gap-2">
                <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}" class="p-2 {{ $viewType === 'list' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded-lg">
                    <i class="fas fa-list"></i>
                </a>
                <a href="{{ request()->fullUrlWithQuery(['view' => 'cards']) }}" class="p-2 {{ $viewType === 'cards' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded-lg">
                    <i class="fas fa-th"></i>
                </a>
                <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}" class="p-2 {{ $viewType === 'grid' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded-lg">
                    <i class="fas fa-th-large"></i>
                </a>
            </div>
        </div>

        <!-- Properties Display -->
        @php
            $properties = \App\Support\PublicProperties::search(request()->only(['search', 'type', 'min_price', 'max_price']), (int) $itemsPerPage);
        @endphp

        @if($properties->count() > 0)
            @if($viewType === 'list')
                <!-- List View -->
                <div class="space-y-4">
                    @foreach($properties as $property)
                        <a href="{{ $property->publicUrl() }}" class="flex gap-4 bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition">
                            <div class="w-48 h-40 flex-shrink-0 bg-gray-200">
                                @if($property->mainImageThumbUrl())
                                    <img src="{{ $property->mainImageThumbUrl() }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
                                @endif
                            </div>

                            <div class="flex-1 p-6 flex flex-col justify-between">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $property->title }}</h3>
                                    <p class="text-gray-600 mb-3">
                                        <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                                        {{ $property->locality }}, {{ $property->country }}
                                    </p>
                                    <p class="text-gray-700">{{ \Illuminate\Support\Str::limit($property->description, 100) }}</p>
                                </div>
                                
                                <div class="flex justify-between items-center mt-4">
                                    <div class="flex gap-6 text-gray-600 text-sm">
                                        <span><i class="fas fa-door-open mr-1 text-blue-600"></i> {{ $property->rooms }}</span>
                                        <span><i class="fas fa-bath mr-1 text-blue-600"></i> {{ $property->bathrooms }}</span>
                                        <span><i class="fas fa-ruler-combined mr-1 text-blue-600"></i> {{ $property->surface }}m²</span>
                                    </div>
                                    <span class="text-2xl font-bold text-blue-600">{{ number_format($property->price, 0, ',', '.') }}€</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

            @elseif($viewType === 'cards')
                <!-- Cards View -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($properties as $property)
                        <a href="{{ $property->publicUrl() }}" class="group bg-white rounded-lg shadow-md hover:shadow-xl overflow-hidden transition">
                            <div class="h-64 bg-gray-200 overflow-hidden relative">
                                @if($property->mainImageThumbUrl())
                                    <img src="{{ $property->mainImageThumbUrl() }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                @endif
                                <div class="absolute top-4 right-4 bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                    {{ number_format($property->price, 0, ',', '.') }}€
                                </div>
                            </div>

                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition">{{ $property->title }}</h3>
                                <p class="text-gray-600 text-sm mb-4">
                                    <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                                    {{ $property->locality }}, {{ $property->country }}
                                </p>
                                <div class="flex justify-between text-gray-600 text-sm">
                                    <span><i class="fas fa-door-open mr-1 text-blue-600"></i> {{ $property->rooms }}</span>
                                    <span><i class="fas fa-bath mr-1 text-blue-600"></i> {{ $property->bathrooms }}</span>
                                    <span><i class="fas fa-ruler-combined mr-1 text-blue-600"></i> {{ $property->surface }}m²</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

            @else
                <!-- Grid View (default) -->
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($properties as $property)
                        <a href="{{ $property->publicUrl() }}" class="group overflow-hidden rounded-lg shadow-md hover:shadow-xl transition">
                            <div class="h-48 bg-gray-200 overflow-hidden relative">
                                @if($property->mainImageThumbUrl())
                                    <img src="{{ $property->mainImageThumbUrl() }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                @endif
                                <div class="absolute top-3 right-3 bg-blue-600 text-white px-2 py-1 rounded text-xs font-semibold">
                                    {{ number_format($property->price, 0, ',', '.') }}€
                                </div>
                            </div>

                            <div class="p-4">
                                <h3 class="text-sm font-bold text-gray-900 mb-1 group-hover:text-blue-600 transition line-clamp-2">{{ $property->title }}</h3>
                                <p class="text-gray-600 text-xs mb-3">
                                    <i class="fas fa-map-marker-alt mr-1 text-blue-600"></i>
                                    {{ $property->locality }}
                                </p>
                                <div class="flex justify-between text-gray-600 text-xs">
                                    <span><i class="fas fa-door-open mr-1 text-blue-600"></i> {{ $property->rooms }}</span>
                                    <span><i class="fas fa-bath mr-1 text-blue-600"></i> {{ $property->bathrooms }}</span>
                                    <span><i class="fas fa-expand mr-1 text-blue-600"></i> {{ $property->surface }}m²</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            <!-- Pagination -->
            <div class="mt-12">
                {{ $properties->links() }}
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-lg">
                <p class="text-gray-500">No se encontraron propiedades</p>
            </div>
        @endif
    </div>
</section>
