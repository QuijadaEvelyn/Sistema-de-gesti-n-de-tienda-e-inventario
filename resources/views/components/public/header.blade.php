@props([
    'activeBranch' => 'Sucursal Central Bellas Artes',
    'cartCount'    => null
])

@php
    $estadoCarritoHeader = auth()->check()
        ? \App\Http\Controllers\CheckoutSimuladoController::obtenerEstadoCarrito()
        : ['cantidad_total' => 0];
    $contadorCarrito = auth()->check() ? (int) ($estadoCarritoHeader['cantidad_total'] ?? 0) : 0;
    if (auth()->check() && $contadorCarrito === 0 && isset($cartCount) && is_numeric($cartCount) && (int) $cartCount > 0) {
        $contadorCarrito = (int) $cartCount;
    }
    $contextoPublico = app(\App\Services\SucursalContextoService::class);
    $sucursalesPublicas = $contextoPublico->obtenerSucursalesActivas();
    $sucursalPublicaActiva = $contextoPublico->resolverSucursalPublica(request());
@endphp

<header class="sticky top-0 z-40 border-b border-purple-200/80 bg-white/95 dark:bg-[#150e22]/95 backdrop-blur-md shadow-xs text-on-surface" data-purpose="global-header">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <!-- Fila Superior: Marca, Búsqueda, Sucursal, Acciones de Usuario -->
        <div class="flex items-center justify-between h-16 sm:h-20 gap-2 sm:gap-4">

            <!-- Logotipo y Monograma de Archivo (Optimizado para móvil) -->
            <a href="{{ route('public.home') }}" class="flex items-center gap-2 sm:gap-3 shrink-0 group focus:outline-none min-w-0" aria-label="Ir al inicio de Archivum">
                <x-ui.archivum-logo size="sm" container-class="w-9 h-9 sm:w-12 sm:h-12" svg-class="w-7 h-7 sm:w-10 sm:h-10" />
                <div class="flex flex-col min-w-0">
                    <span class="text-sm sm:text-xl font-bold font-brand-serif tracking-[0.1em] sm:tracking-[0.2em] uppercase text-on-surface transition-colors group-hover:text-primary truncate">ARCHIVUM</span>
                    <span class="font-label-code text-primary uppercase tracking-wider sm:tracking-widest text-[8.5px] sm:text-[10px] truncate">Bibliotheca &amp; Discos</span>
                </div>
            </a>

            <!-- Barra de Búsqueda Global Omnicanal (Desktop y Tablets >= md) -->
            <div class="flex-1 max-w-2xl hidden md:block">
                <form action="{{ route('public.home') }}" method="GET" class="relative w-full">
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Buscar libros raros, vinilos 180g, primeras ediciones, memorabilia o SKU..."
                        class="w-full bg-surface-container-low/70 dark:bg-[#1e152d] border border-purple-200/80 dark:border-purple-500/30 text-sm rounded-xl pl-11 pr-4 py-2.5 text-on-surface placeholder:text-outline focus:border-primary focus:bg-white dark:focus:bg-[#1b1228] focus:ring-1 focus:ring-primary focus:outline-none transition shadow-2xs"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-primary">
                        <span class="material-symbols-outlined text-[20px]">search</span>
                    </div>
                </form>
            </div>

            <!-- Controles Utilitarios: Búsqueda Móvil, Tema, Acceso y Carrito (Compactos en móvil < sm) -->
            <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">

                <!-- Disparador de Búsqueda Móvil (visible solo en < md) -->
                <button
                    type="button"
                    id="mobile-search-toggle-btn"
                    class="md:hidden btn-glass-surface w-9 h-9 p-0 rounded-xl text-on-surface-variant hover:text-on-surface shadow-xs flex items-center justify-center focus:outline-none cursor-pointer"
                    title="Buscar en el catálogo"
                    aria-label="Abrir buscador"
                    aria-expanded="false"
                >
                    <span class="material-symbols-outlined text-[19px] text-primary" id="mobile-search-toggle-icon">search</span>
                </button>

                <!-- Botón de Alternancia de Tema Claro/Oscuro -->
                <button
                    type="button"
                    id="theme-toggle-btn"
                    class="btn-glass-surface w-9 h-9 sm:w-11 sm:h-11 p-0 sm:p-2.5 rounded-xl text-on-surface-variant hover:text-on-surface shadow-xs flex items-center justify-center focus:outline-none cursor-pointer"
                    title="Alternar Modo Claro/Oscuro"
                    aria-label="Cambiar tema visual"
                >
                    <span class="material-symbols-outlined text-[18px] sm:text-[20px] text-primary">light_mode</span>
                </button>

                <!-- Botón de Alternancia de Sonido Háptico / Silenciar -->
                <button
                    type="button"
                    class="sound-toggle-btn btn-glass-surface w-9 h-9 sm:w-11 sm:h-11 p-0 sm:p-2.5 rounded-xl text-on-surface-variant hover:text-on-surface shadow-xs flex items-center justify-center focus:outline-none cursor-pointer"
                    title="Alternar sonidos táctiles del sistema"
                    aria-label="Alternar sonidos del sistema"
                >
                    <span class="material-symbols-outlined text-[18px] sm:text-[20px] text-primary">volume_up</span>
                </button>

                {{-- ===== Zona de Usuario: Dinámica por Estado de Sesión y Rol ===== --}}

                @guest
                    {{-- Invitado: Botón "Iniciar Sesión / Registro" que abre el Slide-Over Lateral de Login --}}
                    <div class="flex items-center" id="user-menu-guest">
                        <a
                            href="{{ route('login') }}"
                            id="open-login-drawer-btn"
                            data-open-login-drawer="login"
                            class="btn-glass-surface w-9 h-9 sm:w-auto sm:h-11 p-0 sm:p-2.5 sm:px-3.5 rounded-xl text-on-surface-variant hover:text-on-surface shadow-xs flex items-center justify-center sm:justify-start gap-2 focus:outline-none focus:ring-2 focus:ring-primary/40 cursor-pointer"
                            title="Iniciar Sesión o Registrarse en ARCHIVUM"
                        >
                            <span class="material-symbols-outlined text-[18px] sm:text-[20px] text-secondary">person</span>
                            <span class="hidden xl:inline text-xs font-label-md font-semibold">Iniciar Sesión / Registro</span>
                        </a>
                    </div>
                @endguest

                @auth
                    @php
                        /** @var \App\Models\Usuario $authUser */
                        $authUser   = auth()->user();
                        $esPersonal = $authUser->tieneRol('administrador', 'encargado', 'ventas', 'almacen', 'compras');
                        $esCliente  = $authUser->esCliente() && ! $esPersonal;
                        $iniciales  = collect(explode(' ', $authUser->nombre))
                            ->map(fn ($parte) => strtoupper(substr($parte, 0, 1)))
                            ->take(2)
                            ->implode('');
                    @endphp

                    {{-- Usuario autenticado: avatar + menú contextual por rol --}}
                    <div class="relative" id="user-menu-auth">
                        <button
                            type="button"
                            id="btn-usuario"
                            aria-haspopup="true"
                            aria-controls="dropdown-usuario"
                            aria-expanded="false"
                            class="btn-glass-surface w-9 h-9 sm:w-auto sm:h-11 p-0 sm:px-3 sm:py-1.5 rounded-xl shadow-xs flex items-center justify-center sm:justify-start gap-1.5 focus:outline-none focus:ring-2 focus:ring-primary/40 cursor-pointer"
                        >
                            {{-- Avatar con iniciales --}}
                            <span class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-primary-container text-white text-[10px] sm:text-xs font-bold flex items-center justify-center ring-1 ring-primary/30 select-none">
                                {{ $iniciales }}
                            </span>
                            <span class="hidden xl:block text-xs font-label-md truncate max-w-[100px]">{{ $authUser->nombre }}</span>
                            <span class="hidden sm:inline-block material-symbols-outlined text-[14px] text-outline" id="btn-usuario-icon">expand_more</span>
                        </button>

                        <div
                            id="dropdown-usuario"
                            role="menu"
                            aria-labelledby="btn-usuario"
                            class="hidden absolute right-0 mt-2 w-64 rounded-2xl bg-white dark:bg-[#1a1228] border border-purple-200/80 dark:border-purple-500/30 shadow-lg py-2 z-50"
                        >
                            {{-- Cabecera con nombre, email y badge de rol --}}
                            <div class="px-4 py-3 border-b border-purple-100 dark:border-purple-500/20">
                                <p class="text-sm font-semibold text-on-surface font-quicksand truncate">{{ $authUser->nombre }}</p>
                                <p class="text-[11px] text-outline font-label-code truncate">{{ $authUser->email }}</p>
                                @if ($esPersonal)
                                    <span class="inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 rounded-full bg-primary/10 text-primary text-[10px] font-label-code uppercase tracking-wider border border-primary/20">
                                        <span class="material-symbols-outlined text-[11px]">admin_panel_settings</span>
                                        Personal Interno
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 rounded-full bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 text-[10px] font-label-code uppercase tracking-wider border border-purple-200 dark:border-purple-500/30">
                                        <span class="material-symbols-outlined text-[11px]">verified_user</span>
                                        Coleccionista
                                    </span>
                                @endif
                            </div>

                            {{-- Opciones exclusivas para Clientes --}}
                            @if ($esCliente)
                                <a href="{{ route('cliente.dashboard') }}" role="menuitem" class="flex items-center gap-3 px-4 py-2.5 text-sm font-label-md text-on-surface hover:bg-surface-container-low hover:text-primary transition group">
                                    <span class="material-symbols-outlined text-[18px] text-secondary group-hover:text-primary">manage_accounts</span>
                                    <span>Mi Cuenta</span>
                                </a>
                                <a href="{{ route('cliente.pedidos') }}" role="menuitem" class="flex items-center gap-3 px-4 py-2.5 text-sm font-label-md text-on-surface hover:bg-surface-container-low hover:text-primary transition group">
                                    <span class="material-symbols-outlined text-[18px] text-secondary group-hover:text-primary">package_2</span>
                                    <span>Mis Pedidos</span>
                                </a>
                                <a href="{{ route('profile.edit') }}" role="menuitem" class="flex items-center gap-3 px-4 py-2.5 text-sm font-label-md text-on-surface hover:bg-surface-container-low hover:text-primary transition group">
                                    <span class="material-symbols-outlined text-[18px] text-secondary group-hover:text-primary">edit</span>
                                    <span>Editar Perfil</span>
                                </a>
                            @endif

                            {{-- Opciones exclusivas para Personal / Staff --}}
                            @if ($esPersonal)
                                <a href="{{ route('admin.dashboard') }}" role="menuitem" class="flex items-center gap-3 px-4 py-2.5 text-sm font-label-md text-on-surface hover:bg-surface-container-low hover:text-primary transition group">
                                    <span class="material-symbols-outlined text-[18px] text-secondary group-hover:text-primary">dashboard</span>
                                    <span>Panel de Administración</span>
                                </a>
                                <a href="{{ url('/') }}" role="menuitem" class="flex items-center gap-3 px-4 py-2.5 text-sm font-label-md text-on-surface hover:bg-surface-container-low hover:text-primary transition group">
                                    <span class="material-symbols-outlined text-[18px] text-secondary group-hover:text-primary">storefront</span>
                                    <span>Ver Tienda Pública</span>
                                </a>
                                <a href="{{ route('profile.edit') }}" role="menuitem" class="flex items-center gap-3 px-4 py-2.5 text-sm font-label-md text-on-surface hover:bg-surface-container-low hover:text-primary transition group">
                                    <span class="material-symbols-outlined text-[18px] text-secondary group-hover:text-primary">settings</span>
                                    <span>Mi Perfil</span>
                                </a>
                            @endif

                            {{-- Cerrar Sesión --}}
                            <div class="border-t border-purple-100 dark:border-purple-500/20 mt-1 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" role="menuitem" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-label-md text-error hover:bg-rose-50 dark:hover:bg-rose-950/30 transition group text-left">
                                        <span class="material-symbols-outlined text-[18px] group-hover:text-error">logout</span>
                                        <span>Cerrar Sesión</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endauth

                <!-- Disparador del Carrito Desplegable con Contador de Reserva (Nunca cortado por overflow) -->
                <div class="relative inline-flex shrink-0">
                    <button
                        type="button"
                        id="open-cart-btn"
                        class="btn-glass-purple relative w-9 h-9 sm:w-11 sm:h-11 p-0 sm:p-2.5 rounded-xl shadow-xs flex items-center justify-center focus:outline-none cursor-pointer shrink-0 overflow-hidden"
                        aria-label="Abrir Carrito de Compras"
                    >
                        <span class="material-symbols-outlined text-[18px] sm:text-[20px]">shopping_bag</span>
                    </button>
                    <span
                        id="cart-counter-badge"
                        data-cart-count="{{ $contadorCarrito }}"
                        class="pointer-events-none absolute -top-1.5 -right-1.5 z-20 min-w-[20px] h-5 px-1.5 rounded-full bg-white text-purple-900 dark:bg-white dark:text-purple-950 font-tabular-data font-bold text-xs flex items-center justify-center shadow-md border border-purple-300 {{ $contadorCarrito > 0 ? '' : 'hidden' }}"
                    >
                        {{ $contadorCarrito }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Barra de Búsqueda Móvil Colapsable (Móviles < md) -->
        <div id="mobile-search-bar" class="hidden md:hidden pb-3 pt-1 border-t border-purple-200/40 dark:border-purple-500/20">
            <form action="{{ route('public.home') }}" method="GET" class="relative w-full">
                <input
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Buscar libros raros, vinilos, piezas..."
                    class="w-full bg-surface-container-low/90 dark:bg-[#1e152d] border border-purple-200/80 dark:border-purple-500/30 text-xs rounded-xl pl-9 pr-4 py-2 text-on-surface placeholder:text-outline focus:border-primary focus:bg-white dark:focus:bg-[#1b1228] focus:outline-none transition shadow-2xs"
                />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-primary">
                    <span class="material-symbols-outlined text-[17px]">search</span>
                </div>
            </form>
        </div>

        <!-- Barra de Navegación por Categorías Temáticas Independientes (Desplazable en móvil sin cortarse) -->
        <nav aria-label="Categorías principales" class="relative border-t border-purple-200/60 dark:border-purple-500/20 py-2 text-xs font-label-md uppercase tracking-wider -mx-3 sm:mx-0 px-3 sm:px-0">
            <div class="flex items-center gap-4 sm:gap-6 overflow-x-auto no-scrollbar whitespace-nowrap scroll-smooth py-1">
                <a href="{{ route('public.home') }}"
                   class="pb-0.5 border-b-2 transition flex items-center gap-1 shrink-0 {{ request()->routeIs('public.home') ? 'text-primary font-bold border-primary' : 'text-on-surface-variant hover:text-primary border-transparent hover:border-primary' }}">
                    <span>Inicio</span>
                </a>
                <a href="{{ route('public.libros') }}"
                   class="pb-0.5 border-b-2 transition flex items-center gap-1 shrink-0 {{ request()->routeIs('public.libros') ? 'text-primary font-bold border-primary' : 'text-on-surface-variant hover:text-primary border-transparent hover:border-primary' }}">
                    <span class="material-symbols-outlined text-[15px]">auto_stories</span>
                    <span>Libros</span>
                </a>
                <a href="{{ route('public.vinilos') }}"
                   class="pb-0.5 border-b-2 transition flex items-center gap-1 shrink-0 {{ (request()->routeIs('public.vinilos') || request()->routeIs('public.discos')) ? 'text-primary font-bold border-primary' : 'text-on-surface-variant hover:text-primary border-transparent hover:border-primary' }}">
                    <span class="material-symbols-outlined text-[15px]">album</span>
                    <span>Discos</span>
                </a>
                <a href="{{ route('public.memorabilia') }}"
                   class="pb-0.5 border-b-2 transition flex items-center gap-1 shrink-0 {{ (request()->routeIs('public.memorabilia') || request()->routeIs('public.coleccionables')) ? 'text-primary font-bold border-primary' : 'text-on-surface-variant hover:text-primary border-transparent hover:border-primary' }}">
                    <span class="material-symbols-outlined text-[15px]">history_edu</span>
                    <span>Coleccionables</span>
                </a>
                <a href="{{ route('public.adquisiciones') }}"
                   class="pb-0.5 border-b-2 transition flex items-center gap-1 shrink-0 {{ request()->routeIs('public.adquisiciones') ? 'text-primary font-bold border-primary' : 'text-primary hover:text-primary-hover font-bold border-transparent hover:border-primary' }}">
                    <span class="material-symbols-outlined text-[15px]">auto_awesome</span>
                    <span>Ediciones Especiales</span>
                </a>
                <a href="{{ route('public.nosotros') }}"
                   id="nav-tab-nosotros"
                   class="pb-0.5 border-b-2 transition flex items-center gap-1 shrink-0 {{ request()->routeIs('public.nosotros') ? 'text-primary font-bold border-primary' : 'text-on-surface-variant hover:text-primary border-transparent hover:border-primary' }}">
                    <span class="material-symbols-outlined text-[15px]">museum</span>
                    <span>Conoce más de nosotros</span>
                </a>
                <a href="{{ route('public.contacto') }}"
                   id="nav-tab-contacto"
                   class="pb-0.5 border-b-2 transition flex items-center gap-1 shrink-0 {{ request()->routeIs('public.contacto') ? 'text-primary font-bold border-primary' : 'text-on-surface-variant hover:text-primary border-transparent hover:border-primary' }}">
                    <span class="material-symbols-outlined text-[15px]">location_on</span>
                    <span>Contacto &amp; Horarios</span>
                </a>
            </div>
        </nav>
    </div>
</header>

{{-- Script de menús desplegables y buscador móvil --}}
<script>
(function () {
    'use strict';

    function initDropdown(btnId, dropdownId, iconId) {
        const btn      = document.getElementById(btnId);
        const dropdown = document.getElementById(dropdownId);
        const icon     = document.getElementById(iconId);

        if (!btn || !dropdown) { return; }

        function open() {
            dropdown.classList.remove('hidden');
            btn.setAttribute('aria-expanded', 'true');
            if (icon) { icon.textContent = 'expand_less'; }
        }

        function close() {
            dropdown.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
            if (icon) { icon.textContent = 'expand_more'; }
        }

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.classList.contains('hidden') ? open() : close();
        });

        // Cerrar al presionar Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { close(); }
        });

        // Cerrar al hacer clic fuera
        document.addEventListener('click', function (e) {
            if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
                close();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initDropdown('btn-acceso',  'dropdown-acceso',  'btn-acceso-icon');
        initDropdown('btn-usuario', 'dropdown-usuario', 'btn-usuario-icon');

        // Disparador de buscador móvil
        const mobileSearchBtn  = document.getElementById('mobile-search-toggle-btn');
        const mobileSearchBar  = document.getElementById('mobile-search-bar');
        const mobileSearchIcon = document.getElementById('mobile-search-toggle-icon');

        if (mobileSearchBtn && mobileSearchBar) {
            mobileSearchBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                const isHidden = mobileSearchBar.classList.toggle('hidden');
                mobileSearchBtn.setAttribute('aria-expanded', isHidden ? 'false' : 'true');
                if (mobileSearchIcon) {
                    mobileSearchIcon.textContent = isHidden ? 'search' : 'close';
                }
                if (!isHidden) {
                    const input = mobileSearchBar.querySelector('input');
                    if (input) {
                        setTimeout(() => input.focus(), 50);
                    }
                }
            });
        }
    });
}());
</script>
