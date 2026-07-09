@php
    $layout = data_get($navbarSettings ?? [], 'layout', 'horizontal');
    $brand = data_get($navbarSettings ?? [], 'brand', 'KFACIL');
    $logo = data_get($navbarSettings ?? [], 'logo');
    $backgroundColor = data_get($navbarSettings ?? [], 'background_color', '#0f172a');
    $textColor = data_get($navbarSettings ?? [], 'text_color', '#ffffff');
    $showCta = (bool) data_get($navbarSettings ?? [], 'show_cta', false);
    $ctaText = data_get($navbarSettings ?? [], 'cta_text', 'Contactar');
    $ctaUrl = data_get($navbarSettings ?? [], 'cta_url', '/contacto');
    $links = collect(data_get($navbarSettings ?? [], 'links', []));

    if ($links->isEmpty()) {
        $links = collect([
            ['label' => 'Inicio', 'url' => '/inicio'],
            ['label' => 'Servicios', 'url' => '/servicios'],
            ['label' => 'Propiedades', 'url' => '/inmuebles'],
            ['label' => 'Contacto', 'url' => '/contacto'],
        ]);
    }
@endphp

@if($layout === 'lateral')
    <style>
        .global-side-nav {
            width: 250px;
            background: {{ $backgroundColor }};
            color: {{ $textColor }};
        }

        body.has-global-side-nav {
            padding-left: 250px;
        }

        @media (max-width: 1024px) {
            .global-side-nav {
                transform: translateX(-100%);
                transition: transform 0.25s ease;
            }

            .global-side-nav.open {
                transform: translateX(0);
            }

            body.has-global-side-nav {
                padding-left: 0;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.body.classList.add('has-global-side-nav');
        });
    </script>

    <div x-data="{ open: false }" class="relative z-50">
        <button
            type="button"
            @click="open = !open"
            class="fixed top-4 left-4 lg:hidden rounded-full px-3 py-2 text-sm font-semibold shadow"
            style="background: {{ $backgroundColor }}; color: {{ $textColor }}"
        >
            <i class="fa-solid fa-bars mr-1"></i> Menu
        </button>

        <aside :class="open ? 'open' : ''" class="global-side-nav fixed top-0 left-0 h-screen p-6 lg:translate-x-0">
            <div class="flex items-center gap-3 mb-6">
                @if($logo)
                    <img src="{{ $logo }}" alt="{{ $brand }}" class="h-9 w-9 rounded object-cover">
                @endif
                <span class="text-lg font-semibold">{{ $brand }}</span>
            </div>

            <nav class="space-y-2">
                @foreach($links as $link)
                    <a href="{{ data_get($link, 'url', '#') }}" class="block rounded px-3 py-2 text-sm font-medium hover:bg-white/10">
                        {{ data_get($link, 'label', '') }}
                    </a>
                @endforeach
            </nav>

            @if($showCta)
                <a href="{{ $ctaUrl }}" class="mt-6 inline-flex rounded-lg bg-white px-4 py-2 text-sm font-semibold text-slate-900">
                    {{ $ctaText }}
                </a>
            @endif
        </aside>
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
                        <a href="{{ data_get($link, 'url', '#') }}" class="rounded px-3 py-2 text-sm font-medium hover:bg-white/10">
                            {{ data_get($link, 'label', '') }}
                        </a>
                    @endforeach
                </nav>

                <div class="flex items-center gap-2">
                    @if($showCta)
                        <a href="{{ $ctaUrl }}" class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-slate-900">
                            {{ $ctaText }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </header>
@endif
