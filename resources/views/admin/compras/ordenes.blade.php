<x-layouts.admin title="ARCHIVUM · Órdenes de Compra" active-route="ordenes-compra">
    <div class="space-y-6">

        {{-- Encabezado de Sección con Animación y Botones de Acción Glassmórficos --}}
        <div class="bento-reveal bento-delay-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-on-surface font-quicksand">Órdenes de Compra &amp; Recepciones de Proveedor</h1>
                <p class="text-xs text-on-surface-variant font-label-code uppercase tracking-wider mt-1">Abastecimiento directo a sucursales y surtido de preventas</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('compras.proveedores') }}" class="btn-glass-surface px-4 py-2.5 rounded-xl text-xs font-label-md font-semibold text-primary dark:text-[#ccbeff] shadow-xs inline-flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">handshake</span>
                    <span>&larr; Directorio de Proveedores</span>
                </a>
                <a href="{{ route('compras.productos') }}" class="btn-glass-purple px-4 py-2.5 rounded-xl text-white text-xs font-label-md font-bold shadow-xs inline-flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">library_add</span>
                    <span>Catálogo de Productos &rarr;</span>
                </a>
            </div>
        </div>

        {{-- Alertas de Éxito o Error Glassmorphic --}}
        @if (session('status'))
            <div class="bento-reveal bento-delay-0 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-700/60 text-emerald-800 dark:text-emerald-200 text-sm flex items-center gap-3 shadow-xs">
                <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">check_circle</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bento-reveal bento-delay-0 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-700/60 text-rose-800 dark:text-rose-200 text-sm space-y-1.5 shadow-xs">
                <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wider">
                    <span class="material-symbols-outlined text-rose-600 dark:text-rose-400 text-[18px]">error</span>
                    <span>Error al procesar la recepción:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Formulario de Recepción con Estética Glossy Glassmorphism --}}
        <div class="bento-reveal bento-delay-60 bg-white/90 dark:bg-[#1a1626]/90 backdrop-blur-md p-6 rounded-2xl border border-purple-200/70 dark:border-white/10 shadow-2xs transition-all duration-300 hover:border-purple-300 dark:hover:border-purple-500/40">
            <div class="flex items-center gap-2 border-b border-purple-100 dark:border-purple-900/40 pb-3 mb-5">
                <span class="material-symbols-outlined text-primary dark:text-[#ccbeff] text-[20px]">add_task</span>
                <h2 class="text-sm font-bold uppercase font-label-code text-on-surface">Recepción Directa de Mercancía &amp; Abastecimiento</h2>
            </div>

            <form method="POST" action="{{ route('compras.ordenes.recepcionar') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                @csrf
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-label-code uppercase text-on-surface font-semibold">Proveedor / Editorial</label>
                        <a href="{{ route('compras.proveedores') }}" class="text-[10px] text-primary dark:text-[#ccbeff] underline font-label-code font-bold hover:opacity-80">+ Administrar</a>
                    </div>
                    <select name="proveedor_id" id="selector-proveedor" onchange="toggleInputProveedor(this.value)" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface focus:border-primary focus:outline-hidden transition-all shadow-2xs">
                        <option value="">-- Seleccionar Proveedor Registrado --</option>
                        @foreach ($proveedores as $prov)
                            <option value="{{ $prov->id }}">{{ $prov->nombre }} ({{ $prov->rfc ?: 'Sin RFC' }})</option>
                        @endforeach
                        <option value="otro">+ Ingresar otro proveedor manualmente</option>
                    </select>
                    <input type="text" name="proveedor" id="input-proveedor-manual" placeholder="Nombre o Razón Social del proveedor..." class="hidden mt-2 w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface focus:border-primary focus:outline-hidden shadow-2xs" />
                </div>
                <div>
                    <label class="block text-xs font-label-code uppercase mb-1.5 text-on-surface font-semibold">Sucursal Destino</label>
                    <select name="sucursal_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface focus:border-primary focus:outline-hidden transition-all shadow-2xs">
                        @foreach ($sucursales as $suc)<option value="{{ $suc->id }}">{{ $suc->nombre }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-label-code uppercase mb-1.5 text-on-surface font-semibold">Variante SKU</label>
                    <select name="variante_sku_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface focus:border-primary focus:outline-hidden transition-all shadow-2xs">
                        @foreach ($variantes as $var)<option value="{{ $var->id }}">{{ $var->sku }} — {{ $var->descripcion }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-label-code uppercase mb-1.5 text-on-surface font-semibold">Cantidad Recibida</label>
                    <input type="number" name="cantidad" min="1" required placeholder="Ej. 10" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface font-tabular-data focus:border-primary focus:outline-hidden shadow-2xs" />
                </div>
                <div class="md:col-span-4 flex justify-end pt-2">
                    <button type="submit" class="btn-glass-purple px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider shadow-xs inline-flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                        <span>Recepcionar Mercancía</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Historial de Recepciones con Contenedor Glossy Bento --}}
        <div class="bento-reveal bento-delay-120 bg-white/90 dark:bg-[#1a1626]/90 backdrop-blur-md rounded-2xl border border-purple-200/70 dark:border-white/10 shadow-2xs overflow-hidden transition-all duration-300 hover:border-purple-300 dark:hover:border-purple-500/40">
            <div class="p-4 sm:p-5 border-b border-purple-100 dark:border-purple-900/40 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary dark:text-[#ccbeff] text-[20px]">history</span>
                    <h3 class="text-sm font-bold uppercase font-label-code text-on-surface">Historial de Recepciones &amp; Movimientos</h3>
                </div>
                <span class="text-[10px] font-label-code text-primary dark:text-[#ccbeff] uppercase font-bold bg-primary/10 px-2.5 py-0.5 rounded-full border border-primary/20">
                    Registro de Entradas
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-container-low/80 dark:bg-[#201930]/80 backdrop-blur-xs border-b border-purple-200/60 dark:border-white/10 text-xs uppercase font-label-code text-secondary dark:text-purple-300">
                        <tr>
                            <th class="py-3 px-4">Fecha</th>
                            <th class="py-3 px-4">SKU / Sucursal</th>
                            <th class="py-3 px-4 text-right">Cantidad</th>
                            <th class="py-3 px-4">Detalle de Recepción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-purple-100 dark:divide-white/5">
                        @forelse ($recepciones as $rec)
                            <tr class="hover:bg-purple-50/50 dark:hover:bg-purple-900/20 transition-all">
                                <td class="py-3.5 px-4 font-label-code text-xs text-on-surface-variant dark:text-purple-200/70">{{ $rec->created_at }}</td>
                                <td class="py-3.5 px-4 font-medium text-on-surface dark:text-white">
                                    <span class="font-mono text-xs font-bold text-primary dark:text-[#ccbeff]">{{ $rec->existencia?->varianteSku?->sku }}</span>
                                    <span class="text-xs text-on-surface-variant dark:text-purple-200/70">({{ $rec->existencia?->sucursal?->nombre }})</span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-tabular-data font-bold text-emerald-600 dark:text-emerald-400">+{{ $rec->cantidad }}</td>
                                <td class="py-3.5 px-4 text-xs text-on-surface-variant dark:text-purple-200/70">{{ $rec->motivo }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-10 text-center text-outline dark:text-purple-300/60 text-xs space-y-1">
                                    <span class="material-symbols-outlined text-[32px] block text-outline dark:text-purple-300/40">inbox</span>
                                    <p class="font-medium text-on-surface dark:text-white">No se han registrado recepciones de proveedor aún.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
