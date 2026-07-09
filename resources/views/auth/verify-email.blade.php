<x-guest-layout>

    <div class="text-center mb-4">
        <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="mx-auto d-block img-fluid">
    </div>

    <div class="alert alert-info small text-center">
        Gracias por registrarte. Antes de empezar, verifica tu email haciendo clic en el enlace que te hemos enviado.
        Si no lo has recibido, puedes solicitar otro.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success small text-center">
            Se ha enviado un nuevo enlace de verificación a tu correo.
        </div>
    @endif

    <div class="d-grid gap-3 mt-4">

        <!-- Resend -->
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary rounded-pill w-100">
                Reenviar email de verificación
            </button>
        </form>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link text-decoration-none small">
                Cerrar sesión
            </button>
        </form>

    </div>

</x-guest-layout>