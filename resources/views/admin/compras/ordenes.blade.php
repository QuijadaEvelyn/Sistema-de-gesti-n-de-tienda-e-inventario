<x-layouts.admin title="ARCHIVUM · Órdenes de Compra" active-route="ordenes-compra">
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-on-surface font-quicksand">Órdenes de Compra &amp; Recepciones de Proveedor</h1>
                <p class="text-xs text-on-surface-variant font-label-code uppercase tracking-wider mt-1">Abastecimiento directo a sucursales y surtido de preventas</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('compras.proveedores') }}" class="px-4 py-2 rounded-xl border border-purple-200 bg-white text-xs font-label-md text-primary font-semibold hover:bg-surface-container transition">
                    &larr; Directorio de Proveedores
                </a>
                <a href="{{ route('compras.productos') }}" class="px-4 py-2 rounded-xl bg-primary-container text-white text-xs font-label-md font-bold hover:bg-primary transition">
                    Catálogo de Productos &rarr;
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-800 text-sm flex items-center gap-3 shadow-xs">
                <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-800 text-sm space-y-1 shadow-xs">
                <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wider">
                    <span class="material-symbols-outlined text-rose-600 text-[18px]">error</span>
                    Error al procesar la recepción:
                </div>
                <ul class="list-disc list-inside text-xs pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('compras.ordenes.recepcionar') }}" class="bg-surface-container-lowest p-6 rounded-2xl border border-purple-200/70 grid grid-cols-1 md:grid-cols-4 gap-4 items-end shadow-2xs">
            @csrf
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-label-code uppercase">Proveedor / Editorial</label>
                    <a href="{{ route('compras.proveedores') }}" class="text-[10px] text-primary underline font-label-code">+ Administrar</a>
                </div>
                <select name="proveedor_id" id="selector-proveedor" onchange="toggleInputProveedor(this.value)" class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm bg-white">
                    <option value="">-- Seleccionar Proveedor Registrado --</option>
                    @foreach ($proveedores as $prov)
                        <option value="{{ $prov->id }}">{{ $prov->nombre }} ({{ $prov->rfc ?: 'Sin RFC' }})</option>
                    @endforeach
                    <option value="otro">+ Ingresar otro proveedor manualmente</option>
                </select>
                <input type="text" name="proveedor" id="input-proveedor-manual" placeholder="Nombre o Razón Social del proveedor..." class="hidden mt-2 w-full px-3 py-2 rounded-xl border border-purple-200 text-sm" />
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
                <input type="number" name="cantidad" min="1" required placeholder="Ej. 10" class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm" />
            </div>
            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-bold uppercase tracking-wider transition shadow-sm">
                    Recepcionar Mercancía
                </button>
            </div>
        </form>

        <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 shadow-2xs overflow-hidden">
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
                        <tr class="hover:bg-purple-50/40 transition">
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

    <script>
        function toggleInputProveedor(val) {
            const manualInput = document.getElementById('input-proveedor-manual');
            if (val === 'otro') {
                manualInput.classList.remove('hidden');
                manualInput.required = true;
                manualInput.focus();
            } else {
                manualInput.classList.add('hidden');
                manualInput.required = false;
                manualInput.value = '';
            }
        }
    </script>
</x-layouts.admin>
