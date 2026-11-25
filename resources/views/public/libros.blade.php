<x-layouts.public :title="'ARCHIVUM · ' . ($meta['titulo'] ?? 'Libros & Ediciones Especiales')">

    @php
        $coleccionLibros = $productos ?? collect();
        $librosEstante = $librosEstante ?? [];
        $totalLibrosEstante = count($librosEstante);
        $libroInicial = $totalLibrosEstante > 0 ? $librosEstante[0] : null;
    @endphp

    <!-- Encabezado de la Sección de Libros -->
    <section class="relative rounded-2xl overflow-hidden bg-[#FAF8FF] dark:bg-gradient-to-r dark:from-purple-950 dark:via-indigo-950 dark:to-slate-950 text-slate-900 dark:text-white p-6 sm:p-8 mb-8 shadow-xs dark:shadow-lg border border-purple-200 dark:border-purple-700/40">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="max-w-2xl space-y-2">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 dark:bg-purple-500/20 border border-purple-200 dark:border-purple-300/30 text-purple-800 dark:text-purple-200 text-xs font-label-md tracking-wider font-semibold">
                    <span class="material-symbols-outlined text-[15px] text-purple-600 dark:text-purple-300">auto_stories</span>
                    <span>{{ $meta['badge'] ?? 'CATÁLOGO DE LIBROS · EDICIONES ESPECIALES' }}</span>
                </span>
                <h1 class="text-2xl sm:text-4xl font-bold font-quicksand leading-tight text-slate-900 dark:text-white">
                    {{ $meta['titulo'] ?? 'Libros & Primeras Ediciones' }}
                </h1>
                <p class="font-title-editorial text-slate-600 dark:text-purple-100/90 text-sm sm:text-base leading-relaxed">
                    «Explora nuestra estantería interactiva en 3D: examina cada lomo, desliza el cursor para abrir los ejemplares y gira cada libro para descubrir su portada original, ficha técnica y Certificado de Autenticidad.»
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 text-xs font-label-code">
                <span class="px-3 py-1.5 rounded-xl bg-purple-100 dark:bg-white/10 border border-purple-200 dark:border-white/15 text-purple-800 dark:text-purple-200 flex items-center gap-1.5 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Estante 3D Activo</span>
                </span>
                <a href="#acervo-libros-grid" class="px-3.5 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold uppercase tracking-wider transition shadow-sm">
                    Ver Catálogo Completo ({{ $coleccionLibros->count() }})
                </a>
            </div>
        </div>
    </section>

    @if ($totalLibrosEstante > 0 && $libroInicial !== null)
        <!-- =========================================================================
             ESTANTE 3D INTERACTIVO DE LIBROS (LOMO → PORTADA EN PERSPECTIVA)
             ========================================================================= -->
        <section
            id="bookshelf-showcase-section"
            class="relative rounded-3xl overflow-hidden bg-purple-50/80 dark:bg-gradient-to-b dark:from-[#130b24] dark:via-[#1b1033] dark:to-[#0d0718] text-slate-900 dark:text-white border border-purple-200 dark:border-purple-500/30 shadow-sm dark:shadow-2xl mb-12 p-4 sm:p-8 w-full max-w-full"
            aria-label="Estantería Curatorial 3D de Libros Raros"
        >
            <!-- Halo Ambiental Dinámico -->
            <div
                id="bookshelf-ambient-glow"
                class="pointer-events-none absolute -top-24 left-1/2 -translate-x-1/2 w-[580px] h-[580px] rounded-full blur-3xl opacity-35 transition-all duration-700"
                style="background: radial-gradient(circle, {{ $libroInicial['paleta']['accent'] ?? '#8b5cf6' }} 0%, transparent 70%);"
            ></div>

            <!-- Barra Superior de Estado del Estante -->
            <div class="relative z-10 flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-purple-200/80 dark:border-purple-500/20">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-100 dark:bg-white/10 border border-purple-200 dark:border-white/15 text-[11px] font-label-code uppercase tracking-wider text-purple-900 dark:text-purple-200 font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span id="bookshelf-status-label">EDICIONES ESPECIALES · LOMO A PORTADA</span>
                    </span>
                    <span id="bookshelf-genre-pill" class="px-3 py-1 rounded-full bg-purple-200/60 dark:bg-purple-500/20 border border-purple-300 dark:border-purple-400/30 text-purple-900 dark:text-purple-200 text-[11px] font-label-code font-bold uppercase tracking-wider flex items-center gap-1">
                        <span class="material-symbols-outlined text-[13px]" id="bookshelf-genre-icon">{{ $libroInicial['genero_icono'] }}</span>
                        <span id="bookshelf-genre-name">{{ $libroInicial['genero_nombre'] }}</span>
                    </span>
                </div>

                <div class="text-xs font-label-code text-slate-600 dark:text-purple-200/80 flex items-center gap-3">
                    <span class="hidden sm:inline-flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px] text-purple-600 dark:text-purple-300">3d_rotation</span>
                        <span>Pasa el cursor o usa ← → para examinar cada obra</span>
                    </span>
                    <!-- Controles Anterior / Siguiente -->
                    <div class="flex items-center gap-1">
                        <button
                            type="button"
                            id="bookshelf-prev-btn"
                            class="w-8 h-8 rounded-lg bg-white dark:bg-white/10 hover:bg-purple-100 dark:hover:bg-purple-600/60 border border-purple-200 dark:border-white/20 text-purple-900 dark:text-white flex items-center justify-center transition cursor-pointer shadow-xs"
                            title="Libro anterior (Flecha Izquierda)"
                            aria-label="Libro anterior"
                        >
                            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                        </button>
                        <span id="bookshelf-counter-label" class="text-[11px] font-mono px-2 text-purple-800 dark:text-purple-300 font-bold">
                            01 / {{ str_pad((string) $totalLibrosEstante, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        <button
                            type="button"
                            id="bookshelf-next-btn"
                            class="w-8 h-8 rounded-lg bg-white dark:bg-white/10 hover:bg-purple-100 dark:hover:bg-purple-600/60 border border-purple-200 dark:border-white/20 text-purple-900 dark:text-white flex items-center justify-center transition cursor-pointer shadow-xs"
                            title="Siguiente libro (Flecha Derecha)"
                            aria-label="Siguiente libro"
                        >
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Escenario 3D de la Estantería (Perspective 1400px) -->
            <div class="relative z-10 pt-4 sm:pt-6 pb-2">
                <div
                    id="bookshelf-stage"
                    class="bookshelf-stage relative w-full max-w-full min-h-[290px] sm:min-h-[430px] flex items-center justify-center select-none overflow-hidden py-4 px-1"
                    style="perspective: 1400px; min-height: 290px;"
                >
                    <div id="bookshelf-row" class="flex items-end justify-center gap-1 sm:gap-1.5 transition-all duration-300">
                        @php
                            $espesoresXs = [40, 44, 38, 46, 40, 42, 38, 44];
                            $espesoresSm = [44, 48, 42, 50, 42, 46, 42, 46];
                            $espesoresLg = [50, 56, 44, 58, 46, 54, 42, 52];
                            $alturasRatio = [0.97, 1.00, 0.90, 0.95, 0.88, 0.99, 0.92, 0.96];
                        @endphp
                        @foreach ($librosEstante as $idx => $libro)
                            @php
                                $esInicial = $idx === 0;
                                $paleta = $libro['paleta'];
                                $wXs = $espesoresXs[$idx % count($espesoresXs)];
                                $wSm = $espesoresSm[$idx % count($espesoresSm)];
                                $wLg = $espesoresLg[$idx % count($espesoresLg)];
                                $hRatio = $alturasRatio[$idx % count($alturasRatio)];
                                $bandPrimary = $paleta['band_hex'] ?? '#d4af37';
                                $bandSecondary = $paleta['band_secondary_hex'] ?? 'rgba(212, 175, 55, 0.45)';
                                $tejueloBg = $paleta['tejuelo_bg'] ?? 'rgba(15, 10, 20, 0.58)';
                                $tejueloText = $paleta['tejuelo_text'] ?? '#f5e1a4';
                                $bgTop = $paleta['bg_top'] ?? ($paleta['bg_hex'] ?? '#4a0e17');
                                $bgMid = $paleta['bg_hex'] ?? '#4a0e17';
                                $bgBottom = $paleta['bg_bottom'] ?? ($paleta['bg_hex'] ?? '#2d080e');
                                $borderHex = $paleta['border_hex'] ?? 'rgba(212, 175, 55, 0.45)';
                            @endphp

                            <!-- Ranura de Libro 3D -->
                            <div
                                class="shelf-book-slot group relative cursor-pointer select-none transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] {{ $esInicial ? 'active is-expanded' : '' }}"
                                data-book-index="{{ $idx }}"
                                style="--spine-w-xs: {{ $wXs }}px; --spine-w-sm: {{ $wSm }}px; --spine-w-lg: {{ $wLg }}px; --book-h-ratio: {{ $hRatio }};"
                                role="button"
                                tabindex="0"
                                aria-label="Seleccionar libro {{ $libro['nombre'] }}"
                            >
                                <!-- Estructura Tridimensional del Libro -->
                                <div class="shelf-book-3d transform-gpu transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]">
                                    
                                    <!-- ==========================================
                                         1. VISTA EN REPOSO: LOMO 3D FRONTAL (SPINE)
                                         ========================================== -->
                                    <div
                                        class="shelf-book-spine absolute inset-0 w-full h-full rounded-[3px] overflow-hidden shadow-[0_8px_18px_rgba(0,0,0,0.45)] flex flex-col justify-between py-2.5 px-1"
                                        style="background: linear-gradient(180deg, {{ $bgTop }} 0%, {{ $bgMid }} 52%, {{ $bgBottom }} 100%); border-left: 1px solid {{ $borderHex }}; border-right: 1px solid rgba(0,0,0,0.55);"
                                        title="{{ $libro['nombre'] }} · {{ $libro['autor'] }}"
                                    >
                                        <!-- Costilla superior: Bandas doradas/plateadas y tejuelo del año -->
                                        <div class="relative z-10 w-full space-y-1">
                                            <div class="h-[2px] w-full rounded-full opacity-90" style="background-color: {{ $bandPrimary }};"></div>
                                            <div class="h-px w-full opacity-75" style="background-color: {{ $bandSecondary }};"></div>
                                            <div class="mx-auto px-0.5 py-0.5 rounded-[2px] border text-center" style="background-color: {{ $tejueloBg }}; border-color: {{ $bandSecondary }};">
                                                <span class="text-[8px] sm:text-[9px] font-mono font-bold tracking-wider block leading-none" style="color: {{ $tejueloText }};">
                                                    {{ $libro['anio'] }}
                                                </span>
                                            </div>
                                            <div class="h-px w-4/5 mx-auto opacity-60" style="background-color: {{ $bandSecondary }};"></div>
                                        </div>

                                        <!-- Título Vertical del Lomo (centrado, legible y sin cortes) -->
                                        <div class="relative z-10 flex-1 min-h-0 w-full flex items-center justify-center py-2 px-0.5 overflow-hidden">
                                            <span
                                                class="font-title-editorial text-[9.5px] sm:text-[11px] font-bold tracking-wide text-center leading-[1.15] drop-shadow-[0_1px_2px_rgba(0,0,0,0.85)] select-none"
                                                style="writing-mode: vertical-rl; transform: rotate(180deg); color: #f8f3e6; max-height: 100%; overflow-wrap: break-word;"
                                            >
                                                {{ Str::limit($libro['nombre'], 46) }}
                                            </span>
                                        </div>

                                        <!-- Costilla inferior: Sello editorial, autor/editorial y bandas -->
                                        <div class="relative z-10 w-full space-y-1 pt-1">
                                            <div class="h-px w-4/5 mx-auto opacity-60" style="background-color: {{ $bandSecondary }};"></div>
                                            <div class="flex items-center justify-center">
                                                <span class="material-symbols-outlined text-[11px] sm:text-[12px]" style="color: {{ $bandPrimary }};">
                                                    {{ $libro['genero_icono'] }}
                                                </span>
                                            </div>
                                            <p
                                                class="text-[7px] sm:text-[8px] font-label-code font-semibold text-center uppercase tracking-tight leading-[1.1] line-clamp-2 px-0.5"
                                                style="color: {{ $tejueloText }};"
                                            >
                                                {{ $libro['autor'] ?: $libro['editorial'] }}
                                            </p>
                                            <div class="h-px w-full opacity-75" style="background-color: {{ $bandSecondary }};"></div>
                                            <div class="h-[2px] w-full rounded-full opacity-90" style="background-color: {{ $bandPrimary }};"></div>
                                        </div>

                                        <!-- Relieve cilíndrico realista del lomo -->
                                        <div
                                            class="pointer-events-none absolute inset-0"
                                            style="background: linear-gradient(90deg, rgba(0,0,0,0.42) 0%, rgba(255,255,255,0.14) 16%, rgba(255,255,255,0.03) 48%, rgba(0,0,0,0.2) 86%, rgba(0,0,0,0.46) 100%);"
                                        ></div>
                                    </div>

                                    <!-- ==========================================
                                         2. VISTA ACTIVA: PORTADA FRONTAL 3D (COVER)
                                         ========================================== -->
                                    <div
                                        class="shelf-book-cover absolute inset-0 w-[170px] sm:w-[240px] h-[240px] sm:h-[340px] rounded-r-xl overflow-hidden shadow-2xl transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] border-2 {{ $paleta['border'] }}"
                                        style="background: linear-gradient(180deg, {{ $bgTop }} 0%, {{ $bgMid }} 52%, {{ $bgBottom }} 100%);"
                                    >
                                        @if (! empty($libro['imagen']))
                                            <!-- Imagen Real de la Portada con Fallback Integrado -->
                                            <img
                                                src="{{ $libro['imagen'] }}"
                                                alt="{{ $libro['nombre'] }}"
                                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                                loading="lazy"
                                                onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"
                                            />
                                        @endif

                                        <!-- Carátula Tipográfica Curatorial de Archivo (Usada como fallback o diseño editorial de lujo) -->
                                        <div
                                            class="shelf-book-typographic-cover {{ ! empty($libro['imagen']) ? 'hidden' : '' }} w-full h-full p-4 sm:p-5 flex flex-col justify-between relative"
                                            style="background: linear-gradient(180deg, {{ $bgTop }} 0%, {{ $bgMid }} 52%, {{ $bgBottom }} 100%);"
                                        >
                                            <!-- Marco Ornamental de Filigrana Púrpura y Plata -->
                                            <div class="pointer-events-none absolute inset-2.5 rounded-lg border-2 border-purple-400/40 ring-1 ring-purple-400/20"></div>
                                            <div class="pointer-events-none absolute inset-3.5 rounded border border-purple-400/25"></div>

                                            <!-- Cabecera de la Carátula -->
                                            <div class="relative z-10 text-center space-y-1 pt-2">
                                                <span class="inline-flex items-center gap-1 text-[9px] font-label-code uppercase tracking-widest text-purple-300">
                                                    <span class="material-symbols-outlined text-[11px]">auto_stories</span>
                                                    <span>ARCHIVUM · LIBRO RARO</span>
                                                </span>
                                                <div class="h-px w-16 mx-auto bg-purple-400/50"></div>
                                            </div>

                                            <!-- Cuerpo Central: Título y Autor en Tipografía Editorial -->
                                            <div class="relative z-10 text-center space-y-2 px-1">
                                                <h3 class="font-title-editorial text-base sm:text-lg font-bold text-purple-100 leading-snug drop-shadow-md">
                                                    {{ $libro['nombre'] }}
                                                </h3>
                                                <p class="font-title-editorial italic text-xs sm:text-sm text-purple-200/90 font-medium">
                                                    {{ $libro['autor'] }}
                                                </p>
                                                <span class="inline-block px-2 py-0.5 rounded text-[9px] font-mono bg-black/40 text-purple-300 border border-purple-400/30 uppercase">
                                                    {{ $libro['editorial'] }} · {{ $libro['anio'] }}
                                                </span>
                                            </div>

                                            <!-- Pie de la Carátula: Graduación y Sello Notarial -->
                                            <div class="relative z-10 text-center space-y-1 pb-1">
                                                <div class="h-px w-16 mx-auto bg-purple-400/50"></div>
                                                <div class="flex items-center justify-between text-[8px] font-mono text-purple-200/80 px-2">
                                                    <span>{{ $libro['graduacion'] }}</span>
                                                    <span>COA CERTIFICADO</span>
                                                </div>
                                            </div>

                                            <!-- Textura de Piel / Papel Envejecido en Luz Rasante -->
                                            <div class="pointer-events-none absolute inset-0 bg-gradient-to-tr from-black/50 via-transparent to-white/10 mix-blend-overlay"></div>
                                        </div>

                                        <!-- Brillo Curatorial en Borde Exterior -->
                                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-black/60 via-transparent to-purple-400/10"></div>

                                        <!-- Badge Flotante de Precio en Portada -->
                                        <div class="absolute bottom-2.5 right-2.5 z-20">
                                            <span class="px-2.5 py-1 rounded-lg bg-black/85 backdrop-blur-md text-purple-200 font-bold text-xs font-mono border border-purple-400/40 shadow-lg">
                                                {{ $libro['precio_formateado'] }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- ==========================================
                                         3. DETALLES DE VOLUMEN 3D: CANTOS DE HOJAS
                                         ========================================== -->
                                    <div class="shelf-book-top-pages absolute -top-3 left-0 right-0 h-3 bg-gradient-to-r from-stone-200 via-stone-100 to-stone-200 border-t border-purple-300/40"></div>
                                    <div class="shelf-book-side-pages absolute top-0 bottom-0 -right-3 w-3 bg-gradient-to-b from-stone-200 via-stone-100 to-stone-200 border-r border-purple-300/40"></div>
                                </div>

                                <!-- Etiqueta Curatorial Flotante al Pasar el Cursor -->
                                <div class="shelf-book-tooltip pointer-events-none absolute -top-12 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-30 whitespace-nowrap bg-black/90 text-purple-200 text-[11px] px-3 py-1 rounded-full border border-purple-400/30 shadow-xl font-mono">
                                    {{ Str::limit($libro['nombre'], 24) }} · {{ $libro['precio_formateado'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Repisa de Madera con Placa de Archivo Grabada -->
                <div class="relative w-full max-w-4xl mx-auto mt-2">
                    <div class="h-4 sm:h-5 rounded-b-xl bg-gradient-to-r from-purple-200 via-purple-100 to-purple-200 dark:from-[#170e24] dark:via-[#241638] dark:to-[#120a1c] border-t-2 border-purple-400 dark:border-purple-500/50 shadow-md dark:shadow-2xl flex items-center justify-center px-4">
                        <div class="px-4 py-0.5 rounded bg-white dark:bg-gradient-to-r dark:from-purple-900 dark:via-indigo-900 dark:to-purple-900 border border-purple-300 dark:border-purple-400/50 shadow-2xs dark:shadow-inner flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-600 dark:bg-purple-300"></span>
                            <span class="text-[9px] sm:text-[10px] font-label-code text-purple-950 dark:text-purple-100 uppercase tracking-widest font-bold">
                                ARCHIVUM · ESTANTERÍA DE PRIMERAS EDICIONES &amp; EDICIONES ESPECIALES
                            </span>
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-600 dark:bg-purple-300"></span>
                        </div>
                    </div>
                    <!-- Sombra proyectada por la repisa -->
                    <div class="h-3 w-full bg-gradient-to-b from-purple-300/40 dark:from-black/80 to-transparent blur-xs"></div>
                </div>
            </div>

            <!-- =========================================================================
                 DOSSIER CURATORIAL DE INSPECCIÓN RÁPIDA (DINÁMICO AL SELECCIONAR UN LIBRO)
                 ========================================================================= -->
            <div
                id="bookshelf-dossier"
                class="relative z-10 mt-6 rounded-2xl bg-white dark:bg-[#1b1226]/95 backdrop-blur-md text-slate-800 dark:text-stone-100 border border-purple-200 dark:border-purple-500/30 p-5 sm:p-7 shadow-lg dark:shadow-2xl transition-all duration-300"
            >
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- Columna Izquierda (7 cols): Ficha Curatorial, Sinopsis y Metadatos -->
                    <div class="lg:col-span-7 space-y-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span id="dossier-genre-badge" class="px-2.5 py-0.5 rounded-full bg-purple-100 dark:bg-purple-500/20 text-purple-800 dark:text-purple-300 text-[11px] font-label-code font-bold uppercase tracking-wider border border-purple-300 dark:border-purple-400/40 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]" id="dossier-genre-icon">{{ $libroInicial['genero_icono'] }}</span>
                                <span id="dossier-genre-name">{{ $libroInicial['genero_nombre'] }}</span>
                            </span>
                            <span id="dossier-grading-badge" class="px-2.5 py-0.5 rounded-full bg-purple-100 dark:bg-purple-500/20 text-purple-800 dark:text-purple-300 text-[11px] font-label-code font-bold uppercase tracking-wider border border-purple-300 dark:border-purple-400/30">
                                {{ $libroInicial['graduacion'] }}
                            </span>
                            <span id="dossier-unique-badge" class="px-2.5 py-0.5 rounded-full {{ $libroInicial['es_pieza_unica'] ? 'bg-rose-100 dark:bg-rose-500/20 text-rose-800 dark:text-rose-300 border border-rose-300 dark:border-rose-400/40' : 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-400/40' }} text-[11px] font-label-code font-bold uppercase tracking-wider">
                                {{ $libroInicial['es_pieza_unica'] ? 'Pieza Única 1/1' : 'Tiraje Coleccionable' }}
                            </span>
                        </div>

                        <div>
                            <h2 id="dossier-title" class="text-xl sm:text-2xl font-bold font-quicksand text-slate-900 dark:text-white leading-snug">
                                {{ $libroInicial['nombre'] }}
                            </h2>
                            <p id="dossier-author-row" class="font-title-editorial text-sm sm:text-base text-purple-800 dark:text-purple-300 italic mt-0.5 font-medium">
                                {{ $libroInicial['autor'] }} · {{ $libroInicial['editorial'] }} ({{ $libroInicial['anio'] }})
                            </p>
                        </div>

                        <p id="dossier-description" class="text-xs sm:text-sm text-slate-700 dark:text-purple-100 leading-relaxed font-normal">
                            {{ $libroInicial['descripcion'] }}
                        </p>

                        <!-- Datos Técnicos en Píldoras -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 pt-2 text-xs font-mono">
                            <div class="p-2 rounded-xl bg-purple-50 dark:bg-white/5 border border-purple-200 dark:border-purple-500/20">
                                <span class="text-[10px] text-purple-700 dark:text-purple-300 font-semibold block uppercase">ISBN Archivo</span>
                                <span id="dossier-isbn" class="font-bold text-slate-700 dark:text-purple-100 text-[11px]">{{ $libroInicial['isbn'] }}</span>
                            </div>
                            <div class="p-2 rounded-xl bg-purple-50 dark:bg-white/5 border border-purple-200 dark:border-purple-500/20">
                                <span class="text-[10px] text-purple-700 dark:text-purple-300 font-semibold block uppercase">Páginas</span>
                                <span id="dossier-pages" class="font-bold text-slate-700 dark:text-purple-100 text-[11px]">{{ $libroInicial['paginas'] }} págs.</span>
                            </div>
                            <div class="p-2 rounded-xl bg-purple-50 dark:bg-white/5 border border-purple-200 dark:border-purple-500/20 col-span-2 sm:col-span-1">
                                <span class="text-[10px] text-purple-700 dark:text-purple-300 font-semibold block uppercase">Custodia</span>
                                <span id="dossier-sede" class="font-bold text-slate-700 dark:text-purple-100 text-[11px] truncate block">{{ $libroInicial['sede'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Derecha (5 cols): Valoración, Certificado COA y Acciones de Compra -->
                    <div class="lg:col-span-5 flex flex-col justify-between p-4 sm:p-5 rounded-2xl bg-purple-50/70 dark:bg-[#140d20]/90 border border-purple-200/80 dark:border-purple-500/30 space-y-4">
                        <div class="space-y-1">
                            <span class="text-[10px] font-label-code text-purple-700 dark:text-purple-300 uppercase tracking-wider block font-bold">
                                Precio
                            </span>
                            <div class="flex items-baseline gap-2">
                                <span id="dossier-price" class="text-2xl sm:text-3xl font-bold font-mono text-purple-600 dark:text-purple-300">
                                    {{ $libroInicial['precio_formateado'] }}
                                </span>
                                <span class="text-xs text-purple-800 dark:text-purple-200 font-mono font-semibold">MXN</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-xs text-purple-700 dark:text-purple-300 pt-1 font-mono font-medium">
                                <span class="material-symbols-outlined text-[15px] text-emerald-600 dark:text-emerald-400">verified</span>
                                <span id="dossier-certificate">{{ $libroInicial['certificado'] }}</span>
                            </div>
                        </div>

                        <!-- Botones Funcionales: Reservar en Carrito + Ver Detalles Completos -->
                        <div class="space-y-2 pt-2">
                            <form id="dossier-add-form" action="{{ route('carrito.agregar') }}" method="POST" class="w-full">
                                @csrf
                                <input type="hidden" name="variante_sku_id" id="dossier-variante-id" value="{{ $libroInicial['variante_sku_id'] }}">
                                <input type="hidden" name="cantidad" value="1">
                                <button
                                    type="submit"
                                    id="dossier-submit-btn"
                                    class="w-full py-3 px-4 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs font-label-code uppercase tracking-wider shadow-md transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                                    {{ $libroInicial['agotado'] ? 'disabled' : '' }}
                                >
                                    <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                                    <span id="dossier-submit-text">{{ $libroInicial['agotado'] ? 'AGOTADO' : 'AGREGAR AL CARRITO' }}</span>
                                </button>
                            </form>

                            <button
                                type="button"
                                id="dossier-details-btn"
                                class="w-full py-2.5 px-4 rounded-xl text-purple-800 bg-purple-100 hover:bg-purple-200 dark:text-purple-200 dark:bg-purple-900/40 dark:hover:bg-purple-900/60 border border-purple-200 dark:border-purple-500/30 font-bold text-xs font-label-code uppercase tracking-wider transition flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <span class="material-symbols-outlined text-[17px] text-purple-700 dark:text-purple-400">visibility</span>
                                <span>Ver Ficha Técnica Completa</span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    @endif

    <!-- =========================================================================
         BARRA DE BÚSQUEDA, FILTRO DE 12 GÉNEROS Y ORDENAMIENTO
         ========================================================================= -->
    <div id="acervo-libros-grid" class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-purple-200/80 dark:border-purple-500/30 gap-4 mb-6">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-on-surface font-quicksand">
                {{ $meta['subtitulo'] ?? 'Fondo Bibliográfico Clasificado por Géneros' }}
            </h2>
            <p class="text-xs text-primary font-label-code mt-1 font-semibold uppercase tracking-wider">
                Mostrando {{ $coleccionLibros->count() }} {{ $coleccionLibros->count() === 1 ? 'obra catalogada' : 'obras catalogadas' }} en esta sección
            </p>
        </div>

        <form method="GET" action="{{ route('public.libros') }}" class="flex items-center flex-wrap gap-3 text-xs font-label-md">
            @if (! empty($filtroActivo) && $filtroActivo !== 'todos')
                <input type="hidden" name="genero" value="{{ $filtroActivo }}" />
            @endif
            <div class="relative">
                <input
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Filtrar por título, autor o ISBN..."
                    class="bg-white dark:bg-[#1a1228] border border-purple-200/80 dark:border-purple-500/30 rounded-xl px-3.5 py-2 text-xs text-on-surface focus:outline-none focus:border-primary min-w-[220px]"
                />
            </div>
            <select name="orden" onchange="this.form.submit()" class="bg-white dark:bg-[#1a1228] text-on-surface border border-purple-200/80 dark:border-purple-500/30 rounded-xl py-2 px-3 focus:ring-1 focus:ring-primary focus:outline-none shadow-2xs font-medium cursor-pointer">
                <option value="relevancia" @selected(request('orden') === 'relevancia')>Ordenar: Relevancia Curatorial</option>
                <option value="precio_desc" @selected(request('orden') === 'precio_desc')>Precio: Mayor a Menor</option>
                <option value="precio_asc" @selected(request('orden') === 'precio_asc')>Precio: Menor a Mayor</option>
            </select>
        </form>
    </div>

    <!-- =========================================================================
         SELECTOR INTERACTIVO DE GÉNEROS LITERARIOS (DROPDOWN GLASSMORPHISM LIMPIO)
         relative z-40 overflow-visible para flotar siempre sobre la cuadrícula
         ========================================================================= -->
    <div class="relative z-40 mb-8 p-4 sm:p-5 rounded-2xl bg-white/90 dark:bg-[#1a1228]/90 backdrop-blur-md border border-purple-200/80 dark:border-purple-500/30 shadow-sm overflow-visible">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <!-- Izquierda: Título Curatorial + Indicador del Género Activo -->
            <div class="flex items-center flex-wrap gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-primary/10 dark:bg-purple-500/20 text-primary dark:text-purple-300 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[18px]">auto_stories</span>
                </div>
                <div>
                    <span class="text-xs font-label-code font-bold uppercase tracking-wider text-on-surface block">
                        Clasificación Curatorial por Géneros Literarios:
                    </span>
                    <span class="text-[11px] font-label-code text-primary dark:text-purple-300 font-semibold flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span>Género Activo:</span>
                        <span class="font-bold uppercase tracking-wide">{{ $filtros[$filtroActivo] ?? 'Todos los Géneros' }}</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono bg-purple-500/20 text-purple-800 dark:text-purple-300">
                            {{ $conteoGeneros[$filtroActivo] ?? $coleccionLibros->count() }} ejemplares
                        </span>
                        @if ($filtroActivo !== 'todos')
                            <a
                                href="{{ request()->fullUrlWithQuery(['genero' => null]) }}"
                                class="ml-2 text-rose-600 hover:text-rose-700 dark:text-rose-400 hover:underline flex items-center gap-0.5 text-[10px] font-bold uppercase"
                                title="Restablecer a todos los géneros"
                            >
                                <span class="material-symbols-outlined text-[13px]">close</span>
                                <span>Mostrar Todos</span>
                            </a>
                        @endif
                    </span>
                </div>
            </div>

            <!-- Derecha: Selector Desplegable con Buscador Interno (z-50) -->
            <div class="relative z-50 inline-block text-left" id="genre-dropdown-container">
                <button
                    type="button"
                    id="genre-dropdown-btn"
                    class="w-full sm:w-auto inline-flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl bg-white/80 dark:bg-[#221734] border border-purple-300/80 hover:border-primary dark:border-purple-500/40 dark:hover:border-purple-400 text-xs font-label-md font-semibold text-on-surface shadow-xs transition cursor-pointer"
                    aria-expanded="false"
                    aria-haspopup="true"
                >
                    <span class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[17px] text-primary dark:text-purple-400">
                            {{ $iconosGeneros[$filtroActivo] ?? 'auto_stories' }}
                        </span>
                        <span>{{ $filtros[$filtroActivo] ?? 'Todos los Géneros' }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono bg-purple-500/20 text-purple-800 dark:text-purple-300 font-bold">
                            {{ $conteoGeneros[$filtroActivo] ?? $coleccionLibros->count() }}
                        </span>
                    </span>
                    <span class="material-symbols-outlined text-[18px] text-stone-400 transition-transform duration-200" id="genre-dropdown-chevron">
                        expand_more
                    </span>
                </button>

                <!-- Panel Flotante Glassmorphism del Menú Desplegable con Buscador Interno (z-50) -->
                <div
                    id="genre-dropdown-menu"
                    class="hidden absolute right-0 mt-2 w-72 sm:w-80 rounded-2xl bg-white dark:bg-[#1a1228] backdrop-blur-xl border border-purple-400/40 dark:border-purple-500/40 shadow-2xl z-50 p-3 space-y-2 transform origin-top-right transition-all"
                    role="menu"
                >
                    <!-- Buscador Interno de Géneros -->
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-2.5 top-2.5 text-stone-400 text-[16px]">search</span>
                        <input
                            type="text"
                            id="genre-search-input"
                            placeholder="Buscar género literario..."
                            class="w-full pl-8 pr-3 py-1.5 rounded-lg bg-stone-100 dark:bg-[#120b1c] border border-stone-200 dark:border-purple-500/30 text-xs text-on-surface focus:outline-none focus:border-primary"
                        />
                    </div>

                    <!-- Lista Completa con Scroll Vertical Interno (max-h-80) de los 12 Géneros + Todos con Contadores -->
                    <div class="max-h-80 overflow-y-auto space-y-1 no-scrollbar pt-1" id="genre-items-list">
                        @foreach ($filtros as $slug => $nombre)
                            @php
                                $estaActivo = $filtroActivo === $slug;
                                $conteo = $conteoGeneros[$slug] ?? 0;
                                $icono = $iconosGeneros[$slug] ?? 'auto_stories';
                                $url = $slug === 'todos'
                                    ? request()->fullUrlWithQuery(['genero' => null])
                                    : request()->fullUrlWithQuery(['genero' => $slug]);
                            @endphp
                            <a
                                href="{{ $url }}"
                                class="genre-option-item flex items-center justify-between px-3 py-2 rounded-xl text-xs transition {{ $estaActivo ? 'bg-primary-container text-white font-bold shadow-xs' : 'text-stone-700 dark:text-stone-300 hover:bg-purple-50 dark:hover:bg-purple-950/40' }}"
                                data-genre-name="{{ strtolower($nombre) }}"
                            >
                                <span class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[16px] {{ $estaActivo ? 'text-white' : 'text-primary dark:text-purple-400' }}">
                                        {{ $icono }}
                                    </span>
                                    <span>{{ $nombre }}</span>
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono {{ $estaActivo ? 'bg-white/20 text-white' : 'bg-stone-200/70 dark:bg-stone-800 text-stone-600 dark:text-stone-400' }}">
                                    {{ $conteo }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         CUADRÍCULA DE PRODUCTOS (CATÁLOGO DE LIBROS)
         relative z-10 para asegurar que el dropdown (z-50) flote siempre encima
         ========================================================================= -->
    @if ($coleccionLibros->isNotEmpty())
        <div class="relative z-10 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-5 mb-12">
            @foreach ($coleccionLibros as $producto)
                <x-public.product-card :producto="$producto" />
            @endforeach
        </div>
    @else
        <div class="bg-white dark:bg-[#1a1228] rounded-2xl border border-purple-200/80 dark:border-purple-500/30 p-12 text-center space-y-3 mb-12 shadow-sm">
            <span class="material-symbols-outlined text-[48px] text-primary dark:text-purple-400">auto_stories</span>
            <h3 class="text-lg font-bold text-on-surface font-quicksand">No se encontraron libros con los filtros seleccionados</h3>
            <p class="text-xs text-on-surface-variant max-w-md mx-auto">
                No hay obras registradas en este género o búsqueda. Intenta limpiar los filtros o explora la colección completa.
            </p>
            <div class="pt-2">
                <a href="{{ route('public.libros') }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-bold uppercase tracking-wider transition">
                    <span class="material-symbols-outlined text-[16px]">refresh</span>
                    <span>Ver Todo el Fondo Bibliográfico</span>
                </a>
            </div>
        </div>
    @endif

    <!-- Estilos Específicos para la Geometría y Animaciones 3D del Estante de Libros -->
    @push('styles')
    <style>
        /* Contenedor del Estante */
        .bookshelf-stage {
            transform-style: preserve-3d;
        }

        /* Ranura de Libro 3D */
        .shelf-book-slot {
            width: var(--spine-w-xs, 42px);
            height: calc(240px * var(--book-h-ratio, 1));
            transform-style: preserve-3d;
            perspective: 1000px;
            flex-shrink: 0;
        }

        @media (min-width: 400px) {
            .shelf-book-slot {
                width: var(--spine-w-sm, 46px);
                height: calc(260px * var(--book-h-ratio, 1));
            }
        }

        @media (min-width: 640px) {
            .shelf-book-slot {
                width: var(--spine-w-lg, 52px);
                height: calc(350px * var(--book-h-ratio, 1));
            }
        }

        /* Cuando la ranura está activa o expandida */
        .shelf-book-slot.is-expanded {
            width: 170px;
            height: 240px;
            z-index: 25;
        }

        @media (min-width: 400px) {
            .shelf-book-slot.is-expanded {
                width: 185px;
                height: 260px;
            }
        }

        @media (min-width: 640px) {
            .shelf-book-slot.is-expanded {
                width: 255px;
                height: 350px;
            }
        }

        /* Transformación 3D del cuerpo del libro (vista frontal de lomo en reposo) */
        .shelf-book-3d {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transform-origin: left center;
            transform: rotateY(0deg) translateZ(0);
        }

        /* Estado Activo: Se orienta frontalmente y se eleva hacia adelante */
        .shelf-book-slot.is-expanded .shelf-book-3d {
            transform: rotateY(0deg) translateZ(50px) scale(1.03);
        }

        /* Cara de Lomo (Spine) */
        .shelf-book-spine {
            backface-visibility: hidden;
            transform-origin: left center;
            transform: rotateY(0deg) translateZ(0);
            transition: opacity 0.45s cubic-bezier(0.22, 1, 0.36, 1), transform 0.5s cubic-bezier(0.22, 1, 0.36, 1);
        }

        /* Cuando está expandido, el lomo gira suavemente hacia la izquierda */
        .shelf-book-slot.is-expanded .shelf-book-spine {
            opacity: 0;
            transform: rotateY(-88deg) translateZ(0);
            pointer-events: none;
        }

        /* Cara de Portada (Cover) */
        .shelf-book-cover {
            backface-visibility: hidden;
            transform-origin: left center;
            opacity: 0;
            transform: rotateY(88deg) translateZ(0);
            pointer-events: none;
            transition: opacity 0.45s cubic-bezier(0.22, 1, 0.36, 1), transform 0.5s cubic-bezier(0.22, 1, 0.36, 1);
        }

        /* Cuando está expandido, la portada frontal gira hacia el frente en toda su plenitud */
        .shelf-book-slot.is-expanded .shelf-book-cover {
            opacity: 1;
            transform: rotateY(0deg) translateZ(0);
            pointer-events: auto;
        }

        /* Cantos de Páginas (ocultos en reposo, visibles solo al abrir el libro) */
        .shelf-book-top-pages {
            transform-origin: top;
            transform: rotateX(90deg) translateZ(0);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s ease;
        }

        .shelf-book-side-pages {
            transform-origin: right;
            transform: rotateY(90deg) translateZ(0);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s ease;
        }

        .shelf-book-slot.is-expanded .shelf-book-top-pages,
        .shelf-book-slot.is-expanded .shelf-book-side-pages {
            opacity: 0.85;
        }

        /* Ocultar barra de scroll en navegadores WebKit y Firefox */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    @endpush

    <!-- Scripts de Interacción: Estante 3D, Navegación por Teclado y Dropdown con Buscador -->
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const librosData = @json($librosEstante);
            let activeIndex = 0;
            const totalLibros = librosData.length;

            const shelfSlots = document.querySelectorAll('.shelf-book-slot');
            const ambientGlow = document.getElementById('bookshelf-ambient-glow');
            const genrePill = document.getElementById('bookshelf-genre-pill');
            const genreIcon = document.getElementById('bookshelf-genre-icon');
            const genreName = document.getElementById('bookshelf-genre-name');
            const counterLabel = document.getElementById('bookshelf-counter-label');

            // Elementos del Dossier
            const dossierTitle = document.getElementById('dossier-title');
            const dossierAuthorRow = document.getElementById('dossier-author-row');
            const dossierDescription = document.getElementById('dossier-description');
            const dossierGenreBadge = document.getElementById('dossier-genre-badge');
            const dossierGenreIcon = document.getElementById('dossier-genre-icon');
            const dossierGenreName = document.getElementById('dossier-genre-name');
            const dossierGradingBadge = document.getElementById('dossier-grading-badge');
            const dossierUniqueBadge = document.getElementById('dossier-unique-badge');
            const dossierIsbn = document.getElementById('dossier-isbn');
            const dossierPages = document.getElementById('dossier-pages');
            const dossierSede = document.getElementById('dossier-sede');
            const dossierPrice = document.getElementById('dossier-price');
            const dossierCertificate = document.getElementById('dossier-certificate');
            const dossierVarianteId = document.getElementById('dossier-variante-id');
            const dossierSubmitBtn = document.getElementById('dossier-submit-btn');
            const dossierSubmitText = document.getElementById('dossier-submit-text');
            const dossierDetailsBtn = document.getElementById('dossier-details-btn');

            // Función para activar un libro por su índice
            function selectBook(index) {
                if (index < 0 || index >= totalLibros) return;
                activeIndex = index;
                const libro = librosData[activeIndex];
                if (!libro) return;

                // Actualizar ranuras de la estantería
                shelfSlots.forEach((slot, i) => {
                    if (i === activeIndex) {
                        slot.classList.add('is-expanded', 'active');
                    } else {
                        slot.classList.remove('is-expanded', 'active');
                    }
                });

                // Ajustar visibilidad lateral en móvil para centrar 100% la portada abierta
                updateMobileShelfVisibility();

                // Actualizar contador
                if (counterLabel) {
                    const currentFormatted = String(activeIndex + 1).padStart(2, '0');
                    const totalFormatted = String(totalLibros).padStart(2, '0');
                    counterLabel.textContent = `${currentFormatted} / ${totalFormatted}`;
                }

                // Actualizar Halo Ambiental
                if (ambientGlow && libro.paleta && libro.paleta.accent) {
                    ambientGlow.style.background = `radial-gradient(circle, ${libro.paleta.accent} 0%, transparent 70%)`;
                }

                // Actualizar Pastilla de Género en la cabecera del estante
                if (genreIcon) genreIcon.textContent = libro.genero_icono || 'auto_stories';
                if (genreName) genreName.textContent = libro.genero_nombre || 'Novela';

                // Actualizar Ficha Curatorial (Dossier)
                if (dossierTitle) dossierTitle.textContent = libro.nombre;
                if (dossierAuthorRow) {
                    dossierAuthorRow.textContent = `${libro.autor} · ${libro.editorial} (${libro.anio})`;
                    dossierAuthorRow.className = 'font-title-editorial text-sm sm:text-base text-purple-800 dark:text-purple-300 italic mt-0.5 font-medium';
                }
                if (dossierDescription) dossierDescription.textContent = libro.descripcion;
                if (dossierGenreIcon) dossierGenreIcon.textContent = libro.genero_icono || 'auto_stories';
                if (dossierGenreName) dossierGenreName.textContent = libro.genero_nombre || 'Novela';
                if (dossierGradingBadge) dossierGradingBadge.textContent = libro.graduacion || 'MINT (M)';
                
                if (dossierUniqueBadge) {
                    dossierUniqueBadge.textContent = libro.es_pieza_unica ? 'Pieza Única 1/1' : 'Tiraje Coleccionable';
                    if (libro.es_pieza_unica) {
                        dossierUniqueBadge.className = 'px-2.5 py-0.5 rounded-full bg-rose-100 dark:bg-rose-500/20 text-rose-800 dark:text-rose-300 border border-rose-300 dark:border-rose-400/40 text-[11px] font-label-code font-bold uppercase tracking-wider';
                    } else {
                        dossierUniqueBadge.className = 'px-2.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-400/40 text-[11px] font-label-code font-bold uppercase tracking-wider';
                    }
                }

                if (dossierIsbn) dossierIsbn.textContent = libro.isbn || '978-ARCHIVUM-00';
                if (dossierPages) dossierPages.textContent = `${libro.paginas || 300} págs.`;
                if (dossierSede) {
                    dossierSede.textContent = libro.sede || 'Bóveda Central';
                    dossierSede.className = 'font-bold text-slate-700 dark:text-purple-100 text-[11px] truncate block';
                }
                if (dossierPrice) {
                    dossierPrice.textContent = libro.precio_formateado || '$0';
                    dossierPrice.className = 'text-2xl sm:text-3xl font-bold font-mono text-purple-600 dark:text-purple-300';
                }
                if (dossierCertificate) dossierCertificate.textContent = libro.certificado || 'COA-BIB-2026';

                // Actualizar formulario de reserva
                if (dossierVarianteId) dossierVarianteId.value = libro.variante_sku_id || '';
                if (dossierSubmitBtn) {
                    dossierSubmitBtn.disabled = !!libro.agotado;
                }
                if (dossierSubmitText) {
                    dossierSubmitText.textContent = libro.agotado ? 'EJEMPLAR RESERVADO' : 'RESERVAR EN BÓVEDA';
                }

                // Conectar botón de detalles completos con el modal curatorial global
                if (dossierDetailsBtn) {
                    dossierDetailsBtn.onclick = () => {
                        if (typeof window.abrirModalDetalleProducto === 'function' && libro.datos_modal) {
                            window.abrirModalDetalleProducto(libro.datos_modal);
                        }
                    };
                }
            }

            // Visibilidad y centrado dinámico en pantalla móvil (< 640px)
            function updateMobileShelfVisibility() {
                const isMobile = window.innerWidth < 640;
                shelfSlots.forEach((slot, i) => {
                    const diff = Math.abs(i - activeIndex);
                    if (isMobile) {
                        // En móvil, mostrar únicamente el libro activo y hasta 1 lomo adyacente a cada lado para centrarlo 100%
                        if (diff > 1) {
                            slot.style.display = 'none';
                        } else {
                            slot.style.display = '';
                        }
                    } else {
                        slot.style.display = '';
                    }
                });
            }

            window.addEventListener('resize', updateMobileShelfVisibility);

            // Listeners en cada ranura de libro
            shelfSlots.forEach((slot) => {
                const idx = parseInt(slot.getAttribute('data-book-index'), 10);
                
                // Al hacer clic
                slot.addEventListener('click', () => {
                    selectBook(idx);
                });

                // Al pasar el cursor (hover suave)
                slot.addEventListener('mouseenter', () => {
                    selectBook(idx);
                });

                // Soporte de accesibilidad por teclado en la ranura
                slot.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        selectBook(idx);
                    }
                });
            });

            // Botones de navegación Anterior y Siguiente
            const prevBtn = document.getElementById('bookshelf-prev-btn');
            const nextBtn = document.getElementById('bookshelf-next-btn');

            if (prevBtn) {
                prevBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const newIndex = (activeIndex - 1 + totalLibros) % totalLibros;
                    selectBook(newIndex);
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const newIndex = (activeIndex + 1) % totalLibros;
                    selectBook(newIndex);
                });
            }

            // Navegación por flechas del teclado en toda la ventana
            window.addEventListener('keydown', (e) => {
                // Solo si el usuario no está escribiendo en un input o textarea
                if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
                    return;
                }

                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    const newIndex = (activeIndex - 1 + totalLibros) % totalLibros;
                    selectBook(newIndex);
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    const newIndex = (activeIndex + 1) % totalLibros;
                    selectBook(newIndex);
                }
            });

            // =========================================================================
            // LÓGICA DEL DROPDOWN GLASSMORPHISM DE GÉNEROS LITERARIOS
            // =========================================================================
            const dropdownBtn = document.getElementById('genre-dropdown-btn');
            const dropdownMenu = document.getElementById('genre-dropdown-menu');
            const dropdownChevron = document.getElementById('genre-dropdown-chevron');
            const searchInput = document.getElementById('genre-search-input');
            const genreItems = document.querySelectorAll('.genre-option-item');

            function toggleDropdown(show) {
                if (!dropdownMenu) return;
                const isCurrentlyOpen = !dropdownMenu.classList.contains('hidden');
                const shouldOpen = typeof show === 'boolean' ? show : !isCurrentlyOpen;

                if (shouldOpen) {
                    dropdownMenu.classList.remove('hidden');
                    if (dropdownChevron) dropdownChevron.style.transform = 'rotate(180deg)';
                    if (dropdownBtn) dropdownBtn.setAttribute('aria-expanded', 'true');
                    if (searchInput) {
                        setTimeout(() => searchInput.focus(), 50);
                    }
                } else {
                    dropdownMenu.classList.add('hidden');
                    if (dropdownChevron) dropdownChevron.style.transform = 'rotate(0deg)';
                    if (dropdownBtn) dropdownBtn.setAttribute('aria-expanded', 'false');
                }
            }

            if (dropdownBtn) {
                dropdownBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleDropdown();
                });
            }

            // Cerrar el dropdown al hacer clic fuera
            document.addEventListener('click', (e) => {
                const container = document.getElementById('genre-dropdown-container');
                if (container && !container.contains(e.target)) {
                    toggleDropdown(false);
                }
            });

            // Cerrar con Escape
            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    toggleDropdown(false);
                }
            });

            // Filtrado en vivo de la lista de géneros en el dropdown
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    const query = e.target.value.toLowerCase().trim();
                    genreItems.forEach((item) => {
                        const name = item.getAttribute('data-genre-name') || '';
                        if (name.includes(query) || query === '') {
                            item.style.display = 'flex';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }

            // Inicializar selección del primer libro
            if (totalLibros > 0) {
                selectBook(0);
            }
        });
    </script>
    @endpush

</x-layouts.public>
