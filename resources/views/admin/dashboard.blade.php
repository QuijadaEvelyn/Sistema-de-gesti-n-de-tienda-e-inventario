@php
    $nombreOperador = $usuario?->nombre ?? 'Mateo Vargas';
    $rolOperador = strtoupper($usuario?->roles?->pluck('nombre')->implode(' · ') ?: 'Curador en Jefe');
    $codigoPeriodo = $periodo['codigo'] ?? '30d';
    $etiquetaPeriodo = $periodo['etiqueta'] ?? 'ÚLTIMOS 30 DÍAS';
    $etiquetaSede = $sucursalActiva ? $sucursalActiva->nombre : 'Todas las Sedes (Vista Global)';
    $sucursalFiltroExport = $sucursalActiva ? ['sucursal_id' => $sucursalActiva->id] : [];
    $parametrosSedeActiva = $sucursalActiva
        ? ['sucursal_id' => $sucursalActiva->id]
        : ['sucursal_id' => 'all'];

    $ingresosEstimados = (float) ($metricas['ingresos_estimados'] ?? 0);
    $ventasConfirmadas = (float) ($metricas['ventas_confirmadas'] ?? 0);
    $volumenPedidos = (int) ($metricas['volumen_pedidos'] ?? 0);
    $ticketPromedio = (float) ($metricas['ticket_promedio'] ?? 0);

    $unidadesEnCustodia = (int) ($metricas['unidades_en_custodia'] ?? 0);
    $totalSkus = (int) ($metricas['total_skus'] ?? 0);
    $alertasBajoStock = (int) ($metricas['alertas_bajo_stock'] ?? 0);
    $valoracionInventario = (float) ($metricas['valoracion_inventario'] ?? 0);

    $enviosActivos = (int) ($metricas['envios_activos'] ?? 0);
    $reservasActivas = (int) ($metricas['reservas_activas'] ?? 0);
    $movimientosCount = (int) ($metricas['movimientos_count'] ?? 0);

    $totalCoa = (int) ($metricas['total_coa'] ?? 0);
    $coaDisponibles = (int) ($metricas['coa_disponibles'] ?? 0);
    $coaComprometidos = (int) ($metricas['coa_comprometidos'] ?? 0);

    $seriesJson = json_encode($seriesAnaliticas ?? []);
@endphp

