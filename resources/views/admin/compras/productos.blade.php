<x-layouts.admin title="ARCHIVUM · Catálogo y Altas de Productos" active-route="compras-productos">
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-on-surface font-quicksand">Altas de Productos &amp; Variantes SKU</h1>

        <form method="POST" action="{{ route('compras.productos.store') }}" class="bg-surface-container-lowest p-6 rounded-2xl border border-purple-200/70 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-xs font-label-code uppercase mb-1">Catálogo</label>
                <select name="catalogo_id" required class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm bg-white">
                    @foreach ($catalogos as $cat)<option value="{{ $cat->id }}">{{ $cat->nombre }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-label-code uppercase mb-1">Nombre del Producto</label>
                <input type="text" name="nombre" required class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm" />
            </div>
            <div>
                <label class="block text-xs font-label-code uppercase mb-1">Código SKU</label>
                <input type="text" name="sku" required placeholder="ARC-VIN-001" class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm" />
            </div>
            <div>
                <label class="block text-xs font-label-code uppercase mb-1">Precio Base (MXN)</label>
                <input type="number" step="0.01" min="0" name="precio" required class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm" />
            </div>
            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-bold uppercase tracking-wider">Registrar Producto</button>
            </div>
        </form>

        <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-container-low border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                    <tr>
                        <th class="py-3 px-4">ID</th>
                        <th class="py-3 px-4">Producto</th>
                        <th class="py-3 px-4">Catálogo</th>
                        <th class="py-3 px-4 text-right">Precio</th>
                        <th class="py-3 px-4 text-right">Variantes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-100">
                    @forelse ($productos as $prod)
                        <tr>
                            <td class="py-3 px-4 font-label-code">#{{ $prod->id }}</td>
                            <td class="py-3 px-4 font-bold">{{ $prod->nombre }}</td>
                            <td class="py-3 px-4">{{ $prod->catalogo?->nombre }}</td>
                            <td class="py-3 px-4 text-right font-tabular-data font-bold">${{ number_format((float) $prod->precio, 2) }}</td>
                            <td class="py-3 px-4 text-right font-tabular-data">{{ $prod->variantes_sku_count }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-outline">No hay productos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>

