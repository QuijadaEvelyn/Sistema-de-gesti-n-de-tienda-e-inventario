@props([
    'tableSource' => 'pedidos_table & pagos_table',
    'title' => 'Ventas Totales del Mes',
    'value' => '$142,850',
    'numericValue' => null,
    'currency' => 'MXN',
    'trend' => '+12.4%',
    'isPositive' => true,
    'icon' => 'payments',
    'iconBg' => 'bg-primary-container',
    'summaryLeft' => 'Transacciones: 418',
    'summaryRight' => 'Ticket Prom: $341 MXN',
    'badge' => null,
    'badgePulse' => true,
    'badgeColor' => 'emerald',
    'actionUrl' => null,
    'actionText' => null,
    'bentoDelay' => null,
])

@php
    $rawVal = (string) $value;
    $hasDollar = str_starts_with(trim($rawVal), '$');
    $cleanVal = preg_replace('/[^\d.]/', '', str_replace(',', '', $rawVal));
    $targetNum = $numericValue !== null ? (float) $numericValue : (is_numeric($cleanVal) ? (float) $cleanVal : 0);
    $decimals = (str_contains($cleanVal, '.') && strlen(substr(strrchr($cleanVal, '.'), 1)) > 0)
        ? strlen(substr(strrchr($cleanVal, '.'), 1))
        : 0;
    $prefix = $hasDollar ? '$' : '';
@endphp

<div
    {{ $attributes->merge([
        'class' => 'group relative overflow-hidden w-full max-w-full box-border p-4 rounded-2xl flex flex-col justify-between ' .
                   'bg-white/80 dark:bg-[#161329]/80 backdrop-blur-xl ' .
                   'border border-purple-200/60 hover:border-primary/50 dark:border-white/12 dark:hover:border-[#9d7bff]/60 ' .
                   'hover:-translate-y-1.5 transition-all duration-300 ease-out ' .
                   'hover:shadow-[0_12px_28px_-8px_rgba(91,59,179,0.16)] dark:hover:shadow-[0_12px_30px_-8px_rgba(139,92,246,0.28)] ' .
                   'shadow-[0_8px_32px_0_rgba(91,59,179,0.06)] dark:shadow-[0_8px_32px_0_rgba(0,0,0,0.35)] bento-reveal'
    ]) }}
    @if ($bentoDelay !== null) style="--bento-delay: {{ $bentoDelay }}ms;" @endif
    data-purpose="fluid-kpi-card"
>

    {{-- Acento Luminoso Superior: Brillo degradado violeta en hover --}}
    <div
        class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-primary dark:via-[#9d7bff] to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
        aria-hidden="true"
    ></div>

    {{-- Resplandor ambiental suave en hover --}}
    <div
        class="absolute -right-10 -top-10 w-28 h-28 bg-primary/5 dark:bg-purple-500/10 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
        aria-hidden="true"
    ></div>

    <!-- Fila Superior: Origen Técnico y Título -->
    <div class="relative z-10 flex items-start justify-between gap-2.5">
        <div class="flex flex-col space-y-0.5 min-w-0">
            <span class="font-label-code uppercase tracking-wider text-outline dark:text-[#ccbeff] font-semibold text-[10px] truncate max-w-[180px] sm:max-w-[220px]">
                {!! strip_tags(html_entity_decode((string) $tableSource, ENT_QUOTES | ENT_HTML5, 'UTF-8')) !!}
            </span>
            <span class="font-quicksand text-on-surface dark:text-[#f4f0ea] font-bold text-sm leading-snug truncate">
                {!! strip_tags(html_entity_decode((string) $title, ENT_QUOTES | ENT_HTML5, 'UTF-8')) !!}
            </span>
            @if ($badge)
                <div class="pt-0.5">
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700/60 text-[10px] font-label-code font-bold">
                        @if ($badgePulse)
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        @endif
                        <span>{{ $badge }}</span>
                    </span>
                </div>
            @endif
        </div>
        <div class="w-9 h-9 rounded-xl {{ $iconBg }} flex items-center justify-center text-white shadow-2xs shrink-0 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
            <span class="material-symbols-outlined text-[20px]">{{ $icon }}</span>
        </div>
    </div>

    <!-- Valor Principal y Tendencia de Alto Contraste con Count-Up -->
    <div class="relative z-10 mt-3 pt-0.5">
        <div class="font-quicksand text-xl lg:text-2xl text-primary dark:text-[#fdfbf7] tracking-tight font-bold font-tabular-data flex items-baseline gap-1.5">
            <span
                data-countup="{{ $targetNum }}"
                data-prefix="{{ $prefix }}"
                data-decimals="{{ $decimals }}"
            >{{ $value }}</span>
            @if ($currency)
                <span class="font-label-sm font-normal text-on-surface-variant dark:text-[#e6dfd5] text-[11px]">
                    {{ $currency }}
                </span>
            @endif
        </div>

        <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
            <span class="px-2 py-0.5 rounded-full {{ $isPositive ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700/60' : 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-700/60' }} font-label-code text-[10px] flex items-center gap-1 font-bold">
                <span class="material-symbols-outlined text-[12px]">{{ $isPositive ? 'trending_up' : 'trending_down' }}</span>
                <span>{{ $trend }}</span>
            </span>
            <span class="font-tabular-data text-outline dark:text-[#d2bbff] font-medium text-[11px]">
                vs período anterior
            </span>
        </div>
    </div>

    <!-- Barra Inferior de Métricas Secundarias -->
    <div class="relative z-10 mt-3 pt-1">
        <div class="bg-surface-container-low dark:bg-[#12101a]/70 rounded-xl border border-purple-100 dark:border-white/10 px-2.5 py-1.5 flex items-center justify-between font-label-code text-on-surface dark:text-[#f4f0ea] font-semibold text-[11px]">
            <span class="truncate">{{ $summaryLeft }}</span>
            <span class="text-on-surface dark:text-[#fdfbf7] font-bold shrink-0 ml-1">{{ $summaryRight }}</span>
        </div>

        @if ($actionUrl && $actionText)
            <div class="pt-1.5 flex justify-end">
                <a
                    href="{{ $actionUrl }}"
                    class="inline-flex items-center gap-1 text-[10px] font-label-code font-bold uppercase tracking-wider text-primary dark:text-[#9d7bff] hover:underline active:scale-95 transition-transform"
                >
                    <span>{{ $actionText }}</span>
                    <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                </a>
            </div>
        @endif
    </div>

</div>
