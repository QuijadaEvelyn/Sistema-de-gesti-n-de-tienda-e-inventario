<x-layouts.admin title="ARCHIVUM · Catálogo y Altas de Productos" active-route="compras-productos">
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-on-surface font-quicksand">Altas de Productos &amp; Variantes SKU</h1>
                <p class="text-xs text-on-surface-variant font-label-code uppercase tracking-wider mt-1">Gestión del catálogo maestro e importación masiva de inventario</p>
            </div>
            <a href="{{ route('compras.productos.plantilla-csv') }}" class="btn-glass-surface inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-label-md text-xs font-bold shadow-xs">
                <span class="material-symbols-outlined text-[18px]">download</span>
                <span>Descargar Plantilla CSV</span>
            </a>
        </div>

        {{-- Alertas de Notificación y Errores --}}
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
                    No fue posible completar la operación:
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Bloque Grid: Formulario Alta Individual + Formulario Importación Masiva CSV --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- Formulario de Registro Individual (7 cols) --}}
            <div class="lg:col-span-7 bg-surface-container-lowest p-6 rounded-2xl border border-purple-200/70 shadow-2xs space-y-4">
                <div class="flex items-center gap-2 border-b border-purple-100 pb-3">
                    <span class="material-symbols-outlined text-primary text-[20px]">add_box</span>
                    <h2 class="text-sm font-bold uppercase font-label-code text-on-surface">Alta Individual de Producto</h2>
                </div>

                <form method="POST" action="{{ route('compras.productos.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1">Catálogo</label>
                        <select name="catalogo_id" required class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm bg-white">
                            @foreach ($catalogos as $cat)<option value="{{ $cat->id }}">{{ $cat->nombre }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1">Código SKU</label>
                        <input type="text" name="sku" required placeholder="LIB-1980-001" class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-label-code uppercase mb-1">Nombre del Producto</label>
                        <input type="text" name="nombre" required placeholder="Ej. Cien Años de Soledad (Primera Edición)" class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1">Precio Base (MXN)</label>
                        <input type="number" step="0.01" min="0" name="precio" required placeholder="45000.00" class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm" />
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-glass-purple w-full py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider shadow-sm">
                            Registrar Producto
                        </button>
                    </div>
                </form>
            </div>

            {{-- Formulario de Importación Masiva CSV (5 cols) --}}
            <div class="lg:col-span-5 bg-surface-container-lowest p-6 rounded-2xl border border-purple-200/70 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-purple-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary text-[20px]">upload_file</span>
                        <h2 class="text-sm font-bold uppercase font-label-code text-on-surface">Importación Masiva CSV</h2>
                    </div>
                    <span class="text-[10px] font-label-code text-primary uppercase font-bold bg-primary/10 px-2 py-0.5 rounded-full border border-primary/20">
                        Catálogo &amp; Stock
                    </span>
                </div>

                <p class="text-xs text-on-surface-variant leading-relaxed">
                    Suba un archivo <code class="font-mono text-primary font-bold">.csv</code> para cargar de forma atómica productos, variantes y existencias por sucursal.
                </p>

                <form method="POST" action="{{ route('compras.productos.importar-csv') }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <label for="archivo_csv" class="block text-xs font-label-code uppercase text-on-surface mb-1">
                            Seleccionar Archivo (.csv, máx 2MB)
                        </label>
                        <input
                            type="file"
                            id="archivo_csv"
                            name="archivo_csv"
                            accept=".csv,text/csv,text/plain"
                            required
                            class="w-full text-xs text-on-surface file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-container file:text-white hover:file:bg-primary border border-purple-200 rounded-xl p-1.5 bg-surface-container-low/50"
                        />
                    </div>

                    <div class="p-3 rounded-xl bg-purple-50/70 dark:bg-purple-950/20 border border-purple-200/50 text-[11px] font-label-code text-on-surface-variant space-y-1">
                        <span class="font-bold text-primary block">Columnas requeridas:</span>
                        <p class="truncate text-on-surface font-mono">sku, nombre, catalogo, precio, sucursal, cantidad</p>
                    </div>

                    <button type="submit" class="btn-glass-purple w-full py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-2 shadow-md">
                        <span class="material-symbols-outlined text-[16px]">cloud_upload</span>
                        <span>Procesar e Importar CSV</span>
                    </button>
                </form>
            </div>

        </div>

        {{-- Tabla de Productos Registrados --}}
        <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 shadow-2xs overflow-hidden space-y-4 p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h3 class="text-sm font-bold uppercase font-label-code text-on-surface">Catálogo Maestro Registrado</h3>
                <form method="GET" action="{{ route('compras.productos') }}" class="flex items-center gap-2 max-w-sm w-full">
                    <input
                        type="search"
                        name="buscar"
                        value="{{ request('buscar') }}"
                        placeholder="Buscar por nombre..."
                        class="w-full px-3 py-1.5 rounded-xl border border-purple-200 text-xs bg-white"
                    />
                    <button type="submit" class="btn-glass-purple px-3 py-1.5 rounded-xl text-xs font-bold">
                        Buscar
                    </button>
                    @if (request('buscar'))
                        <a href="{{ route('compras.productos') }}" class="text-xs text-outline hover:text-primary">Limpiar</a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-container-low border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                        <tr>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Producto</th>
                            <th class="py-3 px-4">Catálogo</th>
                            <th class="py-3 px-4 text-right">Precio Base</th>
                            <th class="py-3 px-4 text-right">Variantes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-purple-100">
                        @forelse ($productos as $prod)
                            <tr class="hover:bg-purple-50/40 transition">
                                <td class="py-3 px-4 font-label-code text-xs">#{{ $prod->id }}</td>
                                <td class="py-3 px-4 font-bold text-on-surface">{{ $prod->nombre }}</td>
                                <td class="py-3 px-4 text-xs">{{ $prod->catalogo?->nombre }}</td>
                                <td class="py-3 px-4 text-right font-tabular-data font-bold">${{ number_format((float) $prod->precio, 2) }}</td>
                                <td class="py-3 px-4 text-right font-tabular-data font-semibold">{{ $prod->variantes_sku_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-outline text-xs">
                                    No se encontraron productos registrados en el acervo.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($productos->hasPages())
                <div class="pt-2">
                    {{ $productos->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.admin>
