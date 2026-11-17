@props([
    'title' => 'Métrica del Acervo',
    'value' => '0',
    'unit' => null,
    'tableSource' => null,
    'subtitle' => null,
    'trend' => null,
    'isPositive' => true,
    'trendLabel' => 'vs período anterior',
    'icon' => 'analytics',
    'iconBg' => 'bg-primary-container',
    'badge' => null,
    'badgePulse' => true,
    'badgeColor' => 'emerald',
    'summaryLeft' => null,
    'summaryRight' => null,
    'actionUrl' => null,
    'actionText' => null,
    'actionIcon' => 'arrow_forward',
    'interactive' => true,
])

@php
    $badgeColors = [
        'emerald' => [
            'pill' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700/60',
            'dot' => 'bg-emerald-500',
        ],
        'purple' => [
            'pill' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border-purple-200 dark:border-purple-700/60',
            'dot' => 'bg-purple-500',
        ],
        'amber' => [
            'pill' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-700/60',
            'dot' => 'bg-amber-500',
        ],
        'rose' => [
            'pill' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border-rose-200 dark:border-rose-700/60',
            'dot' => 'bg-rose-500',
        ],
    ];

    $colorConfig = $badgeColors[$badgeColor] ?? $badgeColors['emerald'];
@endphp

<article
    {{ $attributes->merge([
        'class' => 'group relative overflow-hidden rounded-2xl p-5 sm:p-6 transition-all duration-300 ease-in-out ' .
                   ($interactive ? 'hover:-translate-y-1 hover:shadow-xl hover:shadow-purple-500/10 dark:hover:shadow-[0_14px_30px_-6px_rgba(168,85,247,0.35)] ' : '') .
                   'bg-white dark:bg-[#171024] ' .
                   'border border-purple-200/80 dark:border-[#5c4a78]/50 hover:border-primary/60 dark:hover:border-primary/80 ' .
                   'flex flex-col justify-between shadow-xs'
    ]) }}
    data-purpose="fluid-metric-card"
>
    {{-- Acento Luminoso Superior: Brillo degradado violeta en hover --}}
    <div
        class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-primary to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
        aria-hidden="true"
    ></div>

    {{-- Resplandor ambiental de fondo muy sutil en hover --}}
    <div
        class="absolute -right-12 -top-12 w-32 h-32 bg-primary/5 dark:bg-primary/10 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
        aria-hidden="true"
    ></div>

    {{-- Cabecera de la Tarjeta: Título, Origen Técnico, Badge de Estado e Icono --}}
    <div class="relative z-10 flex items-start justify-between gap-3">
        <div class="flex flex-col space-y-1">
            @if ($tableSource)
                <span class="font-label-code uppercase tracking-wider text-outline dark:text-[#ccbeff] font-semibold text-[10px] truncate max-w-[200px] sm:max-w-[260px]">
                    {!! strip_tags(html_entity_decode((string) $tableSource, ENT_QUOTES | ENT_HTML5, 'UTF-8')) !!}
                </span>
            @endif

            <h3 class="font-quicksand font-bold text-on-surface dark:text-[#f4f0ea] text-sm sm:text-base leading-snug">
                {!! strip_tags(html_entity_decode((string) $title, ENT_QUOTES | ENT_HTML5, 'UTF-8')) !!}
            </h3>

            @if ($subtitle)
                <p class="text-xs text-on-surface-variant dark:text-[#e6dfd5] leading-relaxed">
                    {{ $subtitle }}
                </p>
            @endif

            @if ($badge)
                <div class="pt-1">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full border text-[11px] font-label-code font-bold {{ $colorConfig['pill'] }}">
                        @if ($badgePulse)
                            <span class="w-1.5 h-1.5 rounded-full {{ $colorConfig['dot'] }} animate-pulse"></span>
                        @endif
                        <span>{{ $badge }}</span>
                    </span>
                </div>
            @endif
        </div>

        {{-- Icono de la Métrica con Microinteracción en Hover --}}
        @if ($icon)
            <div class="w-11 h-11 rounded-xl {{ $iconBg }} flex items-center justify-center text-white shadow-2xs shrink-0 group-hover:scale-105 group-hover:rotate-1 transition-transform duration-300">
                <span class="material-symbols-outlined text-[24px]">{{ $icon }}</span>
            </div>
        @endif
    </div>

    {{-- Cuerpo Principal: Cifra Numérica de Alto Contraste y Tendencia --}}
    <div class="relative z-10 mt-5 pt-1">
        <div class="font-quicksand font-bold text-3xl sm:text-4xl text-primary dark:text-[#fdfbf7] tracking-tight font-tabular-data flex items-baseline gap-2">
            <span>{{ $value }}</span>
            @if ($unit)
                <span class="font-label-md font-medium text-xs sm:text-sm text-on-surface-variant dark:text-[#e6dfd5] tracking-normal">
                    {{ $unit }}
                </span>
            @endif
        </div>

        @if ($trend)
            <div class="flex items-center gap-2 mt-2.5 flex-wrap">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-label-code font-bold border {{ $isPositive ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700/60' : 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border-rose-200 dark:border-rose-700/60' }}">
                    <span class="material-symbols-outlined text-[13px]">{{ $isPositive ? 'trending_up' : 'trending_down' }}</span>
                    <span>{{ $trend }}</span>
                </span>
                @if ($trendLabel)
                    <span class="font-label-code text-xs text-outline dark:text-[#d2bbff]">
                        {{ $trendLabel }}
                    </span>
                @endif
            </div>
        @endif
    </div>

    {{-- Pie de Tarjeta: Resumen Secundario o Microinteracción de Botón/Acción --}}
    @if ($summaryLeft || $summaryRight || ($actionUrl && $actionText))
        <div class="relative z-10 mt-5 pt-3 border-t border-purple-100 dark:border-[#5c4a78]/40 flex items-center justify-between text-xs font-label-code gap-2 flex-wrap">
            @if ($summaryLeft || $summaryRight)
                <div class="flex items-center justify-between w-full text-on-surface-variant dark:text-[#f4f0ea] font-medium">
                    <span>{{ $summaryLeft }}</span>
                    <span class="font-bold text-on-surface dark:text-[#fdfbf7]">{{ $summaryRight }}</span>
                </div>
            @endif

            @if ($actionUrl && $actionText)
                <div class="pt-1 w-full flex justify-end">
                    <a
                        href="{{ $actionUrl }}"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-surface-container-low dark:bg-[#221834] hover:bg-primary hover:text-white dark:hover:bg-primary text-primary dark:text-[#fdfbf7] font-bold text-[11px] uppercase tracking-wider transition-all duration-200 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary/40 shadow-2xs"
                    >
                        <span>{{ $actionText }}</span>
                        <span class="material-symbols-outlined text-[14px]">{{ $actionIcon }}</span>
                    </a>
                </div>
            @endif
        </div>
    @endif

    {{-- Slot Opcional para Contenido Personalizado --}}
    @if (isset($slot) && !empty(trim((string) $slot)))
        <div class="relative z-10 mt-4">
            {{ $slot }}
        </div>
    @endif
</article>
