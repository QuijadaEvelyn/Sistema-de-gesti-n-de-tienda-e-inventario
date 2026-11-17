@props([
    'type' => 'button',
    'href' => null,
    'rounded' => 'rounded-xl',
])

@php
    $classes = "btn-glass-surface {$rounded} px-4 py-2 text-xs font-label-md font-bold uppercase tracking-wider transition-all";
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
