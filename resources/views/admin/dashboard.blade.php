<x-layouts.admin title="ARCHIVUM · Terminal de Control Maestro" active-route="dashboard">
    
    <!-- Barra Operativa Superior: Saludo, Sesión y Filtros -->
    <x-admin.operational-bar 
        session-id="#ADM-8841-MX"
        curator-name="Mateo Vargas"
        curator-role="Curador en Jefe · Sistema de Catalogación &amp; Logística de Anticuario"
        active-period="30d"
    />

    <!-- Fila de Métricas Clave: 4 Tarjetas Especializadas de KPI -->
    <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-space-md mb-space-lg">
        
        <!-- KPI 1: Ventas Totales del Mes -->
        <x-ui.kpi-card 
            table-source="pedidos_table &amp; pagos_table"
            title="Ventas Totales del Mes"
            value="$142,850"
            currency="MXN"
            trend="+12.4%"
            :is-positive="true"
            icon="payments"
            icon-bg="bg-primary-container"
            summary-left="Transacciones: 418"
            summary-right="Ticket Prom: $341 MXN"
        />

        <!-- KPI 2: Unidades en Inventario -->
        <x-ui.kpi-card 
            table-source="existencias_table"
            title="Unidades en Inventario"
            value="8,420"
            currency="ítems activos"
            trend="14 bajo stock"
            :is-positive="false"
            icon="shelves"
            icon-bg="bg-secondary-container text-on-secondary-container"
            summary-left="Valoración: $1.92M"
            summary-right="Rotación: 4.8x/año"
        />

        <!-- KPI 3: Reservas & Envíos Activos -->
        <x-ui.kpi-card 
            table-source="reservas_stock &amp; envios"
            title="Logística Activa"
            value="32"
            currency="órdenes en tránsito"
            trend="100% a tiempo"
            :is-positive="true"
            icon="local_shipping"
            icon-bg="bg-primary-container"
            summary-left="Custodia Blindada: 12"
            summary-right="Retiro en Tienda: 20"
        />

        <!-- KPI 4: Autenticaciones Pendientes -->
        <x-ui.kpi-card 
            table-source="certificados_autenticidad"
            title="Dictamen Notarial"
            value="7"
            currency="lotes por auditar"
            trend="3 prioritarios"
            :is-positive="true"
            icon="verified"
            icon-bg="bg-tertiary"
            summary-left="Peritaje Caligráfico: 4"
            summary-right="Inspección RVG: 3"
        />

    </section>

    <!-- Barra de Accesos Rápidos de Archivo -->
    <div class="flex flex-wrap items-center justify-between gap-4 p-space-md bg-surface-container-low border border-purple-200/70 rounded-xl mb-space-lg">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[20px]">bolt</span>
            <span class="font-label-md text-on-surface font-semibold text-xs uppercase tracking-wider">Acciones Operativas Inmediatas:</span>
        </div>
        <div class="flex flex-wrap items-center gap-space-xs text-xs font-label-md">
            <button type="button" class="bg-primary-container hover:bg-primary text-white px-3.5 py-2 rounded-lg flex items-center gap-1.5 shadow-2xs transition">
                <span class="material-symbols-outlined text-[16px]">add_circle</span>
                <span>Registrar Nueva Adquisición</span>
            </button>
            <button type="button" class="bg-white hover:bg-surface-container text-on-surface border border-purple-200/80 px-3.5 py-2 rounded-lg flex items-center gap-1.5 transition">
                <span class="material-symbols-outlined text-[16px]">point_of_sale</span>
                <span>Venta en Mostrador (POS)</span>
            </button>
            <button type="button" class="bg-white hover:bg-surface-container text-on-surface border border-purple-200/80 px-3.5 py-2 rounded-lg flex items-center gap-1.5 transition">
                <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                <span>Emitir Factura Fiscal SAT</span>
            </button>
            <button type="button" class="bg-white hover:bg-surface-container text-on-surface border border-purple-200/80 px-3.5 py-2 rounded-lg flex items-center gap-1.5 transition">
                <span class="material-symbols-outlined text-[16px]">qr_code_scanner</span>
                <span>Auditar Existencias por Escáner</span>
            </button>
        </div>
    </div>

    <!-- Tabla Maestra de Movimientos Recientes del Libro de Archivo -->
    <section class="bg-surface-container-lowest border border-purple-200/70 rounded-xl shadow-xs overflow-hidden">
        
        <!-- Encabezado de la Tabla -->
        <div class="p-space-lg border-b border-purple-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-headline-sm text-lg text-on-surface font-bold">Bitácora de Adquisiciones &amp; Transacciones</h2>
                <p class="font-title-editorial text-xs text-on-surface-variant italic mt-0.5">
                    Registro cronológico inmutable de entradas, salidas y dictámenes en las 3 sedes
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="font-label-code text-secondary text-xs">Filtro Rápido:</span>
                <span class="px-2.5 py-1 rounded bg-purple-50 text-primary border border-purple-200 text-xs font-label-code font-bold">
                    TODAS LAS SEDES (24H)
                </span>
            </div>
        </div>

        <!-- Tabla Responsiva -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-container-low border-b border-purple-200/70 text-secondary font-label-md uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4 font-semibold">SKU / Identificador</th>
                        <th class="py-3 px-4 font-semibold">Lote / Ejemplar</th>
                        <th class="py-3 px-4 font-semibold">Categoría</th>
                        <th class="py-3 px-4 font-semibold">Sede Custodia</th>
                        <th class="py-3 px-4 font-semibold">Graduación</th>
                        <th class="py-3 px-4 font-semibold text-right">Valor Tasado</th>
                        <th class="py-3 px-4 font-semibold text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-100 font-body-md text-on-surface">
                    
                    <!-- Fila 1 -->
                    <tr class="hover:bg-purple-50/50 transition-colors">
                        <td class="py-3 px-4 font-tabular-data text-primary font-bold">#VIN-1959-COL</td>
                        <td class="py-3 px-4">
                            <div class="font-bold font-quicksand text-sm">Kind of Blue (Mono 1st Press)</div>
                            <div class="font-title-editorial text-[11px] text-on-surface-variant italic">Columbia CL 1355 · 6-Eye Label</div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-900 font-label-code text-[10px]">VINILO HISTÓRICO</span>
                        </td>
                        <td class="py-3 px-4 font-label-md text-secondary">Central Bellas Artes</td>
                        <td class="py-3 px-4">
                            <x-ui.badge-grading type="mint" text="MINT (M)" />
                        </td>
                        <td class="py-3 px-4 font-tabular-data font-bold text-right text-sm">$18,500 MXN</td>
                        <td class="py-3 px-4 text-center">
                            <button class="p-1.5 rounded hover:bg-surface-container text-primary transition" title="Ver Expediente">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </td>
                    </tr>

                    <!-- Fila 2 -->
                    <tr class="hover:bg-purple-50/50 transition-colors">
                        <td class="py-3 px-4 font-tabular-data text-primary font-bold">#LIB-1967-SUD</td>
                        <td class="py-3 px-4">
                            <div class="font-bold font-quicksand text-sm">Cien Años de Soledad</div>
                            <div class="font-title-editorial text-[11px] text-on-surface-variant italic">Editorial Sudamericana · 1ª Edición</div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded bg-indigo-100 text-indigo-900 font-label-code text-[10px]">LIBRO RARO</span>
                        </td>
                        <td class="py-3 px-4 font-label-md text-secondary">Gabinete Coyoacán</td>
                        <td class="py-3 px-4">
                            <x-ui.badge-grading type="rare" text="VERY FINE (VF)" />
                        </td>
                        <td class="py-3 px-4 font-tabular-data font-bold text-right text-sm">$32,000 MXN</td>
                        <td class="py-3 px-4 text-center">
                            <button class="p-1.5 rounded hover:bg-surface-container text-primary transition" title="Ver Expediente">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </td>
                    </tr>

                    <!-- Fila 3 -->
                    <tr class="hover:bg-purple-50/50 transition-colors">
                        <td class="py-3 px-4 font-tabular-data text-primary font-bold">#MEM-1928-AUT</td>
                        <td class="py-3 px-4">
                            <div class="font-bold font-quicksand text-sm">Carta Manuscrita Bellas Artes</div>
                            <div class="font-title-editorial text-[11px] text-on-surface-variant italic">Tinta ferrogálica sobre lino de archivo</div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded bg-purple-200 text-purple-950 font-label-code text-[10px]">MEMORABILIA</span>
                        </td>
                        <td class="py-3 px-4 font-label-md text-secondary">Bóveda Almacén</td>
                        <td class="py-3 px-4">
                            <x-ui.badge-grading type="coa" text="COA NOTARIADO" />
                        </td>
                        <td class="py-3 px-4 font-tabular-data font-bold text-right text-sm">$9,400 MXN</td>
                        <td class="py-3 px-4 text-center">
                            <button class="p-1.5 rounded hover:bg-surface-container text-primary transition" title="Ver Expediente">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <!-- Paginación y Resumen Inferior -->
        <div class="p-space-md bg-surface-container-low/50 border-t border-purple-200/60 flex items-center justify-between text-xs font-label-code text-on-surface-variant">
            <span>Mostrando 3 de 8,420 registros activos</span>
            <div class="flex items-center gap-1">
                <button class="px-3 py-1 rounded border border-purple-200 bg-white hover:bg-surface-container disabled:opacity-50" disabled>Anterior</button>
                <button class="px-3 py-1 rounded bg-primary-container text-white font-bold">1</button>
                <button class="px-3 py-1 rounded border border-purple-200 bg-white hover:bg-surface-container">2</button>
                <button class="px-3 py-1 rounded border border-purple-200 bg-white hover:bg-surface-container">Siguiente</button>
            </div>
        </div>

    </section>

</x-layouts.admin>
