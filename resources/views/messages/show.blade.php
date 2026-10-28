<x-admin-layout>
    <x-slot name="header">
        Mensaje
    </x-slot>

    <div class="py-4 bg-light min-vh-100 accent-blue">
        <div class="container-fluid px-4">
            <a href="{{ route('messages.index') }}" class="btn btn-link px-0 mb-3">&larr; Volver a mensajes</a>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h4 mb-3">{{ $message->subject }}</h2>

                    <dl class="row mb-4">
                        <dt class="col-sm-2">De</dt>
                        <dd class="col-sm-10">{{ $message->name }}</dd>

                        <dt class="col-sm-2">Email</dt>
                        <dd class="col-sm-10"><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></dd>

                        @if ($message->phone)
                            <dt class="col-sm-2">Teléfono</dt>
                            <dd class="col-sm-10"><a href="tel:{{ $message->phone }}">{{ $message->phone }}</a></dd>
                        @endif

                        <dt class="col-sm-2">Recibido</dt>
                        <dd class="col-sm-10">{{ $message->created_at->format('d/m/Y H:i') }}</dd>
                    </dl>

                    <p class="mb-0" style="white-space: pre-line;">{{ $message->message }}</p>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
