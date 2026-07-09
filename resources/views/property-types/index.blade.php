<x-admin-layout>
    <x-slot name="header">
        Tipos de Propiedad
    </x-slot>

    <div class="py-4 bg-light min-vh-100 accent-adobe">
        <div class="container-fluid px-4">
            <x-admin.table-toolbar>
                <x-admin.table-toolbar-button :href="route('property-types.create')" label="Agregar Tipo" />
            </x-admin.table-toolbar>

            @if (session('success'))
                <div class="alert alert-success mb-4">{{ session('success') }}</div>
            @endif

            <x-admin.data-table :paginator="$types" :show-info="false">
                <x-slot:thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Etiqueta</th>
                        <th>Orden</th>
                        <th>Activo</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </x-slot:thead>

                @forelse ($types as $type)
                    <tr>
                        <td>{{ $type->name }}</td>
                        <td>{{ $type->label }}</td>
                        <td>{{ $type->sort_order }}</td>
                        <td>
                            <span class="badge rounded-pill {{ $type->is_active ? 'badge-accent' : 'badge-accent-soft' }}">
                                {{ $type->is_active ? 'SI' : 'NO' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <x-admin.table-actions>
                                <x-admin.table-action :href="route('property-types.edit', $type)" icon="fa-pen-to-square" variant="teal" title="Editar" />

                                <form action="{{ route('property-types.destroy', $type) }}" method="POST" class="d-inline">
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
                            <i class="fa-solid fa-tags mb-2 d-block"></i>
                            No hay tipos de propiedad
                        </td>
                    </tr>
                @endforelse
            </x-admin.data-table>
        </div>
    </div>
</x-admin-layout>
