@props([
    'type' => 'mint', // mint, vg, rare, coa, alert, pending
    'text' => 'MINT (M)'
])

@php
    $classes = match($type) {
        'mint' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
        'vg' => 'bg-purple-100 text-primary border-purple-300',
        'rare' => 'bg-primary-container text-white border-primary-container',
        'coa' => 'bg-tertiary text-white border-tertiary',
        'alert' => 'bg-error-container text-error border-red-300',
        'pending' => 'bg-amber-100 text-amber-800 border-amber-300',
        default => 'bg-surface-container text-on-surface border-purple-200'
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-label-sm font-semibold tracking-wider border shadow-2xs {$classes}"]) }}>
    @if($type === 'coa')
        <span class="material-symbols-outlined text-[12px]">verified</span>
    @elseif($type === 'mint')
        <span class="material-symbols-outlined text-[12px]">check_circle</span>
    @elseif($type === 'alert')
        <span class="material-symbols-outlined text-[12px]">warning</span>
    @endif
    <span>{{ $text }}</span>
</span>
