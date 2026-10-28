<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'KFacil') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- App CSS -->
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

</head>

<body class="bg-light">

    <div class="container min-vh-100 d-flex flex-column justify-content-center align-items-center">

        <!-- Logo -->
        {{-- <div class="mb-4 text-center">
            <a href="/">
                <img src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name') }}" style="max-height:70px;">
            </a>
        </div> --}}

        <!-- Login Card -->
        <div class="col-12 col-sm-10 col-md-6 col-lg-5 col-xxl-4">
            <div class="card shadow-sm border-0">

                <div class="card-body p-4">

                    {{ $slot }}

                </div>

            </div>
        </div>

        <!-- Footer -->
        <div class="mt-4 small text-center text-body-secondary">
            © {{ date('Y') }} {{ config('app.name', 'KFacil') }}
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
