<x-guest-layout>

    <div class="text-center mb-4">
        <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="mx-auto d-block img-fluid">
    </div>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Description -->
        <p class="small text-center mb-4">
            Introduce tu email y te enviaremos un enlace para restablecer tu contraseña.
        </p>

        <!-- Email -->
        <div class="mb-3">
            <label for="email">Email</label>
            <input type="email"
                   id="email"
                   name="email"
                   value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   required
                   autofocus>

            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Actions -->
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary rounded-pill">
                Enviar enlace
            </button>
        </div>

        <div class="text-center small">
            <a href="{{ route('login') }}">
                Volver al login
            </a>
        </div>

    </form>

</x-guest-layout>