<x-layouts.public :title="'ARCHIVUM · ' . $meta['titulo']">

    <!-- Encabezado de la Categoría -->
    <section class="relative rounded-2xl overflow-hidden bg-[#FAF8FF] dark:bg-gradient-to-r dark:from-purple-900 dark:to-indigo-950 text-slate-900 dark:text-white p-6 sm:p-10 mb-8 shadow-xs dark:shadow-lg border border-purple-200 dark:border-purple-800/40">
        <div class="relative z-10 max-w-3xl space-y-3">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 dark:bg-purple-500/20 border border-purple-200 dark:border-purple-300/30 text-purple-800 dark:text-purple-200 text-xs font-label-md tracking-wider font-semibold">
                <span class="material-symbols-outlined text-[15px] text-purple-600 dark:text-purple-300">{{ $meta['icono'] }}</span>
                <span>{{ $meta['badge'] }}</span>
            </span>
            <h1 class="text-2xl sm:text-4xl font-bold font-quicksand leading-tight text-slate-900 dark:text-white">
                {{ $meta['titulo'] }}
            </h1>
            <p class="font-title-editorial text-slate-600 dark:text-purple-100/90 text-base sm:text-lg leading-relaxed">
                «{{ $meta['descripcion'] }}»
            </p>
        </div>
    </section>

    @php
        $esColeccionables = ($categoriaSlug ?? '') === 'memorabilia' || ($categoriaActiva ?? '') === 'memorabilia';
        $listaCarruselColeccionables = ($coleccionablesDestacados ?? $productos ?? collect())->values();
    @endphp

    @if ($esColeccionables && $listaCarruselColeccionables->isNotEmpty())
        <!-- =========================================================================
             CARRUSEL HORIZONTAL CON DESENFOQUE LATERAL ("GRADIENT & EDGE-BLUR CAROUSEL")
             Inspirado en React Bits · Exclusivo para Coleccionables
             ========================================================================= -->
        <section
            id="coleccionables-edge-blur-section"
            class="relative mb-12 rounded-3xl bg-[#FAF8FF] dark:bg-[#130c21] border border-purple-200/80 dark:border-purple-500/30 p-5 sm:p-8 shadow-xs dark:shadow-xl overflow-hidden"
            aria-label="Piezas Destacadas de Colección"
        >
            <!-- Encabezado Moderno y Controles de Navegación Discretos -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-4">
                <div class="space-y-1">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-100 dark:bg-purple-500/20 border border-purple-200 dark:border-purple-400/30 text-purple-800 dark:text-purple-200 text-[11px] font-label-code font-bold uppercase tracking-wider">
                        <span class="material-symbols-outlined text-[14px]">auto_awesome</span>
                        <span>Exhibición Interactiva · Coleccionables</span>
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white font-quicksand">
                        Piezas Destacadas de Colección
                    </h2>
                    <p class="text-xs text-slate-600 dark:text-purple-200/80 font-quicksand">
                        Arrastra horizontalmente con el mouse o desliza en pantalla táctil para enfocar cada artículo de colección.
                    </p>
                </div>

                <!-- Botones de Flecha Izquierda / Derecha Discretos -->
                <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
                    <button
                        type="button"
                        id="edge-blur-prev-btn"
                        class="btn-glass-surface w-10 h-10 rounded-2xl flex items-center justify-center shadow-2xs cursor-pointer"
                        aria-label="Pieza anterior"
                        title="Anterior"
                    >
                        <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                    </button>
                    <button
                        type="button"
                        id="edge-blur-next-btn"
                        class="btn-glass-surface w-10 h-10 rounded-2xl flex items-center justify-center shadow-2xs cursor-pointer"
                        aria-label="Siguiente pieza"
                        title="Siguiente"
                    >
                        <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                    </button>
                </div>
            </div>

            <!-- Viewport con Máscara de Desenfoque y Degradado en Bordes Laterales -->
            <div class="relative">
                <!-- Overlays Laterales Suaves Adaptados a Modo Claro y Modo Oscuro -->
                <div class="pointer-events-none absolute inset-y-0 left-0 w-10 sm:w-20 bg-gradient-to-r from-[#FAF8FF] via-[#FAF8FF]/70 to-transparent dark:from-[#130c21] dark:via-[#130c21]/75 dark:to-transparent z-20"></div>
                <div class="pointer-events-none absolute inset-y-0 right-0 w-10 sm:w-20 bg-gradient-to-l from-[#FAF8FF] via-[#FAF8FF]/70 to-transparent dark:from-[#130c21] dark:via-[#130c21]/75 dark:to-transparent z-20"></div>

                <div class="edge-blur-carousel-viewport">
                    <div
                        id="coleccionables-edge-blur-track"
                        class="edge-blur-carousel-track"
                        role="region"
                        aria-roledescription="carousel"
                        aria-label="Carrusel de Piezas Destacadas de Colección"
                        tabindex="0"
                    >
                        @foreach ($listaCarruselColeccionables as $idxCol => $prodCol)
                            @php
                                /** @var \App\Models\Producto $prodCol */
                                $varianteCol = $prodCol->variantesSku->first();
                                $skuCol = $varianteCol?->sku ?? 'COL-ARC-'.$prodCol->id;
                                $graduacionRawCol = $varianteCol?->descripcion ?? 'MINT (M)';
                                $graduacionCortaCol = trim(explode('·', $graduacionRawCol)[0] ?? 'MINT (M)');
                                $stockCol = $varianteCol ? (int) $varianteCol->existencias->sum('cantidad') : 0;
                                $piezaUnicaCol = $varianteCol
                                    ? \App\Http\Controllers\CheckoutSimuladoController::obtenerPiezaUnicaDeVariante((int) $varianteCol->id)
                                    : null;
                                $esEdicionLimitadaCol = (bool) ($prodCol->coleccionable?->edicion_limitada ?? false);
                                $esPiezaUnicaCol = $piezaUnicaCol !== null || $stockCol === 1;
                                $estaReservadaCol = $piezaUnicaCol !== null && $piezaUnicaCol->estado === 'reservada';
                                $estaAgotadoCol = $stockCol <= 0 || ($piezaUnicaCol !== null && in_array($piezaUnicaCol->estado, ['reservada', 'vendida'], true));
                                $etiquetaBadgeCol = $esPiezaUnicaCol
                                    ? 'Pieza Única 1/1'
                                    : ($esEdicionLimitadaCol ? 'Edición Limitada' : 'Colección Especial');
                                $subtituloCol = ($prodCol->coleccionable?->marca ?? 'Colección Original').' · '.($prodCol->coleccionable?->material ?? 'Edición Certificada');
                                $sedeCol = $piezaUnicaCol?->existencia?->sucursal?->nombre
                                    ?? $varianteCol?->existencias?->first()?->sucursal?->nombre;

                                $datosModalCol = [
                                    'id' => $prodCol->id,
                                    'nombre' => $prodCol->nombre,
                                    'subtitulo' => $subtituloCol,
                                    'descripcion' => $prodCol->descripcion,
                                    'precio' => number_format((float) $prodCol->precio, 0),
                                    'tipo' => 'coleccionable',
                                    'icono' => 'history_edu',
                                    'sku' => $skuCol,
                                    'graduacion' => $graduacionCortaCol,
                                    'badgeType' => str_contains($graduacionCortaCol, 'COA') ? 'coa' : 'mint',
                                    'stock' => $stockCol,
                                    'sede' => $sedeCol ? str_replace('Sucursal ', '', $sedeCol) : null,
                                    'esPiezaUnica' => $esPiezaUnicaCol,
                                    'numeroSerie' => $piezaUnicaCol?->numero_serie,
                                    'imagen' => $prodCol->imagen_url,
                                    'estaAgotado' => $estaAgotadoCol,
                                    'estaReservada' => $estaReservadaCol,
                                    'varianteSkuId' => $varianteCol?->id,
                                    'actionUrl' => route('carrito.agregar'),
                                    'especificaciones' => [
                                        'Marca / Fabricante' => $prodCol->coleccionable?->marca ?? 'Colección Original',
                                        'Material de Confección' => $prodCol->coleccionable?->material ?? 'Material Auténtico',
                                        'Categoría' => ucfirst($prodCol->coleccionable?->categoria ?? 'Coleccionables'),
                                        'Año / Datación' => (string) ($prodCol->coleccionable?->anio_fabricacion ?? 'Edición Clásica'),
                                        'Certificado' => $prodCol->coleccionable?->certificado_autenticidad ?? 'Certificado de Autenticidad',
                                        'Edición' => $etiquetaBadgeCol,
                                    ],
                                ];
                            @endphp

                            <article
                                draggable="false"
                                data-edge-card-index="{{ $idxCol }}"
                                data-product-details="{{ json_encode($datosModalCol, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}"
                                class="edge-blur-card rounded-3xl {{ $idxCol === 0 ? 'is-center' : '' }} group cursor-pointer"
                            >
                                <!-- Fotografía en Alta Nitidez (object-cover) -->
                                @if (! empty($prodCol->imagen_url))
                                    <img
                                        draggable="false"
                                        src="{{ $prodCol->imagen_url }}"
                                        alt="{{ $prodCol->nombre }}"
                                        class="w-full h-full object-cover"
                                        loading="lazy"
                                    />
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-purple-100/70 dark:bg-[#1d1430]">
                                        <span class="material-symbols-outlined text-[72px] text-purple-500 dark:text-purple-300">history_edu</span>
                                    </div>
                                @endif

                                <!-- Degradado sutil para legibilidad de los elementos flotantes -->
                                <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/75 via-black/15 to-transparent"></div>

                                <!-- Badge Discreto en la Esquina Superior (Pieza Única / Edición Limitada + Disponibilidad) -->
                                <div class="absolute top-3.5 inset-x-3.5 flex items-center justify-between gap-2 z-10 pointer-events-none">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white/95 dark:bg-[#1a112b]/90 backdrop-blur-md text-purple-900 dark:text-purple-200 border border-purple-300/80 dark:border-purple-400/40 text-[10px] font-label-code font-bold uppercase tracking-wider shadow-xs">
                                        <span class="material-symbols-outlined text-[12px] text-purple-600 dark:text-purple-300">{{ $esPiezaUnicaCol ? 'diamond' : 'verified' }}</span>
                                        <span>{{ $etiquetaBadgeCol }}</span>
                                    </span>

                                    @if ($estaAgotadoCol)
                                        <span class="px-2.5 py-1 rounded-full bg-rose-600/95 text-white text-[10px] font-label-code font-bold uppercase shadow-xs">
                                            {{ $estaReservadaCol ? 'Reservada' : 'Agotado' }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full bg-emerald-500/90 text-white text-[10px] font-label-code font-bold uppercase shadow-xs">
                                            {{ $stockCol }} {{ $stockCol === 1 ? 'disponible' : 'disponibles' }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Tarjeta Flotante Inferior con Fondo Translúcido (backdrop-blur-md) -->
                                <div class="edge-blur-card-caption absolute bottom-3.5 inset-x-3.5 z-10 p-3.5 rounded-2xl bg-white/90 dark:bg-[#171024]/90 backdrop-blur-md border border-purple-200/90 dark:border-purple-400/35 shadow-lg flex flex-col gap-2.5">
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-label-code uppercase tracking-wider text-purple-700 dark:text-purple-300 font-semibold truncate">
                                            {{ $subtituloCol }}
                                        </p>
                                        <h3
                                            onclick="abrirModalDetalleProducto(this)"
                                            class="text-sm sm:text-base font-bold font-quicksand text-slate-900 dark:text-white truncate hover:text-primary dark:hover:text-purple-300 transition"
                                            title="{{ $prodCol->nombre }}"
                                        >
                                            {{ $prodCol->nombre }}
                                        </h3>
                                    </div>

                                    <div class="flex items-center justify-between gap-2 pt-2 border-t border-purple-200/70 dark:border-purple-500/25">
                                        <div>
                                            <span class="text-[9px] font-label-code uppercase text-slate-500 dark:text-purple-300/75 block">Precio:</span>
                                            <span class="font-tabular-data font-extrabold text-sm sm:text-base text-slate-900 dark:text-white">
                                                ${{ number_format((float) $prodCol->precio, 0) }}
                                                <span class="text-[10px] font-normal text-slate-600 dark:text-purple-200/80">MXN</span>
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-1.5">
                                            <button
                                                type="button"
                                                onclick="abrirModalDetalleProducto(this)"
                                                class="btn-glass-surface px-2.5 py-2 rounded-xl text-[11px] font-label-md font-bold uppercase tracking-wider inline-flex items-center gap-1 cursor-pointer"
                                                title="Ver detalles de la pieza"
                                            >
                                                <span class="material-symbols-outlined text-[15px]">visibility</span>
                                                <span>Ver pieza</span>
                                            </button>

                                            @if ($varianteCol && ! $estaAgotadoCol)
                                                <form method="POST" action="{{ route('carrito.agregar') }}" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="variante_sku_id" value="{{ $varianteCol->id }}" />
                                                    <input type="hidden" name="cantidad" value="1" />
                                                    <button
                                                        type="submit"
                                                        class="btn-glass-purple px-3 py-2 rounded-xl text-[11px] font-label-md font-bold uppercase tracking-wider inline-flex items-center gap-1 shadow-xs cursor-pointer"
                                                        title="Agregar al Carrito de Compras"
                                                    >
                                                        <span class="material-symbols-outlined text-[15px]">add_shopping_cart</span>
                                                        <span>Agregar</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Barra de Filtros, Búsqueda en Categoría y Selector de Orden -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-purple-200/80 gap-4 mb-6">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-on-surface font-quicksand">
                {{ $meta['subtitulo'] }}
            </h2>
            <p class="text-xs text-primary font-label-code mt-1 font-semibold uppercase tracking-wider">
                Mostrando {{ $productos->count() }} {{ $productos->count() === 1 ? 'producto disponible' : 'productos disponibles' }} en esta sección
            </p>
        </div>

        <form method="GET" class="flex items-center flex-wrap gap-3 text-xs font-label-md">
            @if (! empty($filtroTipo) && request()->filled($filtroTipo))
                <input type="hidden" name="{{ $filtroTipo }}" value="{{ request($filtroTipo) }}" />
            @endif
            <input
                type="search"
                name="q"
                value="{{ request('q') }}"
                placeholder="Filtrar en esta sección..."
                class="bg-white border border-purple-200/80 rounded-xl px-3.5 py-2 text-xs text-on-surface focus:outline-none focus:border-primary"
            />
            <select name="orden" onchange="this.form.submit()" class="bg-white text-on-surface border border-purple-200/80 rounded-xl py-2 px-3 focus:ring-1 focus:ring-primary focus:outline-none shadow-2xs font-medium cursor-pointer">
                <option value="relevancia" @selected(request('orden') === 'relevancia')>Ordenar: Destacados</option>
                <option value="precio_desc" @selected(request('orden') === 'precio_desc')>Precio: Mayor a Menor</option>
                <option value="precio_asc" @selected(request('orden') === 'precio_asc')>Precio: Menor a Mayor</option>
            </select>
        </form>
    </div>

    @if (! empty($filtros) && ! empty($filtroTipo))
        <!-- Barra de Filtros Específicos e Interactivos -->
        <div class="mb-8 p-4 sm:p-5 rounded-2xl bg-white/80 dark:bg-[#1a1226]/80 backdrop-blur-md border border-purple-200/80 dark:border-purple-500/30 shadow-xs space-y-3">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-primary">filter_list</span>
                    <span class="text-xs font-label-code font-bold uppercase tracking-wider text-on-surface">
                        @if ($categoriaSlug === 'libros')
                            Clasificación por Géneros Literarios:
                        @else
                            Menú de Colecciones &amp; Categorías:
                        @endif
                    </span>
                </div>
                @if (($filtroActivo ?? 'todos') !== 'todos')
                    <a href="{{ request()->fullUrlWithQuery([$filtroTipo => null]) }}" class="text-[11px] font-label-code text-primary hover:underline flex items-center gap-1 font-semibold">
                        <span class="material-symbols-outlined text-[14px]">close</span>
                        <span>Limpiar Filtro</span>
                    </a>
                @endif
            </div>

            <!-- Píldoras de Filtro Clickeables -->
            <div class="flex items-center flex-wrap gap-2 pt-1">
                @foreach ($filtros as $clave => $etiqueta)
                    @php
                        $estaActivo = ($filtroActivo ?? 'todos') === $clave;
                        $urlFiltro = $clave === 'todos'
                            ? request()->fullUrlWithQuery([$filtroTipo => null])
                            : request()->fullUrlWithQuery([$filtroTipo => $clave]);

                        $iconoFiltro = match ($clave) {
                            'novela' => 'menu_book',
                            'ensayo' => 'history_edu',
                            'poesia' => 'draw',
                            'historia' => 'account_balance',
                            'ciencia' => 'psychology',
                            'figuras' => 'smart_toy',
                            'juguetes' => 'toys',
                            'numismatica' => 'monetization_on',
                            'historicos' => 'museum',
                            'videojuegos' => 'sports_esports',
                            default => 'view_list',
                        };
                    @endphp
                    <a
                        href="{{ $urlFiltro }}"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-label-md font-semibold transition flex items-center gap-1.5 {{ $estaActivo ? 'bg-primary-container text-white shadow-xs border border-purple-400/50' : 'bg-surface-container-low hover:bg-surface-container text-on-surface-variant hover:text-on-surface border border-purple-200/60 dark:border-purple-500/30' }}"
                    >
                        <span class="material-symbols-outlined text-[15px]">{{ $iconoFiltro }}</span>
                        <span>{{ $etiqueta }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Cuadrícula Exclusiva de la Categoría -->
    @if ($productos->isNotEmpty())
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-5 mb-12">
            @foreach ($productos as $producto)
                <x-public.product-card :producto="$producto" />
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-2xl border border-purple-200/80 p-12 text-center space-y-3 mb-12">
            <span class="material-symbols-outlined text-[42px] text-outline">{{ $meta['icono'] }}</span>
            <h3 class="text-lg font-bold text-on-surface font-quicksand">No se encontraron piezas en esta categoría</h3>
            <p class="text-xs text-on-surface-variant max-w-md mx-auto">
                Intenta limpiar los filtros de búsqueda o explora otra sala del catálogo de ARCHIVUM.
            </p>
            <a href="{{ route('public.home') }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary-container text-white text-xs font-bold uppercase tracking-wider">
                <span>Ver Catálogo Completo</span>
            </a>
        </div>
    @endif

    @if ($esColeccionables && $listaCarruselColeccionables->isNotEmpty())
        @push('scripts')
            <script>
                (function () {
                    'use strict';

                    function initColeccionablesEdgeBlurCarousel() {
                        const track = document.getElementById('coleccionables-edge-blur-track');
                        if (!track || track.dataset.edgeBlurBound === 'true') {
                            return;
                        }
                        track.dataset.edgeBlurBound = 'true';

                        const cards = Array.from(track.querySelectorAll('.edge-blur-card'));
                        const prevBtn = document.getElementById('edge-blur-prev-btn');
                        const nextBtn = document.getElementById('edge-blur-next-btn');
                        if (cards.length === 0) {
                            return;
                        }

                        let activeIndex = 0;

                        // Calcula cuál tarjeta está más cerca del centro del viewport y le aplica .is-center (nítida y scale(1))
                        function updateCenterCard() {
                            const trackRect = track.getBoundingClientRect();
                            const trackCenter = trackRect.left + trackRect.width / 2;
                            let closestIndex = 0;
                            let minDistance = Infinity;

                            cards.forEach(function (card, idx) {
                                const rect = card.getBoundingClientRect();
                                const cardCenter = rect.left + rect.width / 2;
                                const dist = Math.abs(cardCenter - trackCenter);
                                if (dist < minDistance) {
                                    minDistance = dist;
                                    closestIndex = idx;
                                }
                            });

                            activeIndex = closestIndex;
                            cards.forEach(function (card, idx) {
                                if (idx === activeIndex) {
                                    card.classList.add('is-center');
                                } else {
                                    card.classList.remove('is-center');
                                }
                            });
                        }

                        function scrollToCard(index) {
                            const targetIdx = (index + cards.length) % cards.length;
                            const targetCard = cards[targetIdx];
                            if (!targetCard) return;

                            const targetLeft = targetCard.offsetLeft - (track.clientWidth - targetCard.clientWidth) / 2;
                            track.scrollTo({
                                left: Math.max(0, targetLeft),
                                behavior: 'smooth',
                            });
                        }

                        // Actualizar estado de enfoque al hacer scroll con requestAnimationFrame
                        let rafPending = false;
                        track.addEventListener('scroll', function () {
                            if (!rafPending) {
                                rafPending = true;
                                requestAnimationFrame(function () {
                                    updateCenterCard();
                                    rafPending = false;
                                });
                            }
                        }, { passive: true });

                        // Botones de flecha izquierda / derecha
                        if (prevBtn) {
                            prevBtn.addEventListener('click', function () {
                                scrollToCard(activeIndex - 1);
                            });
                        }
                        if (nextBtn) {
                            nextBtn.addEventListener('click', function () {
                                scrollToCard(activeIndex + 1);
                            });
                        }

                        // Arrastre fluido con Mouse (drag) e inercia suave sin bloquear botones o enlaces internos
                        let isDragging = false;
                        let dragMoved = false;
                        let startX = 0;
                        let startScrollLeft = 0;
                        let velocityX = 0;
                        let lastClientX = 0;
                        let lastTimestamp = 0;
                        let momentumId = null;

                        function stopMomentum() {
                            if (momentumId) {
                                cancelAnimationFrame(momentumId);
                                momentumId = null;
                            }
                        }

                        track.addEventListener('mousedown', function (e) {
                            if (e.button !== 0) return;
                            if (e.target && typeof e.target.closest === 'function' && e.target.closest('button, a, form, input, select')) {
                                return;
                            }

                            stopMomentum();
                            isDragging = true;
                            dragMoved = false;
                            startX = e.clientX;
                            lastClientX = e.clientX;
                            lastTimestamp = performance.now();
                            velocityX = 0;
                            startScrollLeft = track.scrollLeft;
                            track.classList.add('is-dragging');
                        });

                        window.addEventListener('mousemove', function (e) {
                            if (!isDragging) return;
                            const dx = e.clientX - startX;
                            if (Math.abs(dx) > 6) {
                                dragMoved = true;
                            }
                            const now = performance.now();
                            const dt = Math.max(1, now - lastTimestamp);
                            velocityX = (e.clientX - lastClientX) / dt;
                            lastClientX = e.clientX;
                            lastTimestamp = now;

                            track.scrollLeft = startScrollLeft - dx;
                        });

                        window.addEventListener('mouseup', function () {
                            if (!isDragging) return;
                            isDragging = false;
                            track.classList.remove('is-dragging');

                            if (dragMoved && Math.abs(velocityX) > 0.25) {
                                // Inercia natural y luego centrado suave en la tarjeta más cercana
                                let currentVelocity = velocityX * 18;
                                function applyMomentum() {
                                    track.scrollLeft -= currentVelocity;
                                    currentVelocity *= 0.88;
                                    if (Math.abs(currentVelocity) > 0.8) {
                                        momentumId = requestAnimationFrame(applyMomentum);
                                    } else {
                                        updateCenterCard();
                                        scrollToCard(activeIndex);
                                    }
                                }
                                momentumId = requestAnimationFrame(applyMomentum);
                            } else if (dragMoved) {
                                updateCenterCard();
                                scrollToCard(activeIndex);
                            }
                        });

                        // Clic sobre una tarjeta lateral desenfocada la centra; clic en tarjeta central abre modal si no es botón
                        track.addEventListener('click', function (e) {
                            if (dragMoved) {
                                e.preventDefault();
                                e.stopPropagation();
                                dragMoved = false;
                                return;
                            }

                            if (e.target && typeof e.target.closest === 'function' && e.target.closest('button, a, form, input, select')) {
                                return;
                            }

                            const clickedCard = e.target.closest('.edge-blur-card');
                            if (!clickedCard) return;

                            const idx = parseInt(clickedCard.getAttribute('data-edge-card-index') || '0', 10);
                            if (!isNaN(idx) && idx !== activeIndex) {
                                e.preventDefault();
                                scrollToCard(idx);
                            } else if (typeof window.abrirModalDetalleProducto === 'function') {
                                window.abrirModalDetalleProducto(clickedCard);
                            }
                        });

                        // Soporte de teclado accesible
                        track.addEventListener('keydown', function (e) {
                            if (e.key === 'ArrowLeft') {
                                e.preventDefault();
                                scrollToCard(activeIndex - 1);
                            } else if (e.key === 'ArrowRight') {
                                e.preventDefault();
                                scrollToCard(activeIndex + 1);
                            }
                        });

                        window.addEventListener('resize', updateCenterCard);
                        updateCenterCard();
                    }

                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', initColeccionablesEdgeBlurCarousel);
                    } else {
                        initColeccionablesEdgeBlurCarousel();
                    }
                })();
            </script>
        @endpush
    @endif

</x-layouts.public>

