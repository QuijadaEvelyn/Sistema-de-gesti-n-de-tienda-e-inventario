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
                <div class="flex items-center gap-2 border-b border-purple-100 dark:border-purple-900/40 pb-3">
                    <span class="material-symbols-outlined text-primary text-[20px]">add_box</span>
                    <h2 class="text-sm font-bold uppercase font-label-code text-on-surface">Alta Individual de Producto</h2>
                </div>

                <form method="POST" action="{{ route('compras.productos.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1 text-on-surface">Catálogo</label>
                        <select name="catalogo_id" required class="w-full px-3 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface">
                            @foreach ($catalogos as $cat)<option value="{{ $cat->id }}">{{ $cat->nombre }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1 text-on-surface">Código SKU</label>
                        <input type="text" name="sku" required placeholder="LIB-1980-001" class="w-full px-3 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-label-code uppercase mb-1 text-on-surface">Nombre del Producto</label>
                        <input type="text" name="nombre" required placeholder="Ej. Cien Años de Soledad (Primera Edición)" class="w-full px-3 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface" />
                    </div>

                    {{-- Zona de Subida de Imagen del Producto con Vista Previa en Miniatura y Estilo Glossy --}}
                    <div class="md:col-span-2 space-y-2">
                        <div class="flex items-center justify-between">
                            <label for="input_imagen_producto" class="block text-xs font-label-code uppercase text-on-surface font-semibold">
                                Imagen del Producto (JPG, PNG, WEBP · Máx 4MB)
                            </label>
                            <span class="text-[10px] font-label-code text-primary uppercase font-bold bg-primary/10 px-2 py-0.5 rounded-full border border-primary/20">
                                Portada · Sitio Público
                            </span>
                        </div>

                        {{-- Input nativo visible y estilizado con Glossy Glassmorphism --}}
                        <div class="space-y-2">
                            <input
                                type="file"
                                id="input_imagen_producto"
                                name="imagen"
                                accept="image/*"
                                class="w-full text-xs text-on-surface file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:opacity-90 border border-purple-200/90 dark:border-purple-800/60 rounded-xl p-1.5 bg-surface-container-low/50 cursor-pointer shadow-xs"
                            />

                            {{-- Dropzone en Reposo interactivo con borde vidrioso --}}
                            <div
                                id="dropzone_imagen_producto"
                                class="relative border-2 border-dashed border-purple-200/90 dark:border-purple-800/60 rounded-2xl p-3.5 text-center cursor-pointer transition-all bg-surface-container-low/40 hover:bg-purple-50/40 dark:hover:bg-purple-950/20 hover:border-primary group"
                            >
                                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-purple-100 dark:bg-purple-900/40 text-primary flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                        <span class="material-symbols-outlined text-[20px]">add_photo_alternate</span>
                                    </div>
                                    <div class="text-left sm:flex-1">
                                        <p class="text-xs font-semibold text-on-surface">
                                            Arrastra una imagen de portada o <span class="text-primary underline">haz clic aquí</span>
                                        </p>
                                        <p class="text-[11px] text-on-surface-variant font-label-code mt-0.5">
                                            Aparecerá de inmediato en el catálogo público (`/`, `/catalogo`, `/vinilos`)
                                        </p>
                                    </div>
                                    <button type="button" class="btn-glass-surface px-3 py-1.5 rounded-xl text-xs font-bold inline-flex items-center gap-1.5 pointer-events-none">
                                        <span class="material-symbols-outlined text-[16px]">folder_open</span>
                                        <span>Seleccionar Fotografía</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Contenedor de Vista Previa Instantánea en Miniatura --}}
                        <div
                            id="preview_container_imagen"
                            class="hidden p-3 rounded-2xl border border-purple-200/80 dark:border-purple-800/60 bg-surface-container-low/60 flex items-center justify-between gap-3 shadow-2xs"
                        >
                            <div class="flex items-center gap-3 overflow-hidden">
                                <img
                                    id="preview_img"
                                    src=""
                                    alt="Vista previa en miniatura de la portada"
                                    class="w-14 h-14 object-cover rounded-xl border border-purple-300 dark:border-purple-700/60 shadow-xs shrink-0"
                                />
                                <div class="min-w-0 space-y-0.5">
                                    <p id="preview_nombre" class="text-xs font-bold text-on-surface truncate">portada.jpg</p>
                                    <div class="flex items-center gap-2 text-[11px] text-on-surface-variant font-label-code">
                                        <span id="preview_peso">0 KB</span>
                                        <span class="text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-0.5">
                                            <span class="material-symbols-outlined text-[13px]">check_circle</span>
                                            Listo para publicar
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <button
                                type="button"
                                id="btn_quitar_imagen"
                                class="btn-glass-surface px-3 py-1.5 rounded-xl text-xs font-bold text-rose-600 dark:text-rose-400 hover:text-rose-700 inline-flex items-center gap-1.5 shrink-0"
                                title="Quitar imagen seleccionada"
                            >
                                <span class="material-symbols-outlined text-[16px]">delete</span>
                                <span>Quitar</span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1 text-on-surface">Precio Base (MXN)</label>
                        <input type="number" step="0.01" min="0" name="precio" required placeholder="45000.00" class="w-full px-3 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface" />
                    </div>

                    @if (isset($sucursales) && $sucursales->isNotEmpty())
                        <div>
                            <label class="block text-xs font-label-code uppercase mb-1 text-on-surface">Sede de Ingreso Inicial</label>
                            <select name="sucursal_id" class="w-full px-3 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface">
                                @foreach ($sucursales as $suc)
                                    <option value="{{ $suc->id }}" {{ (isset($sucursalActivaId) && $sucursalActivaId == $suc->id) ? 'selected' : '' }}>
                                        {{ $suc->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="{{ isset($sucursales) && $sucursales->isNotEmpty() ? 'md:col-span-2' : '' }} flex items-end">
                        <button type="submit" class="btn-glass-purple w-full py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider shadow-sm flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">add_circle</span>
                            <span>Registrar Producto</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Formulario de Importación Masiva CSV (5 cols) --}}
            <div class="lg:col-span-5 bg-surface-container-lowest p-6 rounded-2xl border border-purple-200/70 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-purple-100 dark:border-purple-900/40 pb-3">
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
                            class="w-full text-xs text-on-surface file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-container file:text-white hover:file:bg-primary border border-purple-200 dark:border-purple-800/60 rounded-xl p-1.5 bg-surface-container-low/50"
                        />
                    </div>

                    <div class="p-3 rounded-xl bg-purple-50/70 dark:bg-purple-950/20 border border-purple-200/50 dark:border-purple-800/40 text-[11px] font-label-code text-on-surface-variant space-y-1">
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
                        class="w-full px-3 py-1.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs bg-white dark:bg-[#1f1a2e] text-on-surface"
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
                    <thead class="bg-surface-container-low border-b border-purple-200/60 dark:border-purple-800/40 text-xs uppercase font-label-code text-secondary">
                        <tr>
                            <th class="py-3 px-3 text-center w-14">Portada</th>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Producto</th>
                            <th class="py-3 px-4">Catálogo</th>
                            <th class="py-3 px-4 text-right">Precio Base</th>
                            <th class="py-3 px-4 text-right">Variantes</th>
                            <th class="py-3 px-4 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-purple-100 dark:divide-purple-900/40">
                        @forelse ($productos as $prod)
                            <tr class="hover:bg-purple-50/40 dark:hover:bg-purple-950/20 transition">
                                <td class="py-3 px-3 text-center">
                                    @if ($prod->imagen)
                                        <img
                                            src="{{ asset('/' . ltrim($prod->imagen, '/')) }}"
                                            alt="{{ $prod->nombre }}"
                                            class="w-11 h-11 object-cover rounded-xl border border-purple-200 dark:border-purple-700/60 shadow-2xs mx-auto"
                                            loading="lazy"
                                        />
                                    @else
                                        <div class="w-11 h-11 rounded-xl bg-purple-100/60 dark:bg-purple-950/50 border border-purple-200/70 dark:border-purple-800/40 flex items-center justify-center text-primary dark:text-purple-300 shadow-2xs mx-auto" title="Sin fotografía asignada">
                                            <span class="material-symbols-outlined text-[20px]">image_not_supported</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-label-code text-xs">#{{ $prod->id }}</td>
                                <td class="py-3 px-4 font-bold text-on-surface">{{ $prod->nombre }}</td>
                                <td class="py-3 px-4 text-xs">{{ $prod->catalogo?->nombre }}</td>
                                <td class="py-3 px-4 text-right font-tabular-data font-bold">${{ number_format((float) $prod->precio, 2) }}</td>
                                <td class="py-3 px-4 text-right font-tabular-data font-semibold">{{ $prod->variantes_sku_count }}</td>
                                <td class="py-3 px-4 text-center">
                                    <button
                                        type="button"
                                        onclick="abrirModalActualizarImagen({{ $prod->id }}, '{{ addslashes($prod->nombre) }}', '{{ route('compras.productos.actualizar-imagen', $prod) }}', '{{ $prod->imagen ? asset('/' . ltrim($prod->imagen, '/')) : '' }}')"
                                        class="btn-glass-surface px-2.5 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 shadow-2xs"
                                        title="{{ $prod->imagen ? 'Actualizar o cambiar portada' : 'Subir foto de portada' }}"
                                    >
                                        <span class="material-symbols-outlined text-[16px] text-primary">add_a_photo</span>
                                        <span class="hidden sm:inline">{{ $prod->imagen ? 'Cambiar Foto' : 'Subir Foto' }}</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-outline text-xs">
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

    {{-- Modal Vidrioso para Actualizar Portada de Producto Existente --}}
    <div
        id="modal_actualizar_imagen"
        class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity duration-200"
        aria-modal="true"
        role="dialog"
    >
        <div class="bg-surface-container-lowest border border-purple-200/80 dark:border-purple-700/60 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 relative animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-purple-100 dark:border-purple-900/40 pb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">add_a_photo</span>
                    <h3 class="text-sm font-bold uppercase font-label-code text-on-surface">Actualizar Portada</h3>
                </div>
                <button
                    type="button"
                    onclick="cerrarModalActualizarImagen()"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-outline hover:text-on-surface hover:bg-surface-container-low transition"
                    title="Cerrar modal"
                >
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            <div class="space-y-1">
                <p id="modal_producto_nombre" class="font-bold text-sm text-on-surface line-clamp-1"></p>
                <p class="text-xs text-on-surface-variant font-label-code">
                    Seleccione la nueva fotografía para el producto y sus variantes SKU.
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
                    <label for="input_modal_imagen" class="block text-xs font-label-code uppercase text-on-surface">
                        Seleccionar Nueva Imagen (JPG, PNG, WEBP · Máx 4MB)
                    </label>

                    {{-- Vista Previa en Modal --}}
                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-surface-container-low/50 border border-purple-200/60 dark:border-purple-800/40">
                        <img
                            id="modal_preview_img"
                            src=""
                            alt="Portada actual / nueva"
                            class="w-16 h-16 object-cover rounded-xl border border-purple-300 dark:border-purple-700/60 shadow-xs shrink-0"
                        />
                        <div class="min-w-0 space-y-1 flex-1">
                            <input
                                type="file"
                                id="input_modal_imagen"
                                name="imagen"
                                accept="image/*"
                                required
                                class="w-full text-xs text-on-surface file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:opacity-90 border border-purple-200 dark:border-purple-800/60 rounded-xl p-1 bg-surface-container-low/50"
                            />
                            <p id="modal_archivo_info" class="text-[11px] text-on-surface-variant font-label-code truncate">
                                Ningún archivo nuevo seleccionado
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-purple-100 dark:border-purple-900/40">
                    <button
                        type="button"
                        onclick="cerrarModalActualizarImagen()"
                        class="btn-glass-surface px-4 py-2 rounded-xl text-xs font-bold"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="btn-glass-purple px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider inline-flex items-center gap-1.5"
                    >
                        <span class="material-symbols-outlined text-[16px]">save</span>
                        <span>Guardar Portada</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            function initImageInteractions() {
                // Alta Individual - Manejo de Imagen y Drag & Drop
                const inputImagen = document.getElementById('input_imagen_producto');
                const dropzone = document.getElementById('dropzone_imagen_producto');
                const previewContainer = document.getElementById('preview_container_imagen');
                const previewImg = document.getElementById('preview_img');
                const previewNombre = document.getElementById('preview_nombre');
                const previewPeso = document.getElementById('preview_peso');
                const btnQuitar = document.getElementById('btn_quitar_imagen');

                if (dropzone && inputImagen && !dropzone.dataset.initialized) {
                    dropzone.dataset.initialized = 'true';
                    dropzone.addEventListener('click', () => inputImagen.click());

                    ['dragenter', 'dragover'].forEach(eventName => {
                        dropzone.addEventListener(eventName, (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            dropzone.classList.add('border-primary', 'bg-purple-100/50', 'dark:bg-purple-900/30');
                        });
                    });

                    ['dragleave', 'drop'].forEach(eventName => {
                        dropzone.addEventListener(eventName, (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            dropzone.classList.remove('border-primary', 'bg-purple-100/50', 'dark:bg-purple-900/30');
                        });
                    });

                    dropzone.addEventListener('drop', (e) => {
                        const dt = e.dataTransfer;
                        const files = dt.files;
                        if (files && files.length > 0) {
                            inputImagen.files = files;
                            mostrarVistaPrevia(files[0]);
                        }
                    });

                    inputImagen.addEventListener('change', function () {
                        if (this.files && this.files[0]) {
                            mostrarVistaPrevia(this.files[0]);
                        }
                    });

                    if (btnQuitar) {
                        btnQuitar.addEventListener('click', function (e) {
                            e.stopPropagation();
                            inputImagen.value = '';
                            previewContainer.classList.add('hidden');
                            dropzone.classList.remove('hidden');
                            previewImg.src = '';
                        });
                    }

                    function mostrarVistaPrevia(file) {
                        if (!file.type.startsWith('image/')) {
                            alert('Por favor seleccione un archivo de imagen válido (JPG, PNG o WEBP).');
                            inputImagen.value = '';
                            return;
                        }
                        if (file.size > 4 * 1024 * 1024) {
                            alert('La imagen excede el límite permitido de 4MB.');
                            inputImagen.value = '';
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = function (e) {
                            previewImg.src = e.target.result;
                            previewNombre.textContent = file.name;
                            const pesoKb = (file.size / 1024).toFixed(1);
                            previewPeso.textContent = pesoKb > 1024 ? (pesoKb / 1024).toFixed(2) + ' MB' : pesoKb + ' KB';
                            dropzone.classList.add('hidden');
                            previewContainer.classList.remove('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                }

                // Modal de Actualización de Portada para Productos Existentes
                const modal = document.getElementById('modal_actualizar_imagen');
                const formActualizar = document.getElementById('form_actualizar_imagen');
                const modalProdNombre = document.getElementById('modal_producto_nombre');
                const modalPreviewImg = document.getElementById('modal_preview_img');
                const inputModalImagen = document.getElementById('input_modal_imagen');
                const modalArchivoInfo = document.getElementById('modal_archivo_info');

                window.abrirModalActualizarImagen = function (id, nombre, rutaAccion, imagenActual) {
                    if (!modal) return;
                    formActualizar.action = rutaAccion;
                    modalProdNombre.textContent = nombre;
                    if (inputModalImagen) inputModalImagen.value = '';
                    if (modalArchivoInfo) modalArchivoInfo.textContent = 'Ningún archivo nuevo seleccionado';

                    if (imagenActual) {
                        modalPreviewImg.src = imagenActual;
                    } else {
                        modalPreviewImg.src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="%238b5cf6"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>';
                    }

                    modal.classList.remove('hidden');
                };

                window.cerrarModalActualizarImagen = function () {
                    if (!modal) return;
                    modal.classList.add('hidden');
                    if (inputModalImagen) inputModalImagen.value = '';
                };

                if (inputModalImagen && !inputModalImagen.dataset.initialized) {
                    inputModalImagen.dataset.initialized = 'true';
                    inputModalImagen.addEventListener('change', function () {
                        if (this.files && this.files[0]) {
                            const file = this.files[0];
                            if (!file.type.startsWith('image/')) {
                                alert('Por favor seleccione una imagen válida (JPG, PNG o WEBP).');
                                this.value = '';
                                return;
                            }
                            if (file.size > 4 * 1024 * 1024) {
                                alert('La imagen no debe superar los 4MB.');
                                this.value = '';
                                return;
                            }
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                modalPreviewImg.src = e.target.result;
                                const pesoKb = (file.size / 1024).toFixed(1);
                                modalArchivoInfo.textContent = `${file.name} (${pesoKb > 1024 ? (pesoKb / 1024).toFixed(2) + ' MB' : pesoKb + ' KB'})`;
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }

                if (modal && !modal.dataset.initialized) {
                    modal.dataset.initialized = 'true';
                    modal.addEventListener('click', function (e) {
                        if (e.target === modal) {
                            cerrarModalActualizarImagen();
                        }
                    });

                    document.addEventListener('keydown', function (e) {
                        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                            cerrarModalActualizarImagen();
                        }
                    });
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initImageInteractions);
            } else {
                initImageInteractions();
            }

            // Integración con SPA navigation si cambia de sección
            window.addEventListener('admin-spa-navigated', initImageInteractions);
        })();
    </script>
    @endpush
</x-layouts.admin>
