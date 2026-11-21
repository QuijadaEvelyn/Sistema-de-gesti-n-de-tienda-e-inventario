<x-layouts.admin title="ARCHIVUM · Módulo de Reportes & Exportación (CSV / PDF)" active-route="reportes">
    <div class="space-y-6">

        {{-- Encabezado Institucional y Botones de Exportación Directa --}}
        <div class="flex flex-col lg:flex-row lg:items-end justify-between border-b border-purple-200/70 pb-5 gap-4">
            <div>
                <p class="font-label-code text-[10px] uppercase tracking-[0.25em] text-secondary mb-1">Reportes de Tienda &amp; Control de Inventario</p>
                <h1 class="text-2xl font-bold text-on-surface font-quicksand">Módulo de Reportes &amp; Exportación (CSV y PDF)</h1>
                <p class="text-xs text-on-surface-variant mt-1">
                    Resúmenes calculados sobre el estado del catálogo, inventario disponible, certificados de autenticidad y ventas recientes.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Botón Importar Catálogo CSV --}}
                <a href="{{ route('compras.productos') }}"
                   id="btn-importar-csv"
                   class="btn-glass-surface px-4 py-2.5 rounded-xl text-secondary text-xs font-label-md flex items-center gap-2 shadow-xs">
                    <span class="material-symbols-outlined text-[18px]">upload_file</span>
                    <span>Importar Catálogo (CSV)</span>
                </a>

                {{-- Botón Exportar CSV --}}
                <a href="{{ route('admin.reportes.csv', array_filter(['sucursal_id' => $sucursalSeleccionada, 'tipo_catalogo' => $categoriaSeleccionada])) }}"
                   id="btn-exportar-csv"
                   class="btn-glass-surface px-4 py-2.5 rounded-xl text-primary text-xs font-label-md flex items-center gap-2 shadow-xs">
                    <span class="material-symbols-outlined text-[18px]">table_view</span>
                    <span>Exportar CSV (.csv)</span>
                </a>

                {{-- Botón Vista Previa Imprimible Oficial --}}
                <a href="{{ route('admin.reportes.pdf', array_filter(['sucursal_id' => $sucursalSeleccionada, 'tipo_catalogo' => $categoriaSeleccionada, 'vista_previa' => 1])) }}"
                   target="_blank"
                   class="btn-glass-surface px-4 py-2.5 rounded-xl text-on-surface-variant text-xs font-label-md flex items-center gap-2 shadow-xs">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    <span>Hoja Oficial</span>
                </a>

                {{-- Botón Exportar PDF Oficial --}}
                <a href="{{ route('admin.reportes.pdf', array_filter(['sucursal_id' => $sucursalSeleccionada, 'tipo_catalogo' => $categoriaSeleccionada])) }}"
                   id="btn-exportar-pdf"
                   class="btn-glass-purple px-5 py-2.5 rounded-xl text-xs font-label-md flex items-center gap-2 shadow-md">
                    <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
                    <span>Descargar PDF Oficial (.pdf)</span>
                </a>
            </div>
        </div>

        {{-- Barra de Filtros Dinámicos por Sucursal y Categoría --}}
        <form method="GET" action="{{ route('admin.reportes') }}" class="bg-surface-container-lowest p-4 rounded-2xl border border-purple-200/70 flex flex-wrap items-end gap-4 shadow-2xs">
            <div class="flex-1 min-w-[220px]">
                <label for="sucursal_id" class="block font-label-code text-[10px] uppercase tracking-widest text-secondary mb-1">Sucursal</label>
                <select id="sucursal_id" name="sucursal_id" class="w-full px-3.5 py-2 rounded-xl border border-purple-200 text-sm bg-white">
                    <option value="">Todas las Sucursales Activas</option>
                    @foreach($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}" @selected($sucursalSeleccionada === $sucursal->id)>
                            {{ $sucursal->nombre }} ({{ $sucursal->ciudad }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-[220px]">
                <label for="tipo_catalogo" class="block font-label-code text-[10px] uppercase tracking-widest text-secondary mb-1">Categoría</label>
                <select id="tipo_catalogo" name="tipo_catalogo" class="w-full px-3.5 py-2 rounded-xl border border-purple-200 text-sm bg-white">
                    <option value="">Catálogo Completo (Todas las Categorías)</option>
                    <option value="libro" @selected($categoriaSeleccionada === 'libro')>Libros &amp; Ediciones Especiales</option>
                    <option value="disco" @selected($categoriaSeleccionada === 'disco')>Discos &amp; Vinilos</option>
                    <option value="coleccionable" @selected($categoriaSeleccionada === 'coleccionable')>Coleccionables</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="btn-glass-purple px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-wider cursor-pointer">
                    Aplicar Filtro
                </button>
                @if($sucursalSeleccionada !== null || $categoriaSeleccionada !== null)
                    <a href="{{ route('admin.reportes') }}" class="btn-glass-surface px-4 py-2 rounded-xl text-xs font-label-code uppercase tracking-wider">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>

        {{-- 1. KPIs Ejecutivos Calculados del Estado del Catálogo --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 p-5 shadow-2xs">
                <span class="font-label-code text-[10px] uppercase tracking-widest text-secondary block mb-1.5">Valor Total del Catálogo</span>
                <span class="text-2xl font-bold text-on-surface font-quicksand">
                    ${{ number_format($resumen['valor_total_acervo'], 2) }} <span class="text-xs font-normal text-outline">MXN</span>
                </span>
                <div class="mt-2.5 flex items-center gap-1.5 text-secondary text-xs font-label-code">
                    <span class="material-symbols-outlined text-sm">verified</span>
                    <span>Inventario valorizado activo</span>
                </div>
            </div>

            <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 p-5 shadow-2xs">
                <span class="font-label-code text-[10px] uppercase tracking-widest text-secondary block mb-1.5">Productos Disponibles en Inventario</span>
                <span class="text-2xl font-bold text-on-surface font-quicksand">
                    {{ number_format($resumen['total_unidades_boveda']) }} <span class="text-xs font-normal text-outline">unidades</span>
                </span>
                <div class="mt-2.5 flex items-center justify-between text-xs font-label-code text-on-surface-variant">
                    <span>{{ $resumen['total_skus'] }} SKUs registrados</span>
                    <span class="text-amber-700 font-semibold">{{ $resumen['reservas_activas'] }} en carrito</span>
                </div>
            </div>

            <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 p-5 shadow-2xs">
                <span class="font-label-code text-[10px] uppercase tracking-widest text-secondary block mb-1.5">Certificados de Autenticidad</span>
                <span class="text-2xl font-bold text-on-surface font-quicksand">
                    {{ number_format($rotacionPiezas['total_piezas']) }} <span class="text-xs font-normal text-outline">series</span>
                </span>
                <div class="mt-2.5 flex items-center justify-between text-xs font-label-code text-on-surface-variant">
                    <span>Disponibles: {{ $rotacionPiezas['total_disponibles'] }}</span>
                    <span class="text-primary font-semibold">Rotación: {{ number_format($rotacionPiezas['tasa_rotacion_porcentaje'], 1) }}%</span>
                </div>
            </div>

            <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 p-5 shadow-2xs">
                <span class="font-label-code text-[10px] uppercase tracking-widest text-secondary block mb-1.5">Transacciones &amp; Alertas</span>
                <span class="text-2xl font-bold text-on-surface font-quicksand">
                    ${{ number_format($resumen['ingresos_acumulados'], 2) }}
                </span>
                <div class="mt-2.5 flex items-center justify-between text-xs font-label-code">
                    <span class="text-emerald-700 font-semibold">{{ $resumen['total_pedidos_confirmados'] }} órdenes confirmadas</span>
                    <span class="{{ $resumen['skus_agotados'] > 0 ? 'text-red-600 font-bold' : 'text-outline' }}">
                        {{ $resumen['skus_agotados'] }} agotados
                    </span>
                </div>
            </div>
        </div>

        {{-- 2. Desglose por Categoría y Valoración por Sucursal --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Estado del Catálogo por Categoría --}}
            <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 p-5 shadow-2xs space-y-3">
                <div class="flex items-center justify-between border-b border-purple-100 pb-3">
                    <div>
                        <p class="font-label-code text-[10px] uppercase tracking-widest text-secondary">Distribución por Categoría</p>
                        <h2 class="text-lg font-bold text-on-surface font-quicksand">Estado del Catálogo por Categoría</h2>
                    </div>
                    <span class="material-symbols-outlined text-secondary">category</span>
                </div>

                <div class="divide-y divide-purple-100">
                    @foreach($resumen['desglose_categorias'] as $clave => $cat)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-sm text-on-surface">{{ $cat['etiqueta'] }}</p>
                                <p class="font-label-code text-[11px] text-on-surface-variant">
                                    {{ $cat['skus'] }} SKUs registrados &middot; {{ $cat['unidades'] }} unidades disponibles
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-on-surface font-label-code">${{ number_format($cat['valor_tasado'], 2) }} MXN</p>
                                <a href="{{ route('admin.reportes.csv', ['tipo_catalogo' => $clave]) }}" class="font-label-code text-[10px] uppercase tracking-widest text-primary hover:underline">
                                    Exportar CSV categoría →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Valor de Inventario por Sucursal --}}
            <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 p-5 shadow-2xs space-y-3">
                <div class="flex items-center justify-between border-b border-purple-100 pb-3">
                    <div>
                        <p class="font-label-code text-[10px] uppercase tracking-widest text-secondary">Sucursales Físicas</p>
                        <h2 class="text-lg font-bold text-on-surface font-quicksand">Valor de Inventario por Sucursal</h2>
                    </div>
                    <span class="material-symbols-outlined text-secondary">account_balance</span>
                </div>

                <div class="divide-y divide-purple-100">
                    @forelse($valoracionSucursales as $suc)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-sm text-on-surface">{{ $suc['sucursal_nombre'] }}</p>
                                <p class="font-label-code text-[11px] text-on-surface-variant">
                                    {{ $suc['total_skus'] }} SKUs asignados &middot; {{ $suc['total_unidades'] }} unidades físicas
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-on-surface font-label-code">${{ number_format($suc['valor_total_inventario'], 2) }} MXN</p>
                                <a href="{{ route('admin.reportes.pdf', ['sucursal_id' => $suc['sucursal_id']]) }}" class="font-label-code text-[10px] uppercase tracking-widest text-primary hover:underline">
                                    Emitir PDF de sede →
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="py-6 text-xs font-label-code text-on-surface-variant">No hay sucursales activas registradas para el filtro actual.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- 3. Desglose Completo del Inventario Actual --}}
        <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 overflow-hidden shadow-xs">
            <div class="px-5 py-4 bg-surface-container-low border-b border-purple-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <p class="font-label-code text-[10px] uppercase tracking-widest text-secondary">Catálogo Unificado &amp; Certificados de Autenticidad</p>
                    <h2 class="text-lg font-bold text-on-surface font-quicksand">Inventario Actual de Productos Disponibles ({{ $filasInventario->count() }} registros)</h2>
                </div>
                <div class="flex flex-wrap items-center gap-2 font-label-code text-[11px]">
                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-semibold">
                        Disponibles: {{ $filasInventario->where('estado_stock', 'EN BOVEDA')->count() }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-semibold">
                        Bajo Mínimo: {{ $filasInventario->where('estado_stock', 'BAJO MINIMO')->count() }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full bg-red-100 text-red-700 font-semibold">
                        Agotados: {{ $filasInventario->where('estado_stock', 'AGOTADO')->count() }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-container-low border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                        <tr>
                            <th class="py-3 px-4">SKU</th>
                            <th class="py-3 px-4">Producto</th>
                            <th class="py-3 px-4">Categoría</th>
                            <th class="py-3 px-4">Sucursal</th>
                            <th class="py-3 px-4">Certificado / Serie</th>
                            <th class="py-3 px-4 text-center">Stock Disponible</th>
                            <th class="py-3 px-4 text-right">Precio Unitario</th>
                            <th class="py-3 px-4 text-right">Valor Total</th>
                            <th class="py-3 px-4 text-center">Estatus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-purple-100">
                        @forelse($filasInventario as $fila)
                            <tr class="hover:bg-surface-container-low/50 transition">
                                <td class="py-3 px-4 font-label-code font-bold text-primary">{{ $fila['sku'] }}</td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-on-surface">{{ $fila['producto'] }}</div>
                                    <div class="text-xs text-on-surface-variant">{{ $fila['condicion'] }}</div>
                                </td>
                                <td class="py-3 px-4 text-xs text-on-surface-variant">{{ $fila['categoria_etiqueta'] }}</td>
                                <td class="py-3 px-4 text-xs text-on-surface-variant">{{ $fila['sucursal'] }}</td>
                                <td class="py-3 px-4 font-label-code text-xs text-on-surface-variant">{{ $fila['certificados_coa'] }}</td>
                                <td class="py-3 px-4 text-center font-bold">
                                    {{ $fila['stock_boveda'] }} <span class="text-xs font-normal text-on-surface-variant">/ mín {{ $fila['stock_minimo'] }}</span>
                                </td>
                                <td class="py-3 px-4 text-right font-label-code text-xs text-on-surface">
                                    ${{ number_format($fila['precio_unitario'], 2) }}
                                </td>
                                <td class="py-3 px-4 text-right font-label-code text-xs font-bold text-on-surface">
                                    ${{ number_format($fila['valor_total'], 2) }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($fila['estado_stock'] === 'AGOTADO')
                                        <span class="px-2.5 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">AGOTADO</span>
                                    @elseif($fila['estado_stock'] === 'BAJO MINIMO')
                                        <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold">BAJO MÍNIMO</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold">DISPONIBLE</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-on-surface-variant">
                                    No se encontraron existencias para los criterios seleccionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 4. Transacciones y Pedidos Recientes --}}
        <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 overflow-hidden shadow-xs">
            <div class="px-5 py-4 bg-surface-container-low border-b border-purple-200/60 flex items-center justify-between">
                <div>
                    <p class="font-label-code text-[10px] uppercase tracking-widest text-secondary">Actividad de Clientes</p>
                    <h2 class="text-lg font-bold text-on-surface font-quicksand">Transacciones y Pedidos Recientes</h2>
                </div>
                <span class="material-symbols-outlined text-secondary">receipt_long</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-container-low border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                        <tr>
                            <th class="py-3 px-4">Folio</th>
                            <th class="py-3 px-4">Cliente</th>
                            <th class="py-3 px-4">Productos Comprados</th>
                            <th class="py-3 px-4">Estatus Pedido</th>
                            <th class="py-3 px-4">Guía / Envío</th>
                            <th class="py-3 px-4 text-right">Monto Total</th>
                            <th class="py-3 px-4 text-right">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-purple-100">
                        @forelse($transaccionesRecientes as $pedido)
                            @php
                                $envio = $pedido->envios->first();
                            @endphp
                            <tr class="hover:bg-surface-container-low/50 transition">
                                <td class="py-3 px-4 font-label-code font-bold text-primary">{{ $pedido->folio }}</td>
                                <td class="py-3 px-4 text-on-surface">
                                    {{ $pedido->cliente?->nombre_completo ?? $pedido->cliente?->usuario?->name ?? 'Cliente Registrado' }}
                                </td>
                                <td class="py-3 px-4 text-xs text-on-surface-variant">
                                    {{ $pedido->detalles->sum('cantidad') }} unidad(es)
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-1 rounded-full bg-purple-100 text-primary text-xs font-label-code uppercase">
                                        {{ strtoupper($pedido->estatus) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-label-code text-xs text-on-surface-variant">
                                    {{ $envio?->numero_guia ?? 'En preparación' }}
                                </td>
                                <td class="py-3 px-4 text-right font-label-code font-bold text-on-surface">
                                    ${{ number_format((float) $pedido->total, 2) }} MXN
                                </td>
                                <td class="py-3 px-4 text-right font-label-code text-xs text-on-surface-variant">
                                    {{ $pedido->created_at?->format('d/m/Y H:i') ?? 'Reciente' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-on-surface-variant">
                                    Aún no se han registrado ventas en la tienda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-layouts.admin>
