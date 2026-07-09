<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $property->title }} | K Facil Inmobiliaria</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($property->description), 150) }}">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>

<body class="bg-light">
    <main class="py-4">
        <div class="container">
            <div class="mb-4">
                <a href="{{ route('public.properties.index') }}" class="text-decoration-none">&larr; Volver a inmuebles</a>
            </div>

            <div class="row g-4">
                <div class="col-12 col-lg-8">
                    @if($property->mainImageUrl())
                        <img src="{{ $property->mainImageUrl() }}" alt="{{ $property->title }}" class="img-fluid rounded shadow-sm w-100 mb-3">
                    @endif

                    @if($property->images->count() > 0)
                        <div class="row g-2">
                            @foreach($property->images as $image)
                                <div class="col-6 col-md-4">
                                    <img src="{{ $image->url() }}" alt="Imagen de {{ $property->title }}" class="img-fluid rounded">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="col-12 col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h1 class="h4 mb-2">{{ $property->title }}</h1>
                            <p class="h5 text-primary mb-3">{{ $property->formattedPrice() }}</p>

                            <ul class="list-unstyled mb-3">
                                <li><strong>Tipo:</strong> {{ $property->propertyType?->name ?? '-' }}</li>
                                <li><strong>Operacion:</strong> {{ $property->propertyOperation?->name ?? '-' }}</li>
                                <li><strong>Estado:</strong> {{ $property->propertyStatus?->name ?? '-' }}</li>
                                <li><strong>Superficie:</strong> {{ $property->surface ? $property->surface . ' m2' : '-' }}</li>
                                <li><strong>Habitaciones:</strong> {{ $property->rooms ?? '-' }}</li>
                                <li><strong>Banos:</strong> {{ $property->bathrooms ?? '-' }}</li>
                            </ul>

                            <p class="mb-0 text-muted">
                                {{ trim(collect([$property->address, $property->locality, $property->province, $property->country])->filter()->implode(', ')) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5">Descripcion</h2>
                    <p class="mb-0">{{ $property->description }}</p>
                </div>
            </div>
        </div>
    </main>
</body>

</html>
