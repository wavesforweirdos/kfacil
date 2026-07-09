@php
    $layout = data_get($block->data, 'layout', 'horizontal');
    $brand = data_get($block->data, 'brand', 'KFACIL');
    $logo = data_get($block->data, 'logo');
    $backgroundColor = data_get($block->data, 'background_color', '#0f172a');
    $textColor = data_get($block->data, 'text_color', '#ffffff');
    $showCta = (bool) data_get($block->data, 'show_cta', false);
    $ctaText = data_get($block->data, 'cta_text', 'Contactar');
    $ctaUrl = data_get($block->data, 'cta_url', '/contacto');

    $rawLinks = data_get($block->data, 'links', [
        ['label' => 'Inicio', 'url' => '/inicio'],
        ['label' => 'Servicios', 'url' => '/servicios'],
        ['label' => 'Propiedades', 'url' => '/inmuebles'],
        ['label' => 'Contacto', 'url' => '/contacto'],
    ]);

    if (is_string($rawLinks)) {
        $decoded = json_decode($rawLinks, true);
        $rawLinks = is_array($decoded) ? $decoded : [];
    }

    $links = collect(is_array($rawLinks) ? $rawLinks : [])
        ->map(function ($item) {
            return [
                'label' => data_get($item, 'label', ''),
                'url' => data_get($item, 'url', '#'),
            ];
        })
        ->filter(fn ($item) => filled($item['label']))
        ->values();

    $blockKey = 'navbar-' . $block->id;
@endphp

@if($layout === 'lateral')
    <style>
        .{{ $blockKey }}-side {
            width: 260px;
            background: {{ $backgroundColor }};
            color: {{ $textColor }};
        }

        .{{ $blockKey }}-content {
            margin-left: 260px;
        }

        @media (max-width: 1024px) {
            .{{ $blockKey }}-side {
                transform: translateX(-100%);
                transition: transform 0.25s ease;
            }

            .{{ $blockKey }}-side.open {
                transform: translateX(0);
            }

            .{{ $blockKey }}-content {
                margin-left: 0;
            }
        }
    </style>

    <div x-data="{ open: false }" class="relative z-40">
        <button
            type="button"
            @click="open = !open"
            class="fixed top-4 left-4 lg:hidden rounded-full px-3 py-2 text-sm font-semibold shadow"
            style="background: {{ $backgroundColor }}; color: {{ $textColor }}"
        >
            <i class="fa-solid fa-bars mr-1"></i> Menu
        </button>

        <aside
            :class="open ? 'open' : ''"
            class="{{ $blockKey }}-side fixed top-0 left-0 h-screen p-6 lg:translate-x-0"
        >
            <div class="flex items-center gap-3 mb-6">
                @if($logo)
                    <img src="{{ $logo }}" alt="{{ $brand }}" class="h-9 w-9 rounded object-cover">
                @endif
                <span class="text-lg font-semibold">{{ $brand }}</span>
            </div>

            <nav class="space-y-2">
                @foreach($links as $link)
                    <a href="{{ $link['url'] }}" class="block rounded px-3 py-2 text-sm font-medium hover:bg-white/10">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>

            @if($showCta)
                <a href="{{ $ctaUrl }}" class="mt-6 inline-flex rounded-lg bg-white px-4 py-2 text-sm font-semibold text-slate-900">
                    {{ $ctaText }}
                </a>
            @endif
        </aside>

        <div class="{{ $blockKey }}-content min-h-[96px]"></div>
    </div>
@else
    <header class="w-full" style="background: {{ $backgroundColor }}; color: {{ $textColor }};">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    @if($logo)
                        <img src="{{ $logo }}" alt="{{ $brand }}" class="h-9 w-9 rounded object-cover">
                    @endif
                    <span class="text-lg font-semibold tracking-wide">{{ $brand }}</span>
                </div>

                <nav class="hidden md:flex items-center gap-2">
                    @foreach($links as $link)
                        <a href="{{ $link['url'] }}" class="rounded px-3 py-2 text-sm font-medium hover:bg-white/10">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="flex items-center gap-2">
                    @if($showCta)
                        <a href="{{ $ctaUrl }}" class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-slate-900">
                            {{ $ctaText }}
                        </a>
                    @endif
                    <button class="md:hidden rounded px-3 py-2 text-sm font-semibold border border-white/30">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>
@endif
