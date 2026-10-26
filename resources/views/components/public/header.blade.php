@props([
    'activeBranch' => 'Sucursal Central Bellas Artes',
    'cartCount' => 3
])

<header class="sticky top-0 z-40 border-b border-purple-200/80 bg-white/95 backdrop-blur-md shadow-xs text-on-surface" data-purpose="global-header">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Fila Superior: Marca, Búsqueda, Sucursal, Acciones de Usuario -->
        <div class="flex items-center justify-between h-20 gap-4">
            
            <!-- Logotipo y Monograma de Archivo -->
            <a href="{{ route('public.home') ?? url('/') }}" class="flex items-center gap-3 shrink-0 group focus:outline-none" aria-label="Ir al inicio de Archivum">
                <div class="w-11 h-11 rounded-xl bg-primary-container flex items-center justify-center text-white border border-purple-300/40 shadow-sm transition-transform group-hover:scale-105">
                    <!-- Isotipo Geométrico de Bóveda / Vault -->
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
                        <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                        <polyline points="2 17 12 22 22 17"></polyline>
                        <polyline points="2 12 12 17 22 12"></polyline>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-xl font-bold tracking-wider font-quicksand uppercase text-on-surface">Archivum</span>
                    <span class="font-label-code text-primary uppercase tracking-widest text-[10px]">Bibliotheca &amp; Discos</span>
                </div>
            </a>

            <!-- Barra de Búsqueda Global Omnicanal -->
            <div class="flex-1 max-w-2xl hidden md:block">
                <form action="#" method="GET" class="relative w-full">
                    <input 
                        type="search" 
                        name="q"
                        placeholder="Buscar libros raros, vinilos 180g, primeras ediciones, memorabilia o SKU..." 
                        class="w-full bg-surface-container-low/70 border border-purple-200/80 text-sm rounded-xl pl-11 pr-4 py-2.5 text-on-surface placeholder:text-outline focus:border-primary focus:bg-white focus:ring-1 focus:ring-primary focus:outline-none transition shadow-2xs" 
                    />
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-primary">
                        <span class="material-symbols-outlined text-[20px]">search</span>
                    </div>
                </form>
            </div>

            <!-- Controles Utilitarios: Sucursal, Tema, Acceso y Carrito -->
            <div class="flex items-center gap-3 shrink-0">
                
                <!-- Selector de Sucursal Activa -->
                <div class="hidden lg:flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-surface-container border border-purple-200/80 text-xs font-label-md text-secondary font-medium">
                    <span class="material-symbols-outlined text-[16px] text-primary">storefront</span>
                    <span>{{ $activeBranch }}</span>
                </div>

                <!-- Botón de Alternancia de Tema Claro/Oscuro -->
                <button type="button" id="theme-toggle-btn" class="p-2.5 rounded-xl border border-purple-200/80 bg-white hover:bg-surface-container text-on-surface-variant hover:text-on-surface transition focus:outline-none shadow-2xs" title="Alternar Modo Claro/Oscuro" aria-label="Cambiar tema visual">
                    <span class="material-symbols-outlined text-[20px] text-primary">light_mode</span>
                </button>

                <!-- Acceso de Coleccionista / Portal Privado -->
                <a href="{{ url('/admin') }}" class="p-2.5 rounded-xl border border-purple-200/80 bg-white hover:bg-surface-container text-on-surface-variant hover:text-on-surface transition shadow-2xs flex items-center gap-2" title="Portal de Coleccionista y Administración">
                    <span class="material-symbols-outlined text-[20px] text-secondary">person</span>
                    <span class="hidden xl:inline text-xs font-label-md">Acceso</span>
                </a>

                <!-- Disparador del Carrito Desplegable con Contador de Reserva -->
                <button type="button" id="open-cart-btn" class="relative p-2.5 rounded-xl bg-primary-container hover:bg-primary text-white border border-purple-400/40 shadow-sm flex items-center justify-center transition focus:outline-none" aria-label="Abrir Carrito de Adquisiciones">
                    <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
                    <span id="cart-counter-badge" class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-surface-container-high text-on-surface font-tabular-data font-bold text-xs rounded-full flex items-center justify-center shadow-xs border border-primary/40">
                        {{ $cartCount }}
                    </span>
                </button>
            </div>
        </div>

        <!-- Barra de Navegación por Categorías Temáticas -->
        <nav aria-label="Categorías principales" class="flex items-center justify-between border-t border-purple-200/60 py-2.5 text-xs font-label-md uppercase tracking-wider overflow-x-auto whitespace-nowrap no-scrollbar">
            <div class="flex items-center gap-6">
                <a href="{{ url('/') }}" class="text-primary font-bold pb-0.5 border-b-2 border-primary transition flex items-center gap-1">
                    <span>Inicio</span>
                </a>
                <a href="#libros" class="text-on-surface-variant hover:text-primary transition pb-0.5 border-b-2 border-transparent hover:border-primary flex items-center gap-1">
                    <span>Libros Raros &amp; Incunables</span>
                    <span class="material-symbols-outlined text-[14px]">expand_more</span>
                </a>
                <a href="#vinilos" class="text-on-surface-variant hover:text-primary transition pb-0.5 border-b-2 border-transparent hover:border-primary flex items-center gap-1">
                    <span>Discos &amp; Vinilos Históricos</span>
                    <span class="material-symbols-outlined text-[14px]">expand_more</span>
                </a>
                <a href="#memorabilia" class="text-on-surface-variant hover:text-primary transition pb-0.5 border-b-2 border-transparent hover:border-primary flex items-center gap-1">
                    <span>Memorabilia &amp; Archivo</span>
                    <span class="material-symbols-outlined text-[14px]">expand_more</span>
                </a>
                <a href="#adquisiciones" class="text-primary hover:text-primary-hover transition font-bold flex items-center gap-1">
                    <span class="material-symbols-outlined text-[15px]">auto_awesome</span>
                    <span>Adquisiciones Selectas</span>
                </a>
            </div>
            <div class="hidden sm:flex items-center gap-4 text-xs font-normal normal-case text-on-surface-variant">
                <span class="flex items-center gap-1 text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200 text-[11px] font-label-code">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Bóveda Abierta
                </span>
                <a href="#acerca-de" class="hover:text-primary transition">Salón de Curaduría</a>
            </div>
        </nav>
    </div>
</header>
