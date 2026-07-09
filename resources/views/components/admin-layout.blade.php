<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo_color.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>

<body>

    <div class="d-flex min-vh-100">

        {{-- SIDEBAR --}}
        @include('layouts.navigation.admin')

        {{-- MAIN --}}
        <div class="flex-grow-1 d-flex flex-column">

            {{-- TOPBAR --}}
            <header class="bg-white border-bottom">
                <div class="container-fluid px-4 py-3 d-flex justify-content-between align-items-center">

                    <!-- Title -->
                    <h1 class="h6 mb-0 fw-semibold text-dark">
                        {{ $header ?? '' }}
                    </h1>

                    <!-- User -->
                    <div class="d-flex align-items-center gap-3">

                        <!-- Name -->
                        <span class="small text-muted">
                            {{ ucfirst(auth()->user()->name) }}
                        </span>

                        <!-- Avatar -->
                        @if (auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                 class="rounded-circle border"
                                 style="width:36px; height:36px; object-fit:cover;">
                        @else
                            <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold"
                                 style="width:36px; height:36px;">
                                {{ auth()->user()->initials() }}
                            </div>
                        @endif

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-sm btn-link text-muted p-0">
                                <i class="fa-solid fa-right-from-bracket"></i>
                            </button>
                        </form>

                    </div>

                </div>
            </header>

            {{-- CONTENT --}}
            <main class="flex-grow-1 bg-light">
                {{ $slot }}
            </main>

        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>