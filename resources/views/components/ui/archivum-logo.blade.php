@props([
    'size' => 'md',
    'containerClass' => '',
    'svgClass' => '',
    'showText' => false,
    'subtitle' => null,
    'textClass' => '',
    'isotypeOnly' => false,
])

@php
    $dimensions = match ($size) {
        'xs' => ['container' => 'w-7 h-7 rounded-lg', 'svg' => 'w-4 h-4', 'title' => 'text-sm'],
        'sm' => ['container' => 'w-9 h-9 rounded-xl', 'svg' => 'w-5 h-5', 'title' => 'text-base'],
        'md' => ['container' => 'w-11 h-11 rounded-xl', 'svg' => 'w-6 h-6', 'title' => 'text-xl'],
        'lg' => ['container' => 'w-12 h-12 rounded-2xl', 'svg' => 'w-7 h-7', 'title' => 'text-2xl'],
        'xl' => ['container' => 'w-16 h-16 rounded-2xl', 'svg' => 'w-10 h-10', 'title' => 'text-3xl'],
        default => ['container' => 'w-11 h-11 rounded-xl', 'svg' => 'w-6 h-6', 'title' => 'text-xl'],
    };

    $containerBase = "archivum-logo-glossy shrink-0 text-white shadow-xs " . $dimensions['container'] . " " . $containerClass;
    $svgBase = "shrink-0 transition-transform " . $dimensions['svg'] . " " . $svgClass;
@endphp

@if ($isotypeOnly)
    <svg class="{{ $svgBase }}" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" {{ $attributes }} aria-hidden="true">
        <!-- Funda / Lomo Dorado (a la derecha, detrás del disco) -->
        <g class="archivum-sleeve">
            <rect x="44" y="13" width="46" height="74" rx="8" ry="8" fill="#c5a358" />
            <rect x="44" y="27" width="46" height="3.5" fill="#7c622b" />
            <rect x="44" y="69.5" width="46" height="3.5" fill="#7c622b" />
        </g>

        <!-- Disco de Vinilo (Interactivo con salto y giro suave) -->
        <g class="archivum-vinyl-wrapper">
            <g class="archivum-vinyl-disc">
                <circle cx="42" cy="50" r="37" fill="#1b1724" />
                <circle cx="42" cy="50" r="31" stroke="#383148" stroke-width="1.2" fill="none" opacity="0.9" />
                <circle cx="42" cy="50" r="25" stroke="#383148" stroke-width="1.2" fill="none" opacity="0.9" />
                <circle cx="42" cy="50" r="19" stroke="#383148" stroke-width="1.2" fill="none" opacity="0.9" />
                <circle cx="42" cy="50" r="12.5" fill="#6d42b5" />
                <circle cx="42" cy="50" r="2.8" fill="#f4f0ea" />
            </g>
        </g>
    </svg>
@elseif ($showText)
    <div class="inline-flex items-center gap-3 group" {{ $attributes }}>
        <div class="{{ $containerBase }}" aria-hidden="true">
            <svg class="{{ $svgBase }}" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Funda / Lomo Dorado -->
                <g class="archivum-sleeve">
                    <rect x="44" y="13" width="46" height="74" rx="8" ry="8" fill="#c5a358" />
                    <rect x="44" y="27" width="46" height="3.5" fill="#7c622b" />
                    <rect x="44" y="69.5" width="46" height="3.5" fill="#7c622b" />
                </g>

                <!-- Disco de Vinilo (Interactivo con salto y giro suave) -->
                <g class="archivum-vinyl-wrapper">
                    <g class="archivum-vinyl-disc">
                        <circle cx="42" cy="50" r="37" fill="#1b1724" />
                        <circle cx="42" cy="50" r="31" stroke="#383148" stroke-width="1.2" fill="none" opacity="0.9" />
                        <circle cx="42" cy="50" r="25" stroke="#383148" stroke-width="1.2" fill="none" opacity="0.9" />
                        <circle cx="42" cy="50" r="19" stroke="#383148" stroke-width="1.2" fill="none" opacity="0.9" />
                        <circle cx="42" cy="50" r="12.5" fill="#6d42b5" />
                        <circle cx="42" cy="50" r="2.8" fill="#f4f0ea" />
                    </g>
                </g>
            </svg>
        </div>
        <div class="flex flex-col {{ $textClass }}">
            <span class="font-brand-serif {{ $dimensions['title'] }} font-bold uppercase tracking-[0.25em] text-on-surface leading-tight transition-colors group-hover:text-primary">ARCHIVUM</span>
            @if ($subtitle)
                <span class="font-label-code text-primary dark:text-[#ccbeff] uppercase tracking-widest text-[10px] mt-0.5">{{ $subtitle }}</span>
            @endif
        </div>
    </div>
@else
    <div class="{{ $containerBase }}" {{ $attributes }}>
        <svg class="{{ $svgBase }}" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <!-- Funda / Lomo Dorado -->
            <g class="archivum-sleeve">
                <rect x="44" y="13" width="46" height="74" rx="8" ry="8" fill="#c5a358" />
                <rect x="44" y="27" width="46" height="3.5" fill="#7c622b" />
                <rect x="44" y="69.5" width="46" height="3.5" fill="#7c622b" />
            </g>

            <!-- Disco de Vinilo (Interactivo con salto y giro suave) -->
            <g class="archivum-vinyl-wrapper">
                <g class="archivum-vinyl-disc">
                    <circle cx="42" cy="50" r="37" fill="#1b1724" />
                    <circle cx="42" cy="50" r="31" stroke="#383148" stroke-width="1.2" fill="none" opacity="0.9" />
                    <circle cx="42" cy="50" r="25" stroke="#383148" stroke-width="1.2" fill="none" opacity="0.9" />
                    <circle cx="42" cy="50" r="19" stroke="#383148" stroke-width="1.2" fill="none" opacity="0.9" />
                    <circle cx="42" cy="50" r="12.5" fill="#6d42b5" />
                    <circle cx="42" cy="50" r="2.8" fill="#f4f0ea" />
                </g>
            </g>
        </svg>
    </div>
@endif
