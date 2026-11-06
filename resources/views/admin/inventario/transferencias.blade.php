<x-layouts.admin title="ARCHIVUM · Transferencias entre Sucursales" active-route="almacen-transferencias">
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-on-surface font-quicksand">Transferencias de Stock Inter-Sucursal</h1>

        <form method="POST" action="{{ route('almacen.transferencias.store') }}" class="bg-surface-container-lowest p-6 rounded-2xl border border-purple-200/70 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-xs font-label-code uppercase mb-1">Sucursal Origen</label>
                <select name="sucursal_origen_id" required class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm bg-white">
                    @foreach ($sucursales as $suc)<option value="{{ $suc->id }}">{{ $suc->nombre }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-label-code uppercase mb-1">Sucursal Destino</label>
                <select name="sucursal_destino_id" required class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm bg-white">
                    @foreach ($sucursales as $suc)<option value="{{ $suc->id }}">{{ $suc->nombre }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-label-code uppercase mb-1">Variante SKU</label>
                <select name="variante_sku_id" required class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm bg-white">
                    @foreach ($variantes as $var)<option value="{{ $var->id }}">{{ $var->sku }} — {{ $var->descripcion }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-label-code uppercase mb-1">Cantidad</label>
                <input type="number" name="cantidad" min="1" required class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm" />
            </div>
            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-bold uppercase tracking-wider">Enviar Transferencia</button>
            </div>
        </form>

        <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-container-low border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                    <tr>
                        <th class="py-3 px-4">ID</th>
                        <th class="py-3 px-4">SKU / Origen</th>
                        <th class="py-3 px-4">Cantidad</th>
                        <th class="py-3 px-4">Detalle</th>
                        <th class="py-3 px-4 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-100">
                    @forelse ($salidas as $salida)
                        <tr>
                            <td class="py-3 px-4 font-label-code">#{{ $salida->id }}</td>
                            <td class="py-3 px-4">{{ $salida->existencia?->varianteSku?->sku }} ({{ $salida->existencia?->sucursal?->nombre }})</td>
                            <td class="py-3 px-4 font-tabular-data font-bold">{{ abs($salida->cantidad) }}</td>
                            <td class="py-3 px-4 text-xs text-on-surface-variant">{{ $salida->motivo }}</td>
                            <td class="py-3 px-4 text-right">
                                @if (in_array($salida->id, $recibidas, true))
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-label-code">Recibida</span>
                                @else
                                    <form method="POST" action="{{ route('almacen.transferencias.confirmar', $salida) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 rounded-lg bg-primary text-white text-xs font-semibold">Confirmar Recepción</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-outline">No hay transferencias registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>

