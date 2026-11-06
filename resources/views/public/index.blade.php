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
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
        <a href="{{ route('public.libros') }}" class="p-5 rounded-2xl bg-white border border-purple-200/80 hover:border-primary hover:shadow-md transition group">
            <div class="flex items-center justify-between mb-2">
                <span class="material-symbols-outlined text-primary text-[28px]">auto_stories</span>
                <span class="text-[10px] font-label-code uppercase text-outline group-hover:text-primary">Explorar Sala &rarr;</span>
            </div>
            <h2 class="font-bold text-base text-on-surface font-quicksand group-hover:text-primary">Libros Raros &amp; Incunables</h2>
            <p class="text-xs text-on-surface-variant mt-1">Primeras ediciones, ejemplares autografiados e incunables modernos.</p>
        </a>

        <a href="{{ route('public.vinilos') }}" class="p-5 rounded-2xl bg-white border border-purple-200/80 hover:border-primary hover:shadow-md transition group">
            <div class="flex items-center justify-between mb-2">
                <span class="material-symbols-outlined text-primary text-[28px]">album</span>
                <span class="text-[10px] font-label-code uppercase text-outline group-hover:text-primary">Explorar Sala &rarr;</span>
            </div>
            <h2 class="font-bold text-base text-on-surface font-quicksand group-hover:text-primary">Discos &amp; Vinilos Históricos</h2>
            <p class="text-xs text-on-surface-variant mt-1">Prensados originales 180g, matrices mono y sellos Blue Note / Columbia.</p>
        </a>

        <a href="{{ route('public.memorabilia') }}" class="p-5 rounded-2xl bg-white border border-purple-200/80 hover:border-primary hover:shadow-md transition group">
            <div class="flex items-center justify-between mb-2">
                <span class="material-symbols-outlined text-primary text-[28px]">history_edu</span>
                <span class="text-[10px] font-label-code uppercase text-outline group-hover:text-primary">Explorar Sala &rarr;</span>
            </div>
            <h2 class="font-bold text-base text-on-surface font-quicksand group-hover:text-primary">Memorabilia &amp; Archivo</h2>
            <p class="text-xs text-on-surface-variant mt-1">Manuscritos autógrafos, partituras y fotografía en plata sobre gelatina.</p>
        </a>

        <a href="{{ route('public.adquisiciones') }}" class="p-5 rounded-2xl bg-white border border-purple-200/80 hover:border-primary hover:shadow-md transition group">
            <div class="flex items-center justify-between mb-2">
                <span class="material-symbols-outlined text-primary text-[28px]">auto_awesome</span>
                <span class="text-[10px] font-label-code uppercase text-outline group-hover:text-primary">Explorar Sala &rarr;</span>
            </div>
            <h2 class="font-bold text-base text-on-surface font-quicksand group-hover:text-primary">Adquisiciones Selectas</h2>
            <p class="text-xs text-on-surface-variant mt-1">Piezas cumbre con Certificado de Autenticidad (COA) notariado.</p>
        </a>
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
            <select id="sort-select" name="orden" onchange="this.form.submit()" class="bg-white text-on-surface border border-purple-200/80 rounded-lg py-1.5 px-3 focus:ring-1 focus:ring-primary focus:outline-none shadow-2xs font-medium cursor-pointer">
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
        <div class="bg-white border border-purple-200/80 rounded-2xl p-10 text-center space-y-2 mb-12">
            <p class="text-base font-bold text-on-surface font-quicksand">El catálogo está listo para poblarse</p>
            <p class="text-xs text-on-surface-variant">Ejecuta <code class="font-label-code bg-surface-container px-2 py-0.5 rounded">php artisan db:seed</code> para cargar las piezas de ejemplo.</p>
        </div>
    @endif

</x-layouts.public>
