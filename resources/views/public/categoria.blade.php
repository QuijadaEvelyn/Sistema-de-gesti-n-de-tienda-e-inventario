<x-layouts.public :title="'ARCHIVUM · ' . $meta['titulo']">

    <!-- Encabezado Curatorial de la Categoría -->
    <section class="relative rounded-2xl overflow-hidden bg-gradient-to-r from-purple-900 to-indigo-950 text-white p-8 sm:p-10 mb-8 shadow-lg border border-purple-800/40">
        <div class="relative z-10 max-w-3xl space-y-3">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/20 border border-purple-300/30 text-purple-200 text-xs font-label-md tracking-wider">
                <span class="material-symbols-outlined text-[15px] text-purple-300">{{ $meta['icono'] }}</span>
                <span>{{ $meta['badge'] }}</span>
            </span>
            <h1 class="text-3xl sm:text-4xl font-bold font-quicksand leading-tight text-white">
                {{ $meta['titulo'] }}
            </h1>
            <p class="font-title-editorial text-purple-100/90 text-base sm:text-lg leading-relaxed">
                «{{ $meta['descripcion'] }}»
            </p>
        </div>
    </section>

    <!-- Barra de Filtros, Búsqueda en Categoría y Selector de Orden -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-purple-200/80 gap-4 mb-8">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-on-surface font-quicksand">
                {{ $meta['subtitulo'] }}
            </h2>
            <p class="text-xs text-primary font-label-code mt-1 font-semibold uppercase tracking-wider">
                Mostrando {{ $productos->count() }} {{ $productos->count() === 1 ? 'pieza autenticada' : 'piezas autenticadas' }} en esta sección
            </p>
        </div>

        <form method="GET" class="flex items-center flex-wrap gap-3 text-xs font-label-md">
            <input
                type="search"
                name="q"
                value="{{ request('q') }}"
                placeholder="Filtrar en esta sección..."
                class="bg-white border border-purple-200/80 rounded-xl px-3.5 py-2 text-xs text-on-surface focus:outline-none focus:border-primary"
            />
            <select name="orden" onchange="this.form.submit()" class="bg-white text-on-surface border border-purple-200/80 rounded-xl py-2 px-3 focus:ring-1 focus:ring-primary focus:outline-none shadow-2xs font-medium cursor-pointer">
                <option value="relevancia" @selected(request('orden') === 'relevancia')>Ordenar: Relevancia Curatorial</option>
                <option value="precio_desc" @selected(request('orden') === 'precio_desc')>Precio: Mayor a Menor</option>
                <option value="precio_asc" @selected(request('orden') === 'precio_asc')>Precio: Menor a Mayor</option>
            </select>
        </form>
    </div>

    <!-- Cuadrícula Exclusiva de la Categoría -->
    @if ($productos->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
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

</x-layouts.public>

