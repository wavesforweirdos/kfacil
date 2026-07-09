<x-guest-layout>

    <div class="text-center mb-4">
        <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="mx-auto d-block img-fluid">
    </div>

    <div class="alert alert-warning small text-center">
        Esta es una zona segura. Por favor, confirma tu contraseña antes de continuar.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div class="mb-3">
            <label for="password">Contraseña</label>
            <input type="password"
                   id="password"
                   name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required
                   autocomplete="current-password">

            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Actions -->
        <div class="d-grid">
            <button type="submit" class="btn btn-primary rounded-pill">
                Confirmar
            </button>
        </div>

    </form>

</x-guest-layout>