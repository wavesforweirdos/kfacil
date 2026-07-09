@php
    $height = match($block->data['height'] ?? 'lg') {
        'sm' => 'min-h-64',
        'md' => 'min-h-96',
        'lg' => 'min-h-screen',
        'xl' => 'min-h-screen',
        default => 'min-h-screen'
    };
    $bgImage = $block->data['image'] ?? null;
@endphp

<section class="{{ $height }} relative flex items-center justify-center overflow-hidden bg-gradient-to-r from-blue-600 to-blue-800">
    @if($bgImage)
        <img 
            src="{{ $bgImage }}" 
            alt="Hero" 
            class="absolute inset-0 w-full h-full object-cover opacity-40"
        />
    @endif

    <div class="absolute inset-0 bg-gradient-to-r from-blue-600/90 to-blue-800/90"></div>

    <div class="relative z-10 text-center text-white px-4 max-w-4xl">
        @if($block->data['title'] ?? null)
            <h1 class="text-5xl md:text-6xl font-bold mb-6 font-playfair leading-tight">
                {{ $block->data['title'] }}
            </h1>
        @endif

        @if($block->data['subtitle'] ?? null)
            <p class="text-xl md:text-2xl mb-8 text-blue-100">
                {{ $block->data['subtitle'] }}
            </p>
        @endif

        @if(($block->data['cta_text'] ?? null) && ($block->data['cta_url'] ?? null))
            <a 
                href="{{ $block->data['cta_url'] }}"
                class="inline-block px-8 py-3 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-lg transition transform hover:scale-105"
            >
                {{ $block->data['cta_text'] }}
            </a>
        @endif
    </div>

    <!-- Scroll indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20 animate-bounce">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
        </svg>
    </div>
</section>
