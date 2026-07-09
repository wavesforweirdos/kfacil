@props([
    'justify' => 'end',
    'class' => 'mb-4 gap-2',
])

<div {{ $attributes->class(['d-flex', 'justify-content-' . $justify, $class]) }}>
    {{ $slot }}
</div>
