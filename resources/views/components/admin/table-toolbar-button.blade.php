@props([
    'href' => null,
    'type' => 'button',
    'icon' => 'fa-plus',
    'label' => '',
])

@php
    $classes = 'btn btn-outline-accent rounded-pill d-flex align-items-center gap-2 px-4';
@endphp

@if ($href)
    <a href="{{ $href }}" class="{{ $classes }}" {{ $attributes }}>
        <i class="fa-solid {{ $icon }} small"></i>
        {{ $label ?: $slot }}
    </a>
@else
    <button type="{{ $type }}" class="{{ $classes }}" {{ $attributes }}>
        <i class="fa-solid {{ $icon }} small"></i>
        {{ $label ?: $slot }}
    </button>
@endif
