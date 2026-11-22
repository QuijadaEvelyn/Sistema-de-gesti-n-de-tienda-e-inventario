<x-layouts.admin title="ARCHIVUM · Inventario Unificado" active-route="inventario">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 text-[10px] font-label-code uppercase tracking-widest text-primary font-bold mb-1">
                    <span class="material-symbols-outlined text-[14px]">warehouse</span>
                    <span>Panel Operativo de Sede &middot; Gestión de Almacén e Inventario</span>
                </span>
                <h1 class="text-2xl font-bold text-on-surface font-quicksand">
                    Inventario Unificado por Sucursal
                    @if (! empty($sucursalActiva))
                        <span class="text-primary">· {{ $sucursalActiva->nombre }}</span>
                    @endif
                </h1>
                <p class="text-xs text-on-surface-variant font-label-code uppercase tracking-wider mt-1">
                    Sucursal: {{ $sucursalActiva?->nombre ?? 'Todas las Sucursales (Vista Global)' }} &middot; Total disponible: {{ number_format($totalUnidades) }} unidades &middot; Alertas de stock mínimo: {{ $alertasStock }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if (auth()->user()?->esAdmin() || auth()->user()?->esEmpleado())
                    <a href="{{ route('admin.productos.create') }}" class="btn-glass-purple px-4 py-2 rounded-xl text-white text-xs font-label-md font-bold uppercase tracking-wider flex items-center gap-1.5 shadow-sm transition">
                        <span class="material-symbols-outlined text-[16px]">add_circle</span>
                        <span>+ Dar de Alta Producto</span>
                    </a>
                    <a href="{{ route('admin.productos.index') }}" class="px-4 py-2 rounded-xl border border-purple-200 bg-purple-100 hover:bg-purple-200 text-purple-900 dark:bg-white/5 dark:border-purple-500/30 dark:text-purple-300 dark:hover:bg-white/10 text-xs font-label-md flex items-center gap-1.5 transition">
                        <span class="material-symbols-outlined text-[16px]">library_add</span>
                        <span>Catálogo, Altas & Bajas</span>
                    </a>
                @endif
                @if (auth()->user()?->esAdmin())
                    <a href="{{ route('admin.reportes.csv', array_filter(['sucursal_id' => $sucursalActiva?->id ?? request('sucursal_id')])) }}" class="px-4 py-2 rounded-xl border border-purple-200 bg-purple-100 hover:bg-purple-200 text-purple-800 dark:bg-white/5 dark:border-purple-500/30 dark:text-purple-300 dark:hover:bg-white/10 text-xs font-label-md flex items-center gap-1.5 transition">
                        <span class="material-symbols-outlined text-[16px]">table_view</span>
                        <span>Exportar CSV (.csv)</span>
                    </a>
                    <a href="{{ route('admin.reportes.pdf', array_filter(['sucursal_id' => $sucursalActiva?->id ?? request('sucursal_id')])) }}" class="px-4 py-2 rounded-xl border border-purple-200 bg-purple-100 hover:bg-purple-200 text-purple-800 dark:bg-white/5 dark:border-purple-500/30 dark:text-purple-300 dark:hover:bg-white/10 text-xs font-label-md flex items-center gap-1.5 transition">
                        <span class="material-symbols-outlined text-[16px]">picture_as_pdf</span>
                        <span>Exportar PDF (.pdf)</span>
                    </a>
                @endif
                @if ($puedeAjustar)
                    <a href="{{ route('almacen.ajustes', array_filter(['sucursal_id' => $sucursalActiva?->id ?? request('sucursal_id')])) }}" class="px-4 py-2 rounded-xl bg-purple-500 hover:bg-purple-600 dark:bg-purple-600 dark:hover:bg-purple-500 text-white text-xs font-label-md flex items-center gap-1.5 transition shadow-sm">
                        <span class="material-symbols-outlined text-[16px]">tune</span>
                        <span>Ajustes &amp; Recepciones</span>
                    </a>
                    <a href="{{ route('almacen.transferencias', array_filter(['sucursal_id' => $sucursalActiva?->id ?? request('sucursal_id')])) }}" class="px-4 py-2 rounded-xl border border-purple-200 bg-purple-100 hover:bg-purple-200 text-purple-800 dark:bg-white/5 dark:border-purple-500/30 dark:text-purple-300 dark:hover:bg-white/10 text-xs font-label-md flex items-center gap-1.5 transition">
                        <span class="material-symbols-outlined text-[16px]">swap_horiz</span>
                        <span>Transferencias</span>
                    </a>
                    <a href="{{ route('almacen.envios', array_filter(['sucursal_id' => $sucursalActiva?->id ?? request('sucursal_id')])) }}" class="px-4 py-2 rounded-xl border border-purple-200 bg-purple-100 hover:bg-purple-200 text-purple-800 dark:bg-white/5 dark:border-purple-500/30 dark:text-purple-300 dark:hover:bg-white/10 text-xs font-label-md flex items-center gap-1.5 transition">
                        <span class="material-symbols-outlined text-[16px]">local_shipping</span>
                        <span>Envíos &amp; Despacho</span>
                    </a>
                @endif
            </div>
        </div>

        @if (session('status'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-700/60 text-emerald-800 dark:text-emerald-200 text-sm flex items-center gap-3 shadow-xs">
                <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">check_circle</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-700/60 text-rose-800 dark:text-rose-200 text-sm space-y-1.5 shadow-xs">
                <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wider">
                    <span class="material-symbols-outlined text-rose-600 dark:text-rose-400 text-[18px]">error</span>
                    <span>No fue posible procesar la solicitud:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="GET" action="{{ route('admin.inventario') }}" class="bg-surface-container-lowest p-4 rounded-2xl border border-purple-200/70 flex flex-wrap items-center gap-3">
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar SKU o producto..." class="flex-1 min-w-[200px] px-3.5 py-2 rounded-xl border border-purple-200 text-sm" />
            <select name="sucursal_id" onchange="this.form.submit()" class="px-3.5 py-2 rounded-xl border border-purple-200 text-sm bg-white">
                @if ($puedeVerGlobal ?? true)
                    <option value="all" @selected(($sucursalActiva ?? null) === null)>Todas las sucursales</option>
                @endif
                @foreach ($sucursales as $sucursal)
                    <option value="{{ $sucursal->id }}" @selected(($sucursalActiva?->id ?? (int) request('sucursal_id')) === $sucursal->id)>{{ $sucursal->nombre }}</option>
                @endforeach
            </select>
            <label class="flex items-center gap-2 text-xs text-on-surface-variant">
                <input type="checkbox" name="bajo_stock" value="1" @checked(request()->boolean('bajo_stock')) class="rounded border-purple-300 text-primary" />
                <span>Solo stock crítico</span>
            </label>
            <button type="submit" class="px-4 py-2 rounded-xl bg-purple-500 hover:bg-purple-600 dark:bg-purple-600 dark:hover:bg-purple-500 text-white text-xs font-bold uppercase tracking-wider transition shadow-sm cursor-pointer">Filtrar</button>
        </form>

        <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 overflow-hidden shadow-xs">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-container-low border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                    <tr>
                        <th class="py-3 px-4">SKU</th>
                        <th class="py-3 px-4">Producto / Variante</th>
                        <th class="py-3 px-4">Régimen de Inventario</th>
                        <th class="py-3 px-4">Sucursal</th>
                        <th class="py-3 px-4 text-right">Precio (MXN)</th>
                        <th class="py-3 px-4 text-right">Existencia</th>
                        <th class="py-3 px-4 text-right">Stock Mínimo</th>
                        <th class="py-3 px-4 text-right">Acciones en Sede</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-100">
                    @forelse ($existencias as $item)
                        @php
                            $piezaUnicaRow = $item->piezasUnicas?->first();
                            $productoRow = $item->varianteSku?->producto;
                            $precioActualRow = (float) ($productoRow?->precio ?? $item->varianteSku?->precio ?? 0);
                            $skuRow = $item->varianteSku?->sku ?? 'N/A';
                        @endphp
                        <tr class="hover:bg-surface-container-low/50">
                            <td class="py-3 px-4 font-label-code font-bold text-primary">{{ $skuRow }}</td>
                            <td class="py-3 px-4">{{ $productoRow?->nombre ?? $item->varianteSku?->descripcion ?? 'Sin descripción' }}</td>
                            <td class="py-3 px-4">
                                @if ($piezaUnicaRow)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-purple-100 text-primary border border-purple-200 text-[10px] font-label-code font-bold uppercase">
                                        PIEZA ÚNICA 1/1 &middot; {{ $piezaUnicaRow->numero_serie }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-label-code font-semibold uppercase">
                                        STOCK MASIVO &middot; TIRAJE MÚLTIPLE
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">{{ $item->sucursal?->nombre ?? 'General' }}</td>
                            <td class="py-3 px-4 text-right font-tabular-data font-bold text-on-surface dark:text-white">
                                <div class="inline-flex flex-col items-end gap-1">
                                    <div>
                                        ${{ number_format($precioActualRow, 2) }}
                                        <span class="text-[10px] text-on-surface-variant font-normal">MXN</span>
                                    </div>
                                    @if ($productoRow)
                                        <button
                                            type="button"
                                            onclick="abrirModalEditarPrecioInventario({{ $productoRow->id }}, '{{ addslashes($productoRow->nombre) }}', '{{ number_format($precioActualRow, 2, '.', '') }}', '{{ route('admin.productos.actualizar-precio', $productoRow->id) }}', '{{ $skuRow }}')"
                                            class="px-2 py-0.5 rounded-lg bg-purple-100/80 hover:bg-purple-200 text-purple-900 dark:bg-purple-900/50 dark:hover:bg-purple-800/70 dark:text-purple-200 border border-purple-200 dark:border-purple-700/60 text-[10px] font-label-code font-bold uppercase transition inline-flex items-center gap-1 cursor-pointer"
                                            title="Modificar precio de {{ $productoRow->nombre }}"
                                        >
                                            <span class="material-symbols-outlined text-[12px]">payments</span>
                                            <span>Editar Precio</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4 text-right font-tabular-data font-bold {{ $item->cantidad <= $item->stock_minimo ? 'text-error' : 'text-emerald-700' }}">{{ $item->cantidad }}</td>
                            <td class="py-3 px-4 text-right font-tabular-data text-outline">{{ $item->stock_minimo }}</td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    @if ($productoRow)
                                        <button
                                            type="button"
                                            onclick="abrirModalEditarPrecioInventario({{ $productoRow->id }}, '{{ addslashes($productoRow->nombre) }}', '{{ number_format($precioActualRow, 2, '.', '') }}', '{{ route('admin.productos.actualizar-precio', $productoRow->id) }}', '{{ $skuRow }}')"
                                            class="px-2.5 py-1 rounded-lg bg-purple-50 hover:bg-purple-100 dark:bg-purple-950/40 dark:hover:bg-purple-900/60 text-purple-800 dark:text-purple-200 border border-purple-200 dark:border-purple-700/60 text-[11px] font-label-code font-bold transition inline-flex items-center gap-1 cursor-pointer"
                                            title="Editar precio de {{ $productoRow->nombre }}"
                                        >
                                            <span class="material-symbols-outlined text-[14px]">attach_money</span>
                                            <span>Editar Precio</span>
                                        </button>
                                        <a
                                            href="{{ route('admin.productos.edit', $productoRow->id) }}"
                                            class="px-2.5 py-1 rounded-lg bg-surface-container-low hover:bg-purple-100 dark:hover:bg-purple-900/40 text-purple-700 dark:text-purple-300 border border-purple-200/70 text-[11px] font-label-code font-semibold transition inline-flex items-center gap-1"
                                            title="Editar ficha o gestionar estado de {{ $productoRow->nombre }}"
                                        >
                                            <span class="material-symbols-outlined text-[14px]">edit</span>
                                            <span>Ficha</span>
                                        </a>
                                    @endif
                                    <a
                                        href="{{ route('almacen.ajustes', ['sucursal_id' => $item->sucursal_id]) }}"
                                        class="px-2.5 py-1 rounded-lg bg-purple-50 hover:bg-purple-100 text-primary border border-purple-200 text-[11px] font-label-code font-semibold transition inline-flex items-center gap-1"
                                        title="Ajustar o recepcionar stock en {{ $item->sucursal?->nombre }}"
                                    >
                                        <span class="material-symbols-outlined text-[14px]">tune</span>
                                        <span>Ajustar</span>
                                    </a>
                                    <a
                                        href="{{ route('almacen.transferencias', ['sucursal_id' => $item->sucursal_id]) }}"
                                        class="px-2.5 py-1 rounded-lg bg-surface-container-low hover:bg-surface-container text-on-surface-variant border border-purple-200/70 text-[11px] font-label-code font-semibold transition inline-flex items-center gap-1"
                                        title="Transferir stock desde {{ $item->sucursal?->nombre }}"
                                    >
                                        <span class="material-symbols-outlined text-[14px]">swap_horiz</span>
                                        <span>Transferir</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-outline text-sm">No se encontraron registros de inventario con los criterios seleccionados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal para Edición Rápida de Precio desde Inventario --}}
    <div
        id="modal_editar_precio_inventario"
        class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity duration-200"
        aria-modal="true"
        role="dialog"
    >
        <div class="bg-surface-container-lowest dark:bg-[#1f1730] border border-purple-200/80 dark:border-purple-700/60 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 relative animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-purple-100 dark:border-purple-900/40 pb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary dark:text-purple-300 text-[22px]">payments</span>
                    <h3 class="text-sm font-bold uppercase font-label-code text-on-surface dark:text-white">Editar Precio del Producto</h3>
                </div>
                <button
                    type="button"
                    onclick="cerrarModalEditarPrecioInventario()"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-outline hover:text-on-surface hover:bg-surface-container-low transition cursor-pointer"
                    title="Cerrar modal"
                >
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            <div class="space-y-1">
                <span id="inv_modal_precio_sku" class="inline-block text-[10px] font-label-code font-bold uppercase px-2 py-0.5 rounded-md bg-purple-100 dark:bg-purple-900/60 text-purple-900 dark:text-purple-200"></span>
                <p id="inv_modal_precio_nombre" class="font-bold text-sm text-on-surface dark:text-white line-clamp-2"></p>
                <p class="text-xs text-on-surface-variant dark:text-purple-200/70">
                    El nuevo precio se aplicará de inmediato en la tienda pública. Los clientes con productos comunes en su carrito activo mantendrán su precio congelado.
                </p>
            </div>

            <form
                id="form_editar_precio_inventario"
                method="POST"
                action=""
                class="space-y-4"
            >
                @csrf
                @method('PATCH')
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-on-surface-variant dark:text-purple-200/70 font-label-code uppercase">Precio Actual:</span>
                        <span id="inv_modal_precio_actual" class="font-tabular-data font-bold text-on-surface dark:text-white"></span>
                    </div>

                    <label for="inv_input_nuevo_precio" class="block text-xs font-label-code uppercase font-bold text-on-surface dark:text-white">
                        Nuevo Precio (MXN) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-sm font-bold text-primary dark:text-purple-300">$</span>
                        <input
                            type="number"
                            step="0.01"
                            min="0.01"
                            max="99999999.99"
                            id="inv_input_nuevo_precio"
                            name="precio"
                            required
                            placeholder="0.00"
                            class="w-full pl-8 pr-14 py-2.5 rounded-xl border border-purple-300 dark:border-purple-700 text-sm font-tabular-data font-bold bg-white dark:bg-[#160f24] text-on-surface dark:text-white focus:outline-none focus:border-primary"
                        />
                        <span class="absolute right-3.5 top-2.5 text-xs font-label-code text-on-surface-variant dark:text-purple-300/70">MXN</span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-purple-100 dark:border-purple-900/40">
                    <button
                        type="button"
                        onclick="cerrarModalEditarPrecioInventario()"
                        class="px-4 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs font-semibold text-outline hover:text-on-surface transition cursor-pointer"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="btn-glass-purple px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider shadow-sm flex items-center gap-1.5 cursor-pointer"
                    >
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                        <span>Guardar Nuevo Precio</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModalEditarPrecioInventario(productoId, nombreProducto, precioActual, actionUrl, sku) {
            const modal = document.getElementById('modal_editar_precio_inventario');
            const form = document.getElementById('form_editar_precio_inventario');
            const lblNombre = document.getElementById('inv_modal_precio_nombre');
            const lblSku = document.getElementById('inv_modal_precio_sku');
            const lblActual = document.getElementById('inv_modal_precio_actual');
            const inputPrecio = document.getElementById('inv_input_nuevo_precio');

            if (!modal || !form) return;

            form.action = actionUrl;
            if (lblNombre) lblNombre.textContent = nombreProducto;
            if (lblSku) lblSku.textContent = sku || ('ID #' + productoId);
            const numPrecio = parseFloat(precioActual || '0');
            if (lblActual) {
                lblActual.textContent = '$' + numPrecio.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' MXN';
            }
            if (inputPrecio) {
                inputPrecio.value = numPrecio.toFixed(2);
            }

            modal.classList.remove('hidden');
            setTimeout(() => {
                inputPrecio?.focus();
                inputPrecio?.select();
            }, 50);
        }

        function cerrarModalEditarPrecioInventario() {
            const modal = document.getElementById('modal_editar_precio_inventario');
            if (modal) modal.classList.add('hidden');
        }
    </script>
</x-layouts.admin>

