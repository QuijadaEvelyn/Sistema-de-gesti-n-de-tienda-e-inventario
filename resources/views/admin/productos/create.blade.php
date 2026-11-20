<x-layouts.admin title="ARCHIVUM · Dar de Alta Producto" active-route="admin-productos">
    <div class="max-w-5xl mx-auto space-y-6">

        {{-- Encabezado Bento --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('admin.productos.index') }}" class="inline-flex items-center gap-1.5 text-xs font-label-code text-primary dark:text-purple-300 hover:underline mb-1">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>Regresar al Catálogo de Productos</span>
                </a>
                <h1 class="text-2xl font-bold text-on-surface dark:text-white font-quicksand">
                    Dar de Alta Producto en Bóveda
                </h1>
                <p class="text-xs text-on-surface-variant dark:text-purple-200/70 font-label-code uppercase tracking-wider mt-0.5">
                    Registro de nueva pieza histórica &middot; Autorizado para Administradores y Personal Operativo
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.inventario') }}" class="px-3.5 py-2 rounded-xl border border-purple-200 bg-purple-100 hover:bg-purple-200 text-purple-800 dark:bg-white/5 dark:border-purple-500/30 dark:text-purple-300 text-xs font-label-md flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[16px]">warehouse</span>
                    <span>Ver Inventario</span>
                </a>
            </div>
        </div>

        {{-- Alertas de Error --}}
        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-700/60 text-rose-800 dark:text-rose-200 text-sm space-y-1.5 shadow-xs">
                <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wider">
                    <span class="material-symbols-outlined text-rose-600 dark:text-rose-400 text-[18px]">error</span>
                    <span>Corrige los siguientes campos para continuar:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Formulario Principal con Estilo Bento y Soporte de Modo Claro / Oscuro --}}
        <form method="POST" action="{{ route('admin.productos.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- 1. Información General del Producto --}}
            <div class="bg-surface-container-lowest dark:bg-[#1a1329] p-6 rounded-3xl border border-purple-200/70 dark:border-purple-800/40 shadow-xs space-y-4">
                <div class="flex items-center gap-2 border-b border-purple-100 dark:border-purple-900/40 pb-3">
                    <span class="material-symbols-outlined text-primary dark:text-purple-300 text-[22px]">inventory_2</span>
                    <h2 class="text-sm font-bold uppercase font-label-code text-on-surface dark:text-white">1. Identificación Principal del Producto</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Catálogo / Categoría --}}
                    <div>
                        <label for="select_catalogo" class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">
                            Categoría / Catálogo <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="select_catalogo"
                            name="catalogo_id"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white focus:outline-none focus:border-primary"
                        >
                            @foreach ($catalogos as $cat)
                                <option
                                    value="{{ $cat->id }}"
                                    data-tipo="{{ strtolower($cat->tipo ?? '') }}"
                                    data-nombre="{{ strtolower($cat->nombre ?? '') }}"
                                    @selected((string) old('catalogo_id') === (string) $cat->id)
                                >
                                    {{ $cat->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Código SKU --}}
                    <div>
                        <label for="input_sku" class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">
                            Código SKU <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="input_sku"
                            name="sku"
                            value="{{ old('sku') }}"
                            required
                            placeholder="Ej. LIB-1967-001 o VIN-1969-PCS7088"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white font-label-code uppercase focus:outline-none focus:border-primary"
                        />
                    </div>

                    {{-- Estado de Conservación / Graduación --}}
                    <div>
                        <label for="select_conservacion" class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">
                            Estado de Conservación
                        </label>
                        <select
                            id="select_conservacion"
                            name="estado_conservacion"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white focus:outline-none focus:border-primary"
                        >
                            <option value="MINT (M)" @selected(old('estado_conservacion') === 'MINT (M)')>MINT (M) &middot; Impecable / Como Nuevo</option>
                            <option value="NEAR MINT (NM)" @selected(old('estado_conservacion') === 'NEAR MINT (NM)')>NEAR MINT (NM) &middot; Casi Nuevo</option>
                            <option value="VERY FINE (VF)" @selected(old('estado_conservacion') === 'VERY FINE (VF)')>VERY FINE (VF) &middot; Muy Fino de Época</option>
                            <option value="FINE (F)" @selected(old('estado_conservacion') === 'FINE (F)')>FINE (F) &middot; Buen Estado Conservado</option>
                            <option value="VERY GOOD (VG)" @selected(old('estado_conservacion') === 'VERY GOOD (VG)')>VERY GOOD (VG) &middot; Desgaste Clásico de Época</option>
                        </select>
                    </div>

                    {{-- Título / Nombre Completo del Producto --}}
                    <div class="md:col-span-3">
                        <label for="input_nombre" class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">
                            Título / Nombre de la Pieza <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="input_nombre"
                            name="nombre"
                            value="{{ old('nombre') }}"
                            required
                            placeholder="Ej. Cien Años de Soledad (Primera Edición Editorial Sudamericana 1967)"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white font-medium focus:outline-none focus:border-primary"
                        />
                    </div>
                </div>
            </div>

            {{-- 2. Ficha Curatorial Dinámica según Categoría (Libros, Discos o Coleccionables) --}}
            <div class="bg-surface-container-lowest dark:bg-[#1a1329] p-6 rounded-3xl border border-purple-200/70 dark:border-purple-800/40 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-purple-100 dark:border-purple-900/40 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary dark:text-purple-300 text-[22px]" id="icono_ficha">auto_stories</span>
                        <h2 class="text-sm font-bold uppercase font-label-code text-on-surface dark:text-white" id="titulo_ficha">
                            2. Ficha Curatorial Especializada
                        </h2>
                    </div>
                    <span id="badge_ficha" class="text-[10px] font-label-code text-primary dark:text-purple-300 uppercase font-bold bg-purple-100 dark:bg-purple-900/50 px-2.5 py-0.5 rounded-full border border-purple-300 dark:border-purple-700/60">
                        Metadatos &middot; Libros
                    </span>
                </div>

                {{-- Panel A: LIBROS --}}
                <div id="panel_libro" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">Autor / Escritor</label>
                        <input type="text" name="autor" value="{{ old('autor') }}" placeholder="Ej. Gabriel García Márquez" class="w-full px-3 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">Editorial</label>
                        <input type="text" name="editorial" value="{{ old('editorial') }}" placeholder="Ej. Editorial Sudamericana" class="w-full px-3 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">Género Literario</label>
                        <input type="text" name="genero" value="{{ old('genero') }}" placeholder="Ej. Novela / Realismo Mágico" class="w-full px-3 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">Año de Publicación</label>
                        <input type="number" name="anio_publicacion" min="1800" max="2030" value="{{ old('anio_publicacion') }}" placeholder="1967" class="w-full px-3 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white font-tabular-data" />
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">Número de Páginas</label>
                        <input type="number" name="paginas" min="1" max="20000" value="{{ old('paginas') }}" placeholder="471" class="w-full px-3 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white font-tabular-data" />
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">Código ISBN (Opcional)</label>
                        <input type="text" name="isbn" value="{{ old('isbn') }}" placeholder="978-950-07-0000-0" class="w-full px-3 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white font-label-code" />
                    </div>
                </div>

                {{-- Panel B: DISCOS Y VINILOS --}}
                <div id="panel_disco" class="hidden grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">Artista / Intérprete</label>
                        <input type="text" name="artista" value="{{ old('artista') }}" placeholder="Ej. Miles Davis / The Beatles" class="w-full px-3 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">Sello Discográfico</label>
                        <input type="text" name="sello_discografico" value="{{ old('sello_discografico') }}" placeholder="Ej. Columbia Records / Blue Note" class="w-full px-3 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">Formato Fonográfico</label>
                        <select name="formato" class="w-full px-3 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white">
                            <option value="vinilo">Vinilo LP 180g (Prensado Analógico)</option>
                            <option value="CD">Disco Compacto (CD Audiofilia)</option>
                            <option value="cassette">Cassette Original</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">Año de Lanzamiento</label>
                        <input type="number" name="anio_lanzamiento" min="1900" max="2030" value="{{ old('anio_lanzamiento') }}" placeholder="1959" class="w-full px-3 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white font-tabular-data" />
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">Velocidad / RPM</label>
                        <input type="text" name="rpm" value="{{ old('rpm') }}" placeholder="33 ⅓ RPM" class="w-full px-3 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white" />
                    </div>
                </div>

                {{-- Panel C: COLECCIONABLES Y MEMORABILIA --}}
                <div id="panel_coleccionable" class="hidden grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">Fabricante / Marca</label>
                        <input type="text" name="fabricante" value="{{ old('fabricante') }}" placeholder="Ej. Kaiyodo / Royal Mint / Lego" class="w-full px-3 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">Subcategoría</label>
                        <select name="categoria" class="w-full px-3 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white">
                            <option value="figuras">Figuras (Cultura Pop & Anime)</option>
                            <option value="numismatica">Numismática (Monedas & Billetes)</option>
                            <option value="juguetes">Juguetes y Manufacturas</option>
                            <option value="historicos">Objetos Históricos Notariados</option>
                            <option value="videojuegos">Memorabilia Gamer & Retro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">Material de Confección</label>
                        <input type="text" name="material" value="{{ old('material') }}" placeholder="Ej. Resina Polystone / Oro Puro 900" class="w-full px-3 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white" />
                    </div>
                </div>
            </div>

            {{-- 3. Precios, Existencia y Fotografía de Portada --}}
            <div class="bg-surface-container-lowest dark:bg-[#1a1329] p-6 rounded-3xl border border-purple-200/70 dark:border-purple-800/40 shadow-xs space-y-4">
                <div class="flex items-center gap-2 border-b border-purple-100 dark:border-purple-900/40 pb-3">
                    <span class="material-symbols-outlined text-primary dark:text-purple-300 text-[22px]">payments</span>
                    <h2 class="text-sm font-bold uppercase font-label-code text-on-surface dark:text-white">3. Valor Tasado, Sede de Ingreso &amp; Fotografía</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Precio Tasado Base --}}
                    <div>
                        <label for="input_precio" class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">
                            Precio Tasado (MXN) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-sm font-bold text-outline">$</span>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                id="input_precio"
                                name="precio"
                                value="{{ old('precio') }}"
                                required
                                placeholder="18500.00"
                                class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white font-tabular-data font-bold focus:outline-none focus:border-primary"
                            />
                        </div>
                    </div>

                    {{-- Stock Inicial --}}
                    <div>
                        <label for="input_stock" class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">
                            Stock Inicial (Piezas) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="number"
                            min="1"
                            max="10000"
                            id="input_stock"
                            name="stock_inicial"
                            value="{{ old('stock_inicial', 1) }}"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white font-tabular-data focus:outline-none focus:border-primary"
                        />
                        <p class="text-[10px] text-on-surface-variant dark:text-purple-200/70 mt-1 font-label-code">
                            Pieza única: 1 unidad &middot; Stock masivo: &gt;1 unidades
                        </p>
                    </div>

                    {{-- Sucursal / Sede de Custodia --}}
                    <div>
                        <label for="select_sucursal" class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">
                            Sede de Ingreso Inicial
                        </label>
                        <select
                            id="select_sucursal"
                            name="sucursal_id"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white focus:outline-none focus:border-primary"
                        >
                            @foreach ($sucursales as $suc)
                                <option value="{{ $suc->id }}" @selected((int) old('sucursal_id', $sucursalActivaId) === $suc->id)>
                                    {{ $suc->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Descripción Curatorial --}}
                    <div class="md:col-span-3">
                        <label for="input_descripcion" class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold mb-1">
                            Descripción Curatorial &amp; Reseña Patrimonial
                        </label>
                        <textarea
                            id="input_descripcion"
                            name="descripcion"
                            rows="3"
                            placeholder="Describa el origen notariado, estado de conservación, sellos periciales o rareza de la pieza..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white focus:outline-none focus:border-primary resize-y"
                        >{{ old('descripcion') }}</textarea>
                    </div>

                    {{-- Fotografía con Dropzone y Vista Previa --}}
                    <div class="md:col-span-3 space-y-2">
                        <label for="input_imagen" class="block text-xs font-label-code uppercase text-on-surface dark:text-white font-semibold">
                            Fotografía de Portada (JPG, PNG, WEBP &middot; Máx 4MB)
                        </label>

                        <div class="flex flex-col sm:flex-row items-center gap-4 p-4 rounded-2xl border-2 border-dashed border-purple-200 dark:border-purple-800/60 bg-surface-container-low/40 dark:bg-[#140d20]">
                            <img
                                id="create_preview_img"
                                src=""
                                alt="Vista previa"
                                class="hidden w-20 h-20 object-cover rounded-xl border border-purple-300 dark:border-purple-700 shadow-xs"
                            />
                            <div class="flex-1 space-y-1 text-center sm:text-left">
                                <p id="create_preview_label" class="text-xs font-semibold text-on-surface dark:text-white">
                                    Seleccione o arrastre la fotografía oficial de la pieza
                                </p>
                                <p class="text-[11px] text-on-surface-variant dark:text-purple-200/70 font-label-code">
                                    Se publicará en la galería y carrusel 3D de la tienda pública
                                </p>
                            </div>
                            <input
                                type="file"
                                id="input_imagen"
                                name="imagen"
                                accept="image/*"
                                class="text-xs file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:opacity-90 border border-purple-200 dark:border-purple-800/60 rounded-xl p-1.5 bg-white dark:bg-[#1f1a2e] text-on-surface dark:text-white cursor-pointer"
                            />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Botones de Acción Final --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.productos.index') }}" class="px-5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs font-semibold text-outline dark:text-purple-300 hover:text-on-surface transition">
                    Cancelar
                </a>
                <button
                    type="submit"
                    class="btn-glass-purple px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider shadow-md flex items-center gap-2 cursor-pointer"
                >
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                    <span>Registrar &amp; Dar de Alta en Tienda</span>
                </button>
            </div>
        </form>

    </div>

    <script>
        // Sincronización dinámica de campos según el catálogo elegido
        const selectCat = document.getElementById('select_catalogo');
        const panelLibro = document.getElementById('panel_libro');
        const panelDisco = document.getElementById('panel_disco');
        const panelColeccionable = document.getElementById('panel_coleccionable');
        const badgeFicha = document.getElementById('badge_ficha');
        const iconoFicha = document.getElementById('icono_ficha');

        function actualizarFicha() {
            if (!selectCat) return;
            const opt = selectCat.selectedOptions[0];
            const tipo = (opt?.dataset.tipo || '').toLowerCase();
            const nombre = (opt?.dataset.nombre || '').toLowerCase();

            panelLibro.classList.add('hidden');
            panelDisco.classList.add('hidden');
            panelColeccionable.classList.add('hidden');

            if (tipo === 'disco' || tipo === 'vinilo' || nombre.includes('disco') || nombre.includes('vinil')) {
                panelDisco.classList.remove('hidden');
                badgeFicha.textContent = 'Metadatos · Discos & Vinilos';
                iconoFicha.textContent = 'album';
            } else if (tipo === 'coleccionable' || nombre.includes('coleccion') || nombre.includes('memora')) {
                panelColeccionable.classList.remove('hidden');
                badgeFicha.textContent = 'Metadatos · Coleccionables';
                iconoFicha.textContent = 'history_edu';
            } else {
                panelLibro.classList.remove('hidden');
                badgeFicha.textContent = 'Metadatos · Libros';
                iconoFicha.textContent = 'auto_stories';
            }
        }

        selectCat?.addEventListener('change', actualizarFicha);
        actualizarFicha();

        // Previsualización de imagen
        document.getElementById('input_imagen')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const imgPreview = document.getElementById('create_preview_img');
            const lbl = document.getElementById('create_preview_label');

            if (file) {
                lbl.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB) listo para publicar';
                const reader = new FileReader();
                reader.onload = function(evt) {
                    imgPreview.src = evt.target.result;
                    imgPreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</x-layouts.admin>
