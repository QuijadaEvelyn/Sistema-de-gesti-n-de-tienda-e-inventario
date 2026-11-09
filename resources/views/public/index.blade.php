<x-layouts.public title="ARCHIVUM · Galería de Colecciones &amp; Bóveda Pública">

    @php
        $coleccionProductos = $productos ?? collect();
    @endphp

    <!-- Héroe Curatorial & Bienvenida al Archivo -->
    <section class="relative rounded-2xl overflow-hidden bg-gradient-to-r from-purple-900 to-indigo-950 text-white p-8 sm:p-12 mb-10 shadow-lg border border-purple-800/40">
        <div class="relative z-10 max-w-3xl">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/20 border border-purple-300/30 text-purple-200 text-xs font-label-md tracking-wider mb-4">
                <span class="material-symbols-outlined text-[14px] text-purple-300">verified</span>
                CATÁLOGO DESCLASIFICADO · PRIMAVERA 2026
            </span>
            <h1 class="text-3xl sm:text-5xl font-bold font-quicksand leading-tight tracking-tight text-white mb-4">
                Tesoros del Pensamiento &amp; Sonido Histórico
            </h1>
            <p class="font-title-editorial text-purple-100/90 text-lg sm:text-xl leading-relaxed mb-6">
                «Un acervo protegido para bibliófilos y audiófilos: primeras ediciones autografiadas, matrices de prensado original y vestigios culturales autenticados con rigor notarial.»
            </p>
            <div class="flex flex-wrap items-center gap-3 text-xs font-label-md">
                <a href="{{ route('public.libros') }}" class="bg-white text-primary hover:bg-purple-50 px-5 py-3 rounded-xl font-bold uppercase tracking-wider shadow-sm transition flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">auto_stories</span>
                    <span>Libros Raros</span>
                </a>
                <a href="{{ route('public.vinilos') }}" class="bg-purple-800/60 hover:bg-purple-800/90 text-white border border-purple-400/30 px-5 py-3 rounded-xl font-semibold uppercase tracking-wider transition flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">album</span>
                    <span>Vinilos Históricos</span>
                </a>
                <a href="{{ route('public.memorabilia') }}" class="bg-purple-800/60 hover:bg-purple-800/90 text-white border border-purple-400/30 px-5 py-3 rounded-xl font-semibold uppercase tracking-wider transition flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">history_edu</span>
                    <span>Memorabilia</span>
                </a>
                <a href="{{ route('public.adquisiciones') }}" class="bg-amber-400/20 hover:bg-amber-400/30 text-amber-200 border border-amber-300/40 px-5 py-3 rounded-xl font-semibold uppercase tracking-wider transition flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">auto_awesome</span>
                    <span>Adquisiciones Selectas</span>
                </a>
            </div>
        </div>

        <!-- Marca de Agua Geométrica de Bóveda -->
        <div class="absolute -right-12 -bottom-12 opacity-10 pointer-events-none">
            <svg class="w-96 h-96 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                <polyline points="2 17 12 22 22 17"></polyline>
                <polyline points="2 12 12 17 22 12"></polyline>
            </svg>
        </div>
    </section>

    <!-- Acceso Rápido a las 4 Salas Curatoriales -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-12">
        <a href="{{ route('public.libros') }}" class="p-5 rounded-2xl bg-surface-container-lowest border border-purple-200/80 hover:border-primary hover:shadow-md transition group">
            <div class="flex items-center justify-between mb-2">
                <span class="material-symbols-outlined text-primary text-[28px]">auto_stories</span>
                <span class="text-[10px] font-label-code uppercase text-outline group-hover:text-primary">Explorar Sala &rarr;</span>
            </div>
            <h2 class="font-bold text-base text-on-surface font-quicksand group-hover:text-primary">Libros Raros &amp; Incunables</h2>
            <p class="text-xs text-on-surface-variant mt-1">Primeras ediciones, ejemplares autografiados e incunables modernos.</p>
        </a>

        <a href="{{ route('public.vinilos') }}" class="p-5 rounded-2xl bg-surface-container-lowest border border-purple-200/80 hover:border-primary hover:shadow-md transition group">
            <div class="flex items-center justify-between mb-2">
                <span class="material-symbols-outlined text-primary text-[28px]">album</span>
                <span class="text-[10px] font-label-code uppercase text-outline group-hover:text-primary">Explorar Sala &rarr;</span>
            </div>
            <h2 class="font-bold text-base text-on-surface font-quicksand group-hover:text-primary">Discos &amp; Vinilos Históricos</h2>
            <p class="text-xs text-on-surface-variant mt-1">Prensados originales 180g, matrices mono y sellos Blue Note / Columbia.</p>
        </a>

        <a href="{{ route('public.memorabilia') }}" class="p-5 rounded-2xl bg-surface-container-lowest border border-purple-200/80 hover:border-primary hover:shadow-md transition group">
            <div class="flex items-center justify-between mb-2">
                <span class="material-symbols-outlined text-primary text-[28px]">history_edu</span>
                <span class="text-[10px] font-label-code uppercase text-outline group-hover:text-primary">Explorar Sala &rarr;</span>
            </div>
            <h2 class="font-bold text-base text-on-surface font-quicksand group-hover:text-primary">Memorabilia &amp; Archivo</h2>
            <p class="text-xs text-on-surface-variant mt-1">Manuscritos autógrafos, partituras y fotografía en plata sobre gelatina.</p>
        </a>

        <a href="{{ route('public.adquisiciones') }}" class="p-5 rounded-2xl bg-surface-container-lowest border border-purple-200/80 hover:border-primary hover:shadow-md transition group">
            <div class="flex items-center justify-between mb-2">
                <span class="material-symbols-outlined text-primary text-[28px]">auto_awesome</span>
                <span class="text-[10px] font-label-code uppercase text-outline group-hover:text-primary">Explorar Sala &rarr;</span>
            </div>
            <h2 class="font-bold text-base text-on-surface font-quicksand group-hover:text-primary">Adquisiciones Selectas</h2>
            <p class="text-xs text-on-surface-variant mt-1">Piezas cumbre con Certificado de Autenticidad (COA) notariado.</p>
        </a>
    </section>

    <!-- Módulo de Promociones Exclusivas con Preventa Anticipada (Clientes Frecuentes >= 5 Compras) & Contador Regresivo -->
    @php
        $listaPromociones = $promocionesDestacadas ?? collect();
        $clienteVipActivo = (bool) ($esClienteFrecuente ?? false);
        $comprasRealizadasCount = (int) ($totalComprasCliente ?? 0);
        $comprasFaltantesCount = (int) ($comprasRestantesFrecuente ?? 5);
    @endphp
    @if ($listaPromociones->isNotEmpty())
        <section id="promociones-exclusivas" class="mb-14" aria-label="Promociones Exclusivas y Preventa Anticipada">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 mb-6">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="promo-shimmer-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gradient-to-r from-purple-700 via-fuchsia-600 to-amber-500 text-white text-[11px] font-label-code font-bold uppercase tracking-wider shadow-sm">
                            <span class="material-symbols-outlined text-[14px]">workspace_premium</span>
                            <span>Preventa Activa &middot; Edición Especial</span>
                        </span>
                        @if ($clienteVipActivo)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 border border-amber-300 text-amber-900 text-[11px] font-label-code font-bold uppercase">
                                <span class="material-symbols-outlined text-[14px] text-amber-600">verified</span>
                                <span>Cliente Frecuente VIP ({{ $comprasRealizadasCount }} compras) &middot; Acceso Anticipado Desbloqueado</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-100 border border-purple-300 text-purple-900 text-[11px] font-label-code font-semibold uppercase">
                                <span class="material-symbols-outlined text-[14px] text-primary">stars</span>
                                <span>Fidelidad ARCHIVUM: {{ $comprasRealizadasCount }}/5 compras (faltan {{ $comprasFaltantesCount }} para Preventa Anticipada VIP)</span>
                            </span>
                        @endif
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-on-surface font-quicksand">
                        Promociones Exclusivas &amp; Lanzamientos en Preventa
                    </h2>
                    <p class="text-xs text-on-surface-variant mt-1 max-w-3xl">
                        Los <strong>Clientes Frecuentes (5+ compras)</strong> reciben acceso anticipado en la <em>Fase de Preventa Exclusiva</em> para apartar piezas antes del lanzamiento oficial. En la <em>Fase de Apertura</em>, la promoción se desbloquea para todo el público general.
                    </p>
                </div>

                @auth
                    <a href="{{ route('cliente.mis-pedidos') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-purple-950 text-purple-100 hover:bg-primary text-xs font-label-md font-bold uppercase tracking-wider transition self-start lg:self-end shadow-xs">
                        <span class="material-symbols-outlined text-[16px] text-amber-300">notifications_active</span>
                        <span>Ver Mi Panel de Preventas VIP</span>
                    </a>
                @endauth
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                @foreach ($listaPromociones as $promo)
                    @php
                        /** @var \App\Models\Promocion $promo */
                        $fasePromo = $promo->faseActual();
                        $esExclusivaActiva = $fasePromo === 'preventa_exclusiva';
                        $esAbiertaPublico = $fasePromo === 'abierta_publico';
                        $puedeComprarPromo = $promo->puedeAdquirir(auth()->user(), $clienteActual ?? null);
                        $prodPromo = $promo->producto;
                        $variantePromo = $prodPromo?->variantesSku?->first();
                        $stockPromo = $variantePromo ? (int) $variantePromo->existencias->sum('cantidad') : 0;
                        $precioOriginalPromo = (float) ($variantePromo?->precio ?? $prodPromo?->precio ?? 0);
                        $precioConDescuentoPromo = $promo->calcularPrecioPromocional($precioOriginalPromo);
                        $etiquetaDescuento = $promo->tipo_descuento === 'porcentaje'
                            ? '-' . number_format((float) $promo->valor, 0) . '% OFF'
                            : '-$' . number_format((float) $promo->valor, 0) . ' MXN';
                        $countdownTargetMs = $promo->timestampObjetivoCountdownMs();
                    @endphp

                    <article
                        data-promo-card="true"
                        data-promo-phase="{{ $fasePromo }}"
                        class="promo-card-animated rounded-2xl bg-gradient-to-br from-purple-950 via-indigo-950 to-slate-950 text-white p-6 border-2 {{ $esExclusivaActiva ? 'border-amber-400/80' : 'border-purple-400/70' }} flex flex-col justify-between shadow-xl"
                    >
                        <div class="relative z-10 space-y-4">
                            <!-- Cabecera de Insignias Animadas -->
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="promo-shimmer-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-label-code font-bold uppercase tracking-wider {{ $esExclusivaActiva ? 'bg-amber-400 text-slate-950' : 'bg-purple-500 text-white' }}">
                                    <span class="material-symbols-outlined text-[13px]">{{ $esExclusivaActiva ? 'lock_open_right' : 'bolt' }}</span>
                                    <span>{{ $promo->badge_etiqueta ?? ($esExclusivaActiva ? 'PREVENTA ACTIVA · VIP' : 'EDICIÓN ESPECIAL') }}</span>
                                </span>

                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-500/20 border border-emerald-400/50 text-emerald-300 font-tabular-data text-xs font-extrabold">
                                    {{ $etiquetaDescuento }}
                                </span>
                            </div>

                            <!-- Fase de Preventa vs Fase de Apertura -->
                            <div class="flex items-center justify-between text-[10px] font-label-code uppercase tracking-wider px-3 py-1.5 rounded-xl {{ $esExclusivaActiva ? 'bg-amber-500/15 border border-amber-400/40 text-amber-200' : 'bg-emerald-500/15 border border-emerald-400/40 text-emerald-200' }}">
                                <span class="font-bold">{{ $promo->etiquetaFase() }}</span>
                                <span>{{ $stockPromo > 0 ? $stockPromo . ' disp.' : 'Cupo limitado' }}</span>
                            </div>

                            <!-- Título y Descripción -->
                            <div>
                                <h3 class="text-lg font-bold font-quicksand text-white leading-snug">
                                    {{ $promo->nombre }}
                                </h3>
                                <p class="text-xs text-purple-200/90 mt-1.5 leading-relaxed">
                                    {{ $promo->descripcion }}
                                </p>
                            </div>

                            <!-- Reloj Contador Regresivo en Tiempo Real (Countdown) -->
                            <div
                                class="p-3.5 rounded-xl bg-black/40 border border-purple-500/30 space-y-2"
                                data-promo-countdown="{{ $countdownTargetMs }}"
                            >
                                <div class="flex items-center justify-between text-[10px] font-label-code uppercase tracking-wider text-purple-200">
                                    <span class="inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px] text-amber-300 animate-spin">schedule</span>
                                        <span>{{ $promo->etiquetaCountdown() }}</span>
                                    </span>
                                    <span class="text-amber-300 font-bold">TIEMPO REAL</span>
                                </div>
                                <div class="grid grid-cols-4 gap-2 text-center font-tabular-data">
                                    <div class="bg-purple-900/60 border border-purple-500/40 rounded-lg py-1.5 px-1">
                                        <span data-cd-days class="block text-base sm:text-lg font-extrabold text-white">02</span>
                                        <span class="block text-[9px] font-label-code uppercase text-purple-300">Días</span>
                                    </div>
                                    <div class="bg-purple-900/60 border border-purple-500/40 rounded-lg py-1.5 px-1">
                                        <span data-cd-hours class="block text-base sm:text-lg font-extrabold text-white">11</span>
                                        <span class="block text-[9px] font-label-code uppercase text-purple-300">Horas</span>
                                    </div>
                                    <div class="bg-purple-900/60 border border-purple-500/40 rounded-lg py-1.5 px-1">
                                        <span data-cd-minutes class="block text-base sm:text-lg font-extrabold text-white">45</span>
                                        <span class="block text-[9px] font-label-code uppercase text-purple-300">Min</span>
                                    </div>
                                    <div class="bg-purple-900/60 border border-amber-400/50 rounded-lg py-1.5 px-1">
                                        <span data-cd-seconds class="block text-base sm:text-lg font-extrabold text-amber-300">30</span>
                                        <span class="block text-[9px] font-label-code uppercase text-amber-200">Seg</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pie de Tarjeta: Precio Preferencial y Acción según Fase / Nivel de Cliente -->
                        <div class="relative z-10 pt-4 mt-5 border-t border-purple-700/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                @if ($precioOriginalPromo > 0)
                                    <span class="text-[11px] font-tabular-data text-purple-300 line-through block">
                                        Antes: ${{ number_format($precioOriginalPromo, 2) }} MXN
                                    </span>
                                    <span class="text-lg font-extrabold font-tabular-data text-amber-300">
                                        ${{ number_format($precioConDescuentoPromo, 2) }} <span class="text-xs font-normal text-purple-200">MXN</span>
                                    </span>
                                @else
                                    <span class="text-xs font-label-code uppercase text-amber-300 font-bold">
                                        Bonificación: {{ $etiquetaDescuento }}
                                    </span>
                                @endif
                            </div>

                            @if ($variantePromo && $stockPromo > 0 && $puedeComprarPromo)
                                <form method="POST" action="{{ route('carrito.agregar') }}">
                                    @csrf
                                    <input type="hidden" name="variante_sku_id" value="{{ $variantePromo->id }}" />
                                    <input type="hidden" name="cantidad" value="1" />
                                    <button
                                        type="submit"
                                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl {{ $esExclusivaActiva ? 'bg-amber-400 hover:bg-amber-300 text-slate-950' : 'bg-purple-500 hover:bg-purple-400 text-white' }} text-xs font-label-md font-extrabold uppercase tracking-wider flex items-center justify-center gap-1.5 transition shadow-md cursor-pointer"
                                    >
                                        <span class="material-symbols-outlined text-[16px]">shopping_bag</span>
                                        <span>{{ $esExclusivaActiva ? 'Apartar en Preventa VIP' : 'Adquirir en Apertura' }}</span>
                                    </button>
                                </form>
                            @elseif ($esExclusivaActiva && ! $puedeComprarPromo)
                                <div class="text-right">
                                    <span class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-purple-900/80 border border-amber-400/40 text-amber-200 text-[10px] font-label-code font-bold uppercase">
                                        <span class="material-symbols-outlined text-[14px]">lock</span>
                                        <span>Exclusivo 5+ Compras (Abre {{ $promo->fechaAperturaPublica()->format('d/m') }})</span>
                                    </span>
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Vitrina Interactiva: Carrusel 3D con Swiper.js (Efecto Coverflow) -->
    <section class="mb-14" aria-label="Vitrina Curatorial en 3D">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-4">
            <div>
                <span class="inline-flex items-center gap-1.5 text-[11px] font-label-code uppercase tracking-widest text-primary">
                    <span class="material-symbols-outlined text-[15px]">view_carousel</span>
                    <span>Exhibición Tridimensional · Coverflow 3D</span>
                </span>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-on-surface font-quicksand">
                    Vitrina Rotativa de Piezas Destacadas
                </h2>
            </div>
            <p class="text-xs text-on-surface-variant font-label-code">
                Desliza o utiliza las flechas laterales para inspeccionar libros raros, vinilos y memorabilia
            </p>
        </div>

        <div class="swiper product3DCarousel">
            <div class="swiper-wrapper">
                @forelse ($coleccionProductos as $productoCarrusel)
                    <div class="swiper-slide">
                        <x-public.product-card :producto="$productoCarrusel" />
                    </div>
                @empty
                    <!-- Fichas Curatoriales de Respaldo (Libros, Vinilos y Memorabilia) -->
                    <div class="swiper-slide">
                        <article class="bg-white border border-purple-200/80 rounded-2xl p-5 flex flex-col justify-between hover:border-primary hover:shadow-lg transition-all duration-200 group shadow-xs">
                            <div>
                                <div class="w-full aspect-square rounded-xl bg-surface-container-low border border-purple-200/60 flex items-center justify-center relative overflow-hidden mb-4">
                                    <span class="absolute top-2.5 left-2.5 bg-primary-container text-white text-[10px] font-label-sm font-semibold uppercase px-2.5 py-0.5 rounded shadow-2xs">VINILO LP 180G</span>
                                    <span class="material-symbols-outlined text-purple-300 text-[64px]">album</span>
                                </div>
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <span class="font-label-code text-[11px] text-outline">SKU: #VIN-1959-COL</span>
                                        <x-ui.badge-grading type="mint" text="MINT (M)" />
                                    </div>
                                    <h3 class="font-bold text-on-surface text-base font-quicksand">Kind of Blue (Mono 1st Press)</h3>
                                    <p class="font-title-editorial text-xs text-on-surface-variant italic">Miles Davis · Columbia Records CL 1355</p>
                                    <p class="text-xs text-secondary line-clamp-2 pt-1">Matriz original con etiqueta seis ojos («6-eye»). Sonido analógico sin reprocesar.</p>
                                </div>
                            </div>
                            <div class="pt-4 mt-4 border-t border-purple-100 flex items-center justify-between">
                                <span class="font-tabular-data font-bold text-on-surface text-base">$18,500 <span class="text-xs font-normal text-on-surface-variant">MXN</span></span>
                            </div>
                        </article>
                    </div>

                    <div class="swiper-slide">
                        <article class="bg-white border border-purple-200/80 rounded-2xl p-5 flex flex-col justify-between hover:border-primary hover:shadow-lg transition-all duration-200 group shadow-xs">
                            <div>
                                <div class="w-full aspect-square rounded-xl bg-surface-container-low border border-purple-200/60 flex items-center justify-center relative overflow-hidden mb-4">
                                    <span class="absolute top-2.5 left-2.5 bg-primary-container text-white text-[10px] font-label-sm font-semibold uppercase px-2.5 py-0.5 rounded shadow-2xs">PRIMERA EDICIÓN</span>
                                    <span class="material-symbols-outlined text-purple-300 text-[64px]">auto_stories</span>
                                </div>
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <span class="font-label-code text-[11px] text-outline">SKU: #LIB-1967-SUD</span>
                                        <x-ui.badge-grading type="rare" text="VERY FINE (VF)" />
                                    </div>
                                    <h3 class="font-bold text-on-surface text-base font-quicksand">Cien Años de Soledad</h3>
                                    <p class="font-title-editorial text-xs text-on-surface-variant italic">Gabriel García Márquez · Sudamericana</p>
                                    <p class="text-xs text-secondary line-clamp-2 pt-1">Tiraje inicial de Buenos Aires (Mayo 1967). Cubierta original diseñada por Iris Alba.</p>
                                </div>
                            </div>
                            <div class="pt-4 mt-4 border-t border-purple-100 flex items-center justify-between">
                                <span class="font-tabular-data font-bold text-on-surface text-base">$32,000 <span class="text-xs font-normal text-on-surface-variant">MXN</span></span>
                            </div>
                        </article>
                    </div>

                    <div class="swiper-slide">
                        <article class="bg-white border border-purple-200/80 rounded-2xl p-5 flex flex-col justify-between hover:border-primary hover:shadow-lg transition-all duration-200 group shadow-xs">
                            <div>
                                <div class="w-full aspect-square rounded-xl bg-surface-container-low border border-purple-200/60 flex items-center justify-center relative overflow-hidden mb-4">
                                    <span class="absolute top-2.5 left-2.5 bg-tertiary text-white text-[10px] font-label-sm font-semibold uppercase px-2.5 py-0.5 rounded shadow-2xs">MEMORABILIA</span>
                                    <span class="material-symbols-outlined text-purple-300 text-[64px]">history_edu</span>
                                </div>
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <span class="font-label-code text-[11px] text-outline">SKU: #MEM-1928-AUT</span>
                                        <x-ui.badge-grading type="coa" text="COA NOTARIADO" />
                                    </div>
                                    <h3 class="font-bold text-on-surface text-base font-quicksand">Carta Manuscrita Bellas Artes</h3>
                                    <p class="font-title-editorial text-xs text-on-surface-variant italic">Colección Diplomática · Archivo Notarial</p>
                                    <p class="text-xs text-secondary line-clamp-2 pt-1">Pluma estilográfica con tinta ferrogálica original y dictamen caligráfico.</p>
                                </div>
                            </div>
                            <div class="pt-4 mt-4 border-t border-purple-100 flex items-center justify-between">
                                <span class="font-tabular-data font-bold text-on-surface text-base">$9,400 <span class="text-xs font-normal text-on-surface-variant">MXN</span></span>
                            </div>
                        </article>
                    </div>
                @endforelse
            </div>

            <!-- Controles de Navegación y Paginación del Carrusel 3D -->
            <div class="swiper-button-prev" aria-label="Pieza anterior"></div>
            <div class="swiper-button-next" aria-label="Pieza siguiente"></div>
            <div class="swiper-pagination"></div>
        </div>
    </section>

    <!-- Barra de Filtros, Ordenación y Estado del Catálogo -->
    <div id="catalogo" class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-purple-200/80 gap-4 mb-8">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-on-surface font-quicksand">
                Acervo General en Custodia
            </h2>
            <p class="text-xs text-primary font-label-code mt-1 font-semibold uppercase tracking-wider">
                Mostrando {{ $coleccionProductos->count() }} piezas certificadas archivadas en catálogo activo · Sede Central
            </p>
        </div>

        <form method="GET" action="{{ route('public.home') }}" class="flex items-center flex-wrap gap-3 text-xs font-label-md">
            <label for="sort-select" class="text-secondary uppercase tracking-wider font-semibold">Ordenar:</label>
            <select id="sort-select" name="orden" onchange="this.form.submit()" class="bg-surface-container-lowest text-on-surface border border-purple-200/80 rounded-lg py-1.5 px-3 focus:ring-1 focus:ring-primary focus:outline-none shadow-2xs font-medium cursor-pointer">
                <option value="relevancia" @selected(request('orden') === 'relevancia')>Relevancia Curatorial</option>
                <option value="precio_desc" @selected(request('orden') === 'precio_desc')>Precio: Mayor a Menor</option>
                <option value="precio_asc" @selected(request('orden') === 'precio_asc')>Precio: Menor a Mayor</option>
            </select>

            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container border border-purple-200/80 text-secondary font-medium">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Disponibles para Reserva Inmediata</span>
            </span>
        </form>
    </div>

    <!-- Cuadrícula Dinámica de Productos del Catálogo -->
    @if ($coleccionProductos->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            @foreach ($coleccionProductos as $producto)
                <x-public.product-card :producto="$producto" />
            @endforeach
        </div>
    @else
        <div class="bg-surface-container-lowest border border-purple-200/80 rounded-2xl p-10 text-center space-y-2 mb-12">
            <p class="text-base font-bold text-on-surface font-quicksand">El catálogo está listo para poblarse</p>
            <p class="text-xs text-on-surface-variant">Ejecuta <code class="font-label-code bg-surface-container px-2 py-0.5 rounded">php artisan db:seed</code> para cargar las piezas de ejemplo.</p>
        </div>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Inicializar contadores regresivos en tiempo real para Promociones y Preventas
                const countdownNodes = document.querySelectorAll('[data-promo-countdown]');
                if (countdownNodes.length > 0) {
                    const actualizarContadoresPromo = () => {
                        const ahoraMs = Date.now();
                        countdownNodes.forEach((nodo) => {
                            const metaMs = parseInt(nodo.getAttribute('data-promo-countdown') || '0', 10);
                            const diffSeg = Math.max(0, Math.floor((metaMs - ahoraMs) / 1000));
                            const dias = Math.floor(diffSeg / 86400);
                            const horas = Math.floor((diffSeg % 86400) / 3600);
                            const minutos = Math.floor((diffSeg % 3600) / 60);
                            const segundos = diffSeg % 60;

                            const elDias = nodo.querySelector('[data-cd-days]');
                            const elHoras = nodo.querySelector('[data-cd-hours]');
                            const elMin = nodo.querySelector('[data-cd-minutes]');
                            const elSeg = nodo.querySelector('[data-cd-seconds]');

                            if (elDias) elDias.textContent = String(dias).padStart(2, '0');
                            if (elHoras) elHoras.textContent = String(horas).padStart(2, '0');
                            if (elMin) elMin.textContent = String(minutos).padStart(2, '0');
                            if (elSeg) elSeg.textContent = String(segundos).padStart(2, '0');
                        });
                    };

                    actualizarContadoresPromo();
                    setInterval(actualizarContadoresPromo, 1000);
                }

                const carouselElement = document.querySelector('.product3DCarousel');
                if (!carouselElement || typeof Swiper === 'undefined') {
                    return;
                }

                if (carouselElement.swiper) {
                    return;
                }

                const totalSlides = carouselElement.querySelectorAll('.swiper-slide').length;

                new Swiper('.product3DCarousel', {
                    effect: 'coverflow',
                    grabCursor: true,
                    centeredSlides: true,
                    slidesPerView: 'auto',
                    initialSlide: Math.min(1, Math.max(0, totalSlides - 1)),
                    loop: totalSlides > 3,
                    speed: 650,
                    autoplay: {
                        delay: 4200,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
                    },
                    coverflowEffect: {
                        rotate: 22,
                        stretch: 0,
                        depth: 180,
                        modifier: 1.15,
                        slideShadows: true,
                    },
                    pagination: {
                        el: '.product3DCarousel .swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.product3DCarousel .swiper-button-next',
                        prevEl: '.product3DCarousel .swiper-button-prev',
                    },
                    keyboard: {
                        enabled: true,
                        onlyInViewport: true,
                    },
                });
            });
        </script>
    @endpush

</x-layouts.public>
