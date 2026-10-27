@php
    $menuItems = [
        [
            'label' => __('admin_sidebar.properties'),
            'route' => route(auth()->user()->panelPrefix() . '.properties.index'),
            'icon' => 'fa-building',
            'color' => 'adobe',
        ],
        [
            'label' => __('admin_sidebar.contents'),
            'route' => route('pages.index'),
            'icon' => 'fa-file-lines',
            'color' => 'blue',
        ],
    ];

    // Solo los administradores gestionan usuarios.
    if (auth()->user()->isAdmin()) {
        array_unshift($menuItems, [
            'label' => __('admin_sidebar.users'),
            'route' => route('admin.users.index'),
            'icon' => 'fa-users',
            'color' => 'teal',
        ]);
    }
@endphp

<aside class="bg-light border-end min-vh-100 p-4" style="width: 260px;">

    <!-- Logo -->
    <div class="mb-4">
        <a href="{{ url('/' . auth()->user()->panelPrefix()) }}" class="d-flex align-items-center text-decoration-none gap-3">
            <img src="{{ asset('images/logo_color.png') }}" alt="Admin Logo" style="height:40px;">
            <h2 class="h6 mb-0 fw-bold text-dark text-uppercase">
                {{ config('app.name', 'KFacil Inmobiliaria') }}
            </h2>
        </a>
    </div>

    <div class="d-flex flex-column gap-3">

        @foreach ($menuItems as $item)
            <div class="menu-item-group">
                @if ($item['label'] == __('admin_sidebar.properties') && in_array(auth()->user()->role->name, ['admin', 'agente']))
                    @php
                        $propertyDropdownId = 'property-subcards-' . $loop->index;
                    @endphp

                    <x-admin.sidebar-card :route="$item['route']" :icon="$item['icon']" :label="$item['label']"
                        :color="$item['color']" :dropdown="true" :dropdown-id="$propertyDropdownId" />

                    <div id="{{ $propertyDropdownId }}" class="collapse mt-2">
                        <div class="d-flex flex-column gap-2">
                            @can('manage_property_types')
                                <x-admin.sidebar-card :route="route('property-types.index')" icon="fa-tag"
                                    :label="__('admin.property_types') ?? 'Tipos de Propiedad'" color="adobe"
                                    :small="true" />
                            @endcan

                            @can('manage_property_operations')
                                <x-admin.sidebar-card :route="route('property-operations.index')" icon="fa-handshake"
                                    :label="__('admin.property_operations') ?? 'Operaciones'" color="adobe"
                                    :small="true" />
                            @endcan

                            @can('manage_property_statuses')
                                <x-admin.sidebar-card :route="route('property-statuses.index')" icon="fa-circle-dot"
                                    :label="__('admin.property_statuses') ?? 'Estados'" color="adobe"
                                    :small="true" />
                            @endcan
                        </div>
                    </div>
                @else
                    <x-admin.sidebar-card :route="$item['route']" :icon="$item['icon']" :label="$item['label']"
                        :color="$item['color']" />
                @endif
            </div>
        @endforeach

    </div>

</aside>
