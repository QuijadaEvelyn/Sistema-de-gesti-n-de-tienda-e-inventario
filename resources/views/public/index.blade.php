<x-layouts.public title="ARCHIVUM · Tienda de Libros, Discos y Coleccionables">

    @php
        $coleccionProductos = $productos ?? collect();

        // 6 piezas balanceadas para la Vitrina 3D (2 libros, 2 discos, 2 coleccionables intercalados)
        $coleccionCarrusel = $productosCarrusel ?? (
            $coleccionProductos->isNotEmpty()
                ? collect([
                    $coleccionProductos->first(fn ($p) => $p->catalogo?->tipo === 'libro'),
                    $coleccionProductos->first(fn ($p) => in_array($p->catalogo?->tipo, ['disco', 'vinilo'], true)),
                    $coleccionProductos->first(fn ($p) => in_array($p->catalogo?->tipo, ['coleccionable', 'memorabilia'], true)),
                    $coleccionProductos->filter(fn ($p) => $p->catalogo?->tipo === 'libro')->skip(1)->first(),
                    $coleccionProductos->filter(fn ($p) => in_array($p->catalogo?->tipo, ['disco', 'vinilo'], true))->skip(1)->first(),
                    $coleccionProductos->filter(fn ($p) => in_array($p->catalogo?->tipo, ['coleccionable', 'memorabilia'], true))->skip(1)->first(),
                ])->filter()->values()
                : collect()
        );
    @endphp

    <!-- Hero Principal & Bienvenida a la Tienda -->
    <section class="relative rounded-2xl overflow-hidden bg-[#FAF8FF] dark:bg-gradient-to-r dark:from-purple-900 dark:to-indigo-950 text-slate-900 dark:text-white p-6 sm:p-12 mb-10 shadow-xs dark:shadow-lg border border-purple-200 dark:border-purple-800/40">
        <div class="relative z-10 max-w-3xl">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 dark:bg-purple-500/20 border border-purple-200 dark:border-purple-300/30 text-purple-800 dark:text-purple-200 text-xs font-label-md tracking-wider mb-4 font-semibold">
                <span class="material-symbols-outlined text-[14px] text-purple-600 dark:text-purple-300">verified</span>
                CATÁLOGO OFICIAL · TEMPORADA 2026
            </span>
            <h1 class="text-2xl sm:text-5xl font-bold font-quicksand leading-tight tracking-tight text-slate-900 dark:text-white mb-4">
                Libros, Discos de Vinilo &amp; Ediciones de Colección
            </h1>
            <p class="font-title-editorial text-slate-600 dark:text-purple-100/90 text-lg sm:text-xl leading-relaxed mb-6">
                «Descubre nuestro catálogo especializado: primeras ediciones autografiadas, vinilos originales y artículos de colección con Certificado de Autenticidad.»
            </p>
            <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2.5 text-xs sm:text-sm font-label-md">
                <a href="{{ route('public.libros') }}" class="btn-glass-surface px-3 sm:px-5 py-2.5 sm:py-3 rounded-xl font-bold uppercase tracking-wider flex items-center justify-center gap-2 shadow-xs text-center">
                    <span class="material-symbols-outlined text-[16px] sm:text-[18px]">auto_stories</span>
                    <span>Libros</span>
                </a>
                <a href="{{ route('public.vinilos') }}" class="btn-glass-purple px-3 sm:px-5 py-2.5 sm:py-3 rounded-xl font-semibold uppercase tracking-wider flex items-center justify-center gap-2 shadow-xs text-center">
                    <span class="material-symbols-outlined text-[16px] sm:text-[18px]">album</span>
                    <span>Discos</span>
                </a>
                <a href="{{ route('public.memorabilia') }}" class="btn-glass-purple px-3 sm:px-5 py-2.5 sm:py-3 rounded-xl font-semibold uppercase tracking-wider flex items-center justify-center gap-2 shadow-xs text-center">
                    <span class="material-symbols-outlined text-[16px] sm:text-[18px]">history_edu</span>
                    <span>Coleccionables</span>
                </a>
                <a href="{{ route('public.adquisiciones') }}" class="btn-glass-surface px-3 sm:px-5 py-2.5 sm:py-3 rounded-xl font-semibold uppercase tracking-wider flex items-center justify-center gap-2 shadow-xs text-center">
                    <span class="material-symbols-outlined text-[16px] sm:text-[18px]">auto_awesome</span>
                    <span class="truncate">Ediciones Especiales</span>
                </a>
            </div>
        </div>

        <!-- Marca de Agua Geométrica de Bóveda con Nuevo Isotipo ARCHIVUM -->
        <div class="absolute -right-12 -bottom-12 opacity-5 dark:opacity-10 text-purple-900 pointer-events-none">
            <x-ui.archivum-logo size="xl" svg-class="w-96 h-96" :isotype-only="true" />
        </div>
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
                    <a href="{{ route('cliente.mis-pedidos') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 dark:bg-purple-950 text-white dark:text-purple-100 text-xs font-label-md font-bold uppercase tracking-wider transition self-start lg:self-end shadow-md dark:shadow-xs">
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
                        class="promo-card-animated rounded-2xl bg-white dark:bg-gradient-to-br dark:from-purple-950 dark:via-indigo-950 dark:to-slate-950 text-slate-900 dark:text-white p-6 border-2 {{ $esExclusivaActiva ? 'border-amber-300 dark:border-amber-400/80' : 'border-purple-200 dark:border-purple-400/70' }} flex flex-col justify-between shadow-xs dark:shadow-xl"
                    >
                        <div class="relative z-10 space-y-4">
                            <!-- Cabecera de Insignias Animadas -->
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="promo-shimmer-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-label-code font-bold uppercase tracking-wider {{ $esExclusivaActiva ? 'bg-amber-100 text-amber-900 border border-amber-300 dark:bg-amber-400 dark:text-slate-950 dark:border-transparent' : 'bg-purple-100 text-purple-800 border border-purple-200 dark:bg-purple-500 dark:text-white dark:border-transparent' }}">
                                    <span class="material-symbols-outlined text-[13px]">{{ $esExclusivaActiva ? 'lock_open_right' : 'bolt' }}</span>
                                    <span>{{ $promo->badge_etiqueta ?? ($esExclusivaActiva ? 'PREVENTA ACTIVA · VIP' : 'EDICIÓN ESPECIAL') }}</span>
                                </span>

                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-500/20 dark:border-emerald-400/50 dark:text-emerald-300 font-tabular-data text-xs font-extrabold">
                                    {{ $etiquetaDescuento }}
                                </span>
                            </div>

                            <!-- Fase de Preventa vs Fase de Apertura -->
                            <div class="flex items-center justify-between text-[10px] font-label-code uppercase tracking-wider px-3 py-1.5 rounded-xl {{ $esExclusivaActiva ? 'bg-amber-50 border border-amber-200 text-amber-900 dark:bg-amber-500/15 dark:border-amber-400/40 dark:text-amber-200' : 'bg-emerald-50 border border-emerald-200 text-emerald-800 dark:bg-emerald-500/15 dark:border-emerald-400/40 dark:text-emerald-200' }}">
                                <span class="font-bold">{{ $promo->etiquetaFase() }}</span>
                                <span>{{ $stockPromo > 0 ? $stockPromo . ' disp.' : 'Cupo limitado' }}</span>
                            </div>

                            <!-- Título y Descripción -->
                            <div>
                                <h3 class="text-lg font-bold font-quicksand text-slate-900 dark:text-white leading-snug">
                                    {{ $promo->nombre }}
                                </h3>
                                <p class="text-xs text-slate-600 dark:text-purple-200/90 mt-1.5 leading-relaxed">
                                    {{ $promo->descripcion }}
                                </p>
                            </div>

                            <!-- Reloj Contador Regresivo en Tiempo Real (Countdown) -->
                            <div
                                class="p-3.5 rounded-xl bg-purple-50/70 dark:bg-black/40 border border-purple-200 dark:border-purple-500/30 space-y-2"
                                data-promo-countdown="{{ $countdownTargetMs }}"
                            >
                                <div class="flex items-center justify-between text-[10px] font-label-code uppercase tracking-wider text-purple-900 dark:text-purple-200">
                                    <span class="inline-flex items-center gap-1 font-semibold">
                                        <span class="material-symbols-outlined text-[13px] text-amber-600 dark:text-amber-300 animate-spin">schedule</span>
                                        <span>{{ $promo->etiquetaCountdown() }}</span>
                                    </span>
                                    <span class="text-amber-600 dark:text-amber-300 font-bold">TIEMPO REAL</span>
                                </div>
                                <div class="grid grid-cols-4 gap-2 text-center font-tabular-data">
                                    <div class="bg-purple-50 dark:bg-purple-900/60 border border-purple-200 dark:border-purple-500/40 rounded-lg py-1.5 px-1 shadow-2xs">
                                        <span data-cd-days class="block text-base sm:text-lg font-extrabold text-purple-950 dark:text-white">02</span>
                                        <span class="block text-[9px] font-label-code uppercase text-purple-700 dark:text-purple-300 font-semibold">Días</span>
                                    </div>
                                    <div class="bg-purple-50 dark:bg-purple-900/60 border border-purple-200 dark:border-purple-500/40 rounded-lg py-1.5 px-1 shadow-2xs">
                                        <span data-cd-hours class="block text-base sm:text-lg font-extrabold text-purple-950 dark:text-white">11</span>
                                        <span class="block text-[9px] font-label-code uppercase text-purple-700 dark:text-purple-300 font-semibold">Horas</span>
                                    </div>
                                    <div class="bg-purple-50 dark:bg-purple-900/60 border border-purple-200 dark:border-purple-500/40 rounded-lg py-1.5 px-1 shadow-2xs">
                                        <span data-cd-minutes class="block text-base sm:text-lg font-extrabold text-purple-950 dark:text-white">45</span>
                                        <span class="block text-[9px] font-label-code uppercase text-purple-700 dark:text-purple-300 font-semibold">Min</span>
                                    </div>
                                    <div class="bg-purple-50 dark:bg-purple-900/60 border border-amber-300 dark:border-amber-400/50 rounded-lg py-1.5 px-1 shadow-2xs">
                                        <span data-cd-seconds class="block text-base sm:text-lg font-extrabold text-amber-700 dark:text-amber-300">30</span>
                                        <span class="block text-[9px] font-label-code uppercase text-amber-800 dark:text-amber-200 font-semibold">Seg</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pie de Tarjeta: Precio Preferencial y Acción según Fase / Nivel de Cliente -->
                        <div class="relative z-10 pt-4 mt-5 border-t border-purple-200 dark:border-purple-700/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                @if ($precioOriginalPromo > 0)
                                    <span class="text-[11px] font-tabular-data text-slate-400 dark:text-purple-300 line-through block">
                                        Antes: ${{ number_format($precioOriginalPromo, 2) }} MXN
                                    </span>
                                    <span class="text-lg font-extrabold font-tabular-data text-purple-700 dark:text-amber-300">
                                        ${{ number_format($precioConDescuentoPromo, 2) }} <span class="text-xs font-normal text-slate-500 dark:text-purple-200">MXN</span>
                                    </span>
                                @else
                                    <span class="text-xs font-label-code uppercase text-purple-700 dark:text-amber-300 font-bold">
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
                                        class="btn-glass-purple w-full sm:w-auto px-4 py-2.5 rounded-xl text-xs font-label-md font-extrabold uppercase tracking-wider flex items-center justify-center gap-1.5 shadow-sm cursor-pointer"
                                    >
                                        <span class="material-symbols-outlined text-[16px]">shopping_bag</span>
                                        <span>{{ $esExclusivaActiva ? 'Apartar en Preventa VIP' : 'Adquirir en Apertura' }}</span>
                                    </button>
                                </form>
                            @elseif ($esExclusivaActiva && ! $puedeComprarPromo)
                                <div class="text-right">
                                    <span class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-purple-100 dark:bg-purple-900/80 border border-purple-200 dark:border-amber-400/40 text-purple-800 dark:text-amber-200 text-[10px] font-label-code font-bold uppercase">
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

    <!-- Selección Destacada: Carrusel 3D Coverflow Interactivo Puro -->
    <section class="relative block w-full py-6 sm:py-10 mt-6 sm:mt-8 clear-both" aria-label="Selección Destacada de Productos">
        <!-- Encabezado separado con margen inferior amplio -->
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12 sm:mb-16 relative z-20">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-2 sm:gap-4 flex-wrap">
                <div class="max-w-xl">
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-label-code uppercase tracking-widest text-primary dark:text-purple-300">
                        <span class="material-symbols-outlined text-[15px]">view_in_ar</span>
                        <span>Productos Destacados · Desplazamiento Fluido</span>
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-on-surface dark:text-white font-quicksand">
                        Selección Destacada de Productos
                    </h2>
                </div>
                <p class="text-xs text-on-surface-variant dark:text-purple-300/80 font-label-code max-w-sm md:text-right leading-relaxed">
                    Desliza con el mouse, pantalla táctil o rueda para explorar el catálogo 3D
                </p>
            </div>
        </div>

        <!-- Escenario 3D Coverflow Puro: Debajo del encabezado sin subirse ni salirse del flujo -->
        <div class="relative z-10 w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div
                id="vitrina-coverflow-carousel"
                tabindex="0"
                role="region"
                aria-roledescription="carousel"
                aria-label="Selección Destacada de Productos"
                class="coverflow-stage relative z-10 w-full mt-4 h-[520px] sm:h-[580px] flex items-center justify-center overflow-hidden select-none focus:outline-none"
                style="perspective: 1200px; transform-style: preserve-3d;"
            >
                <!-- Degradado sutil en los bordes izquierdo y derecho -->
                <div class="absolute left-0 top-0 bottom-0 w-16 sm:w-28 bg-gradient-to-r from-background dark:from-[#0d0714] to-transparent z-40 pointer-events-none"></div>
                <div class="absolute right-0 top-0 bottom-0 w-16 sm:w-28 bg-gradient-to-l from-background dark:from-[#0d0714] to-transparent z-40 pointer-events-none"></div>

                <!-- Pista Central 3D -->
                <div class="coverflow-track relative w-full h-full flex items-center justify-center overflow-visible" style="transform-style: preserve-3d;">
                    @forelse ($coleccionCarrusel as $productoCarrusel)
                        @php
                            $initIdx = $loop->index;
                            $totalCount = $coleccionCarrusel->count();
                            $initDiff = $initIdx;
                            if ($totalCount > 3) {
                                if ($initDiff > $totalCount / 2) {
                                    $initDiff -= $totalCount;
                                }
                            } elseif ($totalCount === 3) {
                                if ($initDiff === 2) {
                                    $initDiff = -1;
                                }
                            }
                            $initTransform = match ($initDiff) {
                                0 => 'translate3d(0, 0, 0) scale(1)',
                                -1 => 'translate3d(-335px, 0px, -110px) rotateY(24deg) scale(0.86)',
                                1 => 'translate3d(335px, 0px, -110px) rotateY(-24deg) scale(0.86)',
                                -2 => 'translate3d(-610px, 0px, -210px) rotateY(30deg) scale(0.74)',
                                2 => 'translate3d(610px, 0px, -210px) rotateY(-30deg) scale(0.74)',
                                default => 'translate3d(780px, 0px, -300px) rotateY(-35deg) scale(0.65)',
                            };
                            $initOpacity = match ($initDiff) {
                                0 => '1',
                                -1, 1 => '0.85',
                                -2, 2 => '0.55',
                                default => '0',
                            };
                            $initZIndex = match ($initDiff) {
                                0 => '30',
                                -1, 1 => '20',
                                -2, 2 => '10',
                                default => '1',
                            };
                            $initPointerEvents = $initDiff === 0 ? 'auto' : 'none';
                        @endphp
                    <div
                        class="coverflow-slot-anchor {{ $initDiff === 0 ? 'is-active' : '' }} absolute w-[255px] sm:w-[280px] md:w-[300px] h-auto min-h-[430px] sm:min-h-[450px]"
                        style="top: 50%; left: 50%; transform: translate(-50%, -50%); transform-style: preserve-3d; z-index: {{ $initZIndex }}; pointer-events: {{ $initPointerEvents }};"
                        data-slide-index="{{ $initIdx }}"
                    >
                        <div
                            class="coverflow-slot w-full h-auto"
                            style="transform-style: preserve-3d; transform: {{ $initTransform }}; opacity: {{ $initOpacity }}; backface-visibility: hidden; -webkit-backface-visibility: hidden;"
                        >
                            <div class="coverflow-card h-auto min-h-[430px] sm:min-h-[450px] flex flex-col justify-between rounded-2xl {{ $initDiff === 0 ? 'coverflow-card-active is-active' : '' }}">
                                <x-public.product-card :producto="$productoCarrusel" />
                            </div>
                        </div>
                    </div>
                @empty
                    @php
                        $fallbackDisco = [
                            'id' => 9991,
                            'nombre' => 'Kind of Blue (Mono 1st Press)',
                            'subtitulo' => 'Miles Davis · Columbia Records CL 1355',
                            'descripcion' => 'Matriz original con etiqueta seis ojos («6-eye»). Sonido analógico sin reprocesar peritado con alta fidelidad.',
                            'precio' => '18,500',
                            'tipo' => 'disco',
                            'icono' => 'album',
                            'sku' => 'VIN-1959-COL',
                            'graduacion' => 'MINT (M)',
                            'badgeType' => 'mint',
                            'stock' => 1,
                            'sede' => 'Central Bellas Artes',
                            'esPiezaUnica' => true,
                            'numeroSerie' => 'COL-1959-CL1355',
                            'estaAgotado' => false,
                            'estaReservada' => false,
                            'varianteSkuId' => null,
                            'actionUrl' => route('public.vinilos'),
                            'especificaciones' => [
                                'Artista / Intérprete' => 'Miles Davis',
                                'Sello Discográfico' => 'Columbia Records (CL 1355)',
                                'Año de Lanzamiento' => '1959',
                                'Formato' => 'Vinilo LP 180g (Mono Original)',
                                'Velocidad' => '33 ⅓ RPM',
                                'Pistas' => '5 temas magistrales',
                                'Estado Funda / Vinilo' => 'Mint (M) / Mint (M)',
                            ],
                        ];
                        $fallbackLibro = [
                            'id' => 9992,
                            'nombre' => 'Cien Años de Soledad',
                            'subtitulo' => 'Gabriel García Márquez · Sudamericana',
                            'descripcion' => 'Tiraje inicial de Buenos Aires (Mayo 1967). Cubierta original diseñada por Iris Alba.',
                            'precio' => '32,000',
                            'tipo' => 'libro',
                            'icono' => 'auto_stories',
                            'sku' => 'LIB-1967-SUD',
                            'graduacion' => 'VERY FINE (VF)',
                            'badgeType' => 'rare',
                            'stock' => 1,
                            'sede' => 'Coyoacán',
                            'esPiezaUnica' => true,
                            'numeroSerie' => 'SUD-1967-001',
                            'estaAgotado' => false,
                            'estaReservada' => false,
                            'varianteSkuId' => null,
                            'actionUrl' => route('public.libros'),
                            'especificaciones' => [
                                'Autor' => 'Gabriel García Márquez',
                                'Editorial' => 'Editorial Sudamericana (Buenos Aires)',
                                'Año de Publicación' => '1967',
                                'Género Literario' => 'Novela · Realismo Mágico',
                                'Páginas' => '471 págs.',
                                'Encuadernación' => 'Rústica con solapas originales',
                            ],
                        ];
                        $fallbackMemorabilia = [
                            'id' => 9993,
                            'nombre' => 'Carta Manuscrita Bellas Artes',
                            'subtitulo' => 'Colección Diplomática · Archivo Notarial',
                            'descripcion' => 'Pluma estilográfica con tinta ferrogálica original y dictamen caligráfico notariado.',
                            'precio' => '9,400',
                            'tipo' => 'coleccionable',
                            'icono' => 'history_edu',
                            'sku' => 'MEM-1928-AUT',
                            'graduacion' => 'COA NOTARIADO',
                            'badgeType' => 'coa',
                            'stock' => 1,
                            'sede' => 'Central Bellas Artes',
                            'esPiezaUnica' => true,
                            'numeroSerie' => 'COA-1928-AUT',
                            'estaAgotado' => false,
                            'estaReservada' => false,
                            'varianteSkuId' => null,
                            'actionUrl' => route('public.memorabilia'),
                            'especificaciones' => [
                                'Marca / Fabricante' => 'Archivo Notarial de Bellas Artes',
                                'Material de Confección' => 'Papel vitela verjurado con tinta ferrogálica',
                                'Categoría' => 'Manuscritos Históricos',
                                'Año / Datación' => '1928',
                                'Certificación COA' => 'Certamen Notarial con Dictamen Grafológico',
                            ],
                        ];
                        $fallbackDisco2 = [
                            'id' => 9994,
                            'nombre' => 'The Dark Side of the Moon (1st Press UK)',
                            'subtitulo' => 'Pink Floyd · Harvest SHVL 804 (1973)',
                            'descripcion' => 'Prensado original británico con matriz sólida A-2 / B-2, doble póster original y sonido estéreo peritado.',
                            'precio' => '24,500',
                            'tipo' => 'disco',
                            'icono' => 'album',
                            'sku' => 'VIN-1973-HAR',
                            'graduacion' => 'NEAR MINT (NM)',
                            'badgeType' => 'mint',
                            'stock' => 1,
                            'sede' => 'Central Bellas Artes',
                            'esPiezaUnica' => true,
                            'numeroSerie' => 'HAR-1973-SHVL',
                            'estaAgotado' => false,
                            'estaReservada' => false,
                            'varianteSkuId' => null,
                            'actionUrl' => route('public.vinilos'),
                            'especificaciones' => [
                                'Artista / Intérprete' => 'Pink Floyd',
                                'Sello Discográfico' => 'Harvest Records (SHVL 804)',
                                'Año de Lanzamiento' => '1973',
                                'Formato' => 'Vinilo LP 180g (UK Original)',
                                'Velocidad' => '33 ⅓ RPM',
                                'Pistas' => '10 temas conceptuales',
                                'Estado Funda / Vinilo' => 'Near Mint (NM) / Mint (M)',
                            ],
                        ];
                        $fallbackLibro2 = [
                            'id' => 9995,
                            'nombre' => 'El Aleph (1ª Edición Losada)',
                            'subtitulo' => 'Jorge Luis Borges · Editorial Losada (1949)',
                            'descripcion' => 'Tiraje histórico de Buenos Aires con encuadernación rústica original de época y sello curatorial de autenticidad.',
                            'precio' => '28,000',
                            'tipo' => 'libro',
                            'icono' => 'auto_stories',
                            'sku' => 'LIB-1949-LOS',
                            'graduacion' => 'EXCELLENT (EX)',
                            'badgeType' => 'rare',
                            'stock' => 1,
                            'sede' => 'Central Bellas Artes',
                            'esPiezaUnica' => true,
                            'numeroSerie' => 'LOS-1949-ALEPH',
                            'estaAgotado' => false,
                            'estaReservada' => false,
                            'varianteSkuId' => null,
                            'actionUrl' => route('public.libros'),
                            'especificaciones' => [
                                'Autor' => 'Jorge Luis Borges',
                                'Editorial' => 'Editorial Losada (Buenos Aires)',
                                'Año de Publicación' => '1949',
                                'Género Literario' => 'Cuento Filosófico · Ficción Fantástica',
                                'Páginas' => '146 págs.',
                                'Encuadernación' => 'Rústica con cubierta ilustrada',
                            ],
                        ];
                        $fallbackMemorabilia2 = [
                            'id' => 9996,
                            'nombre' => 'Centenario 50 Pesos Oro (1921)',
                            'subtitulo' => 'Casa de Moneda de México · 1.20565 oz Oro Puro',
                            'descripcion' => 'Acuñación del primer centenario de la consumación de la Independencia de México. Peritada con certificación numismática.',
                            'precio' => '65,000',
                            'tipo' => 'coleccionable',
                            'icono' => 'history_edu',
                            'sku' => 'NUM-1921-ORO',
                            'graduacion' => 'UNC / BRILLIANT UNCIRCULATED',
                            'badgeType' => 'coa',
                            'stock' => 1,
                            'sede' => 'Coyoacán',
                            'esPiezaUnica' => true,
                            'numeroSerie' => 'NUM-1921-CENT',
                            'estaAgotado' => false,
                            'estaReservada' => false,
                            'varianteSkuId' => null,
                            'actionUrl' => route('public.memorabilia'),
                            'especificaciones' => [
                                'Marca / Ceca' => 'Casa de Moneda de México (Mo)',
                                'Material' => 'Oro Ley 0.900 (37.5 g oro fino)',
                                'Categoría' => 'Numismática Histórica',
                                'Año / Datación' => '1921 (Año Bicentenario)',
                                'Certificación COA' => 'Certamen Notarial con Graduación Numismática',
                            ],
                        ];
                    @endphp

                    <!-- Fallback 1: Vinilo (Tarjeta Central Activa) -->
                    <div
                        class="coverflow-slot-anchor absolute w-[240px] sm:w-[280px] md:w-[300px] h-[400px] sm:h-[440px] md:h-[460px]"
                        style="top: 50%; left: 50%; transform: translate(-50%, -50%); transform-style: preserve-3d; z-index: 30;"
                        data-slide-index="0"
                    >
                        <div
                            class="coverflow-slot w-full h-full"
                            style="transform-style: preserve-3d; transform: translate3d(0px, 0px, 40px) rotateY(0deg) scale(1); opacity: 1;"
                        >
                            <div class="coverflow-card h-full rounded-2xl coverflow-card-active">
                                <article
                                    draggable="false"
                                    data-product-details="{{ json_encode($fallbackDisco, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}"
                                    class="bg-white dark:bg-[#171024] border-2 border-purple-300 dark:border-purple-500 ring-1 ring-purple-300/30 dark:ring-2 dark:ring-purple-500/20 rounded-2xl p-3 sm:p-5 flex flex-col justify-between hover:border-purple-400 dark:hover:border-purple-400 hover:shadow-md dark:hover:shadow-xl transition-all duration-200 group relative h-full select-none"
                                >
                                    <div>
                                        <div onclick="abrirModalDetalleProducto(this)" class="w-full aspect-square rounded-xl bg-surface-container-low dark:bg-[#140d20] border border-purple-200/60 dark:border-purple-500/30 flex items-center justify-center relative overflow-hidden mb-2.5 sm:mb-4 cursor-pointer group-hover:shadow-inner">
                                            <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/10 to-white/20 dark:via-white/5 dark:to-white/10 pointer-events-none rounded-xl z-20"></div>
                                            <div class="absolute top-2.5 inset-x-2.5 flex items-center justify-between gap-1.5 z-10 pointer-events-none">
                                                <span class="inline-flex items-center gap-1 bg-purple-100 dark:bg-purple-600 text-purple-800 dark:text-white border border-purple-300 dark:border-purple-400/60 text-[9px] sm:text-[10px] font-label-sm font-bold uppercase px-1.5 sm:px-2 py-0.5 rounded-md shadow-2xs dark:shadow-sm">
                                                    <span class="material-symbols-outlined text-[11px] sm:text-[12px]">diamond</span>
                                                    <span class="hidden sm:inline">PIEZA ÚNICA</span>
                                                    <span>1/1</span>
                                                </span>
                                                <span class="inline-flex items-center bg-white/95 dark:bg-[#1b132a]/95 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30 text-[9px] sm:text-[10px] font-label-code font-semibold px-2 py-0.5 rounded-full shadow-2xs">1 disponible</span>
                                            </div>
                                            <span class="material-symbols-outlined text-purple-400 dark:text-purple-300 group-hover:text-primary transition-colors text-[64px]">album</span>
                                        </div>
                                        <div class="space-y-1">
                                            <h3 onclick="abrirModalDetalleProducto(this)" class="font-bold text-on-surface dark:text-white text-base group-hover:text-primary dark:group-hover:text-purple-300 transition font-quicksand cursor-pointer leading-snug">Kind of Blue (Mono 1st Press)</h3>
                                            <p class="font-title-editorial text-xs text-on-surface-variant dark:text-purple-200/70 italic truncate">Miles Davis · Columbia Records CL 1355</p>
                                            <button type="button" onclick="abrirModalDetalleProducto(this)" class="pt-1.5 inline-flex items-center gap-1 text-[11px] font-label-code text-primary dark:text-purple-300 hover:underline font-semibold cursor-pointer group-hover:translate-x-0.5 transition-transform">
                                                <span class="material-symbols-outlined text-[14px]">info</span>
                                                <span>Ver detalles &rarr;</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="pt-2.5 sm:pt-4 mt-2.5 sm:mt-4 border-t border-purple-100 dark:border-purple-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <div>
                                            <span class="text-[10px] uppercase font-label-code text-outline dark:text-purple-300/60 block">Precio:</span>
                                            <span class="font-tabular-data font-bold text-on-surface dark:text-white text-base">$18,500 <span class="text-xs font-normal text-on-surface-variant dark:text-purple-200/60">MXN</span></span>
                                        </div>
                                        <a draggable="false" data-requires-auth-reserve="true" href="{{ route('public.vinilos') }}" class="btn-glass-purple px-3.5 py-2.5 rounded-xl text-xs font-label-md font-bold uppercase tracking-wider flex items-center gap-1.5 shadow-xs cursor-pointer select-none">
                                            <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                                            <span>Reservar</span>
                                        </a>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>

                    <!-- Fallback 2: Libro (Lateral Derecha) -->
                    <div
                        class="coverflow-slot-anchor absolute w-[240px] sm:w-[280px] md:w-[300px] h-[400px] sm:h-[440px] md:h-[460px]"
                        style="top: 50%; left: 50%; transform: translate(-50%, -50%); transform-style: preserve-3d; z-index: 20;"
                        data-slide-index="1"
                    >
                        <div
                            class="coverflow-slot w-full h-full"
                            style="transform-style: preserve-3d; transform: translate3d(335px, 0px, -110px) rotateY(-24deg) scale(0.86); opacity: 0.85;"
                        >
                            <div class="coverflow-card h-full rounded-2xl">
                                <article
                                    draggable="false"
                                    data-product-details="{{ json_encode($fallbackLibro, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}"
                                    class="bg-white dark:bg-[#171024] border-2 border-purple-300 dark:border-purple-500 ring-1 ring-purple-300/30 dark:ring-2 dark:ring-purple-500/20 rounded-2xl p-3 sm:p-5 flex flex-col justify-between hover:border-purple-400 dark:hover:border-purple-400 hover:shadow-md dark:hover:shadow-xl transition-all duration-200 group relative h-full select-none"
                                >
                                    <div>
                                        <div onclick="abrirModalDetalleProducto(this)" class="w-full aspect-square rounded-xl bg-surface-container-low dark:bg-[#140d20] border border-purple-200/60 dark:border-purple-500/30 flex items-center justify-center relative overflow-hidden mb-2.5 sm:mb-4 cursor-pointer group-hover:shadow-inner">
                                            <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/10 to-white/20 dark:via-white/5 dark:to-white/10 pointer-events-none rounded-xl z-20"></div>
                                            <div class="absolute top-2.5 inset-x-2.5 flex items-center justify-between gap-1.5 z-10 pointer-events-none">
                                                <span class="inline-flex items-center gap-1 bg-purple-100 dark:bg-purple-600 text-purple-800 dark:text-white border border-purple-300 dark:border-purple-400/60 text-[9px] sm:text-[10px] font-label-sm font-bold uppercase px-1.5 sm:px-2 py-0.5 rounded-md shadow-2xs dark:shadow-sm">
                                                    <span class="material-symbols-outlined text-[11px] sm:text-[12px]">diamond</span>
                                                    <span class="hidden sm:inline">PIEZA ÚNICA</span>
                                                    <span>1/1</span>
                                                </span>
                                                <span class="inline-flex items-center bg-white/95 dark:bg-[#1b132a]/95 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30 text-[9px] sm:text-[10px] font-label-code font-semibold px-2 py-0.5 rounded-full shadow-2xs">1 disponible</span>
                                            </div>
                                            <span class="material-symbols-outlined text-purple-400 dark:text-purple-300 group-hover:text-primary transition-colors text-[64px]">auto_stories</span>
                                        </div>
                                        <div class="space-y-1">
                                            <h3 onclick="abrirModalDetalleProducto(this)" class="font-bold text-on-surface dark:text-white text-base group-hover:text-primary dark:group-hover:text-purple-300 transition font-quicksand cursor-pointer leading-snug">Cien Años de Soledad</h3>
                                            <p class="font-title-editorial text-xs text-on-surface-variant dark:text-purple-200/70 italic truncate">Gabriel García Márquez · Sudamericana</p>
                                            <button type="button" onclick="abrirModalDetalleProducto(this)" class="pt-1.5 inline-flex items-center gap-1 text-[11px] font-label-code text-primary dark:text-purple-300 hover:underline font-semibold cursor-pointer group-hover:translate-x-0.5 transition-transform">
                                                <span class="material-symbols-outlined text-[14px]">info</span>
                                                <span>Ver detalles &rarr;</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="pt-2.5 sm:pt-4 mt-2.5 sm:mt-4 border-t border-purple-100 dark:border-purple-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <div>
                                            <span class="text-[10px] uppercase font-label-code text-outline dark:text-purple-300/60 block">Precio:</span>
                                            <span class="font-tabular-data font-bold text-on-surface dark:text-white text-base">$32,000 <span class="text-xs font-normal text-on-surface-variant dark:text-purple-200/60">MXN</span></span>
                                        </div>
                                        <a draggable="false" data-requires-auth-reserve="true" href="{{ route('public.libros') }}" class="btn-glass-purple px-3.5 py-2.5 rounded-xl text-xs font-label-md font-bold uppercase tracking-wider flex items-center gap-1.5 shadow-xs cursor-pointer select-none">
                                            <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                                            <span>Reservar</span>
                                        </a>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>

                    <!-- Fallback 3: Memorabilia 1 (Segunda Derecha) -->
                    <div
                        class="coverflow-slot-anchor absolute w-[240px] sm:w-[280px] md:w-[300px] h-[400px] sm:h-[440px] md:h-[460px]"
                        style="top: 50%; left: 50%; transform: translate(-50%, -50%); transform-style: preserve-3d; z-index: 10;"
                        data-slide-index="2"
                    >
                        <div
                            class="coverflow-slot w-full h-full"
                            style="transform-style: preserve-3d; transform: translate3d(610px, 0px, -210px) rotateY(-30deg) scale(0.74); opacity: 0.55;"
                        >
                            <div class="coverflow-card h-full rounded-2xl">
                                <article
                                    draggable="false"
                                    data-product-details="{{ json_encode($fallbackMemorabilia, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}"
                                    class="bg-white dark:bg-[#171024] border-2 border-purple-300 dark:border-purple-500 ring-1 ring-purple-300/30 dark:ring-2 dark:ring-purple-500/20 rounded-2xl p-3 sm:p-5 flex flex-col justify-between hover:border-purple-400 dark:hover:border-purple-400 hover:shadow-md dark:hover:shadow-xl transition-all duration-200 group relative h-full select-none"
                                >
                                    <div>
                                        <div onclick="abrirModalDetalleProducto(this)" class="w-full aspect-square rounded-xl bg-surface-container-low dark:bg-[#140d20] border border-purple-200/60 dark:border-purple-500/30 flex items-center justify-center relative overflow-hidden mb-2.5 sm:mb-4 cursor-pointer group-hover:shadow-inner">
                                            <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/10 to-white/20 dark:via-white/5 dark:to-white/10 pointer-events-none rounded-xl z-20"></div>
                                            <div class="absolute top-2.5 inset-x-2.5 flex items-center justify-between gap-1.5 z-10 pointer-events-none">
                                                <span class="inline-flex items-center gap-1 bg-purple-100 dark:bg-purple-600 text-purple-800 dark:text-white border border-purple-300 dark:border-purple-400/60 text-[9px] sm:text-[10px] font-label-sm font-bold uppercase px-1.5 sm:px-2 py-0.5 rounded-md shadow-2xs dark:shadow-sm">
                                                    <span class="material-symbols-outlined text-[11px] sm:text-[12px]">diamond</span>
                                                    <span class="hidden sm:inline">PIEZA ÚNICA</span>
                                                    <span>1/1</span>
                                                </span>
                                                <span class="inline-flex items-center bg-white/95 dark:bg-[#1b132a]/95 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30 text-[9px] sm:text-[10px] font-label-code font-semibold px-2 py-0.5 rounded-full shadow-2xs">1 disponible</span>
                                            </div>
                                            <span class="material-symbols-outlined text-purple-400 dark:text-purple-300 group-hover:text-primary transition-colors text-[64px]">history_edu</span>
                                        </div>
                                        <div class="space-y-1">
                                            <h3 onclick="abrirModalDetalleProducto(this)" class="font-bold text-on-surface dark:text-white text-base group-hover:text-primary dark:group-hover:text-purple-300 transition font-quicksand cursor-pointer leading-snug">Carta Manuscrita Bellas Artes</h3>
                                            <p class="font-title-editorial text-xs text-on-surface-variant dark:text-purple-200/70 italic truncate">Colección Diplomática · Archivo Notarial</p>
                                            <button type="button" onclick="abrirModalDetalleProducto(this)" class="pt-1.5 inline-flex items-center gap-1 text-[11px] font-label-code text-primary dark:text-purple-300 hover:underline font-semibold cursor-pointer group-hover:translate-x-0.5 transition-transform">
                                                <span class="material-symbols-outlined text-[14px]">info</span>
                                                <span>Ver detalles &rarr;</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="pt-2.5 sm:pt-4 mt-2.5 sm:mt-4 border-t border-purple-100 dark:border-purple-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <div>
                                            <span class="text-[10px] uppercase font-label-code text-outline dark:text-purple-300/60 block">Precio:</span>
                                            <span class="font-tabular-data font-bold text-on-surface dark:text-white text-base">$9,400 <span class="text-xs font-normal text-on-surface-variant dark:text-purple-200/60">MXN</span></span>
                                        </div>
                                        <a draggable="false" data-requires-auth-reserve="true" href="{{ route('public.memorabilia') }}" class="btn-glass-purple px-3.5 py-2.5 rounded-xl text-xs font-label-md font-bold uppercase tracking-wider flex items-center gap-1.5 shadow-xs cursor-pointer select-none">
                                            <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                                            <span>Reservar</span>
                                        </a>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>

                    <!-- Fallback 4: Disco 2 (Posterior Oculto) -->
                    <div
                        class="coverflow-slot-anchor absolute w-[240px] sm:w-[280px] md:w-[300px] h-[400px] sm:h-[440px] md:h-[460px]"
                        style="top: 50%; left: 50%; transform: translate(-50%, -50%); transform-style: preserve-3d; z-index: 1;"
                        data-slide-index="3"
                    >
                        <div
                            class="coverflow-slot w-full h-full"
                            style="transform-style: preserve-3d; transform: translate3d(780px, 0px, -300px) rotateY(-35deg) scale(0.65); opacity: 0;"
                        >
                            <div class="coverflow-card h-full rounded-2xl">
                                <article
                                    draggable="false"
                                    data-product-details="{{ json_encode($fallbackDisco2, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}"
                                    class="bg-white dark:bg-[#171024] border-2 border-purple-300 dark:border-purple-500 ring-1 ring-purple-300/30 dark:ring-2 dark:ring-purple-500/20 rounded-2xl p-3 sm:p-5 flex flex-col justify-between hover:border-purple-400 dark:hover:border-purple-400 hover:shadow-md dark:hover:shadow-xl transition-all duration-200 group relative h-full select-none"
                                >
                                    <div>
                                        <div onclick="abrirModalDetalleProducto(this)" class="w-full aspect-square rounded-xl bg-surface-container-low dark:bg-[#140d20] border border-purple-200/60 dark:border-purple-500/30 flex items-center justify-center relative overflow-hidden mb-2.5 sm:mb-4 cursor-pointer group-hover:shadow-inner">
                                            <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/10 to-white/20 dark:via-white/5 dark:to-white/10 pointer-events-none rounded-xl z-20"></div>
                                            <div class="absolute top-2.5 inset-x-2.5 flex items-center justify-between gap-1.5 z-10 pointer-events-none">
                                                <span class="inline-flex items-center gap-1 bg-purple-100 dark:bg-purple-600 text-purple-800 dark:text-white border border-purple-300 dark:border-purple-400/60 text-[9px] sm:text-[10px] font-label-sm font-bold uppercase px-1.5 sm:px-2 py-0.5 rounded-md shadow-2xs dark:shadow-sm">
                                                    <span class="material-symbols-outlined text-[11px] sm:text-[12px]">diamond</span>
                                                    <span class="hidden sm:inline">PIEZA ÚNICA</span>
                                                    <span>1/1</span>
                                                </span>
                                                <span class="inline-flex items-center bg-white/95 dark:bg-[#1b132a]/95 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30 text-[9px] sm:text-[10px] font-label-code font-semibold px-2 py-0.5 rounded-full shadow-2xs">1 disponible</span>
                                            </div>
                                            <span class="material-symbols-outlined text-purple-400 dark:text-purple-300 group-hover:text-primary transition-colors text-[64px]">album</span>
                                        </div>
                                        <div class="space-y-1">
                                            <h3 onclick="abrirModalDetalleProducto(this)" class="font-bold text-on-surface dark:text-white text-base group-hover:text-primary dark:group-hover:text-purple-300 transition font-quicksand cursor-pointer leading-snug">The Dark Side of the Moon (1st Press UK)</h3>
                                            <p class="font-title-editorial text-xs text-on-surface-variant dark:text-purple-200/70 italic truncate">Pink Floyd · Harvest SHVL 804 (1973)</p>
                                            <button type="button" onclick="abrirModalDetalleProducto(this)" class="pt-1.5 inline-flex items-center gap-1 text-[11px] font-label-code text-primary dark:text-purple-300 hover:underline font-semibold cursor-pointer group-hover:translate-x-0.5 transition-transform">
                                                <span class="material-symbols-outlined text-[14px]">info</span>
                                                <span>Ver detalles &rarr;</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="pt-2.5 sm:pt-4 mt-2.5 sm:mt-4 border-t border-purple-100 dark:border-purple-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <div>
                                            <span class="text-[10px] uppercase font-label-code text-outline dark:text-purple-300/60 block">Precio:</span>
                                            <span class="font-tabular-data font-bold text-on-surface dark:text-white text-base">$24,500 <span class="text-xs font-normal text-on-surface-variant dark:text-purple-200/60">MXN</span></span>
                                        </div>
                                        <a draggable="false" data-requires-auth-reserve="true" href="{{ route('public.vinilos') }}" class="btn-glass-purple px-3.5 py-2.5 rounded-xl text-xs font-label-md font-bold uppercase tracking-wider flex items-center gap-1.5 shadow-xs cursor-pointer select-none">
                                            <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                                            <span>Reservar</span>
                                        </a>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>

                    <!-- Fallback 5: Libro 2 (Segunda Izquierda) -->
                    <div
                        class="coverflow-slot-anchor absolute w-[240px] sm:w-[280px] md:w-[300px] h-[400px] sm:h-[440px] md:h-[460px]"
                        style="top: 50%; left: 50%; transform: translate(-50%, -50%); transform-style: preserve-3d; z-index: 10;"
                        data-slide-index="4"
                    >
                        <div
                            class="coverflow-slot w-full h-full"
                            style="transform-style: preserve-3d; transform: translate3d(-610px, 0px, -210px) rotateY(30deg) scale(0.74); opacity: 0.55;"
                        >
                            <div class="coverflow-card h-full rounded-2xl">
                                <article
                                    draggable="false"
                                    data-product-details="{{ json_encode($fallbackLibro2, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}"
                                    class="bg-white dark:bg-[#171024] border-2 border-purple-300 dark:border-purple-500 ring-1 ring-purple-300/30 dark:ring-2 dark:ring-purple-500/20 rounded-2xl p-3 sm:p-5 flex flex-col justify-between hover:border-purple-400 dark:hover:border-purple-400 hover:shadow-md dark:hover:shadow-xl transition-all duration-200 group relative h-full select-none"
                                >
                                    <div>
                                        <div onclick="abrirModalDetalleProducto(this)" class="w-full aspect-square rounded-xl bg-surface-container-low dark:bg-[#140d20] border border-purple-200/60 dark:border-purple-500/30 flex items-center justify-center relative overflow-hidden mb-2.5 sm:mb-4 cursor-pointer group-hover:shadow-inner">
                                            <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/10 to-white/20 dark:via-white/5 dark:to-white/10 pointer-events-none rounded-xl z-20"></div>
                                            <div class="absolute top-2.5 inset-x-2.5 flex items-center justify-between gap-1.5 z-10 pointer-events-none">
                                                <span class="inline-flex items-center gap-1 bg-purple-100 dark:bg-purple-600 text-purple-800 dark:text-white border border-purple-300 dark:border-purple-400/60 text-[9px] sm:text-[10px] font-label-sm font-bold uppercase px-1.5 sm:px-2 py-0.5 rounded-md shadow-2xs dark:shadow-sm">
                                                    <span class="material-symbols-outlined text-[11px] sm:text-[12px]">diamond</span>
                                                    <span class="hidden sm:inline">PIEZA ÚNICA</span>
                                                    <span>1/1</span>
                                                </span>
                                                <span class="inline-flex items-center bg-white/95 dark:bg-[#1b132a]/95 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30 text-[9px] sm:text-[10px] font-label-code font-semibold px-2 py-0.5 rounded-full shadow-2xs">1 disponible</span>
                                            </div>
                                            <span class="material-symbols-outlined text-purple-400 dark:text-purple-300 group-hover:text-primary transition-colors text-[64px]">auto_stories</span>
                                        </div>
                                        <div class="space-y-1">
                                            <h3 onclick="abrirModalDetalleProducto(this)" class="font-bold text-on-surface dark:text-white text-base group-hover:text-primary dark:group-hover:text-purple-300 transition font-quicksand cursor-pointer leading-snug">El Aleph (1ª Edición Losada)</h3>
                                            <p class="font-title-editorial text-xs text-on-surface-variant dark:text-purple-200/70 italic truncate">Jorge Luis Borges · Editorial Losada (1949)</p>
                                            <button type="button" onclick="abrirModalDetalleProducto(this)" class="pt-1.5 inline-flex items-center gap-1 text-[11px] font-label-code text-primary dark:text-purple-300 hover:underline font-semibold cursor-pointer group-hover:translate-x-0.5 transition-transform">
                                                <span class="material-symbols-outlined text-[14px]">info</span>
                                                <span>Ver detalles &rarr;</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="pt-2.5 sm:pt-4 mt-2.5 sm:mt-4 border-t border-purple-100 dark:border-purple-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <div>
                                            <span class="text-[10px] uppercase font-label-code text-outline dark:text-purple-300/60 block">Precio:</span>
                                            <span class="font-tabular-data font-bold text-on-surface dark:text-white text-base">$28,000 <span class="text-xs font-normal text-on-surface-variant dark:text-purple-200/60">MXN</span></span>
                                        </div>
                                        <a draggable="false" data-requires-auth-reserve="true" href="{{ route('public.libros') }}" class="btn-glass-purple px-3.5 py-2.5 rounded-xl text-xs font-label-md font-bold uppercase tracking-wider flex items-center gap-1.5 shadow-xs cursor-pointer select-none">
                                            <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                                            <span>Reservar</span>
                                        </a>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>

                    <!-- Fallback 6: Memorabilia 2 (Inmediata Izquierda) -->
                    <div
                        class="coverflow-slot-anchor absolute w-[240px] sm:w-[280px] md:w-[300px] h-[400px] sm:h-[440px] md:h-[460px]"
                        style="top: 50%; left: 50%; transform: translate(-50%, -50%); transform-style: preserve-3d; z-index: 20;"
                        data-slide-index="5"
                    >
                        <div
                            class="coverflow-slot w-full h-full"
                            style="transform-style: preserve-3d; transform: translate3d(-335px, 0px, -110px) rotateY(24deg) scale(0.86); opacity: 0.85;"
                        >
                            <div class="coverflow-card h-full rounded-2xl">
                                <article
                                    draggable="false"
                                    data-product-details="{{ json_encode($fallbackMemorabilia2, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}"
                                    class="bg-white dark:bg-[#171024] border-2 border-purple-300 dark:border-purple-500 ring-1 ring-purple-300/30 dark:ring-2 dark:ring-purple-500/20 rounded-2xl p-3 sm:p-5 flex flex-col justify-between hover:border-purple-400 dark:hover:border-purple-400 hover:shadow-md dark:hover:shadow-xl transition-all duration-200 group relative h-full select-none"
                                >
                                    <div>
                                        <div onclick="abrirModalDetalleProducto(this)" class="w-full aspect-square rounded-xl bg-surface-container-low dark:bg-[#140d20] border border-purple-200/60 dark:border-purple-500/30 flex items-center justify-center relative overflow-hidden mb-2.5 sm:mb-4 cursor-pointer group-hover:shadow-inner">
                                            <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/10 to-white/20 dark:via-white/5 dark:to-white/10 pointer-events-none rounded-xl z-20"></div>
                                            <div class="absolute top-2.5 inset-x-2.5 flex items-center justify-between gap-1.5 z-10 pointer-events-none">
                                                <span class="inline-flex items-center gap-1 bg-purple-100 dark:bg-purple-600 text-purple-800 dark:text-white border border-purple-300 dark:border-purple-400/60 text-[9px] sm:text-[10px] font-label-sm font-bold uppercase px-1.5 sm:px-2 py-0.5 rounded-md shadow-2xs dark:shadow-sm">
                                                    <span class="material-symbols-outlined text-[11px] sm:text-[12px]">diamond</span>
                                                    <span class="hidden sm:inline">PIEZA ÚNICA</span>
                                                    <span>1/1</span>
                                                </span>
                                                <span class="inline-flex items-center bg-white/95 dark:bg-[#1b132a]/95 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30 text-[9px] sm:text-[10px] font-label-code font-semibold px-2 py-0.5 rounded-full shadow-2xs">1 disponible</span>
                                            </div>
                                            <span class="material-symbols-outlined text-purple-400 dark:text-purple-300 group-hover:text-primary transition-colors text-[64px]">history_edu</span>
                                        </div>
                                        <div class="space-y-1">
                                            <h3 onclick="abrirModalDetalleProducto(this)" class="font-bold text-on-surface dark:text-white text-base group-hover:text-primary dark:group-hover:text-purple-300 transition font-quicksand cursor-pointer leading-snug">Centenario 50 Pesos Oro (1921)</h3>
                                            <p class="font-title-editorial text-xs text-on-surface-variant dark:text-purple-200/70 italic truncate">Casa de Moneda de México · 1.20565 oz Oro Puro</p>
                                            <button type="button" onclick="abrirModalDetalleProducto(this)" class="pt-1.5 inline-flex items-center gap-1 text-[11px] font-label-code text-primary dark:text-purple-300 hover:underline font-semibold cursor-pointer group-hover:translate-x-0.5 transition-transform">
                                                <span class="material-symbols-outlined text-[14px]">info</span>
                                                <span>Ver detalles &rarr;</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="pt-2.5 sm:pt-4 mt-2.5 sm:mt-4 border-t border-purple-100 dark:border-purple-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <div>
                                            <span class="text-[10px] uppercase font-label-code text-outline dark:text-purple-300/60 block">Precio:</span>
                                            <span class="font-tabular-data font-bold text-on-surface dark:text-white text-base">$65,000 <span class="text-xs font-normal text-on-surface-variant dark:text-purple-200/60">MXN</span></span>
                                        </div>
                                        <a draggable="false" data-requires-auth-reserve="true" href="{{ route('public.memorabilia') }}" class="btn-glass-purple px-3.5 py-2.5 rounded-xl text-xs font-label-md font-bold uppercase tracking-wider flex items-center gap-1.5 shadow-xs cursor-pointer select-none">
                                            <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                                            <span>Reservar</span>
                                        </a>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

    <!-- Barra de Filtros, Ordenación y Estado del Catálogo -->
    <div id="catalogo" class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-purple-200/80 gap-4 mb-8">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-on-surface font-quicksand">
                Catálogo General de Productos
            </h2>
            <p class="text-xs text-primary font-label-code mt-1 font-semibold uppercase tracking-wider">
                Mostrando {{ $coleccionProductos->count() }} productos disponibles en el catálogo activo · Sucursal Central
            </p>
        </div>

        <form method="GET" action="{{ route('public.home') }}" class="flex items-center flex-wrap gap-3 text-xs font-label-md">
            <label for="sort-select" class="text-secondary uppercase tracking-wider font-semibold">Ordenar:</label>
            <select id="sort-select" name="orden" onchange="this.form.submit()" class="bg-surface-container-lowest text-on-surface border border-purple-200/80 rounded-lg py-1.5 px-3 focus:ring-1 focus:ring-primary focus:outline-none shadow-2xs font-medium cursor-pointer">
                <option value="relevancia" @selected(request('orden') === 'relevancia')>Destacados</option>
                <option value="precio_desc" @selected(request('orden') === 'precio_desc')>Precio: Mayor a Menor</option>
                <option value="precio_asc" @selected(request('orden') === 'precio_asc')>Precio: Menor a Mayor</option>
            </select>

            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container border border-purple-200/80 text-secondary font-medium">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Disponibles para Compra Inmediata</span>
            </span>
        </form>
    </div>

    <!-- Cuadrícula Dinámica de Productos del Catálogo -->
    @if ($coleccionProductos->isNotEmpty())
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-5 mb-12">
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
            (function () {
                /**
                 * ==========================================================================
                 * ARCHIVUM · CARRUSEL 3D COVERFLOW INTERACTIVO PURO
                 * Soporte integral: Rueda/Trackpad, Arrastre Mouse/Touch y Clic Lateral
                 * ==========================================================================
                 */
                function setupCoverflowCarousel() {
                    const stage = document.getElementById('vitrina-coverflow-carousel');
                    if (!stage) return;

                    const anchors = Array.from(stage.querySelectorAll('.coverflow-slot-anchor'));
                    const total = anchors.length;
                    if (total === 0) return;

                    let activeIndex = 0;

                    function getSpacingX() {
                        const w = window.innerWidth;
                        if (w < 640) return 120; // Celular compacto y proporcionado
                        if (w < 768) return 160; // Móvil amplio
                        if (w < 1024) return 240; // Tablet amplia (>= 768 ? 240)
                        if (w < 1280) return 280; // Portátil
                        return 320; // Escritorio amplio
                    }

                    function updatePositions() {
                        const spacingX = getSpacingX();

                        anchors.forEach((anchor, idx) => {
                            const slot = anchor.querySelector('.coverflow-slot');
                            const card = anchor.querySelector('.coverflow-card');
                            if (!slot) return;

                            let diff = idx - activeIndex;

                            // Envoltura circular continua
                            if (total > 3) {
                                while (diff > total / 2) diff -= total;
                                while (diff < -total / 2) diff += total;
                            } else if (total === 3) {
                                if (diff === 2) diff = -1;
                                if (diff === -2) diff = 1;
                            } else if (total === 2) {
                                if (diff === -1) diff = 1;
                            }

                            let posX = 0;
                            let posZ = 0;
                            let rotY = 0;
                            let scale = 1;
                            let zIndex = 1;
                            let opacity = 1;
                            let isVisible = true;

                            if (diff === 0) {
                                // Tarjeta central activa: al frente, 100% nítida (Z=0, scale(1), sin blur ni escalado que distorsione texto/imagen)
                                posX = 0;
                                posZ = 0;
                                rotY = 0;
                                scale = 1;
                                zIndex = 30;
                                opacity = 1;

                                anchor.classList.add('is-active');
                                if (card) card.classList.add('coverflow-card-active', 'is-active');
                                anchor.style.pointerEvents = 'auto';
                                anchor.setAttribute('aria-hidden', 'false');
                                slot.style.cursor = 'default';
                                slot.style.transform = 'translate3d(0, 0, 0) scale(1)';
                                slot.style.backfaceVisibility = 'hidden';
                                slot.style.webkitBackfaceVisibility = 'hidden';
                                slot.style.filter = 'none';
                                slot.style.willChange = 'auto';
                            } else if (diff === -1) {
                                // Inmediata izquierda: rotateY(24deg), scale(0.86), z-index 20
                                posX = -spacingX;
                                posZ = -110;
                                rotY = 24;
                                scale = 0.86;
                                zIndex = 20;
                                opacity = 0.85;

                                anchor.classList.remove('is-active');
                                if (card) card.classList.remove('coverflow-card-active', 'is-active');
                                anchor.style.pointerEvents = 'none';
                                anchor.setAttribute('aria-hidden', 'true');
                                slot.style.cursor = 'pointer';
                            } else if (diff === 1) {
                                // Inmediata derecha: rotateY(-24deg), scale(0.86), z-index 20
                                posX = spacingX;
                                posZ = -110;
                                rotY = -24;
                                scale = 0.86;
                                zIndex = 20;
                                opacity = 0.85;

                                anchor.classList.remove('is-active');
                                if (card) card.classList.remove('coverflow-card-active', 'is-active');
                                anchor.style.pointerEvents = 'none';
                                anchor.setAttribute('aria-hidden', 'true');
                                slot.style.cursor = 'pointer';
                            } else if (diff === -2) {
                                // Segunda izquierda: rotateY(30deg), scale(0.74), z-index 10
                                posX = -Math.round(spacingX * 1.82);
                                posZ = -210;
                                rotY = 30;
                                scale = 0.74;
                                zIndex = 10;
                                opacity = 0.55;

                                anchor.classList.remove('is-active');
                                if (card) card.classList.remove('coverflow-card-active', 'is-active');
                                anchor.style.pointerEvents = 'none';
                                anchor.setAttribute('aria-hidden', 'true');
                                slot.style.cursor = 'pointer';
                            } else if (diff === 2) {
                                // Segunda derecha: rotateY(-30deg), scale(0.74), z-index 10
                                posX = Math.round(spacingX * 1.82);
                                posZ = -210;
                                rotY = -30;
                                scale = 0.74;
                                zIndex = 10;
                                opacity = 0.55;

                                anchor.classList.remove('is-active');
                                if (card) card.classList.remove('coverflow-card-active', 'is-active');
                                anchor.style.pointerEvents = 'none';
                                anchor.setAttribute('aria-hidden', 'true');
                                slot.style.cursor = 'pointer';
                            } else {
                                // Lejanas ocultas fuera de la vista
                                const dir = Math.sign(diff) || 1;
                                posX = dir * Math.round(spacingX * 2.4);
                                posZ = -300;
                                rotY = -dir * 35;
                                scale = 0.65;
                                zIndex = 1;
                                opacity = 0;
                                isVisible = false;

                                anchor.classList.remove('is-active');
                                if (card) card.classList.remove('coverflow-card-active', 'is-active');
                                anchor.style.pointerEvents = 'none';
                                anchor.setAttribute('aria-hidden', 'true');
                                slot.style.cursor = 'default';
                            }

                            anchor.style.top = '50%';
                            anchor.style.left = '50%';
                            anchor.style.translate = 'none';
                            anchor.style.transform = 'translate(-50%, -50%)';
                            anchor.style.zIndex = zIndex;
                            if (diff !== 0) {
                                slot.style.transform = `translate3d(${posX}px, 0px, ${posZ}px) rotateY(${rotY}deg) scale(${scale})`;
                                slot.style.backfaceVisibility = 'hidden';
                                slot.style.webkitBackfaceVisibility = 'hidden';
                            }
                            slot.style.opacity = opacity;
                            slot.style.visibility = isVisible ? 'visible' : 'hidden';
                        });
                    }

                    function goTo(index) {
                        activeIndex = (index % total + total) % total;
                        updatePositions();
                    }

                    function next() {
                        goTo(activeIndex + 1);
                    }

                    function prev() {
                        goTo(activeIndex - 1);
                    }

                    // Evitar listeners redundantes
                    if (stage.dataset.coverflowBound === 'true') {
                        updatePositions();
                        return;
                    }
                    stage.dataset.coverflowBound = 'true';

                    // 1. Rueda del Mouse & Trackpad (wheel):
                    // Escucha tanto e.deltaY (rueda vertical normal) como e.deltaX (trackpad)
                    let wheelLocked = false;
                    stage.addEventListener('wheel', function (e) {
                        let delta = 0;
                        const absY = Math.abs(e.deltaY);
                        const absX = Math.abs(e.deltaX);

                        if (absY >= absX && absY > 5) {
                            delta = e.deltaY;
                        } else if (absX > 5) {
                            delta = e.deltaX;
                        }

                        if (Math.abs(delta) > 5) {
                            e.preventDefault();
                            e.stopPropagation();

                            if (wheelLocked) return;
                            wheelLocked = true;

                            if (delta > 0) {
                                next();
                            } else {
                                prev();
                            }

                            setTimeout(function () {
                                wheelLocked = false;
                            }, 220); // Cooldown controlado ~220ms
                        }
                    }, { passive: false });

                    // Prevenir arrastre fantasma de imágenes y enlaces nativo del navegador
                    stage.addEventListener('dragstart', function (e) {
                        e.preventDefault();
                    });

                    // 2. Arrastre con Pointer / Mouse / Touch
                    let isDown = false;
                    let startX = 0;
                    let startY = 0;
                    let currentDeltaX = 0;
                    let dragMoved = false;
                    let activePointerId = null;
                    const DRAG_THRESHOLD = 8;
                    const SWIPE_THRESHOLD = 35;

                    function handleStart(clientX, clientY, pointerId = null) {
                        isDown = true;
                        dragMoved = false;
                        startX = clientX;
                        startY = clientY;
                        currentDeltaX = 0;
                        activePointerId = pointerId;
                        if (pointerId !== null && stage.setPointerCapture) {
                            try {
                                stage.setPointerCapture(pointerId);
                            } catch (err) {}
                        }
                    }

                    function handleMove(clientX, clientY) {
                        if (!isDown) return;
                        currentDeltaX = clientX - startX;
                        const dy = clientY - startY;

                        if (Math.abs(currentDeltaX) > DRAG_THRESHOLD && Math.abs(currentDeltaX) > Math.abs(dy)) {
                            dragMoved = true;
                        }
                    }

                    function handleEnd() {
                        if (!isDown) return;
                        isDown = false;
                        if (activePointerId !== null && stage.releasePointerCapture) {
                            try {
                                stage.releasePointerCapture(activePointerId);
                            } catch (err) {}
                        }
                        activePointerId = null;

                        if (dragMoved) {
                            if (currentDeltaX < -SWIPE_THRESHOLD) {
                                next();
                            } else if (currentDeltaX > SWIPE_THRESHOLD) {
                                prev();
                            }
                        }
                    }

                    stage.addEventListener('pointerdown', function (e) {
                        if (e.button !== undefined && e.button !== 0) return;
                        if (e.target && typeof e.target.closest === 'function' && e.target.closest('button, a, select, input, form')) {
                            return;
                        }
                        handleStart(e.clientX, e.clientY, e.pointerId);
                    });

                    stage.addEventListener('pointermove', function (e) {
                        handleMove(e.clientX, e.clientY);
                    });

                    stage.addEventListener('pointerup', function () {
                        handleEnd();
                    });

                    stage.addEventListener('pointercancel', function () {
                        isDown = false;
                        dragMoved = false;
                        activePointerId = null;
                    });

                    // Soporte táctil móvil adicional
                    stage.addEventListener('touchstart', function (e) {
                        if (e.target && typeof e.target.closest === 'function' && e.target.closest('button, a, select, input, form')) {
                            return;
                        }
                        if (e.touches && e.touches[0]) {
                            handleStart(e.touches[0].clientX, e.touches[0].clientY);
                        }
                    }, { passive: true });

                    stage.addEventListener('touchmove', function (e) {
                        if (e.touches && e.touches[0]) {
                            handleMove(e.touches[0].clientX, e.touches[0].clientY);
                        }
                    }, { passive: true });

                    stage.addEventListener('touchend', function () {
                        handleEnd();
                    });

                    // 3. Clic en el escenario (permite clic normal en botón RESERVAR / Ver detalles de la tarjeta central)
                    stage.addEventListener('click', function (e) {
                        if (dragMoved) {
                            e.preventDefault();
                            e.stopImmediatePropagation();
                            dragMoved = false;
                            return;
                        }

                        const activeAnchor = e.target.closest('.coverflow-slot-anchor.is-active');
                        if (activeAnchor) {
                            return;
                        }

                        const anchor = e.target.closest('.coverflow-slot-anchor');
                        if (anchor) {
                            const slideIdx = parseInt(anchor.dataset.slideIndex, 10);
                            if (!isNaN(slideIdx) && slideIdx !== activeIndex) {
                                e.preventDefault();
                                e.stopPropagation();
                                e.stopImmediatePropagation();
                                goTo(slideIdx);
                                return;
                            }
                        }

                        const rect = stage.getBoundingClientRect();
                        const centerX = rect.left + rect.width / 2;
                        const offsetFromCenter = e.clientX - centerX;
                        if (Math.abs(offsetFromCenter) > 155) {
                            if (offsetFromCenter > 0) {
                                next();
                            } else {
                                prev();
                            }
                        }
                    }, false);

                    // 4. Teclado Accesible
                    stage.addEventListener('keydown', function (e) {
                        if (e.key === 'ArrowLeft') {
                            e.preventDefault();
                            prev();
                        } else if (e.key === 'ArrowRight') {
                            e.preventDefault();
                            next();
                        }
                    });

                    // 5. Redimensionamiento Responsivo
                    let resizeTimer = null;
                    window.addEventListener('resize', function () {
                        if (resizeTimer) clearTimeout(resizeTimer);
                        resizeTimer = setTimeout(updatePositions, 80);
                    });

                    // Geometría inicial
                    updatePositions();
                }

                window.initCoverflowCarousel = setupCoverflowCarousel;

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', setupCoverflowCarousel);
                } else {
                    setupCoverflowCarousel();
                }
                window.addEventListener('load', setupCoverflowCarousel);
            })();

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

                if (typeof window.initCoverflowCarousel === 'function') {
                    window.initCoverflowCarousel();
                }
            });
        </script>
    @endpush

</x-layouts.public>
