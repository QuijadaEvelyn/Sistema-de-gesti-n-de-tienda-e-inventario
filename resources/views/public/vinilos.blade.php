<x-layouts.public :title="'ARCHIVUM · ' . ($meta['titulo'] ?? 'Discos & Vinilos Históricos')">

    @php
        $coleccionDiscos = $productos ?? collect();

        $paletasGalleta = [
            ['bg' => 'from-red-700 via-rose-800 to-red-950', 'ring' => 'border-amber-300/60', 'accent' => '#e11d48', 'sello_corto' => 'COLUMBIA 6-EYE'],
            ['bg' => 'from-blue-600 via-blue-800 to-slate-950', 'ring' => 'border-sky-300/60', 'accent' => '#2563eb', 'sello_corto' => 'BLUE NOTE RVG'],
            ['bg' => 'from-amber-500 via-orange-700 to-stone-950', 'ring' => 'border-amber-200/70', 'accent' => '#d97706', 'sello_corto' => 'IMPULSE! MONO'],
            ['bg' => 'from-emerald-600 via-green-800 to-emerald-950', 'ring' => 'border-lime-300/60', 'accent' => '#16a34a', 'sello_corto' => 'APPLE RECORDS'],
            ['bg' => 'from-purple-600 via-indigo-800 to-slate-950', 'ring' => 'border-purple-300/70', 'accent' => '#7c3aed', 'sello_corto' => 'HARVEST JAPAN CD'],
        ];

        $discosShowcase = [];

        foreach ($coleccionDiscos as $idx => $prod) {
            /** @var \App\Models\Producto $prod */
            $variante = $prod->variantesSku->first();
            $formatoRaw = strtoupper((string) ($prod->disco?->formato ?? 'vinilo'));
            $esCd = $formatoRaw === 'CD' || str_contains(strtoupper($prod->nombre), 'CD');
            $sku = $variante?->sku ?? 'VIN-ARC-'.$prod->id;
            $graduacionRaw = $variante?->descripcion ?? 'MINT (M) · VINILO LP 180G';
            $partesGrad = array_map('trim', explode('·', $graduacionRaw));
            $graduacionCorta = $partesGrad[0] ?? 'MINT (M)';
            $stockTotal = $variante ? (int) $variante->existencias->sum('cantidad') : 0;
            $paleta = $paletasGalleta[$idx % count($paletasGalleta)];

            $discosShowcase[] = [
                'index' => $idx,
                'producto_id' => $prod->id,
                'variante_sku_id' => $variante?->id,
                'sku' => $sku,
                'nombre' => $prod->nombre,
                'artista' => $prod->disco?->artista ?? 'Intérprete Histórico',
                'sello' => $prod->disco?->sello ?? 'Prensado de Época',
                'genero' => $prod->disco?->genero ?? 'Jazz & Clásicos',
                'anio' => $prod->disco?->anio_lanzamiento ?? 1959,
                'formato' => $esCd ? 'CD' : 'vinilo',
                'formato_etiqueta' => $esCd ? 'CD COMPACT DISC · SUPERFICIE REFRACTIVA' : 'VINILO LP 180G · SURCO ANALÓGICO',
                'velocidad' => $esCd ? 'PCM 44.1 kHz · RED BOOK MASTER' : '33⅓ RPM · MICROGROOVE MONO/STEREO',
                'graduacion' => $graduacionCorta,
                'descripcion' => $prod->descripcion,
                'precio' => (float) $prod->precio,
                'precio_formateado' => '$'.number_format((float) $prod->precio, 0),
                'stock' => $stockTotal,
                'agotado' => $stockTotal <= 0,
                'matriz_lado_b' => 'MATRIZ #' . strtoupper(substr(md5($sku), 0, 6)) . '-1A · SELLO PERICIAL ARCHIVUM',
                'paleta' => $paleta,
            ];
        }
    @endphp

    <!-- Encabezado Curatorial de la Sala Fonográfica -->
    <section class="relative rounded-2xl overflow-hidden bg-gradient-to-r from-purple-950 via-indigo-950 to-slate-950 text-white p-6 sm:p-8 mb-8 shadow-lg border border-purple-700/40">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="max-w-2xl space-y-2">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/20 border border-purple-300/30 text-purple-200 text-xs font-label-md tracking-wider">
                    <span class="material-symbols-outlined text-[15px] text-purple-300">album</span>
                    <span>{{ $meta['badge'] ?? 'CATÁLOGO FONOGRÁFICO · VINILOS & CDS HISTÓRICOS' }}</span>
                </span>
                <h1 class="text-2xl sm:text-4xl font-bold font-quicksand leading-tight text-white">
                    {{ $meta['titulo'] ?? 'Discos & Vinilos Históricos' }}
                </h1>
                <p class="font-title-editorial text-purple-100/90 text-sm sm:text-base leading-relaxed">
                    «Explora nuestra cámara fonográfica interactiva en 3D: inspecciona los surcos concéntricos de acetato en 180g, el acabado refractivo de los primeros CDs de colección y voltea cada disco para verificar su matriz.»
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 text-xs font-label-code">
                <span class="px-3 py-1.5 rounded-xl bg-white/10 border border-white/15 text-purple-100 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Tocadiscos 3D Activo</span>
                </span>
                <a href="#acervo-vinilos-grid" class="px-3.5 py-1.5 rounded-xl bg-primary-container hover:bg-primary text-white font-bold uppercase tracking-wider transition">
                    Ver Cuadrícula Completa ({{ count($discosShowcase) }})
                </a>
            </div>
        </div>
    </section>

    @if (! empty($discosShowcase))
        @php
            $discoInicial = $discosShowcase[0];
        @endphp

        <!-- =========================================================================
             GALERÍA 3D INTERACTIVA DE DISCOS & VINILOS (ESTILO DISC SHOWCASE)
             ========================================================================= -->
        <section
            id="disc-showcase-section"
            class="relative rounded-3xl overflow-hidden bg-gradient-to-b from-[#130b24] via-[#1b1033] to-[#0d0718] text-white border border-purple-500/30 shadow-2xl mb-14 p-6 sm:p-10"
            aria-label="Reproductor y Galería Interactiva 3D de Discos y Vinilos"
        >
            <!-- Halo Ambiental Dinámico -->
            <div
                id="showcase-ambient-glow"
                class="pointer-events-none absolute -top-24 left-1/2 -translate-x-1/2 w-[520px] h-[520px] rounded-full blur-3xl opacity-30 transition-all duration-700"
                style="background: radial-gradient(circle, {{ $discoInicial['paleta']['accent'] }} 0%, transparent 70%);"
            ></div>

            <!-- Barra Superior de Estado del Reproductor Curatorial -->
            <div class="relative z-10 flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-white/10">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-[11px] font-label-code uppercase tracking-wider text-purple-200">
                        <span id="showcase-spin-dot" class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        <span id="showcase-status-label">ROTACIÓN CONTINUA · LADO A</span>
                    </span>
                    <span id="showcase-format-pill" class="px-3 py-1 rounded-full bg-primary-container/80 text-white text-[11px] font-label-code font-bold uppercase tracking-wider">
                        {{ $discoInicial['formato_etiqueta'] }}
                    </span>
                </div>

                <div class="text-xs font-label-code text-purple-200/80 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px] text-purple-300">3d_rotation</span>
                    <span>Pasa el cursor sobre el disco para inclinar en 3D (Tilt X/Y)</span>
                </div>
            </div>

            <!-- Contenedor Principal: Escenario 3D de Discos (Izquierda) + Ficha Curatorial Dinámica (Derecha) -->
            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center py-6 sm:py-8">

                <!-- COLUMNA IZQUIERDA (7 cols): Escenario 3D con Discos Circulares en Movimiento y Controles -->
                <div class="lg:col-span-7 flex flex-col items-center">

                    <!-- Escenario 3D de Discos -->
                    <div
                        id="disc-showcase-stage"
                        class="disc-showcase-stage relative w-full h-[330px] sm:h-[390px] flex items-center justify-center select-none overflow-visible"
                    >
                        @foreach ($discosShowcase as $idx => $disco)
                            <div
                                class="disc-3d-slot absolute w-[260px] h-[260px] sm:w-[320px] sm:h-[320px] cursor-pointer"
                                data-disc-index="{{ $idx }}"
                                role="button"
                                tabindex="0"
                                aria-label="Seleccionar {{ $disco['nombre'] }}"
                            >
                                <!-- Capa de Inclinación 3D Interactiva al Pasar el Cursor (Tilt 3D) -->
                                <div class="disc-3d-tilt w-full h-full">
                                    <!-- Capa de Volteo 3D (Flip Lado A / Lado B) -->
                                    <div class="disc-3d-flipper">

                                        <!-- =====================================================
                                             CARA FRONTAL (LADO A): FORMATO CIRCULAR FIEL DEL DISCO
                                             ===================================================== -->
                                        <div class="disc-face disc-face-front">
                                            <div class="disc-spin-rotor relative flex items-center justify-center {{ $disco['formato'] === 'CD' ? 'cd-surface' : 'vinyl-surface' }}">

                                                @if ($disco['formato'] === 'CD')
                                                    <!-- Acabado Refractivo de CD Compact Disc + Arte Impreso Semitransparente -->
                                                    <div class="absolute inset-3 rounded-full border border-white/40 pointer-events-none"></div>
                                                    <div class="absolute inset-0 bg-gradient-to-tr {{ $disco['paleta']['bg'] }} opacity-35 mix-blend-overlay pointer-events-none"></div>

                                                    <!-- Leyenda Superior Impresa sobre el Disco Compacto -->
                                                    <div class="absolute top-6 inset-x-8 text-center pointer-events-none">
                                                        <span class="block text-[9px] font-label-code uppercase tracking-[0.25em] text-slate-900 font-bold drop-shadow">
                                                            COMPACT DISC DIGITAL AUDIO
                                                        </span>
                                                        <span class="block text-[11px] font-bold text-slate-950 truncate mt-0.5 font-quicksand">
                                                            {{ $disco['artista'] }}
                                                        </span>
                                                    </div>

                                                    <!-- Leyenda Inferior Impresa sobre el CD -->
                                                    <div class="absolute bottom-7 inset-x-8 text-center pointer-events-none">
                                                        <span class="block text-[10px] font-bold text-slate-900 truncate font-quicksand">
                                                            {{ $disco['nombre'] }}
                                                        </span>
                                                        <span class="block text-[8px] font-label-code text-slate-800 uppercase tracking-widest mt-0.5">
                                                            {{ $disco['sku'] }} · {{ $disco['anio'] }}
                                                        </span>
                                                    </div>

                                                    <!-- Anillo Central de Policarbonato Transparente (Clamping Area del CD) -->
                                                    <div class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-white/30 backdrop-blur-xs border-4 border-white/70 shadow-inner flex items-center justify-center">
                                                        <div class="w-16 h-16 rounded-full border border-slate-400/60 flex items-center justify-center bg-slate-200/40">
                                                            <!-- Agujero Central Fiel del Disco -->
                                                            <div class="w-7 h-7 rounded-full bg-[#130b24] border-2 border-white/80 shadow-inner"></div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <!-- Surcos Analógicos Concéntricos de Acetato (Vinilo 180g) -->
                                                    <div class="absolute inset-4 rounded-full border border-white/5 pointer-events-none"></div>
                                                    <div class="absolute inset-10 rounded-full border border-white/5 pointer-events-none"></div>
                                                    <div class="absolute inset-16 rounded-full border border-white/10 pointer-events-none"></div>

                                                    <!-- Galleta / Etiqueta Central del Álbum con Agujero Central -->
                                                    <div class="relative w-[108px] h-[108px] sm:w-[126px] sm:h-[126px] rounded-full bg-gradient-to-br {{ $disco['paleta']['bg'] }} border-2 {{ $disco['paleta']['ring'] }} shadow-lg flex flex-col items-center justify-between p-3 text-center">
                                                        <!-- Sello Discográfico Superior -->
                                                        <div class="w-full">
                                                            <span class="block text-[7px] sm:text-[8px] font-label-code uppercase tracking-[0.2em] text-amber-200/90 font-bold truncate">
                                                                {{ $disco['paleta']['sello_corto'] }}
                                                            </span>
                                                            <span class="block text-[8px] sm:text-[9px] font-bold text-white truncate mt-0.5 font-quicksand">
                                                                {{ $disco['artista'] }}
                                                            </span>
                                                        </div>

                                                        <!-- Agujero Central de Eje del Tocadiscos (Spindle Hole) -->
                                                        <div class="w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-[#120a21] border border-amber-200/50 shadow-inner shrink-0"></div>

                                                        <!-- Título y Lado A en la parte inferior de la galleta -->
                                                        <div class="w-full">
                                                            <span class="block text-[8px] font-medium text-white/95 truncate">
                                                                {{ $disco['nombre'] }}
                                                            </span>
                                                            <span class="block text-[7px] font-label-code uppercase tracking-widest text-amber-200/80">
                                                                LADO A · {{ $disco['anio'] }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif

                                            </div>
                                        </div>

                                        <!-- =====================================================
                                             CARA POSTERIOR (LADO B): MATRIZ & CONTRAPORTADA
                                             ===================================================== -->
                                        <div class="disc-face disc-face-back">
                                            <div class="disc-spin-rotor relative flex items-center justify-center {{ $disco['formato'] === 'CD' ? 'cd-surface' : 'vinyl-surface' }}">
                                                <!-- Anillo de Surco Muerto Grabado (Runout Matrix Groove) -->
                                                <div class="absolute inset-6 rounded-full border border-dashed border-amber-300/35 pointer-events-none"></div>
                                                <div class="absolute inset-12 rounded-full border border-purple-300/20 pointer-events-none"></div>

                                                <!-- Etiqueta Central del Lado B / Matriz Certificada -->
                                                <div class="relative w-[124px] h-[124px] sm:w-[144px] sm:h-[144px] rounded-full bg-slate-950/95 border-2 border-amber-400/70 shadow-xl flex flex-col items-center justify-between p-3.5 text-center">
                                                    <div>
                                                        <span class="block text-[8px] font-label-code uppercase tracking-widest text-amber-300 font-bold">
                                                            LADO B · MATRIZ
                                                        </span>
                                                        <span class="block text-[8px] font-label-code text-purple-200 truncate mt-0.5">
                                                            {{ $disco['matriz_lado_b'] }}
                                                        </span>
                                                    </div>

                                                    <!-- Agujero Central -->
                                                    <div class="w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-[#120a21] border border-amber-300/60 shadow-inner shrink-0"></div>

                                                    <div>
                                                        <span class="block text-[8px] font-bold text-emerald-300 uppercase tracking-wider">
                                                            {{ $disco['graduacion'] }}
                                                        </span>
                                                        <span class="block text-[7px] font-label-code text-white/70 uppercase">
                                                            SKU #{{ $disco['sku'] }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- =================================================================
                         BARRA DE CONTROLES INTERACTIVOS (FLIP, PAUSE/PLAY, REPLAY, PREV/NEXT)
                         ================================================================= -->
                    <div class="mt-4 flex flex-wrap items-center justify-center gap-2.5 sm:gap-3 bg-white/10 backdrop-blur-md border border-white/15 px-4 py-2.5 rounded-2xl shadow-lg">
                        <!-- Botón Anterior (Prev) -->
                        <button
                            type="button"
                            id="disc-prev-btn"
                            class="px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-label-md font-bold uppercase tracking-wider flex items-center gap-1 transition cursor-pointer"
                            title="Disco Anterior"
                        >
                            <span class="material-symbols-outlined text-[18px]">skip_previous</span>
                            <span class="hidden sm:inline">Prev</span>
                        </button>

                        <!-- Botón Flip (Voltear Lado A / Lado B) -->
                        <button
                            type="button"
                            id="disc-flip-btn"
                            class="px-3.5 py-2 rounded-xl bg-purple-600/80 hover:bg-purple-500 text-white border border-purple-300/40 text-xs font-label-md font-bold uppercase tracking-wider flex items-center gap-1.5 transition cursor-pointer shadow-xs"
                            title="Voltear disco para inspeccionar Lado B / Matriz"
                        >
                            <span class="material-symbols-outlined text-[18px]">flip_camera_android</span>
                            <span id="disc-flip-text">Flip · Lado B</span>
                        </button>

                        <!-- Botón Pause / Play (Detener o Reanudar Rotación) -->
                        <button
                            type="button"
                            id="disc-play-pause-btn"
                            class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 text-xs font-label-md font-bold uppercase tracking-wider flex items-center gap-1.5 transition cursor-pointer shadow-xs"
                            title="Pausar o reanudar rotación del disco activo"
                        >
                            <span id="disc-play-pause-icon" class="material-symbols-outlined text-[18px]">pause</span>
                            <span id="disc-play-pause-text">Pause</span>
                        </button>

                        <!-- Botón Replay (Reiniciar Giro y Vista Frontal) -->
                        <button
                            type="button"
                            id="disc-replay-btn"
                            class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/15 text-xs font-label-md font-bold uppercase tracking-wider flex items-center gap-1.5 transition cursor-pointer"
                            title="Reiniciar rotación en Lado A"
                        >
                            <span class="material-symbols-outlined text-[18px]">replay</span>
                            <span>Replay</span>
                        </button>

                        <!-- Botón Siguiente (Next) -->
                        <button
                            type="button"
                            id="disc-next-btn"
                            class="px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-label-md font-bold uppercase tracking-wider flex items-center gap-1 transition cursor-pointer"
                            title="Siguiente Disco"
                        >
                            <span class="hidden sm:inline">Next</span>
                            <span class="material-symbols-outlined text-[18px]">skip_next</span>
                        </button>
                    </div>

                    <!-- Selector Rápido de Discos en la Galería -->
                    <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                        @foreach ($discosShowcase as $idx => $disco)
                            <button
                                type="button"
                                class="disc-selector-pill px-3 py-1.5 rounded-full text-[11px] font-label-code transition flex items-center gap-1.5 cursor-pointer {{ $idx === 0 ? 'bg-white text-slate-950 font-bold shadow-sm' : 'bg-white/10 text-purple-200 hover:bg-white/20' }}"
                                data-select-disc="{{ $idx }}"
                            >
                                <span class="material-symbols-outlined text-[14px]">{{ $disco['formato'] === 'CD' ? 'disc_full' : 'album' }}</span>
                                <span class="max-w-[130px] truncate">{{ $disco['nombre'] }}</span>
                            </button>
                        @endforeach
                    </div>

                </div>

                <!-- COLUMNA DERECHA (5 cols): Ficha Curatorial Dinámica & Botón Directo de Reserva -->
                <div class="lg:col-span-5">
                    <div class="rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 p-6 sm:p-7 space-y-5 shadow-xl">

                        <!-- Cabecera de Ficha: SKU, Graduación Goldmine y Disponibilidad en Tiempo Real -->
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-white/10 pb-3">
                            <span id="card-sku" class="font-label-code text-xs text-purple-200 uppercase tracking-wider">
                                SKU: #{{ $discoInicial['sku'] }}
                            </span>

                            <div class="flex items-center gap-2">
                                <span id="card-graduacion" class="px-2.5 py-0.5 rounded bg-amber-400/20 border border-amber-300/40 text-amber-200 text-[11px] font-label-code font-bold uppercase">
                                    {{ $discoInicial['graduacion'] }}
                                </span>

                                <span
                                    id="card-stock-badge"
                                    class="px-2.5 py-0.5 rounded-full text-[11px] font-label-code font-bold uppercase {{ $discoInicial['agotado'] ? 'bg-red-600 text-white border border-red-700' : 'bg-emerald-500/20 text-emerald-200 border border-emerald-400/40' }}"
                                >
                                    {{ $discoInicial['agotado'] ? 'AGOTADO' : $discoInicial['stock'].' en bóveda' }}
                                </span>
                            </div>
                        </div>

                        <!-- Título del Álbum, Artista y Sello Discográfico -->
                        <div class="space-y-1.5">
                            <span id="card-velocidad" class="text-[10px] font-label-code uppercase tracking-widest text-purple-300 block">
                                {{ $discoInicial['velocidad'] }}
                            </span>
                            <h2 id="card-titulo" class="text-2xl sm:text-3xl font-bold font-quicksand text-white leading-tight">
                                {{ $discoInicial['nombre'] }}
                            </h2>
                            <p id="card-artista-sello" class="font-title-editorial text-base text-purple-200 italic">
                                {{ $discoInicial['artista'] }} &middot; {{ $discoInicial['sello'] }} ({{ $discoInicial['anio'] }})
                            </p>
                        </div>

                        <!-- Notas Periciales y Matriz -->
                        <p id="card-descripcion" class="text-xs sm:text-sm text-purple-100/85 leading-relaxed">
                            {{ $discoInicial['descripcion'] }}
                        </p>

                        <div class="grid grid-cols-2 gap-3 p-3.5 rounded-xl bg-black/30 border border-white/10 text-xs">
                            <div>
                                <span class="text-[10px] font-label-code uppercase text-purple-300 block">Género Fonográfico</span>
                                <span id="card-genero" class="font-semibold text-white">{{ $discoInicial['genero'] }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-label-code uppercase text-purple-300 block">Inspección de Matriz</span>
                                <span id="card-matriz" class="font-label-code text-amber-200 text-[11px]">{{ $discoInicial['matriz_lado_b'] }}</span>
                            </div>
                        </div>

                        <!-- Precio Tasado y Botón Directo de «RESERVAR» Vinculado con Stock en Tiempo Real -->
                        <div class="pt-3 border-t border-white/15 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <span class="text-[10px] uppercase font-label-code text-purple-300 block">Precio Tasado en Bóveda:</span>
                                <div class="flex items-baseline gap-1.5">
                                    <span id="card-precio" class="font-tabular-data font-bold text-2xl sm:text-3xl text-white">
                                        {{ $discoInicial['precio_formateado'] }}
                                    </span>
                                    <span class="text-xs font-label-code text-purple-200">MXN</span>
                                </div>
                            </div>

                            <!-- Formulario Dinámico de Reserva para el Disco Seleccionado -->
                            <form id="showcase-reserve-form" method="POST" action="{{ route('carrito.agregar') }}">
                                @csrf
                                <input
                                    type="hidden"
                                    id="showcase-variante-input"
                                    name="variante_sku_id"
                                    value="{{ $discoInicial['variante_sku_id'] }}"
                                />
                                <input type="hidden" name="cantidad" value="1" />

                                <button
                                    type="submit"
                                    id="showcase-reserve-btn"
                                    @disabled($discoInicial['agotado'] || ! $discoInicial['variante_sku_id'])
                                    class="w-full sm:w-auto px-6 py-3.5 rounded-xl text-xs font-label-md font-bold uppercase tracking-wider flex items-center justify-center gap-2 transition shadow-lg {{ ($discoInicial['agotado'] || ! $discoInicial['variante_sku_id']) ? 'bg-slate-600 text-slate-300 opacity-50 cursor-not-allowed pointer-events-none' : 'bg-primary-container hover:bg-primary text-white cursor-pointer' }}"
                                >
                                    <span id="showcase-reserve-icon" class="material-symbols-outlined text-[18px]">
                                        {{ $discoInicial['agotado'] ? 'block' : 'add_shopping_cart' }}
                                    </span>
                                    <span>Reservar</span>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

            </div>
        </section>
    @endif

    <!-- =========================================================================
         CUADRÍCULA Y FILTROS DEL CATÁLOGO DE DISCOS & VINILOS
         ========================================================================= -->
    <div id="acervo-vinilos-grid" class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-purple-200/80 gap-4 mb-8">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-on-surface font-quicksand">
                {{ $meta['subtitulo'] ?? 'Prensados Originales 180g, Matrices Mono y Discos Compactos de Época' }}
            </h2>
            <p class="text-xs text-primary font-label-code mt-1 font-semibold uppercase tracking-wider">
                Mostrando {{ $coleccionDiscos->count() }} {{ $coleccionDiscos->count() === 1 ? 'pieza autenticada' : 'piezas autenticadas' }} en esta sala
            </p>
        </div>

        <form method="GET" action="{{ route('public.vinilos') }}" class="flex items-center flex-wrap gap-3 text-xs font-label-md">
            <input
                type="search"
                name="q"
                value="{{ request('q') }}"
                placeholder="Buscar por álbum, intérprete, sello o SKU..."
                class="bg-surface-container-lowest border border-purple-200/80 rounded-xl px-3.5 py-2 text-xs text-on-surface focus:outline-none focus:border-primary"
            />
            <select name="orden" onchange="this.form.submit()" class="bg-surface-container-lowest text-on-surface border border-purple-200/80 rounded-xl py-2 px-3 focus:ring-1 focus:ring-primary focus:outline-none shadow-2xs font-medium cursor-pointer">
                <option value="relevancia" @selected(request('orden') === 'relevancia')>Ordenar: Relevancia Curatorial</option>
                <option value="precio_desc" @selected(request('orden') === 'precio_desc')>Precio: Mayor a Menor</option>
                <option value="precio_asc" @selected(request('orden') === 'precio_asc')>Precio: Menor a Mayor</option>
            </select>
        </form>
    </div>

    @if ($coleccionDiscos->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            @foreach ($coleccionDiscos as $producto)
                <x-public.product-card :producto="$producto" />
            @endforeach
        </div>
    @else
        <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/80 p-12 text-center space-y-3 mb-12">
            <span class="material-symbols-outlined text-[42px] text-outline">album</span>
            <h3 class="text-lg font-bold text-on-surface font-quicksand">No se encontraron discos con ese criterio</h3>
            <a href="{{ route('public.vinilos') }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary-container text-white text-xs font-bold uppercase tracking-wider">
                <span>Restablecer Sala de Vinilos</span>
            </a>
        </div>
    @endif

    @if (! empty($discosShowcase))
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const discos = @json($discosShowcase);
                    if (!Array.isArray(discos) || discos.length === 0) {
                        return;
                    }

                    const stage = document.getElementById('disc-showcase-stage');
                    const slots = Array.from(document.querySelectorAll('.disc-3d-slot'));
                    const pills = Array.from(document.querySelectorAll('.disc-selector-pill'));

                    const prevBtn = document.getElementById('disc-prev-btn');
                    const nextBtn = document.getElementById('disc-next-btn');
                    const flipBtn = document.getElementById('disc-flip-btn');
                    const flipText = document.getElementById('disc-flip-text');
                    const playPauseBtn = document.getElementById('disc-play-pause-btn');
                    const playPauseIcon = document.getElementById('disc-play-pause-icon');
                    const playPauseText = document.getElementById('disc-play-pause-text');
                    const replayBtn = document.getElementById('disc-replay-btn');

                    const ambientGlow = document.getElementById('showcase-ambient-glow');
                    const statusLabel = document.getElementById('showcase-status-label');
                    const spinDot = document.getElementById('showcase-spin-dot');
                    const formatPill = document.getElementById('showcase-format-pill');

                    // Elementos de la ficha curatorial
                    const cardSku = document.getElementById('card-sku');
                    const cardGraduacion = document.getElementById('card-graduacion');
                    const cardStockBadge = document.getElementById('card-stock-badge');
                    const cardVelocidad = document.getElementById('card-velocidad');
                    const cardTitulo = document.getElementById('card-titulo');
                    const cardArtistaSello = document.getElementById('card-artista-sello');
                    const cardDescripcion = document.getElementById('card-descripcion');
                    const cardGenero = document.getElementById('card-genero');
                    const cardMatriz = document.getElementById('card-matriz');
                    const cardPrecio = document.getElementById('card-precio');
                    const varianteInput = document.getElementById('showcase-variante-input');
                    const reserveBtn = document.getElementById('showcase-reserve-btn');
                    const reserveIcon = document.getElementById('showcase-reserve-icon');

                    let activeIndex = 0;
                    let isPaused = false;
                    let isFlipped = false;

                    function updateStatusBanner() {
                        const ladoTexto = isFlipped ? 'LADO B (MATRIZ)' : 'LADO A';
                        const giroTexto = isPaused ? 'GIRO EN PAUSA' : 'ROTACIÓN CONTINUA';
                        if (statusLabel) {
                            statusLabel.textContent = giroTexto + ' · ' + ladoTexto;
                        }
                        if (spinDot) {
                            spinDot.classList.toggle('animate-ping', !isPaused);
                            spinDot.classList.toggle('bg-emerald-400', !isPaused);
                            spinDot.classList.toggle('bg-amber-400', isPaused);
                        }
                        if (flipText) {
                            flipText.textContent = isFlipped ? 'Flip · Lado A' : 'Flip · Lado B';
                        }
                        if (playPauseIcon) {
                            playPauseIcon.textContent = isPaused ? 'play_arrow' : 'pause';
                        }
                        if (playPauseText) {
                            playPauseText.textContent = isPaused ? 'Play' : 'Pause';
                        }
                    }

                    function renderStagePositions() {
                        const total = slots.length;

                        slots.forEach(function (slot, idx) {
                            const flipper = slot.querySelector('.disc-3d-flipper');
                            const rotors = slot.querySelectorAll('.disc-spin-rotor');
                            const tiltBox = slot.querySelector('.disc-3d-tilt');

                            let diff = idx - activeIndex;
                            if (total > 2) {
                                if (diff > total / 2) diff -= total;
                                if (diff < -total / 2) diff += total;
                            }

                            if (tiltBox && idx !== activeIndex) {
                                tiltBox.style.transform = 'rotateX(0deg) rotateY(0deg) translateZ(0px)';
                            }

                            if (idx === activeIndex) {
                                slot.style.transform = 'translate3d(0px, 0px, 40px) rotateY(0deg) scale(1)';
                                slot.style.opacity = '1';
                                slot.style.zIndex = '30';
                                slot.style.filter = ' none';

                                if (flipper) {
                                    flipper.classList.toggle('is-flipped', isFlipped);
                                }
                                rotors.forEach(function (r) {
                                    r.classList.toggle('is-paused', isPaused);
                                });
                            } else if (diff === -1 || (total === 2 && diff !== 0)) {
                                slot.style.transform = 'translate3d(-210px, 12px, -120px) rotateY(34deg) scale(0.74)';
                                slot.style.opacity = '0.62';
                                slot.style.zIndex = '20';
                                slot.style.filter = 'brightness(0.75)';
                                if (flipper) flipper.classList.remove('is-flipped');
                                rotors.forEach(function (r) {
                                    r.classList.remove('is-paused');
                                });
                            } else if (diff === 1) {
                                slot.style.transform = 'translate3d(210px, 12px, -120px) rotateY(-34deg) scale(0.74)';
                                slot.style.opacity = '0.62';
                                slot.style.zIndex = '20';
                                slot.style.filter = 'brightness(0.75)';
                                if (flipper) flipper.classList.remove('is-flipped');
                                rotors.forEach(function (r) {
                                    r.classList.remove('is-paused');
                                });
                            } else {
                                const dir = diff < 0 ? -1 : 1;
                                slot.style.transform = 'translate3d(' + (dir * 310) + 'px, 28px, -240px) rotateY(' + (-dir * 45) + 'deg) scale(0.55)';
                                slot.style.opacity = '0.22';
                                slot.style.zIndex = '10';
                                slot.style.filter = 'brightness(0.5)';
                                if (flipper) flipper.classList.remove('is-flipped');
                            }
                        });

                        pills.forEach(function (pill, idx) {
                            if (idx === activeIndex) {
                                pill.className = 'disc-selector-pill px-3 py-1.5 rounded-full text-[11px] font-label-code transition flex items-center gap-1.5 cursor-pointer bg-white text-slate-950 font-bold shadow-sm';
                            } else {
                                pill.className = 'disc-selector-pill px-3 py-1.5 rounded-full text-[11px] font-label-code transition flex items-center gap-1.5 cursor-pointer bg-white/10 text-purple-200 hover:bg-white/20';
                            }
                        });
                    }

                    function updateCuratorialCard() {
                        const disco = discos[activeIndex];
                        if (!disco) return;

                        if (ambientGlow && disco.paleta && disco.paleta.accent) {
                            ambientGlow.style.background = 'radial-gradient(circle, ' + disco.paleta.accent + ' 0%, transparent 70%)';
                        }
                        if (formatPill) formatPill.textContent = disco.formato_etiqueta;
                        if (cardSku) cardSku.textContent = 'SKU: #' + disco.sku;
                        if (cardGraduacion) cardGraduacion.textContent = disco.graduacion;
                        if (cardVelocidad) cardVelocidad.textContent = disco.velocidad;
                        if (cardTitulo) cardTitulo.textContent = disco.nombre;
                        if (cardArtistaSello) {
                            cardArtistaSello.textContent = disco.artista + ' · ' + disco.sello + ' (' + disco.anio + ')';
                        }
                        if (cardDescripcion) cardDescripcion.textContent = disco.descripcion;
                        if (cardGenero) cardGenero.textContent = disco.genero;
                        if (cardMatriz) cardMatriz.textContent = disco.matriz_lado_b;
                        if (cardPrecio) cardPrecio.textContent = disco.precio_formateado;

                        if (cardStockBadge) {
                            if (disco.agotado) {
                                cardStockBadge.textContent = 'AGOTADO';
                                cardStockBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-label-code font-bold uppercase bg-red-600 text-white border border-red-700';
                            } else {
                                cardStockBadge.textContent = disco.stock + ' en bóveda';
                                cardStockBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-label-code font-bold uppercase bg-emerald-500/20 text-emerald-200 border border-emerald-400/40';
                            }
                        }

                        if (varianteInput) {
                            varianteInput.value = disco.variante_sku_id || '';
                        }

                        if (reserveBtn) {
                            const deshabilitado = Boolean(disco.agotado || !disco.variante_sku_id);
                            reserveBtn.disabled = deshabilitado;
                            if (deshabilitado) {
                                reserveBtn.className = 'w-full sm:w-auto px-6 py-3.5 rounded-xl text-xs font-label-md font-bold uppercase tracking-wider flex items-center justify-center gap-2 transition shadow-lg bg-slate-600 text-slate-300 opacity-50 cursor-not-allowed pointer-events-none';
                                if (reserveIcon) reserveIcon.textContent = 'block';
                            } else {
                                reserveBtn.className = 'w-full sm:w-auto px-6 py-3.5 rounded-xl text-xs font-label-md font-bold uppercase tracking-wider flex items-center justify-center gap-2 transition shadow-lg bg-primary-container hover:bg-primary text-white cursor-pointer';
                                if (reserveIcon) reserveIcon.textContent = 'add_shopping_cart';
                            }
                        }
                    }

                    function selectDisc(newIndex) {
                        const total = discos.length;
                        activeIndex = ((newIndex % total) + total) % total;
                        isFlipped = false;
                        updateStatusBanner();
                        renderStagePositions();
                        updateCuratorialCard();
                    }

                    // Eventos de clic en cada disco o píldora
                    slots.forEach(function (slot, idx) {
                        slot.addEventListener('click', function () {
                            if (idx === activeIndex) {
                                isFlipped = !isFlipped;
                                updateStatusBanner();
                                renderStagePositions();
                            } else {
                                selectDisc(idx);
                            }
                        });
                    });

                    pills.forEach(function (pill) {
                        pill.addEventListener('click', function () {
                            const targetIdx = parseInt(pill.dataset.selectDisc || '0', 10);
                            selectDisc(targetIdx);
                        });
                    });

                    // Controles Inferiores: Prev, Next, Flip, Pause/Play, Replay
                    if (prevBtn) {
                        prevBtn.addEventListener('click', function () {
                            selectDisc(activeIndex - 1);
                        });
                    }

                    if (nextBtn) {
                        nextBtn.addEventListener('click', function () {
                            selectDisc(activeIndex + 1);
                        });
                    }

                    if (flipBtn) {
                        flipBtn.addEventListener('click', function () {
                            isFlipped = !isFlipped;
                            updateStatusBanner();
                            renderStagePositions();
                        });
                    }

                    if (playPauseBtn) {
                        playPauseBtn.addEventListener('click', function () {
                            isPaused = !isPaused;
                            updateStatusBanner();
                            renderStagePositions();
                        });
                    }

                    if (replayBtn) {
                        replayBtn.addEventListener('click', function () {
                            isFlipped = false;
                            isPaused = false;
                            const activeSlot = slots[activeIndex];
                            if (activeSlot) {
                                const rotors = activeSlot.querySelectorAll('.disc-spin-rotor');
                                rotors.forEach(function (r) {
                                    r.style.animation = 'none';
                                    void r.offsetWidth;
                                    r.style.animation = '';
                                });
                            }
                            updateStatusBanner();
                            renderStagePositions();
                        });
                    }

                    // Perspectiva Tridimensional Interactiva al Pasar el Cursor (Tilt 3D en Ejes X/Y)
                    if (stage) {
                        stage.addEventListener('mousemove', function (e) {
                            const activeSlot = slots[activeIndex];
                            if (!activeSlot) return;
                            const tiltBox = activeSlot.querySelector('.disc-3d-tilt');
                            if (!tiltBox) return;

                            const rect = activeSlot.getBoundingClientRect();
                            const centerX = rect.left + rect.width / 2;
                            const centerY = rect.top + rect.height / 2;

                            const normX = Math.max(-1, Math.min(1, (e.clientX - centerX) / (rect.width / 2)));
                            const normY = Math.max(-1, Math.min(1, (e.clientY - centerY) / (rect.height / 2)));

                            const rotateY = (normX * 18).toFixed(2);
                            const rotateX = (-normY * 18).toFixed(2);
                            const shiftX = (normX * 10).toFixed(1);
                            const shiftY = (normY * 8).toFixed(1);

                            tiltBox.style.transform =
                                'translate3d(' + shiftX + 'px, ' + shiftY + 'px, 24px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg)';
                        });

                        stage.addEventListener('mouseleave', function () {
                            const activeSlot = slots[activeIndex];
                            if (!activeSlot) return;
                            const tiltBox = activeSlot.querySelector('.disc-3d-tilt');
                            if (tiltBox) {
                                tiltBox.style.transform = 'translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg)';
                            }
                        });
                    }

                    // Inicializar posiciones 3D
                    updateStatusBanner();
                    renderStagePositions();
                    updateCuratorialCard();
                });
            </script>
        @endpush
    @endif

</x-layouts.public>

