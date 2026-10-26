@props([
    'sessionId' => '#ADM-8841-MX',
    'curatorName' => 'Mateo Vargas',
    'curatorRole' => 'Administrador General · Sistema de Catalogación & Logística de Anticuario',
    'activePeriod' => '30d'
])

<section class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-space-md bg-surface-container-lowest p-space-lg rounded-xl border border-purple-200/70 shadow-xs mb-space-lg">
    
    <!-- Identificador de Sesión y Saludo -->
    <div class="flex flex-col">
        <div class="flex items-center gap-space-xs mb-1">
            <span class="font-label-code uppercase tracking-wider text-primary font-bold text-[11px]">Terminal de Control Maestro</span>
            <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
            <span class="font-tabular-data text-on-surface-variant font-medium text-xs">SESIÓN ID: {{ $sessionId }}</span>
        </div>
        <h1 class="font-headline-lg text-2xl sm:text-3xl text-on-surface tracking-tight font-bold">Bienvenido, {{ $curatorName }}</h1>
        <p class="font-title-editorial text-on-surface-variant text-sm italic">{{ $curatorRole }}</p>
    </div>

    <!-- Filtros de Período & Acciones de Sincronización -->
    <div class="flex flex-wrap items-center gap-space-sm w-full lg:w-auto">
        
        <!-- Filtro Rápido de Rango de Fechas -->
        <div class="flex items-center bg-surface-container-low p-1 rounded-lg border border-purple-200/70">
            <button type="button" class="px-3 py-1.5 rounded font-label-sm {{ $activePeriod === 'hoy' ? 'bg-primary-container text-white shadow-xs font-semibold' : 'text-on-surface-variant hover:text-on-surface' }}">
                Hoy
            </button>
            <button type="button" class="px-3 py-1.5 rounded font-label-sm {{ $activePeriod === '7d' ? 'bg-primary-container text-white shadow-xs font-semibold' : 'text-on-surface-variant hover:text-on-surface' }}">
                7D
            </button>
            <button type="button" class="px-3 py-1.5 rounded font-label-sm {{ $activePeriod === '30d' ? 'bg-primary-container text-white shadow-xs font-semibold' : 'text-on-surface-variant hover:text-on-surface' }}">
                30D
            </button>
            <button type="button" class="px-3 py-1.5 rounded font-label-sm {{ $activePeriod === '2026' ? 'bg-primary-container text-white shadow-xs font-semibold' : 'text-on-surface-variant hover:text-on-surface' }}">
                Año 2026
            </button>
        </div>

        <!-- Selector de Sucursales para Métricas -->
        <div class="relative bg-surface-container-low border border-purple-200/70 rounded-lg px-3 py-1.5 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px] text-primary">storefront</span>
            <select class="bg-transparent font-label-md text-on-surface font-semibold focus:outline-none cursor-pointer pr-4 text-xs" id="kpi-branch-select">
                <option value="all">Todas las Sedes (3)</option>
                <option value="centro">Sucursal Bellas Artes</option>
                <option value="coyoacan">Gabinete Coyoacán</option>
                <option value="almacen">Almacén Central</option>
            </select>
        </div>

        <!-- Botón de Sincronización en Tiempo Real -->
        <button type="button" class="p-2 rounded-lg bg-surface-container-low hover:bg-surface-container border border-purple-200/70 text-primary transition-colors flex items-center justify-center shadow-xs" title="Sincronizar base de datos de existencias">
            <span class="material-symbols-outlined text-[20px]">sync</span>
        </button>
    </div>

</section>
