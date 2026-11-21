<x-layouts.admin title="ARCHIVUM · Catálogo & Altas y Bajas de Productos" active-route="admin-productos">
    <div class="space-y-6">

        {{-- Encabezado Principal con Botón de Alta Rápida --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 text-[10px] font-label-code uppercase tracking-widest text-primary dark:text-purple-300 font-bold mb-1">
                    <span class="material-symbols-outlined text-[14px]">library_add</span>
                    <span>Gestión de Catálogo &middot; Permisos para Administrador y Personal</span>
                </span>
                <h1 class="text-2xl font-bold text-on-surface dark:text-white font-quicksand">
                    Catálogo de Productos &amp; Control de Altas y Bajas
                </h1>
                <p class="text-xs text-on-surface-variant dark:text-purple-200/70 font-label-code uppercase tracking-wider mt-1">
                    Total: {{ number_format($totalProductos) }} productos &middot; Activos en tienda: {{ number_format($totalActivos) }} &middot; Dados de baja: {{ number_format($totalBaja) }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2.5">
                <a href="{{ route('admin.productos.create') }}" class="btn-glass-purple px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider flex items-center gap-2 shadow-sm transition">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span>
                    <span>+ Dar de Alta Producto</span>
                </a>
                <a href="{{ route('admin.inventario') }}" class="px-4 py-2.5 rounded-xl border border-purple-200 bg-purple-100 hover:bg-purple-200 text-purple-800 dark:bg-white/5 dark:border-purple-500/30 dark:text-purple-300 dark:hover:bg-white/10 text-xs font-label-md flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[18px]">warehouse</span>
                    <span>Inventario por Sede</span>
                </a>
            </div>
        </div>

        {{-- Alertas de Estado y Validación --}}
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
                    <span>No fue posible procesar la solicitud:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Barra de Filtros Rápidos de Estado (Pestañas Bento) y Búsqueda --}}
        <div class="space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-3">
                {{-- Filtro Rápido por Pestañas de Estado --}}
                <div class="inline-flex p-1 rounded-2xl bg-purple-100/70 dark:bg-[#1a1329] border border-purple-200/80 dark:border-purple-800/40 text-xs font-label-code">
                    <a
                        href="{{ route('admin.productos.index', array_merge(request()->query(), ['estado' => 'activos', 'page' => 1])) }}"
                        class="px-3.5 py-1.5 rounded-xl font-bold uppercase transition flex items-center gap-1.5 {{ $estadoFiltro === 'activos' ? 'bg-white dark:bg-purple-600 text-purple-900 dark:text-white shadow-xs' : 'text-slate-600 dark:text-purple-300 hover:text-purple-900' }}"
                    >
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Activos ({{ $totalActivos }})</span>
                    </a>
                    <a
                        href="{{ route('admin.productos.index', array_merge(request()->query(), ['estado' => 'baja', 'page' => 1])) }}"
                        class="px-3.5 py-1.5 rounded-xl font-bold uppercase transition flex items-center gap-1.5 {{ $estadoFiltro === 'baja' || $estadoFiltro === 'inactivos' ? 'bg-white dark:bg-purple-600 text-purple-900 dark:text-white shadow-xs' : 'text-slate-600 dark:text-purple-300 hover:text-purple-900' }}"
                    >
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span>Dados de Baja ({{ $totalBaja }})</span>
                    </a>
                    <a
                        href="{{ route('admin.productos.index', array_merge(request()->query(), ['estado' => 'todos', 'page' => 1])) }}"
                        class="px-3.5 py-1.5 rounded-xl font-bold uppercase transition flex items-center gap-1.5 {{ $estadoFiltro === 'todos' ? 'bg-white dark:bg-purple-600 text-purple-900 dark:text-white shadow-xs' : 'text-slate-600 dark:text-purple-300 hover:text-purple-900' }}"
                    >
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                        <span>Todos ({{ $totalProductos }})</span>
                    </a>
                </div>

                {{-- Acceso Rápido a Descarga de Plantilla CSV --}}
                <a href="{{ route('compras.productos.plantilla-csv') }}" class="text-xs font-label-code text-primary dark:text-purple-300 hover:underline flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">file_download</span>
                    <span>Descargar Plantilla CSV</span>
                </a>
            </div>

            {{-- Formulario de Búsqueda y Selección de Catálogo --}}
            <form method="GET" action="{{ route('admin.productos.index') }}" class="bg-surface-container-lowest dark:bg-[#1a1329] p-4 rounded-2xl border border-purple-200/70 dark:border-purple-800/40 flex flex-wrap items-center gap-3 shadow-2xs">
                <input type="hidden" name="estado" value="{{ $estadoFiltro }}" />

                <div class="flex-1 min-w-[220px] relative">
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-[18px] text-outline">search</span>
                    <input
                        type="text"
                        name="buscar"
                        value="{{ request('buscar') }}"
                        placeholder="Buscar por título, SKU, autor, artista o fabricante..."
                        class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white focus:outline-none focus:border-primary"
                    />
                </div>

                <select name="catalogo_id" onchange="this.form.submit()" class="px-3.5 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white">
                    <option value="">Todos los Catálogos</option>
                    @foreach ($catalogos as $cat)
                        <option value="{{ $cat->id }}" @selected((string) request('catalogo_id') === (string) $cat->id)>{{ $cat->nombre }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold uppercase tracking-wider transition shadow-sm cursor-pointer">
                    Filtrar
                </button>

                @if (request()->filled('buscar') || request()->filled('catalogo_id'))
                    <a href="{{ route('admin.productos.index', ['estado' => $estadoFiltro]) }}" class="px-3 py-2 rounded-xl border border-purple-200 text-xs text-outline hover:text-primary transition">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        {{-- Tabla de Productos --}}
        <div class="bg-surface-container-lowest dark:bg-[#1a1329] rounded-2xl border border-purple-200/70 dark:border-purple-800/40 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-container-low/80 dark:bg-[#201930] border-b border-purple-200/60 dark:border-white/10 text-xs uppercase font-label-code text-secondary dark:text-purple-300">
                        <tr>
                            <th class="py-3 px-3 text-center w-14">Portada</th>
                            <th class="py-3 px-4">SKU</th>
                            <th class="py-3 px-4">Producto &amp; Ficha Técnica</th>
                            <th class="py-3 px-4">Catálogo</th>
                            <th class="py-3 px-4 text-right">Precio Base</th>
                            <th class="py-3 px-4 text-center">Estado en Tienda</th>
                            <th class="py-3 px-4 text-center">Acciones de Alta / Baja</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-purple-100 dark:divide-white/5">
                        @forelse ($productos as $prod)
                            @php
                                $primeraVariante = $prod->variantesSku->first();
                                $skuText = $primeraVariante?->sku ?? 'ARC-SKU-' . $prod->id;
                                $esActivo = (bool) $prod->activo;
                            @endphp
                            <tr class="hover:bg-purple-50/40 dark:hover:bg-purple-950/20 transition {{ ! $esActivo ? 'opacity-85 bg-slate-50/50 dark:bg-black/20' : '' }}">
                                {{-- Portada en Miniatura --}}
                                <td class="py-3 px-3 text-center">
                                    @if ($prod->imagen_url)
                                        <img
                                            src="{{ $prod->imagen_url }}"
                                            alt="{{ $prod->nombre }}"
                                            class="w-12 h-12 object-cover rounded-xl border border-purple-200 dark:border-purple-700/60 shadow-2xs mx-auto"
                                            loading="lazy"
                                        />
                                    @else
                                        <div class="w-12 h-12 rounded-xl bg-purple-100/60 dark:bg-purple-950/50 border border-purple-200/70 dark:border-purple-800/40 flex items-center justify-center text-primary dark:text-purple-300 shadow-2xs mx-auto">
                                            <span class="material-symbols-outlined text-[20px]">image_not_supported</span>
                                        </div>
                                    @endif
                                </td>

                                {{-- SKU --}}
                                <td class="py-3 px-4 font-label-code font-bold text-primary dark:text-purple-300 text-xs">
                                    {{ $skuText }}
                                </td>

                                {{-- Producto & Subtítulo --}}
                                <td class="py-3 px-4 max-w-xs">
                                    <div class="font-bold text-on-surface dark:text-white leading-snug">
                                        {{ $prod->nombre }}
                                    </div>
                                    <div class="text-[11px] text-on-surface-variant dark:text-purple-200/70 font-title-editorial italic truncate mt-0.5">
                                        @if ($prod->libro)
                                            {{ $prod->libro->autor }} &middot; {{ $prod->libro->editorial }} ({{ $prod->libro->anio_publicacion }})
                                        @elseif ($prod->disco)
                                            {{ $prod->disco->artista }} &middot; {{ $prod->disco->sello }} ({{ $prod->disco->anio_lanzamiento }})
                                        @elseif ($prod->coleccionable)
                                            {{ $prod->coleccionable->marca }} &middot; {{ $prod->coleccionable->material }}
                                        @else
                                            {{ Str::limit($prod->descripcion, 55) }}
                                        @endif
                                    </div>
                                </td>

                                {{-- Catálogo --}}
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-label-code font-bold uppercase bg-purple-100 text-purple-900 dark:bg-purple-900/50 dark:text-purple-200 border border-purple-200 dark:border-purple-700/50">
                                        {{ $prod->catalogo?->nombre ?? 'General' }}
                                    </span>
                                </td>

                                {{-- Precio Base --}}
                                <td class="py-3 px-4 text-right font-tabular-data font-bold text-on-surface dark:text-white">
                                    ${{ number_format((float) $prod->precio, 2) }}
                                    <span class="text-[10px] text-on-surface-variant font-normal">MXN</span>
                                </td>

                                {{-- Badge de Estado en Tienda Pública --}}
                                <td class="py-3 px-4 text-center">
                                    @if ($esActivo)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700/60 text-[10px] font-label-code font-bold uppercase tracking-wider shadow-2xs">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span>Activo &middot; Disponible</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border border-rose-300 dark:border-rose-700/60 text-[10px] font-label-code font-bold uppercase tracking-wider shadow-2xs">
                                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                            <span>Dado de Baja</span>
                                        </span>
                                    @endif
                                </td>

                                {{-- Acciones: Dar de Baja / Reactivar / Editar / Cambiar Foto --}}
                                <td class="py-3 px-4 text-center">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        {{-- Botón de Alternar Estado (Baja o Reactivación) --}}
                                        @if ($esActivo)
                                            <form
                                                method="POST"
                                                action="{{ route('admin.productos.baja', $prod) }}"
                                                onsubmit="return confirm('¿Confirma que desea DAR DE BAJA «{{ addslashes($prod->nombre) }}»? Se ocultará de la tienda pública inmediatamente.');"
                                            >
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 dark:bg-rose-950/30 dark:hover:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800/60 text-[11px] font-label-code font-bold uppercase transition flex items-center gap-1 cursor-pointer"
                                                    title="Dar de baja: ocultar de la tienda pública sin borrar el historial"
                                                >
                                                    <span class="material-symbols-outlined text-[15px]">visibility_off</span>
                                                    <span>Dar de Baja</span>
                                                </button>
                                            </form>
                                        @else
                                            <form
                                                method="POST"
                                                action="{{ route('admin.productos.reactivar', $prod) }}"
                                                onsubmit="return confirm('¿Confirma que desea REACTIVAR «{{ addslashes($prod->nombre) }}»? Volverá a estar visible para compra en la tienda pública.');"
                                            >
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/30 dark:hover:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800/60 text-[11px] font-label-code font-bold uppercase transition flex items-center gap-1 cursor-pointer"
                                                    title="Reactivar: volver a poner a la venta en el catálogo público"
                                                >
                                                    <span class="material-symbols-outlined text-[15px]">check_circle</span>
                                                    <span>Reactivar</span>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Botón Editar --}}
                                        <a
                                            href="{{ route('admin.productos.edit', $prod) }}"
                                            class="px-2.5 py-1.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-purple-800 dark:text-purple-300 hover:bg-purple-50 dark:hover:bg-purple-950/40 text-[11px] font-label-code font-semibold transition inline-flex items-center gap-1"
                                            title="Editar datos, precio y metadatos del producto"
                                        >
                                            <span class="material-symbols-outlined text-[15px]">edit</span>
                                            <span class="hidden sm:inline">Editar</span>
                                        </a>

                                        {{-- Botón Cambiar Foto --}}
                                        <button
                                            type="button"
                                            onclick="abrirModalActualizarImagen({{ $prod->id }}, '{{ addslashes($prod->nombre) }}', '{{ route('admin.productos.actualizar-imagen', $prod) }}', '{{ $prod->imagen ? asset('/' . ltrim($prod->imagen, '/')) : '' }}')"
                                            class="px-2 py-1.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-outline hover:text-primary dark:hover:text-purple-300 hover:bg-surface-container-low transition inline-flex items-center"
                                            title="Actualizar fotografía de portada"
                                        >
                                            <span class="material-symbols-outlined text-[16px]">add_a_photo</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-outline text-sm">
                                    No se encontraron productos registrados con el filtro seleccionado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($productos->hasPages())
                <div class="p-4 border-t border-purple-100 dark:border-purple-900/40">
                    {{ $productos->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- Modal para Actualización Rápida de Fotografía --}}
    <div
        id="modal_actualizar_imagen"
        class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity duration-200"
        aria-modal="true"
        role="dialog"
    >
        <div class="bg-surface-container-lowest dark:bg-[#1f1730] border border-purple-200/80 dark:border-purple-700/60 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 relative animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-purple-100 dark:border-purple-900/40 pb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary dark:text-purple-300 text-[22px]">add_a_photo</span>
                    <h3 class="text-sm font-bold uppercase font-label-code text-on-surface dark:text-white">Actualizar Portada</h3>
                </div>
                <button
                    type="button"
                    onclick="cerrarModalActualizarImagen()"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-outline hover:text-on-surface hover:bg-surface-container-low transition cursor-pointer"
                    title="Cerrar modal"
                >
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            <div class="space-y-1">
                <p id="modal_producto_nombre" class="font-bold text-sm text-on-surface dark:text-white line-clamp-1"></p>
                <p class="text-xs text-on-surface-variant dark:text-purple-200/70 font-label-code">
                    Seleccione la nueva imagen de portada. Se actualizará en el catálogo público de inmediato.
                </p>
            </div>

            <form
                id="form_actualizar_imagen"
                method="POST"
                action=""
                enctype="multipart/form-data"
                class="space-y-4"
            >
                @csrf
                <div class="space-y-2">
                    <label for="input_modal_imagen" class="block text-xs font-label-code uppercase text-on-surface dark:text-white">
                        Seleccionar Archivo (JPG, PNG, WEBP &middot; Máx 4MB)
                    </label>

                    {{-- Previsualizador --}}
                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-surface-container-low/50 dark:bg-[#160f24] border border-purple-200/60 dark:border-purple-800/40">
                        <img
                            id="modal_preview_img"
                            src=""
                            alt="Portada actual / nueva"
                            class="w-14 h-14 object-cover rounded-xl border border-purple-300 dark:border-purple-700/60 shadow-xs shrink-0"
                            onerror="this.style.display='none'"
                        />
                        <div class="min-w-0 space-y-0.5">
                            <p id="modal_preview_nombre" class="text-xs font-bold text-on-surface dark:text-white truncate">Seleccione una imagen</p>
                            <p class="text-[11px] text-on-surface-variant dark:text-purple-200/70 font-label-code">Resolución recomendada: 800x800px</p>
                        </div>
                    </div>

                    <input
                        type="file"
                        id="input_modal_imagen"
                        name="imagen"
                        accept="image/*"
                        required
                        class="w-full text-xs text-on-surface dark:text-white file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:opacity-90 border border-purple-200 dark:border-purple-800/60 rounded-xl p-1.5 bg-surface-container-low/50 dark:bg-[#160f24]"
                    />
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-purple-100 dark:border-purple-900/40">
                    <button
                        type="button"
                        onclick="cerrarModalActualizarImagen()"
                        class="px-4 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs font-semibold text-outline hover:text-on-surface transition cursor-pointer"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="btn-glass-purple px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider shadow-sm flex items-center gap-1.5 cursor-pointer"
                    >
                        <span class="material-symbols-outlined text-[16px]">cloud_upload</span>
                        <span>Guardar Portada</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModalActualizarImagen(productoId, nombreProducto, actionUrl, imagenActualUrl) {
            const modal = document.getElementById('modal_actualizar_imagen');
            const form = document.getElementById('form_actualizar_imagen');
            const lblNombre = document.getElementById('modal_producto_nombre');
            const imgPreview = document.getElementById('modal_preview_img');
            const lblArchivo = document.getElementById('modal_preview_nombre');

            if (!modal || !form) return;

            form.action = actionUrl;
            lblNombre.textContent = nombreProducto;

            if (imagenActualUrl && imagenActualUrl.trim() !== '') {
                imgPreview.src = imagenActualUrl;
                imgPreview.style.display = 'block';
                lblArchivo.textContent = 'Portada actual';
            } else {
                imgPreview.style.display = 'none';
                lblArchivo.textContent = 'Sin portada actual';
            }

            modal.classList.remove('hidden');
        }

        function cerrarModalActualizarImagen() {
            const modal = document.getElementById('modal_actualizar_imagen');
            if (modal) modal.classList.add('hidden');
        }

        document.getElementById('input_modal_imagen')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const imgPreview = document.getElementById('modal_preview_img');
            const lblArchivo = document.getElementById('modal_preview_nombre');

            if (file) {
                lblArchivo.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                const reader = new FileReader();
                reader.onload = function(evt) {
                    imgPreview.src = evt.target.result;
                    imgPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</x-layouts.admin>
