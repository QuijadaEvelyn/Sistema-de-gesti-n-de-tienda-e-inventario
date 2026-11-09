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

    <!-- Acceso Rápido a Reportes Calculados y Exportación (CSV / PDF) Sincronizado con la Sede Activa -->
    <div class="mb-space-md bg-surface-container-lowest rounded-2xl border border-purple-200/70 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-primary text-2xl">analytics</span>
            <div>
                <h2 class="text-sm font-bold text-on-surface font-quicksand">
                    Módulo de Reportes Oficiales &amp; Exportación de Acervo ({{ $etiquetaSede }})
                </h2>
                <p class="text-xs text-on-surface-variant">
                    Genera resúmenes calculados de inventario en bóveda, lotes certificados COA y descargas directas en CSV y PDF para la sede y período activos.
                </p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.reportes', $sucursalFiltroExport) }}" class="px-3.5 py-2 rounded-xl border border-purple-200 bg-white hover:bg-purple-50 text-primary text-xs font-label-md flex items-center gap-1.5 transition">
                <span class="material-symbols-outlined text-[16px]">monitoring</span>
                <span>Ver Reportes Calculados</span>
            </a>
            <a href="{{ route('admin.reportes.csv', $sucursalFiltroExport) }}" class="px-3.5 py-2 rounded-xl border border-purple-200 bg-white hover:bg-purple-50 text-primary text-xs font-label-md flex items-center gap-1.5 transition">
                <span class="material-symbols-outlined text-[16px]">table_view</span>
                <span>Exportar CSV</span>
            </a>
            <a href="{{ route('admin.reportes.pdf', $sucursalFiltroExport) }}" class="px-4 py-2 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-label-md flex items-center gap-1.5 transition">
                <span class="material-symbols-outlined text-[16px]">picture_as_pdf</span>
                <span>Exportar PDF Oficial</span>
            </a>
        </div>
    </div>

    <!-- Fila de Métricas Clave Dinámicas Filtradas por Sucursal y Temporalidad -->
    <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-space-md mb-space-lg" id="dashboard-kpi-grid">
        
        <!-- KPI 1: Ventas & Ingresos Estimados en la Sede y Período -->
        <x-ui.kpi-card 
            table-source="pedidos_table &amp; pagos_table ({{ $etiquetaPeriodo }})"
            title="Ventas &amp; Ingresos Estimados"
            :value="'$'.number_format($ingresosEstimados, 0)"
            currency="MXN"
            :trend="'$'.number_format($ventasConfirmadas, 0).' cobrados'"
            :is-positive="true"
            icon="payments"
            icon-bg="bg-primary-container"
            :summary-left="'Pedidos ('.$codigoPeriodo.'): '.$volumenPedidos"
            :summary-right="'Ticket Prom: $'.number_format($ticketPromedio, 0).' MXN'"
        />

        <!-- KPI 2: Piezas en Custodia / Stock de la Sucursal Seleccionada -->
        <x-ui.kpi-card 
            table-source="existencias_table ({{ $sucursalActiva ? $sucursalActiva->nombre : 'Global' }})"
            title="Piezas en Custodia"
            :value="number_format($unidadesEnCustodia)"
            currency="unidades en bóveda"
            :trend="$alertasBajoStock.' bajo stock'"
            :is-positive="$alertasBajoStock === 0"
            icon="shelves"
            icon-bg="bg-secondary-container text-on-secondary-container"
            :summary-left="'Valoración: $'.number_format($valoracionInventario, 0)"
            :summary-right="'SKUs en sede: '.$totalSkus"
        />

        <!-- KPI 3: Reservas, Envíos y Movimientos en la Sede y Período -->
        <x-ui.kpi-card 
            table-source="reservas_stock &amp; envios ({{ $etiquetaPeriodo }})"
            title="Logística &amp; Movimientos"
            :value="(string) ($enviosActivos + $reservasActivas + $movimientosCount)"
            currency="operaciones en sede"
            :trend="$enviosActivos.' en tránsito'"
            :is-positive="true"
            icon="local_shipping"
            icon-bg="bg-primary-container"
            :summary-left="'Reservas activas: '.$reservasActivas"
            :summary-right="'Movimientos ('.$codigoPeriodo.'): '.$movimientosCount"
        />

        <!-- KPI 4: Lotes Certificados COA en la Sede Seleccionada -->
        <x-ui.kpi-card 
            table-source="piezas_unicas ({{ $sucursalActiva ? $sucursalActiva->nombre : 'Global' }})"
            title="Dictamen Notarial COA"
            :value="(string) $totalCoa"
            currency="series certificadas"
            :trend="$coaDisponibles.' disponibles'"
            :is-positive="true"
            icon="verified"
            icon-bg="bg-tertiary"
            :summary-left="'En bóveda: '.$coaDisponibles"
            :summary-right="'Comprometidos: '.$coaComprometidos"
        />

    </section>

    <!-- Barra de Accesos Rápidos de Archivo -->
    <div class="flex flex-wrap items-center justify-between gap-4 p-space-md bg-surface-container-low border border-purple-200/70 rounded-xl mb-space-lg">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[20px]">bolt</span>
            <span class="font-label-md text-on-surface font-semibold text-xs uppercase tracking-wider">Acciones Operativas en {{ $etiquetaSede }}:</span>
        </div>
        <div class="flex flex-wrap items-center gap-space-xs text-xs font-label-md">
            <a
                id="btn-gestionar-inventario-sede"
                href="{{ route('admin.inventario', $parametrosSedeActiva) }}"
                data-base-url="{{ route('admin.inventario') }}"
                data-sucursal-id="{{ $sucursalActiva?->id ?? 'all' }}"
                class="bg-primary-container hover:bg-primary text-white px-3.5 py-2 rounded-lg flex items-center gap-1.5 shadow-2xs transition cursor-pointer"
            >
                <span class="material-symbols-outlined text-[16px]">inventory_2</span>
                <span>Gestionar Inventario de Sede</span>
            </a>
            @if ($usuario && $usuario->tieneRol('administrador', 'almacen', 'encargado', 'compras', 'ventas'))
                <a
                    id="btn-ajustes-sede"
                    href="{{ route('almacen.ajustes', $parametrosSedeActiva) }}"
                    data-base-url="{{ route('almacen.ajustes') }}"
                    class="bg-white hover:bg-surface-container text-on-surface border border-purple-200/80 px-3.5 py-2 rounded-lg flex items-center gap-1.5 transition cursor-pointer"
                >
                    <span class="material-symbols-outlined text-[16px]">tune</span>
                    <span>Ajustes &amp; Recepciones</span>
                </a>
                <a
                    id="btn-transferencias-sede"
                    href="{{ route('almacen.transferencias', $parametrosSedeActiva) }}"
                    data-base-url="{{ route('almacen.transferencias') }}"
                    class="bg-white hover:bg-surface-container text-on-surface border border-purple-200/80 px-3.5 py-2 rounded-lg flex items-center gap-1.5 transition cursor-pointer"
                >
                    <span class="material-symbols-outlined text-[16px]">swap_horiz</span>
                    <span>Transferencias entre Sedes</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Tabla Maestra Dinámica de Existencias y Custodia Filtrada por Sucursal -->
    <section class="bg-surface-container-lowest border border-purple-200/70 rounded-xl shadow-xs overflow-hidden mb-space-lg">
        
        <!-- Encabezado de la Tabla con Indicador de Filtro Activo (Sede + Temporalidad) -->
        <div class="p-space-lg border-b border-purple-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-headline-sm text-lg text-on-surface font-bold">
                    Existencias en Custodia &amp; Valoración Patrimonial — {{ $etiquetaSede }}
                </h2>
                <p class="font-title-editorial text-xs text-on-surface-variant italic mt-0.5">
                    Inventario filtrado estrictamente según la sede seleccionada y el rango temporal activo ({{ $etiquetaPeriodo }})
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="font-label-code text-secondary text-xs">Filtro Activo:</span>
                <span id="active-branch-filter-badge" class="px-2.5 py-1 rounded bg-purple-50 text-primary border border-purple-200 text-xs font-label-code font-bold uppercase">
                    {{ $sucursalActiva ? mb_strtoupper($sucursalActiva->nombre) : 'TODAS LAS SEDES' }} ({{ mb_strtoupper($codigoPeriodo) }})
                </span>
            </div>
        </div>

        <!-- Tabla Responsiva de Piezas en la Sucursal Seleccionada -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-container-low border-b border-purple-200/70 text-secondary font-label-md uppercase tracking-wider">
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
                <tbody class="divide-y divide-purple-100 font-body-md text-on-surface">
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
                        <tr class="hover:bg-purple-50/50 transition-colors">
                            <td class="py-3 px-4 font-tabular-data text-primary font-bold">#{{ $variante?->sku ?? 'SIN-SKU' }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold font-quicksand text-sm">{{ $producto?->nombre ?? 'Pieza sin título' }}</div>
                                <div class="font-title-editorial text-[11px] text-on-surface-variant italic">{{ $variante?->descripcion }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-900 font-label-code text-[10px] uppercase">
                                    {{ $producto?->catalogo?->nombre ?? strtoupper($tipoCat) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-label-md text-secondary font-semibold">
                                {{ $existencia->sucursal?->nombre ?? 'Sede Central' }}
                            </td>
                            <td class="py-3 px-4">
                                <x-ui.badge-grading :type="$badgeTipo" :text="trim($graduacionTexto)" />
                            </td>
                            <td class="py-3 px-4 text-center font-tabular-data font-bold">
                                @if ($existencia->cantidad <= 0)
                                    <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-[10px] font-bold">AGOTADO (0)</span>
                                @elseif ($existencia->cantidad <= $existencia->stock_minimo)
                                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[11px] font-bold">{{ $existencia->cantidad }} uds (Bajo mín)</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">{{ $existencia->cantidad }} uds</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-tabular-data text-right text-xs">
                                ${{ number_format($precioUnit, 0) }} MXN
                            </td>
                            <td class="py-3 px-4 font-tabular-data font-bold text-right text-sm">
                                ${{ number_format($valorFila, 0) }} MXN
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-on-surface-variant font-label-code">
                                No hay registros de existencias para la sede seleccionada ({{ $etiquetaSede }}).
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pie de Tabla Dinámico -->
        <div class="p-space-md bg-surface-container-low/50 border-t border-purple-200/60 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs font-label-code text-on-surface-variant">
            <span>
                Mostrando {{ ($existenciasSede ?? collect())->count() }} lotes en <strong>{{ $etiquetaSede }}</strong> &middot; Total físico: {{ number_format($unidadesEnCustodia) }} unidades
            </span>
            <span class="font-bold text-primary">
                Valoración Patrimonial en Sede: ${{ number_format($valoracionInventario, 2) }} MXN
            </span>
        </div>

    </section>

    <!-- Bitácora de Transacciones y Movimientos Filtrados por Sucursal y Período -->
    <section class="bg-surface-container-lowest border border-purple-200/70 rounded-xl shadow-xs overflow-hidden">
        <div class="p-space-lg border-b border-purple-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-headline-sm text-lg text-on-surface font-bold">
                    Movimientos e Historial de Transacciones ({{ $etiquetaSede }} · {{ $etiquetaPeriodo }})
                </h2>
                <p class="font-title-editorial text-xs text-on-surface-variant italic mt-0.5">
                    Pedidos procesados y movimientos de inventario registrados dentro del período {{ $etiquetaPeriodo }} en {{ $etiquetaSede }}
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-container-low border-b border-purple-200/70 text-secondary font-label-md uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4 font-semibold">Tipo / Folio</th>
                        <th class="py-3 px-4 font-semibold">Detalle / Producto</th>
                        <th class="py-3 px-4 font-semibold">Sede</th>
                        <th class="py-3 px-4 font-semibold text-center">Cantidad / Estatus</th>
                        <th class="py-3 px-4 font-semibold text-right">Monto / Impacto</th>
                        <th class="py-3 px-4 font-semibold text-right">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-100 font-body-md text-on-surface">
                    @foreach ($pedidosPeriodo ?? [] as $pedido)
                        @php
                            $envioSede = $pedido->envios->first()?->sucursal?->nombre ?? $etiquetaSede;
                        @endphp
                        <tr class="hover:bg-purple-50/50 transition-colors">
                            <td class="py-3 px-4 font-tabular-data font-bold text-primary">PEDIDO #{{ $pedido->folio }}</td>
                            <td class="py-3 px-4">
                                <div class="font-semibold">{{ $pedido->cliente?->nombre_completo ?? 'Coleccionista Registrado' }}</div>
                                <div class="text-[11px] text-on-surface-variant">{{ $pedido->detalles->sum('cantidad') }} pieza(s) adquirida(s)</div>
                            </td>
                            <td class="py-3 px-4 font-label-md text-secondary">{{ $envioSede }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold uppercase">{{ $pedido->estatus }}</span>
                            </td>
                            <td class="py-3 px-4 text-right font-tabular-data font-bold">${{ number_format((float) $pedido->total, 2) }} MXN</td>
                            <td class="py-3 px-4 text-right font-label-code text-on-surface-variant">{{ $pedido->created_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach

                    @foreach ($movimientosPeriodo ?? [] as $mov)
                        <tr class="hover:bg-purple-50/50 transition-colors">
                            <td class="py-3 px-4 font-tabular-data font-bold text-secondary">MOV-{{ strtoupper($mov->tipo) }} #{{ $mov->id }}</td>
                            <td class="py-3 px-4">
                                <div class="font-semibold">{{ $mov->existencia?->varianteSku?->producto?->nombre ?? 'Ajuste de Stock' }}</div>
                                <div class="text-[11px] text-on-surface-variant">{{ $mov->motivo }}</div>
                            </td>
                            <td class="py-3 px-4 font-label-md text-secondary">{{ $mov->existencia?->sucursal?->nombre ?? $etiquetaSede }}</td>
                            <td class="py-3 px-4 text-center font-tabular-data font-bold">
                                {{ $mov->cantidad > 0 ? '+'.$mov->cantidad : $mov->cantidad }} uds
                            </td>
                            <td class="py-3 px-4 text-right font-label-code text-on-surface-variant">SKU: {{ $mov->existencia?->varianteSku?->sku ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-right font-label-code text-on-surface-variant">{{ $mov->created_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach

                    @if (($pedidosPeriodo ?? collect())->isEmpty() && ($movimientosPeriodo ?? collect())->isEmpty())
                        <tr>
                            <td colspan="6" class="py-6 text-center text-on-surface-variant font-label-code">
                                Sin transacciones o movimientos adicionales registrados en {{ $etiquetaSede }} durante el período {{ $etiquetaPeriodo }}.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
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
