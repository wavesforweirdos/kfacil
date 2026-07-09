<x-guest-layout>

<div class="text-center mb-4">
    <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="mx-auto d-block img-fluid">
</div>

<form method="POST" action="{{ route('register') }}">
@csrf

<div class="mb-3">
    <label for="name">Nombre</label>

    <input
        type="text"
        id="name"
        name="name"
        value="{{ old('name') }}"
        class="form-control @error('name') is-invalid @enderror"
        required
        autofocus
    >

    @error('name')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="mb-3">
    <label for="email">Email</label>

    <input
        type="email"
        id="email"
        name="email"
        value="{{ old('email') }}"
        class="form-control @error('email') is-invalid @enderror"
        required
    >

    @error('email')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="mb-3">
    <label for="password">Contraseña</label>

    <input
        type="password"
        id="password"
        name="password"
        class="form-control @error('password') is-invalid @enderror"
        required
    >

    @error('password')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="mb-3">
    <label for="password_confirmation">Confirmar contraseña</label>

    <input
        type="password"
        id="password_confirmation"
        name="password_confirmation"
        class="form-control @error('password_confirmation') is-invalid @enderror"
        required
    >

    @error('password_confirmation')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="d-grid mb-3">
    <button type="submit" class="btn btn-primary rounded-pill">
        Registrarse
    </button>
</div>

<div class="text-center small">
    ¿Ya tienes cuenta?
    <a href="{{ route('login') }}">
        Inicia sesión
    </a>
</div>

</form>

</x-guest-layout>