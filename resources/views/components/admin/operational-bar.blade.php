@props([
    'sessionId' => '#ADM-8841-MX',
    'curatorName' => 'Mateo Vargas',
    'curatorRole' => 'Administrador General · Sistema de Catalogación & Logística de Anticuario',
    'activePeriod' => '30d',
    'sucursales' => collect(),
    'sucursalActiva' => null,
    'puedeVerGlobal' => true,
])

@php
    $sucursalParam = $sucursalActiva ? $sucursalActiva->id : ($puedeVerGlobal ? 'all' : null);
@endphp

<section class="bento-reveal bento-delay-0 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-space-md bg-white/90 dark:bg-[#1a1626]/90 backdrop-blur-md p-space-lg rounded-2xl border border-purple-200/70 dark:border-white/10 shadow-xs mb-space-lg transition-all duration-300">
    
    <!-- Identificador de Sesión, Saludo y Sede Activa -->
    <div class="flex flex-col">
        <div class="flex flex-wrap items-center gap-space-xs mb-1">
            <span class="font-label-code uppercase tracking-wider text-primary dark:text-[#ccbeff] font-bold text-[11px]">Terminal de Control Maestro</span>
            <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
            <span class="font-tabular-data text-on-surface-variant dark:text-[#e6dfd5] font-medium text-xs">SESIÓN ID: {{ $sessionId }}</span>
            <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
            <span class="px-2 py-0.5 rounded bg-purple-100 dark:bg-purple-950/60 text-primary dark:text-[#ccbeff] border border-purple-200 dark:border-purple-800/50 font-label-code text-[10px] font-bold uppercase tracking-wider">
                {{ $sucursalActiva ? 'SEDE: '.$sucursalActiva->nombre : 'VISTA GLOBAL · TODAS LAS SEDES' }}
            </span>
        </div>
        <h1 class="font-headline-lg text-2xl sm:text-3xl text-on-surface dark:text-[#fdfbf7] tracking-tight font-bold">Bienvenido, {{ $curatorName }}</h1>
        <p class="font-title-editorial text-on-surface-variant dark:text-[#e6dfd5] text-sm italic">{{ $curatorRole }}</p>
    </div>

    <!-- Filtros de Período & Selector Síncrono de Sucursal -->
    <div class="flex flex-wrap items-center gap-space-sm w-full lg:w-auto">
        
        <!-- Filtro Rápido de Rango de Fechas (Hoy, 7D, 30D, Año 2026) integrado con la Sucursal Activa -->
        <div class="flex items-center gap-1.5 p-1 rounded-2xl bg-surface-container-low/80 dark:bg-[#12101a]/70 border border-purple-200/70 dark:border-white/10" role="group" aria-label="Filtro de temporalidad">
            @foreach ([
                'hoy' => 'Hoy',
                '7d' => '7D',
                '30d' => '30D',
                '2026' => 'Año 2026',
            ] as $codigoPeriodo => $etiquetaPeriodo)
                <a
                    href="{{ route('admin.dashboard', array_filter(['sucursal_id' => $sucursalParam, 'periodo' => $codigoPeriodo])) }}"
                    class="px-3.5 py-1.5 rounded-xl font-label-sm text-xs transition-all {{ $activePeriod === $codigoPeriodo ? 'btn-glass-purple text-white shadow-xs font-bold' : 'btn-glass-surface font-medium' }}"
                >
                    <span>{{ $etiquetaPeriodo }}</span>
                </a>
            @endforeach
        </div>

        <!-- Selector Síncrono de Sucursales para Métricas del Dashboard -->
        <form method="GET" action="{{ route('admin.dashboard') }}" class="relative bg-surface-container-low dark:bg-[#12101a]/70 border border-purple-200/70 dark:border-white/10 rounded-xl px-3 py-1.5 flex items-center gap-2 transition-all duration-200 hover:border-primary/40 focus-within:ring-2 focus-within:ring-primary/30">
            <input type="hidden" name="periodo" value="{{ $activePeriod }}" />
            <span class="material-symbols-outlined text-[18px] text-primary dark:text-[#ccbeff]">storefront</span>
            <label for="kpi-branch-select" class="sr-only">Filtrar métricas por sucursal</label>
            <select
                id="kpi-branch-select"
                name="sucursal_id"
                onchange="this.form.submit()"
                class="bg-transparent font-label-md text-on-surface dark:text-[#fdfbf7] font-semibold focus:outline-none cursor-pointer pr-4 text-xs transition-colors"
            >
                @if ($puedeVerGlobal)
                    <option value="all" @selected($sucursalActiva === null) class="dark:bg-[#1a1626]">
                        Todas las Sedes ({{ $sucursales->count() ?: 3 }})
                    </option>
                @endif
                @forelse ($sucursales as $sucursal)
                    <option value="{{ $sucursal->id }}" @selected($sucursalActiva?->id === $sucursal->id) class="dark:bg-[#1a1626]">
                        {{ $sucursal->nombre }}
                    </option>
                @empty
                    <option value="central" class="dark:bg-[#1a1626]">Sucursal Central Bellas Artes</option>
                    <option value="coyoacan" class="dark:bg-[#1a1626]">Sucursal Coyoacán</option>
                    <option value="almacen" class="dark:bg-[#1a1626]">Almacén General</option>
                @endforelse
            </select>
        </form>

        <!-- Botón de Sincronización en Tiempo Real -->
        <a
            href="{{ route('admin.dashboard', array_filter(['sucursal_id' => $sucursalParam, 'periodo' => $activePeriod])) }}"
            class="btn-glass-surface p-2 rounded-xl text-primary dark:text-[#ccbeff] flex items-center justify-center shadow-xs"
            title="Sincronizar base de datos de existencias"
        >
            <span class="material-symbols-outlined text-[20px]">sync</span>
        </a>
    </div>

</section>
