<x-admin-layout>
    <x-slot name="header">
        {{ __('admin_properties.title') }}
    </x-slot>

    <div x-data="{
        open: false,
        mode: 'show',
        loading: false,
        property: {},
        map: null,

        async openDrawer(mode, propertyId = null) {
            this.mode = mode;
            this.property = {};
            this.open = true;

            if (propertyId) {
                this.loading = true;
                try {
                    const res = await fetch(`{{ url(auth()->user()->panelPrefix() . '/properties') }}/${propertyId}`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    this.property = await res.json();

                    // Inicializar mapa si tiene coordenadas
                    if (this.property.latitude && this.property.longitude) {
                        setTimeout(() => this.initMap(this.property.latitude, this.property.longitude, this.property.id, this.property.title), 500);
                    }
                } finally {
                    this.loading = false;
                }
            }
        },

        async initMap(lat, lng, propertyId, title) {
            // Esperar a que Leaflet esté cargado
            let attempts = 0;
            while (!window.L && attempts < 50) {
                await new Promise(r => setTimeout(r, 100));
                attempts++;
            }

            if (!window.L) {
                console.warn('Leaflet no se cargó');
                return false;
            }

            // Construir ID del mapa
            const mapId = 'property-map-' + propertyId;

            // Esperar a que el elemento exista en el DOM
            let mapAttempts = 0;
            let mapEl = document.getElementById(mapId);
            while (!mapEl && mapAttempts < 30) {
                await new Promise(r => setTimeout(r, 50));
                mapEl = document.getElementById(mapId);
                mapAttempts++;
            }

            if (!mapEl) {
                console.warn(`Elemento #${mapId} no encontrado en el DOM`);
                return false;
            }

            // Destruir mapa anterior si existe
            if (this.map) {
                this.map.remove();
                this.map = null;
            }

            try {
                // Crear nuevo mapa
                this.map = window.L.map(mapId, {
                    center: [lat, lng],
                    zoom: 16,
                    scrollWheelZoom: false
                });

                // Agregar tile layer
                window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(this.map);

                // Agregar marcador
                window.L.marker([lat, lng]).addTo(this.map)
                    .bindPopup(title);

                // Forzar redibujado del mapa
                setTimeout(() => this.map?.invalidateSize(), 200);

                console.log(`Mapa #${mapId} inicializado correctamente`);
            } catch (e) {
                console.error(`Error al inicializar mapa #${mapId}:`, e);
                return false;
            }
        },

        init() {
            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') this.open = false;
            });

            @if($errors->any())
            this.open = true;
            this.mode = @js(old('_drawer_mode', 'create'));
            @endif

            // Cargar Leaflet
            if (!window.L) {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                document.head.appendChild(link);

                const script = document.createElement('script');
                script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                document.body.appendChild(script);
            }
        }
    }" class="py-4 bg-light min-vh-100 accent-adobe">

        <div class="container-fluid px-4">

            {{-- Header --}}
            <x-admin.table-toolbar>
                <x-admin.table-toolbar-button :label="__('admin_properties.create')" @click="openDrawer('create')" />
            </x-admin.table-toolbar>

            {{-- Table card --}}
            <form method="GET" action="{{ route(auth()->user()->panelPrefix() . '.properties.index') }}"
                x-data="{
                        submitNow() { this.$el.submit(); },
                            debounce: null,
                            debounceSubmit() {
                                clearTimeout(this.debounce);
                                this.debounce = setTimeout(() => this.submitNow(), 400);
                            },
                            visibleCols: @js(array_fill_keys(['reference', 'type', 'operation', 'price', 'city', 'status'], true) + ['title' => false]),
                            colMenuOpen: false,
                            toggleCol(key) { this.visibleCols[key] = !this.visibleCols[key]; }
                    }">


                @php
                    $columns = [
                        [
                            'key' => 'reference',
                            'label' => __('admin_properties.table_reference'),
                            'sortable' => true,
                            'searchable' => true,
                            'placeholder' => __('admin_properties.search_reference'),
                        ],
                        [
                            'key' => 'title',
                            'label' => __('admin_properties.table_title'),
                            'sortable' => true,
                            'searchable' => true,
                            'placeholder' => __('admin_properties.search_title'),
                        ],
                        [
                            'key' => 'type',
                            'label' => __('admin_properties.table_type'),
                            'sortable' => true,
                            'searchable' => true,
                            'type' => 'select',
                            'options' => $types,
                            'optionKey' => 'name',
                            'optionLabel' => 'label',
                        ],
                        [
                            'key' => 'operation',
                            'label' => __('admin_properties.table_operation'),
                            'sortable' => true,
                            'searchable' => true,
                            'type' => 'select',
                            'options' => $operations,
                            'optionKey' => 'name',
                            'optionLabel' => 'label',
                        ],
                        [
                            'key' => 'price',
                            'label' => __('admin_properties.table_price'),
                            'sortable' => true,
                            'searchable' => true,
                            'type' => 'range',
                            'placeholder_min' => __('admin_properties.search_price_min'),
                            'placeholder_max' => __('admin_properties.search_price_max'),
                        ],
                        [
                            'key' => 'city',
                            'label' => __('admin_properties.table_city'),
                            'sortable' => true,
                            'searchable' => true,
                            'placeholder' => __('admin_properties.search_city'),
                        ],
                        [
                            'key' => 'status',
                            'label' => __('admin_properties.table_status'),
                            'sortable' => true,
                            'searchable' => true,
                            'type' => 'select',
                            'options' => $statuses,
                            'optionKey' => 'name',
                            'optionLabel' => 'label',
                        ],
                        [
                            'key' => 'actions',
                            'label' => __('admin_properties.table_actions'),
                            'sortable' => false,
                            'searchable' => false,
                        ],
                    ];
                @endphp

                <x-admin.data-table :paginator="$properties" info-key="admin_properties.pagination_info">
                    <x-slot:thead>
                        <tr>
                            @foreach ($columns as $column)
                                <th
                                    @if (!in_array($column['key'], ['actions'])) x-show="visibleCols['{{ $column['key'] }}'] ?? true" @endif>
                                    @if ($column['sortable'])
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => $column['key'], 'dir' => $sort === $column['key'] && $dir === 'asc' ? 'desc' : 'asc']) }}"
                                            class="text-white text-decoration-none d-flex align-items-center gap-1">
                                            {{ $column['label'] }}
                                            <span class="d-inline-flex flex-column lh-1">
                                                <i class="fa-solid fa-caret-up {{ $sort === $column['key'] && $dir === 'asc' ? 'opacity-100' : 'opacity-25' }}"></i>
                                                <i class="fa-solid fa-caret-down {{ $sort === $column['key'] && $dir === 'desc' ? 'opacity-100' : 'opacity-25' }}"></i>
                                            </span>
                                        </a>
                                    @else
                                        {{ $column['label'] }}
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                    </x-slot:thead>

                    <x-slot:search>
                        @foreach ($columns as $column)
                            <th
                                @if (!in_array($column['key'], ['actions'])) x-show="visibleCols['{{ $column['key'] }}'] ?? true" @endif>
                                @if ($column['searchable'] ?? false)
                                    @if (($column['type'] ?? 'text') === 'select')
                                        <select name="{{ $column['key'] }}" @change="submitNow()"
                                            class="form-select form-select-sm">
                                            <option value="">
                                                @if ($column['key'] === 'type')
                                                    {{ __('admin_properties.search_type') }}
                                                @elseif($column['key'] === 'operation')
                                                    {{ __('admin_properties.search_operation') }}
                                                @else
                                                    {{ __('admin_properties.search_status') }}
                                                @endif
                                            </option>
                                            @foreach ($column['options'] as $opt)
                                                @php
                                                    $optValue = is_object($opt) ? $opt->{$column['optionKey'] ?? 'id'} : $opt;
                                                    $optLabel = is_object($opt) ? $opt->{$column['optionLabel'] ?? 'name'} : $opt;
                                                @endphp
                                                <option value="{{ $optValue }}"
                                                    @selected(request($column['key']) === (string)$optValue)>
                                                    {{ $optLabel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @elseif(($column['type'] ?? '') === 'range')
                                        <div class="d-flex gap-1">
                                            <input type="number" name="price_min"
                                                value="{{ request('price_min') }}"
                                                placeholder="{{ $column['placeholder_min'] }}"
                                                class="form-control form-control-sm" min="0"
                                                @input="debounceSubmit()">
                                            <input type="number" name="price_max"
                                                value="{{ request('price_max') }}"
                                                placeholder="{{ $column['placeholder_max'] }}"
                                                class="form-control form-control-sm" min="0"
                                                @input="debounceSubmit()">
                                        </div>
                                    @else
                                        <input type="text" name="{{ $column['key'] }}"
                                            value="{{ request($column['key']) }}"
                                            placeholder="{{ $column['placeholder'] ?? '' }}"
                                            class="form-control form-control-sm" @input="debounceSubmit()">
                                    @endif
                                @elseif($column['key'] === 'actions')
                                    <div
                                        class="d-flex align-items-center justify-content-center gap-2 position-relative">
                                        <button type="submit"
                                            class="btn btn--pill btn-light rounded-pill icon-box"
                                            title="{{ __('admin_properties.search') }}">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                        @if (request()->hasAny(['title', 'city', 'type', 'operation', 'status', 'reference', 'price_min', 'price_max']))
                                            <a href="{{ route(auth()->user()->panelPrefix() . '.properties.index') }}"
                                                class="btn btn--pill btn-light rounded-pill icon-box"
                                                title="{{ __('admin_properties.clear') }}">
                                                <i class="fa-solid fa-rotate-left"></i>
                                            </a>
                                        @endif
                                        <div class="position-relative">
                                            <button type="button"
                                                class="btn btn--pill btn-light rounded-pill icon-box"
                                                title="Mostrar/ocultar columnas"
                                                @click="colMenuOpen = !colMenuOpen"
                                                @click.outside="colMenuOpen = false">
                                                <i class="fa-solid fa-table-columns"></i>
                                            </button>
                                            <div x-show="colMenuOpen" x-cloak
                                                x-transition:enter="anim-dialog-enter-to"
                                                x-transition:enter-start="anim-dialog-enter"
                                                class="position-absolute end-0 mt-1 bg-white rounded-3 shadow border p-3"
                                                style="min-width:170px; z-index:200; top:100%;">
                                                @foreach (['reference' => __('admin_properties.table_reference'), 'title' => __('admin_properties.table_title'), 'type' => __('admin_properties.table_type'), 'operation' => __('admin_properties.table_operation'), 'price' => __('admin_properties.table_price'), 'city' => __('admin_properties.table_city'), 'status' => __('admin_properties.table_status')] as $colKey => $colLabel)
                                                    <label
                                                        class="d-flex align-items-center gap-2 small py-1 text-dark"
                                                        style="cursor:pointer; white-space:nowrap; font-weight:400;">
                                                        <input type="checkbox" class="form-check-input m-0"
                                                            :checked="visibleCols['{{ $colKey }}']"
                                                            @change="toggleCol('{{ $colKey }}')">
                                                        {{ $colLabel }}
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </th>
                        @endforeach
                    </x-slot:search>

                    @forelse($properties as $property)
                        <tr>
                            <td class="fw-bold" x-show="visibleCols['reference']">
                                {{ $property->reference }}
                            </td>
                            <td x-show="visibleCols['title']">{{ $property->title }}</td>
                            <td x-show="visibleCols['type']">
                                {{ __('admin_properties.type_' . $property->type) }}
                            </td>
                            <td x-show="visibleCols['operation']">
                                {{ __('admin_properties.operation_' . $property->operation) }}
                            </td>
                            <td x-show="visibleCols['price']">
                                {{ $property->formattedPrice() }}</td>
                            <td class="text-muted" x-show="visibleCols['city']">{{ $property->city ?? '—' }}
                            </td>
                            <td x-show="visibleCols['status']">
                                <span class="badge rounded-pill {{ $property->statusBadgeClass() }}">
                                    {{ __('admin_properties.status_' . $property->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <x-admin.table-actions>
                                    <x-admin.table-action icon="fa-eye" variant="teal" title="Ver"
                                        @click="openDrawer('show', {{ $property->id }})" />
                                    @can('update', $property)
                                    <x-admin.table-action icon="fa-copy" variant="mint" title="Clonar"
                                        @click="$dispatch('confirm-show', {
                                                title: 'Clonar inmueble',
                                                message: `¿Clonar el inmueble {{ $property->reference }}? Se creará una copia con estado disponible.`,
                                                confirmLabel: 'Clonar',
                                                type: 'warning',
                                                showIcon: true,
                                                url: '{{ route(auth()->user()->panelPrefix() . '.properties.clone', $property->id) }}',
                                                method: 'POST'
                                            })" />
                                    <x-admin.table-action icon="fa-pen" variant="adobe" title="Editar"
                                        @click="openDrawer('edit', {{ $property->id }})" />
                                    @endcan
                                    @can('delete', $property)
                                    <x-admin.table-action icon="fa-trash" variant="danger" title="Eliminar"
                                        @click="$dispatch('confirm-show', {
                                                title: '¿Eliminar inmueble?',
                                                message: 'Esta acción eliminará permanentemente el inmueble {{ addslashes($property->reference) }}.',
                                                confirmLabel: 'Eliminar',
                                                cancelLabel: 'Cancelar',
                                                type: 'danger',
                                                url: '{{ route(auth()->user()->panelPrefix() . '.properties.destroy', $property) }}',
                                                method: 'DELETE'
                                            })" />
                                    @endcan
                                </x-admin.table-actions>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) }}" class="text-center text-muted py-5">
                                <i class="fa-solid fa-building mb-2 d-block"></i>
                                No hay inmuebles registrados
                            </td>
                        </tr>
                    @endforelse
                </x-admin.data-table>
            </form>

        {{-- DRAWER BACKDROP --}}
        <div x-show="open" x-cloak x-transition:enter="anim-fade-enter-to"
            x-transition:enter-start="anim-fade-enter" x-transition:leave="anim-fade-leave-to"
            x-transition:leave-end="anim-fade-leave-to" class="offcanvas-backdrop fade show" @click="open=false">
        </div>

        {{-- DRAWER PANEL --}}
        <div :class="{ 'show': open }" class="offcanvas offcanvas-end"
            style="width: min(720px, 95vw); border-left: 3px solid var(--accent, #bf9180);"
            @keydown.escape.window="open=false">

            {{-- Header fijo --}}
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom flex-shrink-0">
                <h5 class="fw-semibold mb-0"
                    x-text="mode==='show' ? '{{ __('admin_properties.show_title') }}' : mode==='edit' ? '{{ __('admin_properties.edit_title') }}' : '{{ __('admin_properties.create_title') }}'">
                </h5>
                <button class="btn-close" @click="open=false"></button>
            </div>

            {{-- Body scrolleable --}}
            <div class="px-4 py-4 overflow-y-auto flex-grow-1">

                {{-- Loading --}}
                <div x-show="loading" x-cloak style="display:flex; justify-content:center; padding:3rem 0;">
                    <div class="spinner-border" style="color: var(--accent);" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>

                <div x-show="!loading">

                    {{-- SHOW --}}
                    <template x-if="mode==='show'">
                        @include('admin.properties.index-show')
                    </template>

                    {{-- CREATE --}}
                    <template x-if="mode==='create'">
                        @include('admin.properties.index-create')
                    </template>

                    {{-- EDIT --}}
                    <template x-if="mode==='edit'">
                        @include('admin.properties.index-edit')
                    </template>

                </div>
            </div>
        </div>

        {{-- DELETE MODAL --}}
        <x-confirm-modal />

        {{-- TOAST --}}
        <x-toast-notify />

    </div>
</x-admin-layout>
