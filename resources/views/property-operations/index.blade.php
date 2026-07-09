<x-admin-layout>
    <x-slot name="header">
        Tipos de Operacion
    </x-slot>

    <div class="py-4 bg-light min-vh-100 accent-adobe">
        <div class="container-fluid px-4">
            <x-admin.table-toolbar>
                <x-admin.table-toolbar-button :href="route('property-operations.create')" label="Agregar Tipo" />
            </x-admin.table-toolbar>

            @if (session('success'))
                <div class="alert alert-success mb-4">{{ session('success') }}</div>
            @endif

            <x-admin.data-table :paginator="$operations" :show-info="false">
                <x-slot:thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Etiqueta</th>
                        <th>Orden</th>
                        <th>Activo</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </x-slot:thead>

                @forelse ($operations as $operation)
                    <tr>
                        <td>{{ $operation->name }}</td>
                        <td>{{ $operation->label }}</td>
                        <td>{{ $operation->sort_order }}</td>
                        <td>
                            <span class="badge rounded-pill {{ $operation->is_active ? 'badge-accent' : 'badge-accent-soft' }}">
                                {{ $operation->is_active ? 'SI' : 'NO' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <x-admin.table-actions>
                                <x-admin.table-action :href="route('property-operations.edit', $operation)" icon="fa-pen-to-square" variant="teal" title="Editar" />

                                <form action="{{ route('property-operations.destroy', $operation) }}" method="POST" class="d-inline">
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
                            <i class="fa-solid fa-handshake mb-2 d-block"></i>
                            No hay tipos de operacion
                        </td>
                    </tr>
                @endforelse
            </x-admin.data-table>
        </div>
    </div>
</x-admin-layout>
