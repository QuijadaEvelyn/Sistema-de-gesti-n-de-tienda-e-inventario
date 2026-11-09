<x-layouts.admin title="ARCHIVUM · Transferencias entre Sucursales" active-route="almacen-transferencias">
    <div class="space-y-6">
        <!-- Encabezado Operativo -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-primary text-[11px] font-label-code font-bold uppercase tracking-wider">
                        {{ $sucursalActiva ? 'Bóveda Activa: '.$sucursalActiva->nombre : 'Todas las Sedes (Consolidado)' }}
                    </span>
                    <span class="text-xs text-outline font-label-code">· Custodia Inter-Sede</span>
                </div>
                <h1 class="text-2xl font-bold text-on-surface font-quicksand">Transferencias de Stock Inter-Sucursal</h1>
                <p class="text-xs text-on-surface-variant font-label-code uppercase tracking-wider mt-1">
                    Traslado certificado de ejemplares, vinilos y piezas de archivo entre bóvedas de ARCHIVUM
                </p>
            </div>
            <a href="{{ route('admin.inventario') }}"
               class="px-4 py-2 rounded-xl border border-purple-200 bg-white hover:bg-purple-50 text-xs font-label-md text-primary transition">
                &larr; Inventario Unificado
            </a>
        </div>

        @if (session('status'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulario de Nueva Transferencia -->
        <form method="POST" action="{{ route('almacen.transferencias.store') }}" class="bg-surface-container-lowest p-6 rounded-2xl border border-purple-200/70 grid grid-cols-1 md:grid-cols-4 gap-4 items-end shadow-2xs">
            @csrf
            <div>
                <label class="block text-xs font-label-code uppercase mb-1 text-secondary">Sucursal Origen</label>
                <select name="sucursal_origen_id" required class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary/30">
                    @foreach ($sucursales as $suc)
                        <option value="{{ $suc->id }}" @selected($sucursalActiva && $sucursalActiva->id === $suc->id)>{{ $suc->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-label-code uppercase mb-1 text-secondary">Sucursal Destino</label>
                <select name="sucursal_destino_id" required class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary/30">
                    @foreach ($sucursales as $suc)
                        <option value="{{ $suc->id }}" @selected($sucursalActiva && $sucursalActiva->id !== $suc->id && $loop->index === 1)>{{ $suc->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-label-code uppercase mb-1 text-secondary">Variante SKU</label>
                <select name="variante_sku_id" required class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary/30">
                    @foreach ($variantes as $var)
                        <option value="{{ $var->id }}">
                            {{ $var->sku }} — {{ $var->catalogo?->titulo ?? $var->descripcion }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-label-code uppercase mb-1 text-secondary">Cantidad a Trasladar</label>
                <input type="number" name="cantidad" min="1" value="1" required class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30" />
            </div>
            <div class="md:col-span-4 flex justify-end pt-2 border-t border-purple-100">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-bold uppercase tracking-wider transition cursor-pointer">
                    Enviar Transferencia
                </button>
            </div>
        </form>

        <!-- Tabla de Transferencias Registradas -->
        <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 overflow-hidden shadow-2xs">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-container-low border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                    <tr>
                        <th class="py-3 px-4">Folio</th>
                        <th class="py-3 px-4">SKU / Pieza</th>
                        <th class="py-3 px-4">Ruta (Origen &rarr; Destino)</th>
                        <th class="py-3 px-4 text-right">Cantidad</th>
                        <th class="py-3 px-4">Detalle</th>
                        <th class="py-3 px-4 text-right">Estado / Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-100">
                    @forelse ($salidas as $salida)
                        @php
                            $nombreOrigen = $salida->existencia?->sucursal?->nombre ?? 'Sede Origen';
                            $nombreDestino = $mapaSucursales[(int) $salida->referencia_id] ?? ('Sede #'.$salida->referencia_id);
                            $yaRecibida = in_array($salida->id, $recibidas, true);
                        @endphp
                        <tr class="hover:bg-surface-container-low/50 transition">
                            <td class="py-3 px-4 font-label-code font-bold text-primary">#TR-{{ str_pad((string) $salida->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-on-surface">{{ $salida->existencia?->varianteSku?->sku }}</div>
                                <div class="text-xs text-outline">{{ $salida->existencia?->varianteSku?->catalogo?->titulo ?? $salida->existencia?->varianteSku?->descripcion }}</div>
                            </td>
                            <td class="py-3 px-4 text-xs font-label-code">
                                <span class="text-on-surface font-semibold">{{ $nombreOrigen }}</span>
                                <span class="text-primary mx-1">&rarr;</span>
                                <span class="text-on-surface font-semibold">{{ $nombreDestino }}</span>
                            </td>
                            <td class="py-3 px-4 text-right font-tabular-data font-bold">{{ abs($salida->cantidad) }}</td>
                            <td class="py-3 px-4 text-xs text-on-surface-variant">{{ $salida->motivo }}</td>
                            <td class="py-3 px-4 text-right">
                                @if ($yaRecibida)
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-label-code font-bold">
                                        Recibida en Bóveda
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('almacen.transferencias.confirmar', $salida) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-primary-container hover:bg-primary text-white text-xs font-semibold transition cursor-pointer">
                                            Confirmar Recepción
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-outline">No hay transferencias inter-sucursal registradas todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
