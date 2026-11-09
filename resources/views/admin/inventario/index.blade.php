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
                    Sede: {{ $sucursalActiva?->nombre ?? 'Todas las Sedes (Vista Global)' }} &middot; Total en custodia: {{ number_format($totalUnidades) }} unidades &middot; Alertas de stock mínimo: {{ $alertasStock }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if (auth()->user()?->esAdmin())
                    <a href="{{ route('admin.reportes.csv', array_filter(['sucursal_id' => $sucursalActiva?->id ?? request('sucursal_id')])) }}" class="px-4 py-2 rounded-xl border border-purple-200 bg-white hover:bg-purple-50 text-primary text-xs font-label-md flex items-center gap-1.5 transition">
                        <span class="material-symbols-outlined text-[16px]">table_view</span>
                        <span>Exportar CSV (.csv)</span>
                    </a>
                    <a href="{{ route('admin.reportes.pdf', array_filter(['sucursal_id' => $sucursalActiva?->id ?? request('sucursal_id')])) }}" class="px-4 py-2 rounded-xl border border-purple-200 bg-white hover:bg-purple-50 text-primary text-xs font-label-md flex items-center gap-1.5 transition">
                        <span class="material-symbols-outlined text-[16px]">picture_as_pdf</span>
                        <span>Exportar PDF (.pdf)</span>
                    </a>
                @endif
                @if ($puedeAjustar)
                    <a href="{{ route('almacen.ajustes', array_filter(['sucursal_id' => $sucursalActiva?->id ?? request('sucursal_id')])) }}" class="px-4 py-2 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-label-md flex items-center gap-1.5 transition">
                        <span class="material-symbols-outlined text-[16px]">tune</span>
                        <span>Ajustes &amp; Recepciones</span>
                    </a>
                    <a href="{{ route('almacen.transferencias', array_filter(['sucursal_id' => $sucursalActiva?->id ?? request('sucursal_id')])) }}" class="px-4 py-2 rounded-xl border border-purple-200 bg-white hover:bg-purple-50 text-primary text-xs font-label-md flex items-center gap-1.5 transition">
                        <span class="material-symbols-outlined text-[16px]">swap_horiz</span>
                        <span>Transferencias</span>
                    </a>
                    <a href="{{ route('almacen.envios', array_filter(['sucursal_id' => $sucursalActiva?->id ?? request('sucursal_id')])) }}" class="px-4 py-2 rounded-xl border border-purple-200 bg-white hover:bg-purple-50 text-primary text-xs font-label-md flex items-center gap-1.5 transition">
                        <span class="material-symbols-outlined text-[16px]">local_shipping</span>
                        <span>Envíos &amp; Despacho</span>
                    </a>
                @endif
            </div>
        </div>

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
            <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold uppercase tracking-wider">Filtrar</button>
        </form>

        <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 overflow-hidden shadow-xs">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-container-low border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                    <tr>
                        <th class="py-3 px-4">SKU</th>
                        <th class="py-3 px-4">Producto / Variante</th>
                        <th class="py-3 px-4">Régimen de Inventario</th>
                        <th class="py-3 px-4">Sucursal</th>
                        <th class="py-3 px-4 text-right">Existencia</th>
                        <th class="py-3 px-4 text-right">Stock Mínimo</th>
                        <th class="py-3 px-4 text-right">Acciones en Sede</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-100">
                    @forelse ($existencias as $item)
                        @php
                            $piezaUnicaRow = $item->piezasUnicas?->first();
                        @endphp
                        <tr class="hover:bg-surface-container-low/50">
                            <td class="py-3 px-4 font-label-code font-bold text-primary">{{ $item->varianteSku?->sku ?? 'N/A' }}</td>
                            <td class="py-3 px-4">{{ $item->varianteSku?->producto?->nombre ?? $item->varianteSku?->descripcion ?? 'Sin descripción' }}</td>
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
                            <td class="py-3 px-4 text-right font-tabular-data font-bold {{ $item->cantidad <= $item->stock_minimo ? 'text-error' : 'text-emerald-700' }}">{{ $item->cantidad }}</td>
                            <td class="py-3 px-4 text-right font-tabular-data text-outline">{{ $item->stock_minimo }}</td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center justify-end gap-1.5">
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
                            <td colspan="7" class="py-8 text-center text-outline text-sm">No se encontraron registros de inventario con los criterios seleccionados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>

