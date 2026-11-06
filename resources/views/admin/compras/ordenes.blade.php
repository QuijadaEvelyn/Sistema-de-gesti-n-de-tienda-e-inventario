<x-layouts.admin title="ARCHIVUM · Órdenes de Compra" active-route="ordenes-compra">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-on-surface font-quicksand">Órdenes de Compra &amp; Recepciones de Proveedor</h1>
                <p class="text-xs text-on-surface-variant font-label-code uppercase tracking-wider mt-1">Abastecimiento directo a sucursales y surtido de preventas</p>
            </div>
            <a href="{{ route('compras.productos') }}" class="px-4 py-2 rounded-xl border border-purple-200 bg-white text-xs font-label-md text-primary">Catálogo de Productos &rarr;</a>
        </div>

        <form method="POST" action="{{ route('compras.ordenes.recepcionar') }}" class="bg-surface-container-lowest p-6 rounded-2xl border border-purple-200/70 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-xs font-label-code uppercase mb-1">Proveedor / Editorial</label>
                <input type="text" name="proveedor" required placeholder="Ej. Editorial Sudamericana" class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm" />
            </div>
            <div>
                <label class="block text-xs font-label-code uppercase mb-1">Sucursal Destino</label>
                <select name="sucursal_id" required class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm bg-white">
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
                <label class="block text-xs font-label-code uppercase mb-1">Cantidad Recibida</label>
                <input type="number" name="cantidad" min="1" required class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm" />
            </div>
            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-bold uppercase tracking-wider">Recepcionar Mercancía</button>
            </div>
        </form>

        <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-container-low border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                    <tr>
                        <th class="py-3 px-4">Fecha</th>
                        <th class="py-3 px-4">SKU / Sucursal</th>
                        <th class="py-3 px-4 text-right">Cantidad</th>
                        <th class="py-3 px-4">Detalle de Recepción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-100">
                    @forelse ($recepciones as $rec)
                        <tr>
                            <td class="py-3 px-4 font-label-code text-xs">{{ $rec->created_at }}</td>
                            <td class="py-3 px-4">{{ $rec->existencia?->varianteSku?->sku }} ({{ $rec->existencia?->sucursal?->nombre }})</td>
                            <td class="py-3 px-4 text-right font-tabular-data font-bold text-emerald-700">+{{ $rec->cantidad }}</td>
                            <td class="py-3 px-4 text-xs text-on-surface-variant">{{ $rec->motivo }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-outline">No se han registrado recepciones de proveedor aún.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>

