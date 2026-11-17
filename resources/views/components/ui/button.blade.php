@props([
    'type' => 'button',
    'href' => null,
    'variant' => 'purple',
    'rounded' => 'rounded-xl',
])

@php
    $baseClass = $variant === 'surface' ? 'btn-glass-surface' : 'btn-glass-purple';
    $classes = "{$baseClass} {$rounded} px-5 py-2.5 text-xs font-label-md font-bold uppercase tracking-wider transition-all";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
