<x-layouts.admin title="ARCHIVUM · Editar Producto" active-route="admin-productos">
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Encabezado --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('admin.productos.index') }}" class="inline-flex items-center gap-1.5 text-xs font-label-code text-primary dark:text-purple-300 hover:underline mb-1">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>Regresar al Catálogo de Productos</span>
                </a>
                <h1 class="text-2xl font-bold text-on-surface dark:text-white font-quicksand">
                    Editar Producto: {{ $producto->nombre }}
                </h1>
                <p class="text-xs text-on-surface-variant dark:text-purple-200/70 font-label-code uppercase tracking-wider mt-0.5">
                    Modificación de ficha técnica, precio y portada
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if ($producto->activo)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700/60 text-xs font-label-code font-bold uppercase">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Activo en Tienda</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border border-rose-300 dark:border-rose-700/60 text-xs font-label-code font-bold uppercase">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span>Dado de Baja</span>
                    </span>
                @endif
            </div>
        </div>

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-700/60 text-rose-800 dark:text-rose-200 text-sm space-y-1.5 shadow-xs">
                <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wider">
                    <span class="material-symbols-outlined text-rose-600 dark:text-rose-400 text-[18px]">error</span>
                    <span>Corrige los siguientes campos:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $variante = $producto->variantesSku->first();
            $esPiezaUnicaVista = $esPiezaUnica ?? (
                $variante !== null && (
                    \App\Http\Controllers\CheckoutSimuladoController::obtenerPiezaUnicaDeVariante((int) $variante->id) !== null
                    || (bool) ($producto->coleccionable?->edicion_limitada ?? false)
                    || \App\Http\Controllers\CheckoutSimuladoController::esVariantePiezaUnica($variante)
                )
            );
            $stockActualVista = $stockActual ?? ($variante ? (int) $variante->existencias->sum('cantidad') : 0);
            $valorStockUnico = $stockActualVista > 0 ? 1 : 0;
        @endphp

        <form method="POST" action="{{ route('admin.productos.update', $producto) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-surface-container-lowest dark:bg-[#1a1329] p-6 rounded-3xl border border-purple-200/70 dark:border-purple-800/40 shadow-xs space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Código SKU --}}
                    <div>
                        <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">
                            Código SKU <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="sku"
                            value="{{ old('sku', $variante?->sku) }}"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white font-label-code uppercase focus:outline-none focus:border-primary"
                        />
                    </div>

                    {{-- Precio --}}
                    <div>
                        <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">
                            Precio (MXN) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="number"
                            step="0.01"
                            min="0.01"
                            name="precio"
                            value="{{ old('precio', $producto->precio) }}"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white font-tabular-data font-bold focus:outline-none focus:border-primary"
                        />
                    </div>

                    {{-- Stock Disponible (Unidades) --}}
                    <div>
                        <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">
                            Stock Disponible (Unidades)
                            @if (! $esPiezaUnicaVista)
                                <span class="text-rose-500">*</span>
                            @endif
                        </label>
                        @if ($esPiezaUnicaVista)
                            <input
                                type="number"
                                value="{{ $valorStockUnico }}"
                                disabled
                                readonly
                                class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200/70 dark:border-purple-800/40 text-sm bg-purple-100/50 dark:bg-purple-950/40 text-on-surface-variant dark:text-purple-300/80 font-tabular-data font-bold cursor-not-allowed select-none"
                                title="Pieza Única / Exclusiva — Stock fijo (no editable)"
                            />
                            <p class="mt-1.5 text-[11px] font-label-code font-semibold text-primary dark:text-purple-300 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">lock</span>
                                <span>Pieza Única / Exclusiva — Stock fijo (no editable)</span>
                            </p>
                        @else
                            <input
                                type="number"
                                name="stock"
                                min="0"
                                step="1"
                                value="{{ old('stock', $stockActualVista) }}"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white font-tabular-data font-bold focus:outline-none focus:border-primary"
                            />
                            <p class="mt-1 text-[11px] font-label-code text-on-surface-variant dark:text-purple-200/70">
                                Producto común — Ajuste directo de unidades en inventario
                            </p>
                        @endif
                    </div>

                    {{-- Nombre / Título --}}
                    <div class="md:col-span-3">
                        <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">
                            Título / Nombre del Producto <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="nombre"
                            value="{{ old('nombre', $producto->nombre) }}"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white font-medium focus:outline-none focus:border-primary"
                        />
                    </div>

                    {{-- Metadatos específicos --}}
                    @if ($producto->libro)
                        <div>
                            <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">Autor</label>
                            <input type="text" name="autor" value="{{ old('autor', $producto->libro->autor) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white" />
                        </div>
                        <div>
                            <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">Editorial</label>
                            <input type="text" name="editorial" value="{{ old('editorial', $producto->libro->editorial) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white" />
                        </div>
                        <div>
                            <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">Género Literario</label>
                            <input type="text" name="genero" value="{{ old('genero', $producto->libro->genero) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white" />
                        </div>
                    @elseif ($producto->disco)
                        <div class="md:col-span-2">
                            <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">Artista / Intérprete</label>
                            <input type="text" name="artista" value="{{ old('artista', $producto->disco->artista) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white" />
                        </div>
                        <div>
                            <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">Sello Discográfico</label>
                            <input type="text" name="sello_discografico" value="{{ old('sello_discografico', $producto->disco->sello) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white" />
                        </div>
                    @elseif ($producto->coleccionable)
                        <div class="md:col-span-3">
                            <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">Fabricante / Marca</label>
                            <input type="text" name="fabricante" value="{{ old('fabricante', $producto->coleccionable->marca) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white" />
                        </div>
                    @endif

                    {{-- Descripción --}}
                    <div class="md:col-span-3">
                        <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">
                            Descripción del Producto
                        </label>
                        <textarea
                            name="descripcion"
                            rows="4"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white focus:outline-none focus:border-primary resize-y"
                        >{{ old('descripcion', $producto->descripcion) }}</textarea>
                    </div>

                    {{-- Fotografía de Portada --}}
                    <div class="md:col-span-3 space-y-2">
                        <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold">
                            Cambiar Fotografía de Portada (Opcional &middot; JPG, PNG, WEBP)
                        </label>

                        <div class="flex items-center gap-4 p-4 rounded-2xl border border-purple-200 dark:border-purple-800/60 bg-surface-container-low/40 dark:bg-[#140d20]">
                            @if ($producto->imagen_url)
                                <img
                                    src="{{ $producto->imagen_url }}"
                                    alt="{{ $producto->nombre }}"
                                    class="w-16 h-16 object-cover rounded-xl border border-purple-300 dark:border-purple-700 shadow-xs shrink-0"
                                />
                            @endif
                            <input
                                type="file"
                                name="imagen"
                                accept="image/*"
                                class="text-xs file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:opacity-90 border border-purple-200 dark:border-purple-800/60 rounded-xl p-1.5 bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white cursor-pointer w-full"
                            />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Botones de Guardar --}}
            <div class="flex items-center justify-between pt-2">
                <div>
                    {{-- Botón rápido de alternar estado desde la edición --}}
                    <a
                        href="{{ route('admin.productos.index') }}"
                        class="px-4 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs font-semibold text-outline dark:text-purple-300 hover:text-on-surface transition"
                    >
                        Cancelar
                    </a>
                </div>
                <button
                    type="submit"
                    class="btn-glass-purple px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider shadow-md flex items-center gap-2 cursor-pointer"
                >
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span>Guardar Cambios</span>
                </button>
            </div>
        </form>

    </div>
</x-layouts.admin>