<x-layouts.admin title="ARCHIVUM · Terminal de Control Maestro" active-route="dashboard">

    <!-- Barra Operativa Superior: Saludo, Sesión, Filtros de Temporalidad y Selector de Sucursal -->
    <x-admin.operational-bar 
        session-id="#ADM-8841-MX"
        :curator-name="$nombreOperador"
        :curator-role="$rolOperador.' · Sistema de Catalogación & Logística de Anticuario'"
        :active-period="$codigoPeriodo"
        :sucursales="$sucursales ?? collect()"
        :sucursal-activa="$sucursalActiva ?? null"
        :puede-ver-global="$puedeVerGlobal ?? true"
    />

    <!-- =========================================================================
         FILA SUPERIOR COMPACTA: 3 COLUMNAS KPI EN UNA SOLA FILA
         Agrupa las 3 tarjetas KPI: Ventas & Ingresos Estimados, Piezas en Custodia y Dictamen Notarial COA
         ========================================================================= -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full mb-4" id="dashboard-summary-grid">
        
        <!-- KPI 1: Ventas & Ingresos Estimados -->
        <x-ui.kpi-card 
            table-source="pedidos_table & pagos_table ({{ $etiquetaPeriodo }})"
            title="Ventas &amp; Ingresos Estimados"
            :value="'$'.number_format($ingresosEstimados, 0)"
            currency="MXN"
            :trend="'$'.number_format($ventasConfirmadas, 0).' cobrados'"
            :is-positive="true"
            badge="Transacciones en Línea"
            :badge-pulse="true"
            badge-color="emerald"
            icon="payments"
            icon-bg="bg-primary-container"
            :summary-left="'Pedidos ('.$codigoPeriodo.'): '.$volumenPedidos"
            :summary-right="'Ticket: $'.number_format($ticketPromedio, 0).' MXN'"
            :bento-delay="100"
            class="w-full p-4"
        />

        <!-- KPI 2: Piezas en Custodia -->
        <x-ui.kpi-card 
            table-source="existencias_table ({{ $sucursalActiva ? $sucursalActiva->nombre : 'Global' }})"
            title="Piezas en Custodia"
            :value="number_format($unidadesEnCustodia)"
            currency="unidades en bóveda"
            :trend="$alertasBajoStock.' bajo stock'"
            :is-positive="$alertasBajoStock === 0"
            badge="Bóveda Abierta"
            :badge-pulse="true"
            badge-color="purple"
            icon="shelves"
            icon-bg="bg-secondary-container text-on-secondary-container"
            :summary-left="'Valoración: $'.number_format($valoracionInventario, 0)"
            :summary-right="'SKUs en sede: '.$totalSkus"
            :bento-delay="140"
            class="w-full p-4"
        />

        <!-- KPI 3: Dictamen Notarial COA -->
        <x-ui.kpi-card 
            table-source="piezas_unicas ({{ $sucursalActiva ? $sucursalActiva->nombre : 'Global' }})"
            title="Dictamen Notarial COA"
            :value="(string) $totalCoa"
            currency="series certificadas"
            :trend="$coaDisponibles.' disponibles'"
            :is-positive="true"
            badge="Certificación Vigente"
            :badge-pulse="true"
            badge-color="purple"
            icon="verified"
            icon-bg="bg-secondary-container text-on-secondary-container"
            :summary-left="'Disponibles: '.$coaDisponibles"
            :summary-right="'Comprometidos: '.$coaComprometidos"
            :bento-delay="180"
            class="w-full p-4"
        />

    </section>

    <!-- =========================================================================
         GRÁFICA ANALÍTICA PRINCIPAL: FLUJO DE BÓVEDA (CHART.JS)
         Subida inmediatamente debajo de la fila de 3 KPIs
         ========================================================================= -->
    <section class="bento-reveal bento-delay-140 relative w-full max-w-full box-border overflow-hidden p-4 sm:p-5 rounded-2xl bg-white/80 dark:bg-[#161329]/80 backdrop-blur-xl border border-purple-200/60 dark:border-white/12 shadow-[0_8px_32px_0_rgba(91,59,179,0.06)] dark:shadow-[0_8px_32px_0_rgba(0,0,0,0.35)] mb-4 transition-all duration-300">
        
        <!-- Cabecera de la Gráfica: Título, Filtro Interactivo de Período y Mini-KPIs Dinámicos -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3 border-b border-purple-200/60 dark:border-white/10">
            <div class="space-y-0.5 min-w-0">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-primary/10 dark:bg-purple-500/20 text-primary dark:text-[#ccbeff] shrink-0">
                        <span class="material-symbols-outlined text-[18px]">query_stats</span>
                    </span>
                    <h2 class="text-sm sm:text-base font-bold text-on-surface dark:text-[#fdfbf7] font-quicksand flex items-center gap-2 flex-wrap">
                        <span>Flujo de Bóveda &amp; Rendimiento Curatorial</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-purple-100 dark:bg-purple-950/80 text-purple-900 dark:text-purple-200 font-label-code font-bold uppercase tracking-wider">
                            {{ $etiquetaSede }}
                        </span>
                    </h2>
                </div>
                <p class="text-xs text-on-surface-variant dark:text-[#e6dfd5] font-title-editorial italic">
                    «Monitoreo de ingresos comerciales consolidados y volumen de órdenes de coleccionismo con proyección de curva suave.»
                </p>
            </div>

            <!-- Selector de Rango Temporal Interactivo para Chart.js -->
            <div class="flex items-center gap-1 p-1 bg-surface-container-low/70 dark:bg-black/30 backdrop-blur-md rounded-xl border border-purple-200/60 dark:border-white/10 shrink-0 self-start lg:self-auto">
                <button
                    type="button"
                    class="chart-range-pill px-2.5 py-1 rounded-lg text-[11px] font-label-code font-bold uppercase tracking-wider transition-all cursor-pointer {{ ($codigoPeriodo === '7d') ? 'bg-primary text-white shadow-xs' : 'text-on-surface-variant hover:text-on-surface hover:bg-white/40 dark:hover:bg-white/5' }}"
                    data-range="7d"
                >
                    7 DÍAS
                </button>
                <button
                    type="button"
                    class="chart-range-pill px-2.5 py-1 rounded-lg text-[11px] font-label-code font-bold uppercase tracking-wider transition-all cursor-pointer {{ ($codigoPeriodo === '30d' || !in_array($codigoPeriodo, ['7d', '2026'])) ? 'bg-primary text-white shadow-xs' : 'text-on-surface-variant hover:text-on-surface hover:bg-white/40 dark:hover:bg-white/5' }}"
                    data-range="30d"
                >
                    30 DÍAS
                </button>
                <button
                    type="button"
                    class="chart-range-pill px-2.5 py-1 rounded-lg text-[11px] font-label-code font-bold uppercase tracking-wider transition-all cursor-pointer {{ ($codigoPeriodo === '2026') ? 'bg-primary text-white shadow-xs' : 'text-on-surface-variant hover:text-on-surface hover:bg-white/40 dark:hover:bg-white/5' }}"
                    data-range="2026"
                >
                    AÑO 2026
                </button>
            </div>
        </div>

        <!-- Mini-Tarjetas de Métricas Rápidas Sincronizadas con la Gráfica (Los 4 KPIs internos) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 pt-3 pb-1 w-full box-border">
            <div class="p-2.5 sm:p-3 rounded-xl bg-surface-container-low/50 dark:bg-white/5 border border-purple-200/40 dark:border-white/5 backdrop-blur-sm min-w-0 overflow-hidden">
                <span class="block text-[10px] font-label-code uppercase tracking-wider text-on-surface-variant dark:text-[#ccbeff] truncate">Ingresos Totales</span>
                <div class="flex items-baseline gap-1.5 mt-0.5 truncate">
                    <span id="chart-kpi-ingresos" class="font-quicksand font-bold text-base sm:text-lg text-primary dark:text-[#fdfbf7] font-tabular-data truncate">
                        ${{ number_format($ventasConfirmadas, 0) }} MXN
                    </span>
                    <span id="chart-kpi-trend" class="text-[10px] font-label-code text-emerald-600 dark:text-emerald-400 font-bold shrink-0">+18.4%</span>
                </div>
            </div>

            <div class="p-2.5 sm:p-3 rounded-xl bg-surface-container-low/50 dark:bg-white/5 border border-purple-200/40 dark:border-white/5 backdrop-blur-sm min-w-0 overflow-hidden">
                <span class="block text-[10px] font-label-code uppercase tracking-wider text-on-surface-variant dark:text-[#ccbeff] truncate">Volumen de Órdenes</span>
                <div class="flex items-baseline gap-1.5 mt-0.5 truncate">
                    <span id="chart-kpi-pedidos" class="font-quicksand font-bold text-base sm:text-lg text-on-surface dark:text-[#fdfbf7] font-tabular-data truncate">
                        {{ $volumenPedidos }} órdenes
                    </span>
                    <span class="text-[10px] font-label-code text-secondary dark:text-purple-300 shrink-0">conf.</span>
                </div>
            </div>

            <div class="p-2.5 sm:p-3 rounded-xl bg-surface-container-low/50 dark:bg-white/5 border border-purple-200/40 dark:border-white/5 backdrop-blur-sm min-w-0 overflow-hidden">
                <span class="block text-[10px] font-label-code uppercase tracking-wider text-on-surface-variant dark:text-[#ccbeff] truncate">Ticket Promedio</span>
                <div class="flex items-baseline gap-1.5 mt-0.5 truncate">
                    <span id="chart-kpi-ticket" class="font-quicksand font-bold text-base sm:text-lg text-on-surface dark:text-[#fdfbf7] font-tabular-data truncate">
                        ${{ number_format($ticketPromedio, 0) }} MXN
                    </span>
                    <span class="text-[10px] font-label-code text-on-surface-variant dark:text-white/60 shrink-0">prom.</span>
                </div>
            </div>

            <div class="p-2.5 sm:p-3 rounded-xl bg-surface-container-low/50 dark:bg-white/5 border border-purple-200/40 dark:border-white/5 backdrop-blur-sm min-w-0 overflow-hidden">
                <span class="block text-[10px] font-label-code uppercase tracking-wider text-on-surface-variant dark:text-[#ccbeff] truncate">Valoración en Custodia</span>
                <div class="flex items-baseline gap-1.5 mt-0.5 truncate">
                    <span class="font-quicksand font-bold text-base sm:text-lg text-secondary dark:text-purple-200 font-tabular-data truncate">
                        ${{ number_format($valoracionInventario, 0) }} MXN
                    </span>
                    <span class="text-[10px] font-label-code text-emerald-600 dark:text-emerald-400 font-bold shrink-0">Activo</span>
                </div>
            </div>
        </div>

        <!-- Contenedor del Lienzo de Chart.js con Altura Reducida h-48 sm:h-52 y w-full relative -->
        <div class="relative w-full h-48 sm:h-52 overflow-hidden mt-2">
            <canvas id="archivumPerformanceChart" class="w-full h-full"></canvas>
        </div>
    </section>

    <!-- Acceso Rápido a Reportes Calculados y Exportación (CSV / PDF) Sincronizado con la Sede Activa -->
    <div class="bento-reveal bento-delay-200 mb-5 bg-white/80 dark:bg-[#161329]/80 backdrop-blur-xl rounded-2xl border border-purple-200/60 dark:border-white/12 p-3.5 sm:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-[0_8px_32px_0_rgba(91,59,179,0.06)] dark:shadow-[0_8px_32px_0_rgba(0,0,0,0.35)] transition-all duration-300">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-primary/10 dark:bg-purple-500/15 flex items-center justify-center text-primary dark:text-[#ccbeff] shrink-0 shadow-inner">
                <span class="material-symbols-outlined text-xl">analytics</span>
            </div>
            <div>
                <h2 class="text-xs sm:text-sm font-bold text-on-surface dark:text-[#fdfbf7] font-quicksand">
                    Módulo de Reportes Oficiales &amp; Exportación de Acervo ({{ $etiquetaSede }})
                </h2>
                <p class="text-[11px] text-on-surface-variant dark:text-[#e6dfd5]">
                    Genera resúmenes calculados de inventario en bóveda, lotes certificados COA y descargas directas en CSV y PDF para la sede y período activos.
                </p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.reportes', $sucursalFiltroExport) }}" class="btn-glass-surface px-3 py-1.5 rounded-xl text-xs font-label-md flex items-center gap-1.5 shadow-xs">
                <span class="material-symbols-outlined text-[15px]">monitoring</span>
                <span>Ver Reportes Calculados</span>
            </a>
            <a href="{{ route('admin.reportes.csv', $sucursalFiltroExport) }}" class="btn-glass-surface px-3 py-1.5 rounded-xl text-xs font-label-md flex items-center gap-1.5 shadow-xs">
                <span class="material-symbols-outlined text-[15px]">table_view</span>
                <span>Exportar CSV</span>
            </a>
            <a href="{{ route('admin.reportes.pdf', $sucursalFiltroExport) }}" class="btn-glass-purple px-3.5 py-1.5 rounded-xl text-white text-xs font-label-md flex items-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-[15px]">picture_as_pdf</span>
                <span>Exportar PDF Oficial</span>
            </a>
        </div>
    </div>

    <!-- Barra de Accesos Rápidos de Archivo -->
    <div class="bento-reveal bento-delay-340 flex flex-wrap items-center justify-between gap-4 p-space-md bg-white/80 dark:bg-[#161329]/80 backdrop-blur-xl border border-purple-200/60 dark:border-white/12 rounded-2xl mb-6 shadow-[0_8px_32px_0_rgba(91,59,179,0.06)] dark:shadow-[0_8px_32px_0_rgba(0,0,0,0.35)] transition-all duration-300">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary dark:text-[#ccbeff] text-[20px]">bolt</span>
            <span class="font-label-md text-on-surface dark:text-[#fdfbf7] font-semibold text-xs uppercase tracking-wider">Acciones Operativas en {{ $etiquetaSede }}:</span>
        </div>
        <div class="flex flex-wrap items-center gap-space-xs text-xs font-label-md">
            <a
                id="btn-gestionar-inventario-sede"
                href="{{ route('admin.inventario', $parametrosSedeActiva) }}"
                data-base-url="{{ route('admin.inventario') }}"
                data-sucursal-id="{{ $sucursalActiva?->id ?? 'all' }}"
                class="btn-glass-purple text-white px-3.5 py-2 rounded-xl flex items-center gap-1.5 cursor-pointer shadow-xs"
            >
                <span class="material-symbols-outlined text-[16px]">inventory_2</span>
                <span>Gestionar Inventario de Sede</span>
            </a>
            @if ($usuario && $usuario->tieneRol('administrador', 'almacen', 'encargado', 'compras', 'ventas'))
                <a
                    id="btn-ajustes-sede"
                    href="{{ route('almacen.ajustes', $parametrosSedeActiva) }}"
                    data-base-url="{{ route('almacen.ajustes') }}"
                    class="btn-glass-surface px-3.5 py-2 rounded-xl flex items-center gap-1.5 cursor-pointer shadow-xs"
                >
                    <span class="material-symbols-outlined text-[16px]">tune</span>
                    <span>Ajustes &amp; Recepciones</span>
                </a>
                <a
                    id="btn-transferencias-sede"
                    href="{{ route('almacen.transferencias', $parametrosSedeActiva) }}"
                    data-base-url="{{ route('almacen.transferencias') }}"
                    class="btn-glass-surface px-3.5 py-2 rounded-xl flex items-center gap-1.5 cursor-pointer shadow-xs"
                >
                    <span class="material-symbols-outlined text-[16px]">swap_horiz</span>
                    <span>Transferencias entre Sedes</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Tabla Maestra Dinámica de Existencias y Custodia Filtrada por Sucursal -->
    <section class="bento-reveal bento-delay-400 bg-white/80 dark:bg-[#161329]/80 backdrop-blur-xl border border-purple-200/60 dark:border-white/12 rounded-2xl shadow-[0_8px_32px_0_rgba(91,59,179,0.06)] dark:shadow-[0_8px_32px_0_rgba(0,0,0,0.35)] overflow-hidden mb-6 transition-all duration-300">
        
        <!-- Encabezado de la Tabla con Indicador de Filtro Activo (Sede + Temporalidad) -->
        <div class="p-space-lg border-b border-purple-200/60 dark:border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-headline-sm text-lg text-on-surface dark:text-[#fdfbf7] font-bold">
                    Existencias en Custodia &amp; Valoración Patrimonial — {{ $etiquetaSede }}
                </h2>
                <p class="font-title-editorial text-xs text-on-surface-variant dark:text-[#e6dfd5] italic mt-0.5">
                    Inventario filtrado estrictamente según la sede seleccionada y el rango temporal activo ({{ $etiquetaPeriodo }})
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="font-label-code text-secondary dark:text-purple-300 text-xs">Filtro Activo:</span>
                <span id="active-branch-filter-badge" class="px-2.5 py-1 rounded bg-purple-50 dark:bg-purple-950/70 text-primary dark:text-[#ccbeff] border border-purple-200 dark:border-purple-500/40 text-xs font-label-code font-bold uppercase">
                    {{ $sucursalActiva ? mb_strtoupper($sucursalActiva->nombre) : 'TODAS LAS SEDES' }} ({{ mb_strtoupper($codigoPeriodo) }})
                </span>
            </div>
        </div>

        <!-- Tabla Responsiva de Piezas en la Sucursal Seleccionada -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-purple-50/70 dark:bg-purple-950/40 backdrop-blur-sm border-b border-purple-200/70 dark:border-white/10 text-secondary dark:text-purple-200 font-label-md uppercase tracking-wider font-bold">
                    <tr>
                        <th class="py-3 px-4 font-semibold">SKU / Identificador</th>
                        <th class="py-3 px-4 font-semibold">Lote / Ejemplar</th>
                        <th class="py-3 px-4 font-semibold">Categoría</th>
                        <th class="py-3 px-4 font-semibold">Sede Custodia</th>
                        <th class="py-3 px-4 font-semibold">Graduación</th>
                        <th class="py-3 px-4 font-semibold text-center">Stock en Sede</th>
                        <th class="py-3 px-4 font-semibold text-right">Precio Unitario</th>
                        <th class="py-3 px-4 font-semibold text-right">Valor Tasado en Sede</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-100 dark:divide-white/5 font-body-md text-on-surface dark:text-[#e6dfd5]">
                    @forelse ($existenciasSede ?? [] as $existencia)
                        @php
                            $variante = $existencia->varianteSku;
                            $producto = $variante?->producto;
                            $tipoCat = $producto?->catalogo?->tipo ?? 'coleccionable';
                            $precioUnit = (float) ($variante?->precio ?? $producto?->precio ?? 0);
                            $valorFila = $precioUnit * (int) $existencia->cantidad;
                            $graduacionTexto = explode('·', (string) ($variante?->descripcion ?? 'MINT (M)'))[0] ?? 'MINT (M)';
                            $badgeTipo = str_contains($graduacionTexto, 'COA') ? 'coa' : (str_contains($graduacionTexto, 'VERY FINE') ? 'rare' : 'mint');
                        @endphp
                        <tr class="hover:bg-purple-50/60 dark:hover:bg-purple-900/25 transition-colors">
                            <td class="py-3 px-4 font-tabular-data text-primary dark:text-[#ccbeff] font-bold">#{{ $variante?->sku ?? 'SIN-SKU' }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold font-quicksand text-sm text-slate-900 dark:text-white">{{ $producto?->nombre ?? 'Pieza sin título' }}</div>
                                <div class="font-title-editorial text-[11px] text-on-surface-variant dark:text-[#d4c8bd] italic">{{ $variante?->descripcion }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded bg-purple-100 dark:bg-purple-950/80 text-purple-900 dark:text-purple-200 font-label-code text-[10px] uppercase font-bold">
                                    {{ $producto?->catalogo?->nombre ?? strtoupper($tipoCat) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-label-md text-secondary dark:text-purple-300 font-semibold">
                                {{ $existencia->sucursal?->nombre ?? 'Sede Central' }}
                            </td>
                            <td class="py-3 px-4">
                                <x-ui.badge-grading :type="$badgeTipo" :text="trim($graduacionTexto)" />
                            </td>
                            <td class="py-3 px-4 text-center font-tabular-data font-bold">
                                @if ($existencia->cantidad <= 0)
                                    <span class="px-2 py-0.5 rounded-full bg-red-100 dark:bg-red-950/70 text-red-700 dark:text-red-300 text-[10px] font-bold border border-red-300 dark:border-red-800">AGOTADO (0)</span>
                                @elseif ($existencia->cantidad <= $existencia->stock_minimo)
                                    <span class="px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950/70 text-amber-800 dark:text-amber-200 text-[11px] font-bold border border-amber-300 dark:border-amber-800">{{ $existencia->cantidad }} uds (Bajo mín)</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-200 text-[11px] font-bold border border-emerald-300 dark:border-emerald-800">{{ $existencia->cantidad }} uds</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-tabular-data text-right text-xs">
                                ${{ number_format($precioUnit, 0) }} MXN
                            </td>
                            <td class="py-3 px-4 font-tabular-data font-bold text-right text-sm text-primary dark:text-[#fdfbf7]">
                                ${{ number_format($valorFila, 0) }} MXN
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-on-surface-variant dark:text-[#d4c8bd] font-label-code">
                                No hay registros de existencias para la sede seleccionada ({{ $etiquetaSede }}).
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pie de Tabla Dinámico -->
        <div class="p-space-md bg-purple-50/40 dark:bg-white/5 border-t border-purple-200/60 dark:border-white/10 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs font-label-code text-on-surface-variant dark:text-[#d4c8bd]">
            <span>
                Mostrando {{ ($existenciasSede ?? collect())->count() }} lotes en <strong>{{ $etiquetaSede }}</strong> &middot; Total físico: {{ number_format($unidadesEnCustodia) }} unidades
            </span>
            <span class="font-bold text-primary dark:text-[#ccbeff]">
                Valoración Patrimonial en Sede: ${{ number_format($valoracionInventario, 2) }} MXN
            </span>
        </div>

    </section>

    <!-- Bitácora de Transacciones y Movimientos Filtrados por Sucursal y Período -->
    <section class="bento-reveal bento-delay-400 bg-white/80 dark:bg-[#161329]/80 backdrop-blur-xl border border-purple-200/60 dark:border-white/12 rounded-2xl shadow-[0_8px_32px_0_rgba(91,59,179,0.06)] dark:shadow-[0_8px_32px_0_rgba(0,0,0,0.35)] overflow-hidden transition-all duration-300">
        <div class="p-space-lg border-b border-purple-200/60 dark:border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-headline-sm text-lg text-on-surface dark:text-[#fdfbf7] font-bold">
                    Movimientos e Historial de Transacciones ({{ $etiquetaSede }} · {{ $etiquetaPeriodo }})
                </h2>
                <p class="font-title-editorial text-xs text-on-surface-variant dark:text-[#e6dfd5] italic mt-0.5">
                    Pedidos procesados y movimientos de inventario registrados dentro del período {{ $etiquetaPeriodo }} en {{ $etiquetaSede }}
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-purple-50/70 dark:bg-purple-950/40 backdrop-blur-sm border-b border-purple-200/70 dark:border-white/10 text-secondary dark:text-purple-200 font-label-md uppercase tracking-wider font-bold">
                    <tr>
                        <th class="py-3 px-4 font-semibold">Tipo / Folio</th>
                        <th class="py-3 px-4 font-semibold">Detalle / Producto</th>
                        <th class="py-3 px-4 font-semibold">Sede</th>
                        <th class="py-3 px-4 font-semibold text-center">Cantidad / Estatus</th>
                        <th class="py-3 px-4 font-semibold text-right">Monto / Impacto</th>
                        <th class="py-3 px-4 font-semibold text-right">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-100 dark:divide-white/5 font-body-md text-on-surface dark:text-[#e6dfd5]">
                    @foreach ($pedidosPeriodo ?? [] as $pedido)
                        @php
                            $envioSede = $pedido->envios->first()?->sucursal?->nombre ?? $etiquetaSede;
                        @endphp
                        <tr class="hover:bg-purple-50/60 dark:hover:bg-purple-900/25 transition-colors">
                            <td class="py-3 px-4 font-tabular-data font-bold text-primary dark:text-[#ccbeff]">PEDIDO #{{ $pedido->folio }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $pedido->cliente?->nombre_completo ?? 'Coleccionista Registrado' }}</div>
                                <div class="text-[11px] text-on-surface-variant dark:text-[#d4c8bd]">{{ $pedido->detalles->sum('cantidad') }} pieza(s) adquirida(s)</div>
                            </td>
                            <td class="py-3 px-4 font-label-md text-secondary dark:text-purple-300 font-semibold">{{ $envioSede }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-200 text-[10px] font-bold uppercase border border-emerald-300 dark:border-emerald-800">{{ $pedido->estatus }}</span>
                            </td>
                            <td class="py-3 px-4 text-right font-tabular-data font-bold text-primary dark:text-[#fdfbf7]">${{ number_format((float) $pedido->total, 2) }} MXN</td>
                            <td class="py-3 px-4 text-right font-label-code text-on-surface-variant dark:text-[#d4c8bd]">{{ $pedido->created_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach

                    @foreach ($movimientosPeriodo ?? [] as $mov)
                        <tr class="hover:bg-purple-50/60 dark:hover:bg-purple-900/25 transition-colors">
                            <td class="py-3 px-4 font-tabular-data font-bold text-secondary dark:text-purple-300">MOV-{{ strtoupper($mov->tipo) }} #{{ $mov->id }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $mov->existencia?->varianteSku?->producto?->nombre ?? 'Ajuste de Stock' }}</div>
                                <div class="text-[11px] text-on-surface-variant dark:text-[#d4c8bd]">{{ $mov->motivo }}</div>
                            </td>
                            <td class="py-3 px-4 font-label-md text-secondary dark:text-purple-300 font-semibold">{{ $mov->existencia?->sucursal?->nombre ?? $etiquetaSede }}</td>
                            <td class="py-3 px-4 text-center font-tabular-data font-bold">
                                {{ $mov->cantidad > 0 ? '+'.$mov->cantidad : $mov->cantidad }} uds
                            </td>
                            <td class="py-3 px-4 text-right font-label-code text-on-surface-variant dark:text-[#d4c8bd]">SKU: {{ $mov->existencia?->varianteSku?->sku ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-right font-label-code text-on-surface-variant dark:text-[#d4c8bd]">{{ $mov->created_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach

                    @if (($pedidosPeriodo ?? collect())->isEmpty() && ($movimientosPeriodo ?? collect())->isEmpty())
                        <tr>
                            <td colspan="6" class="py-6 text-center text-on-surface-variant dark:text-[#d4c8bd] font-label-code">
                                Sin transacciones o movimientos adicionales registrados en {{ $etiquetaSede }} durante el período {{ $etiquetaPeriodo }}.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </section>

    @push('scripts')
        <!-- Librería Chart.js v4 UMD -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // =====================================================================
                // 1. Manejo y Renderizado Interactivo de Chart.js
                // =====================================================================
                const seriesAnaliticas = {!! $seriesJson !!};
                let activeRange = '{{ in_array($codigoPeriodo, ['7d', '30d', '2026']) ? $codigoPeriodo : '30d' }}';
                if (!seriesAnaliticas[activeRange]) {
                    activeRange = '30d';
                }

                const canvas = document.getElementById('archivumPerformanceChart');
                let chartInstance = null;

                function isDarkMode() {
                    return document.documentElement.classList.contains('dark');
                }

                function getThemePalette() {
                    const dark = isDarkMode();
                    return {
                        textColor: dark ? '#fdfbf7' : '#1e1035',
                        mutedTextColor: dark ? '#ccbeff' : '#6b5a8e',
                        gridColor: dark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(109, 40, 217, 0.08)',
                        linePrimary: dark ? '#c084fc' : '#7c3aed',
                        pointPrimary: dark ? '#f5d0fe' : '#5b21b6',
                        gradientStart: dark ? 'rgba(192, 132, 252, 0.45)' : 'rgba(124, 58, 237, 0.28)',
                        gradientEnd: dark ? 'rgba(76, 29, 149, 0.0)' : 'rgba(147, 51, 234, 0.0)',
                        lineSecondary: dark ? '#38bdf8' : '#0284c7',
                        pointSecondary: dark ? '#bae6fd' : '#0369a1',
                        tooltipBg: dark ? 'rgba(22, 19, 41, 0.95)' : 'rgba(255, 255, 255, 0.96)',
                        tooltipBorder: dark ? 'rgba(255, 255, 255, 0.18)' : 'rgba(124, 58, 237, 0.25)',
                        tooltipText: dark ? '#ffffff' : '#1e1035',
                    };
                }

                function updateKPIHeaders(rangeKey) {
                    const data = seriesAnaliticas[rangeKey];
                    if (!data || !data.resumen) return;

                    const kpiIngresos = document.getElementById('chart-kpi-ingresos');
                    const kpiPedidos = document.getElementById('chart-kpi-pedidos');
                    const kpiTicket = document.getElementById('chart-kpi-ticket');
                    const kpiTrend = document.getElementById('chart-kpi-trend');

                    if (kpiIngresos) {
                        kpiIngresos.textContent = '$' + Number(data.resumen.total_ingresos || 0).toLocaleString('es-MX', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' MXN';
                    }
                    if (kpiPedidos) {
                        kpiPedidos.textContent = (data.resumen.total_pedidos || 0) + ' órdenes';
                    }
                    if (kpiTicket) {
                        kpiTicket.textContent = '$' + Number(data.resumen.ticket_promedio || 0).toLocaleString('es-MX', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) + ' MXN';
                    }
                    if (kpiTrend) {
                        kpiTrend.textContent = '+' + (data.resumen.crecimiento_porcentaje || 18.4) + '% ritmo';
                    }
                }

                function renderChart() {
                    if (!canvas || !window.Chart) return;

                    const ctx = canvas.getContext('2d');
                    const theme = getThemePalette();
                    const currentData = seriesAnaliticas[activeRange] || { labels: [], ingresos: [], pedidos: [] };

                    const gradient = ctx.createLinearGradient(0, 0, 0, 200);
                    gradient.addColorStop(0, theme.gradientStart);
                    gradient.addColorStop(1, theme.gradientEnd);

                    if (!chartInstance) {
                        chartInstance = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: currentData.labels,
                                datasets: [
                                    {
                                        label: 'Ingresos Totales (MXN)',
                                        data: currentData.ingresos,
                                        borderColor: theme.linePrimary,
                                        borderWidth: 3,
                                        tension: 0.4,
                                        fill: true,
                                        backgroundColor: gradient,
                                        pointBackgroundColor: theme.pointPrimary,
                                        pointBorderColor: '#ffffff',
                                        pointBorderWidth: 1.5,
                                        pointRadius: 4,
                                        pointHoverRadius: 6,
                                        pointHoverBackgroundColor: theme.linePrimary,
                                        pointHoverBorderColor: '#ffffff',
                                        pointHoverBorderWidth: 2,
                                        yAxisID: 'y',
                                    },
                                    {
                                        label: 'Volumen de Órdenes',
                                        data: currentData.pedidos,
                                        borderColor: theme.lineSecondary,
                                        borderWidth: 2,
                                        borderDash: [5, 5],
                                        tension: 0.4,
                                        fill: false,
                                        pointBackgroundColor: theme.pointSecondary,
                                        pointBorderColor: '#ffffff',
                                        pointBorderWidth: 1.5,
                                        pointRadius: 3.5,
                                        pointHoverRadius: 5.5,
                                        yAxisID: 'y1',
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: {
                                    mode: 'index',
                                    intersect: false,
                                },
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top',
                                        align: 'end',
                                        labels: {
                                            color: theme.textColor,
                                            font: {
                                                family: 'Quicksand, sans-serif',
                                                size: 11,
                                                weight: '600',
                                            },
                                            usePointStyle: true,
                                            pointStyle: 'circle',
                                            padding: 16,
                                        }
                                    },
                                    tooltip: {
                                        backgroundColor: theme.tooltipBg,
                                        borderColor: theme.tooltipBorder,
                                        borderWidth: 1.5,
                                        titleColor: theme.tooltipText,
                                        bodyColor: theme.tooltipText,
                                        padding: 12,
                                        cornerRadius: 14,
                                        displayColors: true,
                                        boxPadding: 6,
                                        titleFont: {
                                            family: 'Quicksand, sans-serif',
                                            size: 12,
                                            weight: '700',
                                        },
                                        bodyFont: {
                                            family: 'Barlow Condensed, monospace',
                                            size: 13,
                                            weight: '600',
                                        },
                                        callbacks: {
                                            label: function (context) {
                                                if (context.datasetIndex === 0) {
                                                    return ' Ingresos: $' + Number(context.raw).toLocaleString('es-MX') + ' MXN';
                                                }
                                                return ' Órdenes: ' + context.raw + ' confirmadas';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: {
                                            color: theme.gridColor,
                                            drawBorder: false,
                                        },
                                        ticks: {
                                            color: theme.mutedTextColor,
                                            font: {
                                                family: 'Barlow Condensed, sans-serif',
                                                size: 11,
                                                weight: '600',
                                            },
                                            maxRotation: 0,
                                            padding: 8,
                                        }
                                    },
                                    y: {
                                        type: 'linear',
                                        display: true,
                                        position: 'left',
                                        grid: {
                                            color: theme.gridColor,
                                            drawBorder: false,
                                        },
                                        ticks: {
                                            color: theme.mutedTextColor,
                                            font: {
                                                family: 'Barlow Condensed, monospace',
                                                size: 11,
                                            },
                                            callback: function (val) {
                                                if (val >= 1000) {
                                                    return '$' + (val / 1000).toFixed(0) + 'k';
                                                }
                                                return '$' + val;
                                            },
                                            padding: 8,
                                        }
                                    },
                                    y1: {
                                        type: 'linear',
                                        display: true,
                                        position: 'right',
                                        grid: {
                                            drawOnChartArea: false,
                                        },
                                        ticks: {
                                            color: theme.lineSecondary,
                                            font: {
                                                family: 'Barlow Condensed, monospace',
                                                size: 11,
                                            },
                                            precision: 0,
                                            padding: 8,
                                        }
                                    }
                                }
                            }
                        });
                    } else {
                        chartInstance.data.labels = currentData.labels;
                        chartInstance.data.datasets[0].data = currentData.ingresos;
                        chartInstance.data.datasets[0].borderColor = theme.linePrimary;
                        chartInstance.data.datasets[0].backgroundColor = gradient;
                        chartInstance.data.datasets[0].pointBackgroundColor = theme.pointPrimary;
                        chartInstance.data.datasets[1].data = currentData.pedidos;
                        chartInstance.data.datasets[1].borderColor = theme.lineSecondary;
                        chartInstance.data.datasets[1].pointBackgroundColor = theme.pointSecondary;

                        chartInstance.options.plugins.legend.labels.color = theme.textColor;
                        chartInstance.options.plugins.tooltip.backgroundColor = theme.tooltipBg;
                        chartInstance.options.plugins.tooltip.borderColor = theme.tooltipBorder;
                        chartInstance.options.plugins.tooltip.titleColor = theme.tooltipText;
                        chartInstance.options.plugins.tooltip.bodyColor = theme.tooltipText;
                        chartInstance.options.scales.x.grid.color = theme.gridColor;
                        chartInstance.options.scales.x.ticks.color = theme.mutedTextColor;
                        chartInstance.options.scales.y.grid.color = theme.gridColor;
                        chartInstance.options.scales.y.ticks.color = theme.mutedTextColor;
                        chartInstance.options.scales.y1.ticks.color = theme.lineSecondary;

                        chartInstance.update();
                    }

                    window.archivumChartInstance = chartInstance;
                    updateKPIHeaders(activeRange);
                }

                // Limpieza de instancia Chart.js al navegar fuera del dashboard en modo SPA
                window.addEventListener('admin-spa-navigated', function () {
                    if (chartInstance) {
                        try { chartInstance.destroy(); } catch (e) {}
                        chartInstance = null;
                        window.archivumChartInstance = null;
                    }
                });

                // Render inicial
                renderChart();

                // Botones interactivos de rango temporal
                const rangePills = document.querySelectorAll('.chart-range-pill');
                rangePills.forEach(pill => {
                    pill.addEventListener('click', function () {
                        const targetRange = this.dataset.range;
                        if (!targetRange || targetRange === activeRange) return;

                        activeRange = targetRange;
                        rangePills.forEach(p => {
                            p.className = 'chart-range-pill px-3 py-1.5 rounded-xl text-xs font-label-code font-bold uppercase tracking-wider transition-all cursor-pointer text-on-surface-variant hover:text-on-surface hover:bg-white/40 dark:hover:bg-white/5';
                        });
                        this.className = 'chart-range-pill px-3 py-1.5 rounded-xl text-xs font-label-code font-bold uppercase tracking-wider transition-all cursor-pointer bg-primary text-white shadow-xs';

                        renderChart();
                    });
                });

                // Escuchar el evento de cambio de tema en tiempo real
                window.addEventListener('archivum-theme-change', function () {
                    renderChart();
                });

                // =====================================================================
                // 2. Sincronización de Botones de Acción de Sede
                // =====================================================================
                const btnInventario = document.getElementById('btn-gestionar-inventario-sede');
                const btnAjustes = document.getElementById('btn-ajustes-sede');
                const btnTransferencias = document.getElementById('btn-transferencias-sede');
                const kpiSelect = document.getElementById('kpi-branch-select');
                const headerSelect = document.getElementById('branch-select');

                function actualizarEnlacesSede(sucursalValor) {
                    const param = sucursalValor || 'all';
                    [btnInventario, btnAjustes, btnTransferencias].forEach(function (enlace) {
                        if (!enlace || !enlace.dataset.baseUrl) {
                            return;
                        }
                        enlace.href = enlace.dataset.baseUrl + '?sucursal_id=' + encodeURIComponent(param);
                    });
                }

                if (kpiSelect) {
                    kpiSelect.addEventListener('change', function () {
                        actualizarEnlacesSede(kpiSelect.value);
                    });
                }

                if (headerSelect) {
                    headerSelect.addEventListener('change', function () {
                        actualizarEnlacesSede(headerSelect.value);
                    });
                }
            });
        </script>
    @endpush

</x-layouts.admin>
