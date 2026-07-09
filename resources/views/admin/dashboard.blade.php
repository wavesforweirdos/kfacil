<x-admin-layout>

    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-bold text-dark">
                {{ __('admin.dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-4 bg-light min-vh-100">
        <div class="container-fluid px-4">

            <!-- Grid -->
            <div class="row g-4">

                @php
                    $cards = [
                        [
                            'comment' => 'USERS',
                            'route' => auth()->user()->panelPrefix() . '.users.index',
                            'icon' => 'fa-users',
                            'color' => 'teal',
                            'title' => __('admin.user_management'),
                            'description' => __('admin.user_management_description'),
                            'roles' => ['admin'],
                        ],
                        [
                            'comment' => 'PROPERTIES',
                            'route' => auth()->user()->panelPrefix() . '.properties.index',
                            'icon' => 'fa-building',
                            'color' => 'adobe',
                            'title' => __('admin.property_management'),
                            'description' => __('admin.property_management_description'),
                            'roles' => ['admin', 'agente'],
                        ],
                        [
                            'comment' => 'CONTENT',
                            'route' => 'pages.index',
                            'icon' => 'fa-file-lines',
                            'color' => 'blue',
                            'title' => __('admin.content_management'),
                            'description' => __('admin.content_management_description'),
                            'roles' => ['admin', 'agente'],
                        ],
                    ];
                @endphp

                @foreach ($cards as $card)
                    @if (in_array(auth()->user()->role->name, $card['roles']))

                        <div class="col-12 col-sm-6 col-xl-4">

                                     <a href="{{ route($card['route']) }}"
                               class="text-decoration-none">

                                <div class="card border-0 shadow-sm dashboard-card h-100">
                                    <div class="card-body p-0 d-flex">

                                        <!-- Franja color -->
                                        <div class="dashboard-card__stripe bg-{{ $card['color'] }}-soft d-flex align-items-center justify-content-center">
                                            <div class="icon-box icon-box--lg text-{{ $card['color'] }}">
                                                <i class="fa-solid {{ $card['icon'] }}"></i>
                                            </div>
                                        </div>

                                        <!-- Contenido -->
                                        <div class="p-4 d-flex flex-column justify-content-center flex-grow-1">
                                            <h3 class="h6 fw-semibold mb-1" style="color: #1a1a2e;">
                                                {{ $card['title'] }}
                                            </h3>
                                            <p class="small text-muted mb-0 lh-sm">
                                                {{ $card['description'] }}
                                            </p>
                                        </div>

                                        <!-- Arrow -->
                                        <div class="d-flex align-items-center pe-3">
                                            <i class="fa-solid fa-chevron-right text-muted opacity-50 small"></i>
                                        </div>

                                    </div>
                                </div>

                            </a>

                        </div>

                    @endif
                @endforeach

            </div>

        </div>
    </div>

</x-admin-layout>
