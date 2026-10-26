<x-layouts.public title="ARCHIVUM · Galería de Colecciones &amp; Bóveda Pública">
    
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
            <div class="flex flex-wrap items-center gap-4 text-xs font-label-md">
                <a href="#catalogo" class="bg-white text-primary hover:bg-purple-50 px-6 py-3 rounded-xl font-bold uppercase tracking-wider shadow-sm transition flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">explore</span>
                    <span>Explorar Fondos Activos</span>
                </a>
                <a href="#sucursales" class="bg-purple-800/60 hover:bg-purple-800/90 text-white border border-purple-400/30 px-5 py-3 rounded-xl font-semibold uppercase tracking-wider transition flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">storefront</span>
                    <span>Visitar Galerías Físicas</span>
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

    <!-- Barra de Filtros, Ordenación y Estado del Catálogo -->
    <div id="catalogo" class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-purple-200/80 gap-4 mb-8">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-on-surface font-quicksand">
                Adquisiciones Selectas &amp; Lotes Recientes
            </h2>
            <p class="text-xs text-primary font-label-code mt-1 font-semibold uppercase tracking-wider">
                Mostrando 8 piezas certificadas archivadas en catálogo activo · Sede Central
            </p>
        </div>

        <!-- Controles de Filtro Rápido y Ordenación -->
        <div class="flex items-center flex-wrap gap-3 text-xs font-label-md">
            <label for="sort-select" class="text-secondary uppercase tracking-wider font-semibold">Ordenar:</label>
            <select id="sort-select" class="bg-white text-on-surface border border-purple-200/80 rounded-lg py-1.5 px-3 focus:ring-1 focus:ring-primary focus:outline-none shadow-2xs font-medium cursor-pointer">
                <option>Relevancia Curatorial</option>
                <option>Precio: Mayor a Menor</option>
                <option>Precio: Menor a Mayor</option>
                <option>Graduación (Mint / Sellado)</option>
                <option>Antigüedad de Prensado</option>
            </select>

            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container border border-purple-200/80 text-secondary font-medium">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Disponibles para Examen Físico</span>
            </span>
        </div>
    </div>

    <!-- Rejilla de Coleccionables (8 Fichas Curatoriales) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Tarjeta 1: Vinilo Mono 1959 -->
        <article class="bg-white border border-purple-200/80 rounded-2xl p-4 flex flex-col justify-between hover:border-primary hover:shadow-lg transition-all duration-200 group shadow-xs">
            <div>
                <!-- Contenedor de Carátula con Badge -->
                <div class="w-full aspect-square rounded-xl bg-surface-container-low border border-purple-200/60 flex items-center justify-center relative overflow-hidden mb-4 group-hover:bg-purple-50 transition-colors">
                    <span class="absolute top-2.5 left-2.5 bg-primary-container text-white text-[10px] font-label-sm font-semibold uppercase px-2 py-0.5 rounded shadow-2xs">
                        VINILO LP 180G
                    </span>
                    <span class="material-symbols-outlined text-purple-300 group-hover:text-primary transition-colors text-[64px]">album</span>
                </div>

                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-label-code text-[11px] text-outline">SKU: #VIN-1959-COL</span>
                        <x-ui.badge-grading type="mint" text="MINT (M)" />
                    </div>
                    <h3 class="font-bold text-on-surface text-base group-hover:text-primary transition font-quicksand">Kind of Blue (Mono 1st Press)</h3>
                    <p class="font-title-editorial text-xs text-on-surface-variant italic">Miles Davis · Columbia Records CL 1355</p>
                    <p class="text-xs text-secondary line-clamp-2 pt-1 font-normal">
                        Matriz original con etiqueta seis ojos («6-eye»). Sonido analógico sin reprocesar con funda interior original.
                    </p>
                </div>
            </div>

            <div class="pt-4 mt-3 border-t border-purple-100 flex items-center justify-between">
                <div>
                    <span class="text-[10px] uppercase font-label-code text-outline block">Precio Tasado:</span>
                    <span class="font-tabular-data font-bold text-on-surface text-base">$18,500 <span class="text-xs font-normal text-on-surface-variant">MXN</span></span>
                </div>
                <button type="button" class="p-2.5 rounded-xl bg-surface-container-low hover:bg-primary-container hover:text-white text-primary border border-purple-200 transition shadow-2xs" title="Reservar en Bóveda">
                    <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
                </button>
            </div>
        </article>

        <!-- Tarjeta 2: Libro Raro 1967 -->
        <article class="bg-white border border-purple-200/80 rounded-2xl p-4 flex flex-col justify-between hover:border-primary hover:shadow-lg transition-all duration-200 group shadow-xs">
            <div>
                <div class="w-full aspect-square rounded-xl bg-surface-container-low border border-purple-200/60 flex items-center justify-center relative overflow-hidden mb-4 group-hover:bg-purple-50 transition-colors">
                    <span class="absolute top-2.5 left-2.5 bg-primary-container text-white text-[10px] font-label-sm font-semibold uppercase px-2 py-0.5 rounded shadow-2xs">
                        PRIMERA EDICIÓN
                    </span>
                    <span class="material-symbols-outlined text-purple-300 group-hover:text-primary transition-colors text-[64px]">auto_stories</span>
                </div>

                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-label-code text-[11px] text-outline">SKU: #LIB-1967-SUD</span>
                        <x-ui.badge-grading type="rare" text="VERY FINE (VF)" />
                    </div>
                    <h3 class="font-bold text-on-surface text-base group-hover:text-primary transition font-quicksand">Cien Años de Soledad</h3>
                    <p class="font-title-editorial text-xs text-on-surface-variant italic">Gabriel García Márquez · Sudamericana</p>
                    <p class="text-xs text-secondary line-clamp-2 pt-1 font-normal">
                        Tiraje inicial de Buenos Aires (Mayo 1967). Cubierta con barco en la selva diseñada por Iris Alba.
                    </p>
                </div>
            </div>

            <div class="pt-4 mt-3 border-t border-purple-100 flex items-center justify-between">
                <div>
                    <span class="text-[10px] uppercase font-label-code text-outline block">Precio Tasado:</span>
                    <span class="font-tabular-data font-bold text-on-surface text-base">$32,000 <span class="text-xs font-normal text-on-surface-variant">MXN</span></span>
                </div>
                <button type="button" class="p-2.5 rounded-xl bg-surface-container-low hover:bg-primary-container hover:text-white text-primary border border-purple-200 transition shadow-2xs" title="Reservar en Bóveda">
                    <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
                </button>
            </div>
        </article>

        <!-- Tarjeta 3: Memorabilia Notariada -->
        <article class="bg-white border border-purple-200/80 rounded-2xl p-4 flex flex-col justify-between hover:border-primary hover:shadow-lg transition-all duration-200 group shadow-xs">
            <div>
                <div class="w-full aspect-square rounded-xl bg-surface-container-low border border-purple-200/60 flex items-center justify-center relative overflow-hidden mb-4 group-hover:bg-purple-50 transition-colors">
                    <span class="absolute top-2.5 left-2.5 bg-tertiary text-white text-[10px] font-label-sm font-semibold uppercase px-2 py-0.5 rounded shadow-2xs">
                        MEMORABILIA
                    </span>
                    <span class="material-symbols-outlined text-purple-300 group-hover:text-primary transition-colors text-[64px]">history_edu</span>
                </div>

                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-label-code text-[11px] text-outline">SKU: #MEM-1928-AUT</span>
                        <x-ui.badge-grading type="coa" text="COA NOTARIADO" />
                    </div>
                    <h3 class="font-bold text-on-surface text-base group-hover:text-primary transition font-quicksand">Carta Manuscrita Bellas Artes</h3>
                    <p class="font-title-editorial text-xs text-on-surface-variant italic">Colección Diplomática · Archivo Notarial</p>
                    <p class="text-xs text-secondary line-clamp-2 pt-1 font-normal">
                        Pluma estilográfica con tinta ferrogálica original. Certificado notarial e informe pericial caligráfico.
                    </p>
                </div>
            </div>

            <div class="pt-4 mt-3 border-t border-purple-100 flex items-center justify-between">
                <div>
                    <span class="text-[10px] uppercase font-label-code text-outline block">Precio Tasado:</span>
                    <span class="font-tabular-data font-bold text-on-surface text-base">$9,400 <span class="text-xs font-normal text-on-surface-variant">MXN</span></span>
                </div>
                <button type="button" class="p-2.5 rounded-xl bg-surface-container-low hover:bg-primary-container hover:text-white text-primary border border-purple-200 transition shadow-2xs" title="Reservar en Bóveda">
                    <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
                </button>
            </div>
        </article>

        <!-- Tarjeta 4: Vinilo Jazz Histórico 1957 -->
        <article class="bg-white border border-purple-200/80 rounded-2xl p-4 flex flex-col justify-between hover:border-primary hover:shadow-lg transition-all duration-200 group shadow-xs">
            <div>
                <div class="w-full aspect-square rounded-xl bg-surface-container-low border border-purple-200/60 flex items-center justify-center relative overflow-hidden mb-4 group-hover:bg-purple-50 transition-colors">
                    <span class="absolute top-2.5 left-2.5 bg-primary-container text-white text-[10px] font-label-sm font-semibold uppercase px-2 py-0.5 rounded shadow-2xs">
                        BLUE NOTE RVG
                    </span>
                    <span class="material-symbols-outlined text-purple-300 group-hover:text-primary transition-colors text-[64px]">album</span>
                </div>

                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-label-code text-[11px] text-outline">SKU: #VIN-1957-BN1577</span>
                        <x-ui.badge-grading type="mint" text="NEAR MINT (NM)" />
                    </div>
                    <h3 class="font-bold text-on-surface text-base group-hover:text-primary transition font-quicksand">Blue Train (Plastylite Press)</h3>
                    <p class="font-title-editorial text-xs text-on-surface-variant italic">John Coltrane · Blue Note BLP 1577</p>
                    <p class="text-xs text-secondary line-clamp-2 pt-1 font-normal">
                        Grabado por Rudy Van Gelder con sello de oído «ear» en el surco muerto. Etiqueta 47 West 63rd NYC.
                    </p>
                </div>
            </div>

            <div class="pt-4 mt-3 border-t border-purple-100 flex items-center justify-between">
                <div>
                    <span class="text-[10px] uppercase font-label-code text-outline block">Precio Tasado:</span>
                    <span class="font-tabular-data font-bold text-on-surface text-base">$27,500 <span class="text-xs font-normal text-on-surface-variant">MXN</span></span>
                </div>
                <button type="button" class="p-2.5 rounded-xl bg-surface-container-low hover:bg-primary-container hover:text-white text-primary border border-purple-200 transition shadow-2xs" title="Reservar en Bóveda">
                    <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
                </button>
            </div>
        </article>

    </div>

</x-layouts.public>
