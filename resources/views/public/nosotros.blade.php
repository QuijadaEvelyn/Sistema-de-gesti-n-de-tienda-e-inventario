<x-layouts.public title="ARCHIVUM · Conoce más de nosotros &amp; Manifiesto Curatorial">

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

        <!-- Encabezado y Manifiesto Institucional -->
        <div class="relative z-10 max-w-4xl mx-auto text-center space-y-5">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-purple-100/95 dark:bg-purple-900/60 border border-purple-300 dark:border-purple-400/40 text-primary dark:text-amber-50 text-xs font-label-code font-bold uppercase tracking-widest shadow-2xs">
                <span class="material-symbols-outlined text-[16px] text-primary dark:text-amber-50">museum</span>
                <span>Manifiesto Institucional &middot; Identidad ARCHIVUM</span>
            </div>

            <h1 id="titulo-conoce-mas" class="text-3xl sm:text-5xl font-bold text-on-surface dark:text-white font-quicksand tracking-tight leading-tight">
                Conoce más de nosotros
            </h1>

            <p class="font-title-editorial text-lg sm:text-2xl text-primary dark:text-amber-50 italic max-w-3xl mx-auto leading-relaxed">
                «El santuario donde la bibliofilia, la fonografía analógica y la memoria histórica convergen bajo custodia certificada.»
            </p>

            <p class="text-sm sm:text-base text-on-surface-variant dark:text-slate-100 leading-relaxed max-w-3xl mx-auto">
                <strong class="text-on-surface dark:text-white font-bold">ARCHIVUM</strong> es un sistema curado y especializado en el resguardo climatizado, la exhibición interactiva y el <strong class="text-on-surface dark:text-white font-bold">comercio híbrido de libros raros e incunables, vinilos de primer tiraje y piezas de colección únicas</strong> distribuidas en múltiples sedes. Nuestra arquitectura tecnológica une el rigor de un archivo museográfico con una experiencia de adquisición transparente, trazable y protegida contra sobreventas.
            </p>

            <!-- Acciones Principales -->
            <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                <a href="{{ route('public.home') }}#catalogo" class="px-6 py-3 rounded-xl bg-gradient-to-r from-primary to-purple-600 hover:from-purple-700 hover:to-primary text-white text-xs font-label-md font-bold uppercase tracking-wider shadow-md transition flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">explore</span>
                    <span>Explorar Catálogo General</span>
                </a>
                <a href="{{ route('public.adquisiciones') }}" class="px-6 py-3 rounded-xl bg-white/95 dark:bg-[#281b44] hover:bg-purple-50 dark:hover:bg-purple-900/50 text-primary dark:text-amber-50 border border-purple-300 dark:border-purple-400/40 text-xs font-label-md font-bold uppercase tracking-wider shadow-2xs transition flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">auto_awesome</span>
                    <span>Ver Adquisiciones Selectas</span>
                </a>
            </div>
        </div>

        <!-- Tarjeta Central Estilo Vitrina Curatorial -->
        <div class="relative z-10 mt-10 max-w-4xl mx-auto rounded-2xl bg-white/95 dark:bg-[#1e1430]/95 backdrop-blur-md border border-purple-200/90 dark:border-purple-500/30 shadow-xl p-6 sm:p-10 space-y-8">
            <div class="flex flex-wrap items-center justify-between gap-2 pb-4 border-b border-purple-100 dark:border-purple-500/20 text-xs font-label-code text-secondary dark:text-amber-50">
                <span class="inline-flex items-center gap-1.5 font-bold text-primary dark:text-amber-50 uppercase">
                    <span class="material-symbols-outlined text-[16px] text-primary dark:text-amber-50">verified_user</span>
                    <span>Arquitectura de Resguardo &amp; Comercio Híbrido</span>
                </span>
                <span class="inline-flex items-center gap-1 text-on-surface-variant dark:text-slate-100">
                    <span class="material-symbols-outlined text-[15px] text-purple-600 dark:text-amber-50">hub</span>
                    <span>{{ $metricas['total_sedes'] }} Sedes Interconectadas &middot; {{ $metricas['total_productos'] }} Lotes Activos</span>
                </span>
            </div>

            <div class="text-center space-y-2">
                <h2 class="text-2xl sm:text-3xl font-bold text-on-surface dark:text-white font-quicksand">
                    Nuestra Esencia Curatorial
                </h2>
                <p class="font-title-editorial text-sm sm:text-base text-on-surface-variant dark:text-amber-50 italic">
                    Autenticidad pericial, conservación libre de ácido y exclusividad garantizada en cada ejemplar
                </p>
            </div>

            <!-- Tres Ejes Temáticos del Acervo -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 text-left">
                <div class="p-5 rounded-2xl bg-surface-container-low/90 dark:bg-[#271a3f]/90 border border-purple-200/80 dark:border-purple-500/30 space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/50 text-primary dark:text-amber-50 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px]">auto_stories</span>
                    </div>
                    <h3 class="font-bold text-base text-on-surface dark:text-white font-quicksand">Libros Raros &amp; Incunables</h3>
                    <p class="text-xs text-on-surface-variant dark:text-slate-100 leading-relaxed">
                        Primeras ediciones hispanoamericanas, pruebas de galera, ejemplares autografiados y encuadernaciones históricas con dictamen bibliográfico.
                    </p>
                    <a href="{{ route('public.libros') }}" class="inline-flex items-center gap-1 text-[11px] font-label-code uppercase text-primary dark:text-amber-50 font-bold hover:underline pt-1">
                        <span>Ir a Sala Bibliográfica</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <div class="p-5 rounded-2xl bg-surface-container-low/90 dark:bg-[#271a3f]/90 border border-purple-200/80 dark:border-purple-500/30 space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-amber-50 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px]">album</span>
                    </div>
                    <h3 class="font-bold text-base text-on-surface dark:text-white font-quicksand">Vinilos de Primer Tiraje</h3>
                    <p class="text-xs text-on-surface-variant dark:text-slate-100 leading-relaxed">
                        Prensados originales en 180 gramos, matrices analógicas mono y sellos legendarios graduados bajo el estándar internacional <em>Goldmine</em>.
                    </p>
                    <a href="{{ route('public.vinilos') }}" class="inline-flex items-center gap-1 text-[11px] font-label-code uppercase text-indigo-700 dark:text-amber-50 font-bold hover:underline pt-1">
                        <span>Ir a Sala Fonográfica 3D</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <div class="p-5 rounded-2xl bg-surface-container-low/90 dark:bg-[#271a3f]/90 border border-purple-200/80 dark:border-purple-500/30 space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-50 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px]">history_edu</span>
                    </div>
                    <h3 class="font-bold text-base text-on-surface dark:text-white font-quicksand">Memorabilia &amp; Piezas 1/1</h3>
                    <p class="text-xs text-on-surface-variant dark:text-slate-100 leading-relaxed">
                        Manuscritos autógrafos, partituras de época, medallas históricas y fotografía en plata sobre gelatina con Certificado de Autenticidad (COA) notariado.
                    </p>
                    <a href="{{ route('public.memorabilia') }}" class="inline-flex items-center gap-1 text-[11px] font-label-code uppercase text-amber-700 dark:text-amber-50 font-bold hover:underline pt-1">
                        <span>Ir a Archivo Documental</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Pilares Tecnológicos y Reglas de Custodia -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-purple-100 dark:border-purple-500/20 text-xs">
                <div class="p-4 rounded-xl bg-purple-50/70 dark:bg-purple-950/40 border border-purple-200/70 dark:border-purple-500/30 flex items-start gap-3">
                    <span class="material-symbols-outlined text-purple-700 dark:text-amber-50 text-[22px] shrink-0 mt-0.5">timer</span>
                    <div>
                        <h4 class="font-bold text-on-surface dark:text-white font-quicksand text-sm">Reserva Exclusiva en Bóveda (5 Minutos)</h4>
                        <p class="text-on-surface-variant dark:text-slate-100 mt-0.5 leading-relaxed">
                            Cuando un coleccionista añade una <strong class="text-on-surface dark:text-amber-50 font-bold">Pieza Única (1/1)</strong> a su carrito, el sistema bloquea la pieza de forma inmediata durante 5 minutos para evitar sobreventas concurrentes.
                        </p>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-amber-50/70 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-500/30 flex items-start gap-3">
                    <span class="material-symbols-outlined text-amber-600 dark:text-amber-300 text-[22px] shrink-0 mt-0.5">workspace_premium</span>
                    <div>
                        <h4 class="font-bold text-on-surface dark:text-white font-quicksand text-sm">Preventas Anticipadas para Clientes Frecuentes</h4>
                        <p class="text-on-surface-variant dark:text-slate-100 mt-0.5 leading-relaxed">
                            Los coleccionistas que alcanzan <strong class="text-on-surface dark:text-amber-50 font-bold">5 compras completadas</strong> obtienen acceso anticipado VIP para apartar lanzamientos especiales días antes de su apertura al público general.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Red de Sedes y Bóvedas Físicas de ARCHIVUM -->
        <div class="relative z-10 mt-10 max-w-4xl mx-auto">
            <div class="text-center mb-5">
                <span class="text-[11px] font-label-code uppercase tracking-widest text-primary dark:text-amber-50 font-bold">Distribución Multicéntrica</span>
                <h2 class="text-xl sm:text-2xl font-bold text-on-surface dark:text-white font-quicksand">Nuestras Sedes y Bóvedas de Resguardo</h2>
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
                                Ver Acervo &rarr;
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

