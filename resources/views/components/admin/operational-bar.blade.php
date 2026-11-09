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

<section class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-space-md bg-surface-container-lowest p-space-lg rounded-xl border border-purple-200/70 shadow-xs mb-space-lg">
    
    <!-- Identificador de Sesión, Saludo y Sede Activa -->
    <div class="flex flex-col">
        <div class="flex flex-wrap items-center gap-space-xs mb-1">
            <span class="font-label-code uppercase tracking-wider text-primary font-bold text-[11px]">Terminal de Control Maestro</span>
            <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
            <span class="font-tabular-data text-on-surface-variant font-medium text-xs">SESIÓN ID: {{ $sessionId }}</span>
            <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
            <span class="px-2 py-0.5 rounded bg-purple-100 text-primary font-label-code text-[10px] font-bold uppercase tracking-wider">
                {{ $sucursalActiva ? 'SEDE: '.$sucursalActiva->nombre : 'VISTA GLOBAL · TODAS LAS SEDES' }}
            </span>
        </div>
        <h1 class="font-headline-lg text-2xl sm:text-3xl text-on-surface tracking-tight font-bold">Bienvenido, {{ $curatorName }}</h1>
        <p class="font-title-editorial text-on-surface-variant text-sm italic">{{ $curatorRole }}</p>
    </div>

    <!-- Filtros de Período & Selector Síncrono de Sucursal -->
    <div class="flex flex-wrap items-center gap-space-sm w-full lg:w-auto">
        
        <!-- Filtro Rápido de Rango de Fechas (Hoy, 7D, 30D, Año 2026) integrado con la Sucursal Activa -->
        <div class="flex items-center bg-surface-container-low p-1 rounded-lg border border-purple-200/70" role="group" aria-label="Filtro de temporalidad">
            @foreach ([
                'hoy' => 'Hoy',
                '7d' => '7D',
                '30d' => '30D',
                '2026' => 'Año 2026',
            ] as $codigoPeriodo => $etiquetaPeriodo)
                <a
                    href="{{ route('admin.dashboard', array_filter(['sucursal_id' => $sucursalParam, 'periodo' => $codigoPeriodo])) }}"
                    class="px-3 py-1.5 rounded font-label-sm transition {{ $activePeriod === $codigoPeriodo ? 'bg-primary-container text-white shadow-xs font-semibold' : 'text-on-surface-variant hover:text-on-surface' }}"
                >
                    {{ $etiquetaPeriodo }}
                </a>
            @endforeach
        </div>

        <!-- Selector Síncrono de Sucursales para Métricas del Dashboard -->
        <form method="GET" action="{{ route('admin.dashboard') }}" class="relative bg-surface-container-low border border-purple-200/70 rounded-lg px-3 py-1.5 flex items-center gap-2">
            <input type="hidden" name="periodo" value="{{ $activePeriod }}" />
            <span class="material-symbols-outlined text-[18px] text-primary">storefront</span>
            <label for="kpi-branch-select" class="sr-only">Filtrar métricas por sucursal</label>
            <select
                id="kpi-branch-select"
                name="sucursal_id"
                onchange="this.form.submit()"
                class="bg-transparent font-label-md text-on-surface font-semibold focus:outline-none cursor-pointer pr-4 text-xs"
            >
                @if ($puedeVerGlobal)
                    <option value="all" @selected($sucursalActiva === null)>
                        Todas las Sedes ({{ $sucursales->count() ?: 3 }})
                    </option>
                @endif
                @forelse ($sucursales as $sucursal)
                    <option value="{{ $sucursal->id }}" @selected($sucursalActiva?->id === $sucursal->id)>
                        {{ $sucursal->nombre }}
                    </option>
                @empty
                    <option value="central">Sucursal Central Bellas Artes</option>
                    <option value="coyoacan">Sucursal Coyoacán</option>
                    <option value="almacen">Almacén General</option>
                @endforelse
            </select>
        </form>

        <!-- Botón de Sincronización en Tiempo Real -->
        <a
            href="{{ route('admin.dashboard', array_filter(['sucursal_id' => $sucursalParam, 'periodo' => $activePeriod])) }}"
            class="p-2 rounded-lg bg-surface-container-low hover:bg-surface-container border border-purple-200/70 text-primary transition-colors flex items-center justify-center shadow-xs"
            title="Sincronizar base de datos de existencias"
        >
            <span class="material-symbols-outlined text-[20px]">sync</span>
        </a>
    </div>

</section>
