<x-guest-layout>

    <div class="text-center mb-4">
        <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="mx-auto d-block img-fluid">
    </div>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                class="form-control @error('email') is-invalid @enderror" required autofocus>

            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password"
                class="form-control @error('password') is-invalid @enderror" required>

            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div class="form-check">
                <input type="checkbox" class="form-check-input" name="remember" id="remember">
                <label class="form-check-label" for="remember">
                    Recordarme
                </label>
            </div>

            <a href="{{ route('password.request') }}" class="small">
                ¿Olvidaste tu contraseña?
            </a>

        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary rounded-pill">
                Iniciar sesión
            </button>
        </div>

    </form>

</x-guest-layout>
