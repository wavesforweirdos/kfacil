<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0 fw-semibold text-dark">Gestion de paginas</h2>
        </div>
    </x-slot>

    <div class="py-4 bg-light min-vh-100 accent-blue">
        <div class="container-fluid px-4">
            @if(session('success'))
                <div class="alert alert-success py-2 px-3 small mb-3" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <x-admin.table-toolbar>
                <x-admin.table-toolbar-button :href="route('pages.navbar.edit')" icon="fa-compass" label="Editar navbar" />
                @can('create', App\Models\Page::class)
                    <x-admin.table-toolbar-button :href="route('pages.create')" label="Nueva pagina" />
                @endcan
            </x-admin.table-toolbar>

            @php
                $columns = [
                    ['key' => 'title', 'label' => 'Titulo'],
                    ['key' => 'slug', 'label' => 'Slug'],
                    ['key' => 'type', 'label' => 'Tipo'],
                    ['key' => 'status', 'label' => 'Estado'],
                    ['key' => 'agents', 'label' => 'Agentes'],
                    ['key' => 'actions', 'label' => 'Acciones'],
                ];
            @endphp

            @if($pages->count() > 0)
                <x-admin.data-table :paginator="$pages" :show-info="false">
                    <x-slot:thead>
                        <tr class="table-headers">
                            @foreach($columns as $column)
                                <th class="text-center px-3 py-3">{{ $column['label'] }}</th>
                            @endforeach
                        </tr>
                    </x-slot:thead>

                    @foreach($pages as $page)
                        <tr>
                            <td class="text-center fw-semibold px-3 py-3">{{ $page->title }}</td>
                            <td class="text-center px-3 py-3"><code>{{ $page->slug }}</code></td>
                            <td class="text-center px-3 py-3">
                                <span class="badge rounded-pill badge-accent-soft">{{ ucfirst($page->type) }}</span>
                            </td>
                            <td class="text-center px-3 py-3">
                                @if($page->published)
                                    <span class="badge rounded-pill badge-status-available">Publicada</span>
                                @else
                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis">Borrador</span>
                                @endif
                            </td>
                            <td class="text-center px-3 py-3">
                                @if($page->editable_by_agents)
                                    <span class="badge rounded-pill bg-info-subtle text-info-emphasis">Editable</span>
                                @else
                                    <span class="text-muted small">Solo admin</span>
                                @endif
                            </td>
                            <td class="text-center px-3 py-3">
                                <x-admin.table-actions>
                                    @can('update', $page)
                                        <x-admin.table-action :href="route('pages.edit', $page)" icon="fa-pen-to-square" variant="teal" title="Editar" />
                                    @endcan

                                    @can('view', $page)
                                        <x-admin.table-action :href="route('pages.show', $page)" icon="fa-eye" variant="mint" title="Ver" target="_blank" />
                                    @endcan

                                    @can('delete', $page)
                                        <form action="{{ route('pages.destroy', $page) }}" method="POST" class="d-inline" onsubmit="return confirm('Eliminar esta pagina?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-admin.table-action type="submit" icon="fa-trash" variant="adobe" title="Eliminar" />
                                        </form>
                                    @endcan
                                </x-admin.table-actions>
                            </td>
                        </tr>
                    @endforeach
                </x-admin.data-table>
            @else
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-5 px-4">
                        <p class="text-muted mb-3 small">No hay paginas creadas aun.</p>
                        @can('create', App\Models\Page::class)
                            <x-admin.table-toolbar-button :href="route('pages.create')" label="Crear primera pagina" />
                        @endcan
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
