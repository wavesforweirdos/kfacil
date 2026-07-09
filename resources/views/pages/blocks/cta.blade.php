@php
    $bgColor = $block->data['background_color'] ?? '#2e6fa3';
@endphp

<section class="py-20 px-4" style="background-color: {{ $bgColor }};">
    <div class="max-w-4xl mx-auto text-center text-white">
        @if($block->data['title'] ?? null)
            <h2 class="text-4xl md:text-5xl font-bold mb-6 font-playfair">
                {{ $block->data['title'] }}
            </h2>
        @endif

        @if($block->data['description'] ?? null)
            <p class="text-xl mb-8 text-blue-100">
                {{ $block->data['description'] }}
            </p>
        @endif

        @if(($block->data['button_text'] ?? null) && ($block->data['button_url'] ?? null))
            <a 
                href="{{ $block->data['button_url'] }}"
                class="inline-block px-10 py-4 bg-white text-blue-600 font-bold rounded-lg hover:bg-gray-100 transition transform hover:scale-105"
            >
                {{ $block->data['button_text'] }}
            </a>
        @endif
    </div>
</section>
