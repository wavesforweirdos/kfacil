@php
    $testimonials = is_array($block->data['testimonials'] ?? []) ? $block->data['testimonials'] : [];
@endphp

<section class="py-20 px-4 bg-gradient-to-br from-blue-50 to-blue-100">
    <div class="max-w-7xl mx-auto">
        @if($block->data['title'] ?? null)
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 font-playfair">
                    {{ $block->data['title'] }}
                </h2>
            </div>
        @endif

        @if(count($testimonials) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($testimonials as $testimonial)
                    <div class="bg-white p-8 rounded-lg shadow-lg">
                        <div class="flex items-center mb-4">
                            @for($i = 0; $i < 5; $i++)
                                <span class="text-amber-400">
                                    @if($i < ($testimonial['rating'] ?? 5))
                                        ★
                                    @else
                                        ☆
                                    @endif
                                </span>
                            @endfor
                        </div>
                        
                        <p class="text-gray-700 mb-6 italic">
                            "{{ $testimonial['text'] ?? '' }}"
                        </p>

                        <div class="flex items-center">
                            @if($testimonial['avatar'] ?? null)
                                <img 
                                    src="{{ $testimonial['avatar'] }}" 
                                    alt="{{ $testimonial['author'] ?? 'Cliente' }}"
                                    class="w-12 h-12 rounded-full mr-4 object-cover"
                                />
                            @endif
                            <div>
                                <p class="font-semibold text-gray-900">
                                    {{ $testimonial['author'] ?? 'Cliente' }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    {{ $testimonial['role'] ?? '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-lg">
                <p class="text-gray-500">No hay testimonios configurados</p>
            </div>
        @endif
    </div>
</section>
