@props([
    'tableSource' => 'pedidos_table & pagos_table',
    'title' => 'Ventas Totales del Mes',
    'value' => '$142,850',
    'currency' => 'MXN',
    'trend' => '+12.4%',
    'isPositive' => true,
    'icon' => 'payments',
    'iconBg' => 'bg-primary-container',
    'summaryLeft' => 'Transacciones: 418',
    'summaryRight' => 'Ticket Prom: $341 MXN'
])

<div class="bg-surface-container-lowest p-space-md rounded-xl border border-purple-200/70 flex flex-col justify-between hover:border-purple-300 hover:shadow-md transition-all shadow-xs">
    
    <!-- Fila Superior: Origen Técnico y Título -->
    <div class="flex items-start justify-between">
        <div class="flex flex-col">
            <span class="font-label-code uppercase tracking-wider text-outline font-semibold text-[10px]">{{ $tableSource }}</span>
            <span class="font-label-md text-on-surface mt-1 font-semibold text-sm">{{ $title }}</span>
        </div>
        <div class="w-10 h-10 rounded-lg {{ $iconBg }} flex items-center justify-center text-white shadow-2xs shrink-0">
            <span class="material-symbols-outlined text-[22px]">{{ $icon }}</span>
        </div>
    </div>

    <!-- Valor Principal y Tendencia -->
    <div class="mt-space-md">
        <div class="font-headline-lg text-2xl lg:text-3xl text-primary tracking-tight font-bold">
            {{ $value }} 
            @if($currency)
                <span class="font-label-sm font-normal text-on-surface-variant text-xs">{{ $currency }}</span>
            @endif
        </div>
        
        <div class="flex items-center gap-space-xs mt-2">
            <span class="px-2 py-0.5 rounded-full {{ $isPositive ? 'bg-emerald-100 text-emerald-800' : 'bg-error-container text-error' }} font-label-code text-[11px] flex items-center gap-1 font-semibold">
                <span class="material-symbols-outlined text-[13px]">{{ $isPositive ? 'trending_up' : 'trending_down' }}</span>
                {{ $trend }}
            </span>
            <span class="font-tabular-data text-outline font-medium text-xs">vs período anterior</span>
        </div>
    </div>

    <!-- Barra Inferior de Métricas Secundarias -->
    <div class="mt-space-sm pt-space-xs bg-surface-container-low rounded border border-purple-100 px-2.5 py-1.5 flex justify-between font-label-code text-on-surface font-semibold text-xs">
        <span>{{ $summaryLeft }}</span>
        <span>{{ $summaryRight }}</span>
    </div>

</div>
