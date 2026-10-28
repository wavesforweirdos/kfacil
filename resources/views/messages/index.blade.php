<x-admin-layout>
    <x-slot name="header">
        Mensajes
    </x-slot>

    <div class="py-4 bg-light min-vh-100 accent-blue">
        <div class="container-fluid px-4">
            <x-admin.table-toolbar>
                @if (request('estado') === 'sin-leer')
                    <x-admin.table-toolbar-button :href="route('messages.index')" label="Ver todos" />
                @else
                    <x-admin.table-toolbar-button :href="route('messages.index', ['estado' => 'sin-leer'])" label="Solo sin leer" />
                @endif
            </x-admin.table-toolbar>

            @if (session('success'))
                <div class="alert alert-success mb-4">{{ session('success') }}</div>
            @endif

            <x-admin.data-table :paginator="$messages" :show-info="false">
                <x-slot:thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Nombre</th>
                        <th>Asunto</th>
                        <th>Email</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </x-slot:thead>

                @forelse ($messages as $message)
                    <tr class="{{ $message->isRead() ? '' : 'fw-bold' }}">
                        <td>{{ $message->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @unless ($message->isRead())
                                <span class="badge rounded-pill badge-accent me-1">Nuevo</span>
                            @endunless
                            {{ $message->name }}
                        </td>
                        <td>{{ $message->subject }}</td>
                        <td>{{ $message->email }}</td>
                        <td class="text-center">
                            <x-admin.table-actions>
                                <x-admin.table-action :href="route('messages.show', $message)" icon="fa-eye" variant="teal" title="Ver" />

                                @if (auth()->user()->isAdmin())
                                    <form action="{{ route('messages.destroy', $message) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <x-admin.table-action type="submit" icon="fa-trash" variant="adobe" title="Eliminar"
                                            onclick="return confirm('¿Estás seguro?')" />
                                    </form>
                                @endif
                            </x-admin.table-actions>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="fa-solid fa-envelope mb-2 d-block"></i>
                            No hay mensajes
                        </td>
                    </tr>
                @endforelse
            </x-admin.data-table>
        </div>
    </div>
</x-admin-layout>
