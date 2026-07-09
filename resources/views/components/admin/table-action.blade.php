@props([
    'icon',
    'variant' => 'teal',
    'title' => null,
    'href' => null,
    'type' => 'button',
])

@php
    $classes = 'table-action-btn table-action-btn--' . $variant;
@endphp

@if ($href)
    <a href="{{ $href }}" class="{{ $classes }}" @if($title) title="{{ $title }}" @endif {{ $attributes }}>
        <i class="fa-solid {{ $icon }}"></i>
    </a>
@else
    <button type="{{ $type }}" class="{{ $classes }}" @if($title) title="{{ $title }}" @endif {{ $attributes }}>
        <i class="fa-solid {{ $icon }}"></i>
    </button>
@endif
