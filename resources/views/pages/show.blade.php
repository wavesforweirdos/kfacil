<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="generator" content="Hostinger Horizons">

    <!-- SEO -->
    <title>{{ $page->meta_title ?? $page->title }} | K Fácil Inmobiliaria</title>
    <meta name="description" content="{{ $page->meta_description ?? $page->description }}">
    <meta name="keywords" content="{{ $page->meta_keywords }}">
    
    <!-- Open Graph -->
    <meta property="og:title" content="{{ $page->meta_title ?? $page->title }}">
    <meta property="og:description" content="{{ $page->meta_description ?? $page->description }}">
    <meta property="og:type" content="website">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400&family=Great+Vibes&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind utilities for block templates -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Scripts -->
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 30%, #f9fafb 100%);
        }

        .font-playfair {
            font-family: 'Playfair Display', serif;
        }
    </style>
</head>
<body class="antialiased">
    @include('layouts.public-navbar', ['navbarSettings' => $navbarSettings ?? []])

    <!-- Page Blocks -->
    <main>
        @foreach($page->blocks as $block)
            @continue($block->component === 'navbar')
            @includeIf("pages.blocks.{$block->component}", ['block' => $block])
        @endforeach
    </main>
</body>
</html>
