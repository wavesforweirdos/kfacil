<x-admin-layout>
    <x-slot name="header">
        Estados de Propiedad
    </x-slot>

    <div class="py-4 bg-light min-vh-100 accent-adobe">
        <div class="container-fluid px-4">
            <x-admin.table-toolbar>
                <x-admin.table-toolbar-button :href="route('property-statuses.create')" label="Agregar Estado" />
            </x-admin.table-toolbar>

            @if (session('success'))
                <div class="alert alert-success mb-4">{{ session('success') }}</div>
            @endif

            <x-admin.data-table :paginator="$statuses" :show-info="false">
                <x-slot:thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Etiqueta</th>
                        <th>Orden</th>
                        <th>Activo</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </x-slot:thead>

                @forelse ($statuses as $status)
                    <tr>
                        <td>{{ $status->name }}</td>
                        <td>{{ $status->label }}</td>
                        <td>{{ $status->sort_order }}</td>
                        <td>
                            <span class="badge rounded-pill {{ $status->is_active ? 'badge-accent' : 'badge-accent-soft' }}">
                                {{ $status->is_active ? 'SI' : 'NO' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <x-admin.table-actions>
                                <x-admin.table-action :href="route('property-statuses.edit', $status)" icon="fa-pen-to-square" variant="teal" title="Editar" />

                                <form action="{{ route('property-statuses.destroy', $status) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.table-action type="submit" icon="fa-trash" variant="adobe" title="Eliminar"
                                        onclick="return confirm('¿Estás seguro?')" />
                                </form>
                            </x-admin.table-actions>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="fa-solid fa-circle-dot mb-2 d-block"></i>
                            No hay estados de propiedad
                        </td>
                    </tr>
                @endforelse
            </x-admin.data-table>
        </div>
    </div>
</x-admin-layout>
