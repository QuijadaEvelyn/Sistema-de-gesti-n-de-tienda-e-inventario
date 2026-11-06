<x-layouts.admin title="ARCHIVUM · Ajustes y Recepciones de Inventario" active-route="almacen-ajustes">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-on-surface font-quicksand">Ajustes de Stock &amp; Recepciones</h1>
                <p class="text-xs text-on-surface-variant font-label-code uppercase tracking-wider mt-1">Registro auditable de entradas, salidas y ajustes físicos</p>
            </div>
            <a href="{{ route('admin.inventario') }}" class="px-4 py-2 rounded-xl border border-purple-200 bg-white text-xs font-label-md text-primary">&larr; Inventario Unificado</a>
        </div>

        <form method="POST" action="{{ route('almacen.ajustes.store') }}" class="bg-surface-container-lowest p-6 rounded-2xl border border-purple-200/70 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-xs font-label-code uppercase mb-1">Existencia / SKU</label>
                <select name="existencia_id" required class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm bg-white">
                    @foreach ($existencias as $ex)
                        <option value="{{ $ex->id }}">{{ $ex->varianteSku?->sku }} · {{ $ex->sucursal?->nombre }} (Actual: {{ $ex->cantidad }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-label-code uppercase mb-1">Tipo de Movimiento</label>
                <select name="tipo" required class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm bg-white">
                    <option value="entrada" @selected($tipoPreseleccionado === 'entrada')>Entrada / Recepción</option>
                    <option value="salida" @selected($tipoPreseleccionado === 'salida')>Salida / Merma</option>
                    <option value="ajuste" @selected($tipoPreseleccionado === 'ajuste')>Ajuste Físico</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-label-code uppercase mb-1">Cantidad</label>
                <input type="number" name="cantidad" min="1" required class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm" />
            </div>
            <div>
                <label class="block text-xs font-label-code uppercase mb-1">Motivo</label>
                <input type="text" name="motivo" required placeholder="Ej. Conteo cíclico" class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm" />
            </div>
            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-bold uppercase tracking-wider">Registrar Movimiento</button>
            </div>
        </form>

        <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-container-low border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                    <tr>
                        <th class="py-3 px-4">Fecha</th>
                        <th class="py-3 px-4">Tipo</th>
                        <th class="py-3 px-4">SKU / Sucursal</th>
                        <th class="py-3 px-4 text-right">Cantidad</th>
                        <th class="py-3 px-4">Motivo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-100">
                    @forelse ($movimientos as $mov)
                        <tr>
                            <td class="py-3 px-4 font-label-code text-xs">{{ $mov->created_at }}</td>
                            <td class="py-3 px-4 uppercase font-label-code text-xs font-bold">{{ $mov->tipo }}</td>
                            <td class="py-3 px-4">{{ $mov->existencia?->varianteSku?->sku }} · {{ $mov->existencia?->sucursal?->nombre }}</td>
                            <td class="py-3 px-4 text-right font-tabular-data font-bold">{{ $mov->cantidad }}</td>
                            <td class="py-3 px-4 text-xs text-on-surface-variant">{{ $mov->motivo }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-outline">Sin movimientos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>

