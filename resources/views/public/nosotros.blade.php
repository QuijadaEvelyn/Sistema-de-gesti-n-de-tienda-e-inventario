<x-layouts.public title="ARCHIVUM · Conoce más de nosotros">

    @php
        $particulasLluvia = [
            ['tipo' => 'libro', 'left' => '3%', 'duracion' => '11.5s', 'delay' => '-1.2s', 'drift' => '28px', 'sway' => '15px', 'size' => '25px', 'opacidad' => '0.38'],
            ['tipo' => 'vinilo', 'left' => '9%', 'duracion' => '14.5s', 'delay' => '-5.8s', 'drift' => '-24px', 'sway' => '-16px', 'size' => '28px', 'opacidad' => '0.35'],
            ['tipo' => 'coleccionable', 'left' => '15%', 'duracion' => '12.2s', 'delay' => '-3.4s', 'drift' => '30px', 'sway' => '18px', 'size' => '22px', 'opacidad' => '0.40'],
            ['tipo' => 'libro', 'left' => '22%', 'duracion' => '15.0s', 'delay' => '-8.6s', 'drift' => '-20px', 'sway' => '13px', 'size' => '24px', 'opacidad' => '0.32'],
            ['tipo' => 'vinilo', 'left' => '29%', 'duracion' => '13.0s', 'delay' => '-2.1s', 'drift' => '26px', 'sway' => '-14px', 'size' => '29px', 'opacidad' => '0.36'],
            ['tipo' => 'coleccionable', 'left' => '36%', 'duracion' => '16.2s', 'delay' => '-10.5s', 'drift' => '-28px', 'sway' => '16px', 'size' => '23px', 'opacidad' => '0.34'],
            ['tipo' => 'libro', 'left' => '43%', 'duracion' => '12.6s', 'delay' => '-4.3s', 'drift' => '22px', 'sway' => '-12px', 'size' => '26px', 'opacidad' => '0.36'],
            ['tipo' => 'vinilo', 'left' => '51%', 'duracion' => '14.8s', 'delay' => '-7.4s', 'drift' => '-26px', 'sway' => '17px', 'size' => '27px', 'opacidad' => '0.34'],
            ['tipo' => 'coleccionable', 'left' => '59%', 'duracion' => '11.8s', 'delay' => '-1.5s', 'drift' => '24px', 'sway' => '-15px', 'size' => '22px', 'opacidad' => '0.38'],
            ['tipo' => 'libro', 'left' => '67%', 'duracion' => '15.4s', 'delay' => '-9.0s', 'drift' => '-22px', 'sway' => '14px', 'size' => '25px', 'opacidad' => '0.33'],
            ['tipo' => 'vinilo', 'left' => '75%', 'duracion' => '13.4s', 'delay' => '-5.1s', 'drift' => '28px', 'sway' => '-18px', 'size' => '28px', 'opacidad' => '0.37'],
            ['tipo' => 'coleccionable', 'left' => '83%', 'duracion' => '12.9s', 'delay' => '-11.2s', 'drift' => '-25px', 'sway' => '14px', 'size' => '23px', 'opacidad' => '0.36'],
            ['tipo' => 'libro', 'left' => '90%', 'duracion' => '14.1s', 'delay' => '-3.7s', 'drift' => '20px', 'sway' => '-13px', 'size' => '24px', 'opacidad' => '0.35'],
            ['tipo' => 'vinilo', 'left' => '96%', 'duracion' => '13.6s', 'delay' => '-6.9s', 'drift' => '-18px', 'sway' => '12px', 'size' => '26px', 'opacidad' => '0.34'],
        ];

        $metricas = $metricasAcervo ?? [
            'total_productos' => 20,
            'total_piezas_unicas' => 10,
            'total_sedes' => 3,
        ];
    @endphp

    <!-- Escenario Principal: "Conoce más de nosotros" con Lluvia Animada de Elementos Temáticos -->
    <section
        id="conoce-mas-de-nosotros"
        aria-labelledby="titulo-conoce-mas"
        class="archivum-rain-section rounded-3xl bg-gradient-to-b from-surface-container-lowest via-purple-50/80 to-surface-container-low dark:from-[#1b132a] dark:via-[#211636] dark:to-[#171024] border border-purple-200/90 dark:border-purple-500/30 p-6 sm:p-12 lg:p-16 shadow-lg mb-12 text-on-surface dark:text-slate-100"
    >
        <!-- Capa de Animación Tipo "Lluvia" de Elementos Flotantes (Libros, Discos de Vinilo y Piezas de Colección) -->
        <div
            id="archivum-thematic-rain"
            class="archivum-rain-layer"
            aria-hidden="true"
            data-rain-container="true"
        >
            @foreach ($particulasLluvia as $p)
                <div
                    class="archivum-rain-particle"
                    data-rain-item="{{ $p['tipo'] }}"
                    style="left: {{ $p['left'] }}; --rain-duration: {{ $p['duracion'] }}; --rain-delay: {{ $p['delay'] }}; --rain-drift: {{ $p['drift'] }}; --rain-sway: {{ $p['sway'] }}; --rain-opacity: {{ $p['opacidad'] }}; width: {{ $p['size'] }}; height: {{ $p['size'] }};"
                >
                    @if ($p['tipo'] === 'libro')
                        <!-- Silueta de Libro Raro / Incunable Abierto -->
                        <svg viewBox="0 0 24 24" class="w-full h-full text-purple-700 dark:text-purple-300" fill="currentColor">
                            <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                        </svg>
                    @elseif ($p['tipo'] === 'vinilo')
                        <!-- Silueta de Disco de Vinilo 180g con Surcos y Etiqueta Central -->
                        <svg viewBox="0 0 24 24" class="w-full h-full text-indigo-700 dark:text-indigo-300" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 14.5c-2.49 0-4.5-2.01-4.5-4.5S9.51 7.5 12 7.5s4.5 2.01 4.5 4.5-2.01 4.5-4.5 4.5zm0-5.5c-.55 0-1 .45-1 1s.45 1 1 1 1-.45 1-1-.45-1-1-1z"/>
                        </svg>
                    @else
                        <!-- Silueta de Pieza Coleccionable / Joya Patrimonial COA -->
                        <svg viewBox="0 0 24 24" class="w-full h-full text-amber-600 dark:text-amber-300" fill="currentColor">
                            <path d="M19 3H5L2 9l10 12L22 9l-3-6zM9.62 8l1.5-3h1.76l1.5 3H9.62zM11 10v6.68L5.44 10H11zm2 0h5.56L13 16.68V10zm6.26-2h-2.65l-1.5-3h2.65l1.5 3zM6.24 5h2.65l-1.5 3H4.74l1.5-3z"/>
                        </svg>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Encabezado Principal -->
        <div class="relative z-10 max-w-4xl mx-auto text-center space-y-5">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-purple-100/95 dark:bg-purple-900/60 border border-purple-300 dark:border-purple-400/40 text-primary dark:text-amber-50 text-xs font-label-code font-bold uppercase tracking-widest shadow-2xs">
                <span class="material-symbols-outlined text-[16px] text-primary dark:text-amber-50">storefront</span>
                <span>NUESTRA TIENDA &middot; IDENTIDAD ARCHIVUM</span>
            </div>

            <h1 id="titulo-conoce-mas" class="text-3xl sm:text-5xl font-bold text-on-surface dark:text-white font-quicksand tracking-tight leading-tight">
                Conoce más de nosotros
            </h1>

            <p class="font-title-editorial text-lg sm:text-2xl text-primary dark:text-amber-50 italic max-w-3xl mx-auto leading-relaxed">
                «Tu tienda especializada en libros, discos, vinilos y coleccionables: desde novelas actuales, lanzamientos comerciales y ediciones de tus creadores favoritos, hasta piezas exclusivas y de colección.»
            </p>

            <p class="text-sm sm:text-base text-on-surface-variant dark:text-slate-100 leading-relaxed max-w-3xl mx-auto">
                <strong class="text-on-surface dark:text-white font-bold">ARCHIVUM</strong> es una tienda abierta a todo tipo de lectores, melómanos y fans donde conviven el <strong class="text-on-surface dark:text-white font-bold">catálogo comercial y actual</strong> (novelas, literatura juvenil, libros de youtubers y creadores, CDs, vinilos modernos y figuras de cultura pop) con <strong class="text-on-surface dark:text-white font-bold">ediciones especiales y piezas únicas</strong> (primeras ediciones, prensados limitados y artículos exclusivos) distribuidos en nuestras sucursales.
            </p>

            <!-- Acciones Principales -->
            <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                <a href="{{ route('public.home') }}#catalogo" class="px-6 py-3 rounded-xl bg-gradient-to-r from-primary to-purple-600 hover:from-purple-700 hover:to-primary text-white text-xs font-label-md font-bold uppercase tracking-wider shadow-md transition flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">explore</span>
                    <span>Explorar Catálogo General</span>
                </a>
                <a href="{{ route('public.adquisiciones') }}" class="px-6 py-3 rounded-xl bg-white/95 dark:bg-[#281b44] hover:bg-purple-50 dark:hover:bg-purple-900/50 text-primary dark:text-amber-50 border border-purple-300 dark:border-purple-400/40 text-xs font-label-md font-bold uppercase tracking-wider shadow-2xs transition flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">auto_awesome</span>
                    <span>Ver Ediciones Especiales</span>
                </a>
            </div>
        </div>

        <!-- Panel Interactivo de Métricas (Fluid Dashboard) -->
        <div class="relative z-10 mt-8 max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-5">
            <x-ui.metric-card
                title="Sucursales Conectadas"
                :value="$metricas['total_sedes']"
                unit="sucursales"
                subtitle="Red de tiendas y almacén general"
                badge="Activas"
                :badge-pulse="true"
                badge-color="emerald"
                icon="storefront"
                icon-bg="bg-primary-container"
                summary-left="Atención directa"
                summary-right="100% Verificado"
                :action-url="route('public.contacto')"
                action-text="Ver sucursales"
                action-icon="location_on"
            />
            <x-ui.metric-card
                title="Catálogo Disponible"
                :value="$metricas['total_productos']"
                unit="productos"
                subtitle="Lanzamientos actuales y ediciones especiales"
                badge="En Catálogo"
                :badge-pulse="true"
                badge-color="purple"
                icon="inventory_2"
                icon-bg="bg-secondary-container text-on-secondary-container"
                summary-left="Disponibilidad"
                summary-right="En tiempo real"
                :action-url="route('public.home') . '#catalogo'"
                action-text="Explorar catálogo"
                action-icon="explore"
            />
            <x-ui.metric-card
                title="Piezas Únicas (1/1)"
                :value="$metricas['total_piezas_unicas']"
                unit="ejemplares"
                subtitle="Ediciones limitadas y de colección"
                badge="Reserva Activa"
                :badge-pulse="true"
                badge-color="amber"
                icon="workspace_premium"
                icon-bg="bg-tertiary"
                summary-left="Reserva temporal"
                summary-right="5 minutos"
                :action-url="route('public.adquisiciones')"
                action-text="Ver ediciones"
                action-icon="auto_awesome"
            />
        </div>

        <!-- Tarjeta Central de Oferta Comercial y Coleccionismo -->
        <div class="relative z-10 mt-10 max-w-4xl mx-auto rounded-2xl bg-white/95 dark:bg-[#1e1430]/95 backdrop-blur-md border border-purple-200/90 dark:border-purple-500/30 shadow-xl p-6 sm:p-10 space-y-8">
            <div class="flex flex-wrap items-center justify-between gap-2 pb-4 border-b border-purple-100 dark:border-purple-500/20 text-xs font-label-code text-secondary dark:text-amber-50">
                <span class="inline-flex items-center gap-1.5 font-bold text-primary dark:text-amber-50 uppercase">
                    <span class="material-symbols-outlined text-[16px] text-primary dark:text-amber-50">verified_user</span>
                    <span>Catálogo Comercial &amp; Ediciones de Colección</span>
                </span>
                <span class="inline-flex items-center gap-1 text-on-surface-variant dark:text-slate-100">
                    <span class="material-symbols-outlined text-[15px] text-purple-600 dark:text-amber-50">hub</span>
                    <span>{{ $metricas['total_sedes'] }} Sucursales Conectadas &middot; {{ $metricas['total_productos'] }} Productos Activos</span>
                </span>
            </div>

            <div class="text-center space-y-2">
                <h2 class="text-2xl sm:text-3xl font-bold text-on-surface dark:text-white font-quicksand">
                    ¿Qué encontrarás en ARCHIVUM?
                </h2>
                <p class="font-title-editorial text-sm sm:text-base text-on-surface-variant dark:text-amber-50 italic">
                    Un espacio moderno que reúne tus lanzamientos favoritos del día a día junto con ediciones especiales y piezas únicas
                </p>
            </div>

            <!-- Dos mundos que conviven en ARCHIVUM -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                <div class="p-5 rounded-2xl bg-purple-50/80 dark:bg-purple-950/40 border border-purple-200/80 dark:border-purple-500/30 space-y-2">
                    <div class="inline-flex items-center gap-1.5 text-xs font-label-code font-bold uppercase text-primary dark:text-purple-300">
                        <span class="material-symbols-outlined text-[18px]">local_mall</span>
                        <span>Catálogo Comercial y Actual</span>
                    </div>
                    <p class="text-xs sm:text-sm text-on-surface-variant dark:text-slate-100 leading-relaxed">
                        Novelas contemporáneas, literatura juvenil, libros de youtubers y creadores de contenido, álbumes recientes en CD y vinilo moderno, videojuegos y artículos coleccionables de cultura pop y anime con stock múltiple.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-amber-50/80 dark:bg-amber-950/35 border border-amber-200/80 dark:border-amber-500/30 space-y-2">
                    <div class="inline-flex items-center gap-1.5 text-xs font-label-code font-bold uppercase text-amber-700 dark:text-amber-300">
                        <span class="material-symbols-outlined text-[18px]">auto_awesome</span>
                        <span>Ediciones Especiales y Piezas Únicas</span>
                    </div>
                    <p class="text-xs sm:text-sm text-on-surface-variant dark:text-slate-100 leading-relaxed">
                        Primeras ediciones, libros firmados, prensados limitados en vinilo de 180g, monedas, figuras numeradas y coleccionables exclusivos o antiguos verificados para coleccionistas.
                    </p>
                </div>
            </div>

            <!-- Tres Categorías Principales de la Tienda -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 text-left">
                <div class="p-5 rounded-2xl bg-surface-container-low/90 dark:bg-[#271a3f]/90 border border-purple-200/80 dark:border-purple-500/30 space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/50 text-primary dark:text-amber-50 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px]">auto_stories</span>
                    </div>
                    <h3 class="font-bold text-base text-on-surface dark:text-white font-quicksand">Libros para Todos los Gustos</h3>
                    <p class="text-xs text-on-surface-variant dark:text-slate-100 leading-relaxed">
                        Desde novelas actuales, bestsellers juveniles y libros de creadores o youtubers, hasta clásicos literarios, primeras ediciones y ejemplares firmados.
                    </p>
                    <a href="{{ route('public.libros') }}" class="inline-flex items-center gap-1 text-[11px] font-label-code uppercase text-primary dark:text-amber-50 font-bold hover:underline pt-1">
                        <span>Ver Libros</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <div class="p-5 rounded-2xl bg-surface-container-low/90 dark:bg-[#271a3f]/90 border border-purple-200/80 dark:border-purple-500/30 space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-amber-50 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px]">album</span>
                    </div>
                    <h3 class="font-bold text-base text-on-surface dark:text-white font-quicksand">Discos, CDs &amp; Vinilos</h3>
                    <p class="text-xs text-on-surface-variant dark:text-slate-100 leading-relaxed">
                        Álbumes recientes, reediciones modernas en vinilo de 180g, discos compactos (CD) y prensados especiales de tus artistas y bandas favoritas.
                    </p>
                    <a href="{{ route('public.vinilos') }}" class="inline-flex items-center gap-1 text-[11px] font-label-code uppercase text-indigo-700 dark:text-amber-50 font-bold hover:underline pt-1">
                        <span>Ver Discos 3D</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <div class="p-5 rounded-2xl bg-surface-container-low/90 dark:bg-[#271a3f]/90 border border-purple-200/80 dark:border-purple-500/30 space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-50 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px]">history_edu</span>
                    </div>
                    <h3 class="font-bold text-base text-on-surface dark:text-white font-quicksand">Coleccionables &amp; Cultura Pop</h3>
                    <p class="text-xs text-on-surface-variant dark:text-slate-100 leading-relaxed">
                        Figuras de anime y cultura pop, juguetes, videojuegos retro, monedas y piezas exclusivas 1/1 con certificado de autenticidad.
                    </p>
                    <a href="{{ route('public.memorabilia') }}" class="inline-flex items-center gap-1 text-[11px] font-label-code uppercase text-amber-700 dark:text-amber-50 font-bold hover:underline pt-1">
                        <span>Ver Coleccionables</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Pilares Tecnológicos y Reglas del Carrito -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-purple-100 dark:border-purple-500/20 text-xs">
                <div class="p-4 rounded-xl bg-purple-50/70 dark:bg-purple-950/40 border border-purple-200/70 dark:border-purple-500/30 flex items-start gap-3">
                    <span class="material-symbols-outlined text-purple-700 dark:text-amber-50 text-[22px] shrink-0 mt-0.5">shopping_bag</span>
                    <div>
                        <h4 class="font-bold text-on-surface dark:text-white font-quicksand text-sm">Carrito Inteligente y Precio Protegido</h4>
                        <p class="text-on-surface-variant dark:text-slate-100 mt-0.5 leading-relaxed">
                            Los <strong class="text-on-surface dark:text-amber-50 font-bold">productos comunes</strong> se guardan en tu carrito por 7 días respetando el precio al que los agregaste, mientras que las <strong class="text-on-surface dark:text-amber-50 font-bold">Piezas Únicas (1/1)</strong> cuentan con reserva exclusiva de 5 minutos.
                        </p>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-amber-50/70 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-500/30 flex items-start gap-3">
                    <span class="material-symbols-outlined text-amber-600 dark:text-amber-300 text-[22px] shrink-0 mt-0.5">workspace_premium</span>
                    <div>
                        <h4 class="font-bold text-on-surface dark:text-white font-quicksand text-sm">Preventas Anticipadas para Clientes Frecuentes</h4>
                        <p class="text-on-surface-variant dark:text-slate-100 mt-0.5 leading-relaxed">
                            Los clientes que completan <strong class="text-on-surface dark:text-amber-50 font-bold">5 compras</strong> obtienen acceso anticipado VIP para apartar nuevos lanzamientos y ediciones especiales días antes de su venta general.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Red de Sucursales Físicas de ARCHIVUM -->
        <div class="relative z-10 mt-10 max-w-4xl mx-auto">
            <div class="text-center mb-5">
                <span class="text-[11px] font-label-code uppercase tracking-widest text-primary dark:text-amber-50 font-bold">Disponibilidad por Sucursal</span>
                <h2 class="text-xl sm:text-2xl font-bold text-on-surface dark:text-white font-quicksand">Nuestras Tiendas y Sucursales</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @forelse ($sucursales ?? [] as $sede)
                    <div class="bg-white/95 dark:bg-[#1e1430]/95 backdrop-blur-xs rounded-2xl border border-purple-200/80 dark:border-purple-500/30 p-5 shadow-sm flex flex-col justify-between">
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs font-label-code text-primary dark:text-amber-50">
                                <span class="font-bold uppercase">Sede Activa</span>
                                <span class="material-symbols-outlined text-[18px]">storefront</span>
                            </div>
                            <h3 class="font-bold text-base text-on-surface dark:text-white font-quicksand">{{ $sede->nombre }}</h3>
                            <p class="text-xs text-on-surface-variant dark:text-slate-100">{{ $sede->direccion }}</p>
                        </div>
                        <div class="pt-4 mt-4 border-t border-purple-100 dark:border-purple-500/20 flex items-center justify-between text-xs">
                            <span class="font-tabular-data text-outline dark:text-amber-50/80">Tel: {{ $sede->telefono }}</span>
                            <a href="{{ route('public.home', ['sucursal_id' => $sede->id]) }}" class="font-label-code uppercase font-bold text-primary dark:text-amber-50 hover:underline">
                                Ver Catálogo &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white/95 dark:bg-[#1e1430]/95 rounded-2xl border border-purple-200/80 dark:border-purple-500/30 p-5">
                        <h3 class="font-bold text-base text-on-surface dark:text-white font-quicksand">Sucursal Central Bellas Artes</h3>
                        <p class="text-xs text-on-surface-variant dark:text-slate-100 mt-1">Av. Juárez 18, Centro Histórico, CDMX</p>
                    </div>
                    <div class="bg-white/95 dark:bg-[#1e1430]/95 rounded-2xl border border-purple-200/80 dark:border-purple-500/30 p-5">
                        <h3 class="font-bold text-base text-on-surface dark:text-white font-quicksand">Sucursal Coyoacán</h3>
                        <p class="text-xs text-on-surface-variant dark:text-slate-100 mt-1">Jardín Centenario 14, Villa Coyoacán, CDMX</p>
                    </div>
                    <div class="bg-white/95 dark:bg-[#1e1430]/95 rounded-2xl border border-purple-200/80 dark:border-purple-500/30 p-5">
                        <h3 class="font-bold text-base text-on-surface dark:text-white font-quicksand">Almacén General</h3>
                        <p class="text-xs text-on-surface-variant dark:text-slate-100 mt-1">Calz. Vallejo 1020, Azcapotzalco, CDMX</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const rainLayer = document.getElementById('archivum-thematic-rain');
                if (rainLayer && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    const plantillas = Array.from(rainLayer.querySelectorAll('.archivum-rain-particle'));
                    if (plantillas.length > 0 && plantillas.length <= 20) {
                        for (let i = 0; i < 10; i++) {
                            const clon = plantillas[i % plantillas.length].cloneNode(true);
                            const leftPct = (5 + (i * 9.5)) % 96;
                            const dur = (10.2 + (i % 5) * 1.3).toFixed(1) + 's';
                            const delay = (-1.6 * (i + 1)).toFixed(1) + 's';
                            const drift = ((i % 2 === 0 ? 1 : -1) * (20 + (i * 3))).toFixed(0) + 'px';
                            clon.style.left = leftPct.toFixed(1) + '%';
                            clon.style.setProperty('--rain-duration', dur);
                            clon.style.setProperty('--rain-delay', delay);
                            clon.style.setProperty('--rain-drift', drift);
                            rainLayer.appendChild(clon);
                        }
                    }
                }
            });
        </script>
    @endpush

</x-layouts.public>

