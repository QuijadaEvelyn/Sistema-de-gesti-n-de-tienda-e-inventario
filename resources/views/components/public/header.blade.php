@props([
    'activeBranch' => 'Sucursal Central Bellas Artes',
    'cartCount'    => null
])

@php
    $estadoCarritoHeader = \App\Http\Controllers\CheckoutSimuladoController::obtenerEstadoCarrito();
    $contadorCarrito = (int) ($estadoCarritoHeader['cantidad_total'] ?? 0);
    if ($contadorCarrito === 0 && isset($cartCount) && is_numeric($cartCount) && (int) $cartCount > 0) {
        $contadorCarrito = (int) $cartCount;
    }
    $contextoPublico = app(\App\Services\SucursalContextoService::class);
    $sucursalesPublicas = $contextoPublico->obtenerSucursalesActivas();
    $sucursalPublicaActiva = $contextoPublico->resolverSucursalPublica(request());
@endphp

<header class="sticky top-0 z-40 border-b border-purple-200/80 bg-white/95 backdrop-blur-md shadow-xs text-on-surface" data-purpose="global-header">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Fila Superior: Marca, Búsqueda, Sucursal, Acciones de Usuario -->
        <div class="flex items-center justify-between h-20 gap-4">

            <!-- Logotipo y Monograma de Archivo -->
            <a href="{{ route('public.home') }}" class="flex items-center gap-3 shrink-0 group focus:outline-none" aria-label="Ir al inicio de Archivum">
                <div class="w-11 h-11 rounded-xl bg-primary-container flex items-center justify-center text-white border border-purple-300/40 shadow-sm transition-transform group-hover:scale-105">
                    <!-- Isotipo Geométrico con Salto y Giro Continuo -->
                    <svg class="w-6 h-6 animate-bounce" style="animation-duration: 2s; animation: bounce 2s infinite, spin 8s linear infinite;" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
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
                <form action="{{ route('public.home') }}" method="GET" class="relative w-full">
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Buscar libros raros, vinilos 180g, primeras ediciones, memorabilia o SKU..."
                        class="w-full bg-surface-container-low/70 border border-purple-200/80 text-sm rounded-xl pl-11 pr-4 py-2.5 text-on-surface placeholder:text-outline focus:border-primary focus:bg-white focus:ring-1 focus:ring-primary focus:outline-none transition shadow-2xs"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-primary">
                        <span class="material-symbols-outlined text-[20px]">search</span>
                    </div>
                </form>
            </div>

            <!-- Controles Utilitarios: Tema, Acceso y Carrito (Selector de sucursales exclusivo para panel interno) -->
            <div class="flex items-center gap-3 shrink-0">

                <!-- Botón de Alternancia de Tema Claro/Oscuro -->
                <button type="button" id="theme-toggle-btn" class="p-2.5 rounded-xl border border-purple-200/80 bg-white hover:bg-surface-container text-on-surface-variant hover:text-on-surface transition focus:outline-none shadow-2xs" title="Alternar Modo Claro/Oscuro" aria-label="Cambiar tema visual">
                    <span class="material-symbols-outlined text-[20px] text-primary">light_mode</span>
                </button>

                {{-- ===== Zona de Usuario: Dinámica por Estado de Sesión y Rol ===== --}}

                @guest
                    {{-- Invitado: Botón "Iniciar Sesión / Registro" que abre el Slide-Over Lateral de Login al 50% --}}
                    <div class="flex items-center gap-2" id="user-menu-guest">
                        <a
                            href="{{ route('login') }}"
                            id="open-login-drawer-btn"
                            data-open-login-drawer="login"
                            class="p-2.5 px-3.5 rounded-xl border border-purple-200/80 bg-white hover:bg-surface-container text-on-surface-variant hover:text-on-surface transition shadow-2xs flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-primary/40"
                            title="Iniciar Sesión o Registrarse en ARCHIVUM"
                        >
                            <span class="material-symbols-outlined text-[20px] text-secondary">person</span>
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
                            class="flex items-center gap-2 px-3 py-1.5 rounded-xl border border-purple-200/80 bg-white hover:bg-surface-container text-on-surface transition shadow-2xs focus:outline-none focus:ring-2 focus:ring-primary/40"
                        >
                            {{-- Avatar con iniciales --}}
                            <span class="w-7 h-7 rounded-full bg-primary-container text-white text-xs font-bold flex items-center justify-center ring-1 ring-primary/30 select-none">
                                {{ $iniciales }}
                            </span>
                            <span class="hidden xl:block text-xs font-label-md truncate max-w-[100px]">{{ $authUser->nombre }}</span>
                            <span class="material-symbols-outlined text-[14px] text-outline" id="btn-usuario-icon">expand_more</span>
                        </button>

                        <div
                            id="dropdown-usuario"
                            role="menu"
                            aria-labelledby="btn-usuario"
                            class="hidden absolute right-0 mt-2 w-64 rounded-2xl bg-white border border-purple-200/80 shadow-lg py-2 z-50"
                        >
                            {{-- Cabecera con nombre, email y badge de rol --}}
                            <div class="px-4 py-3 border-b border-purple-100">
                                <p class="text-sm font-semibold text-on-surface font-quicksand truncate">{{ $authUser->nombre }}</p>
                                <p class="text-[11px] text-outline font-label-code truncate">{{ $authUser->email }}</p>
                                @if ($esPersonal)
                                    <span class="inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 rounded-full bg-primary/10 text-primary text-[10px] font-label-code uppercase tracking-wider border border-primary/20">
                                        <span class="material-symbols-outlined text-[11px]">admin_panel_settings</span>
                                        Personal Interno
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 text-[10px] font-label-code uppercase tracking-wider border border-purple-200">
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
                            <div class="border-t border-purple-100 mt-1 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" role="menuitem" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-label-md text-error hover:bg-rose-50 transition group text-left">
                                        <span class="material-symbols-outlined text-[18px] group-hover:text-error">logout</span>
                                        <span>Cerrar Sesión</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endauth

                <!-- Disparador del Carrito Desplegable con Contador de Reserva -->
                <button type="button" id="open-cart-btn" class="relative p-2.5 rounded-xl bg-primary-container hover:bg-primary text-white border border-purple-400/40 shadow-sm flex items-center justify-center transition focus:outline-none" aria-label="Abrir Carrito de Adquisiciones">
                    <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
                    <span
                        id="cart-counter-badge"
                        class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-surface-container-high text-on-surface font-tabular-data font-bold text-xs rounded-full flex items-center justify-center shadow-xs border border-primary/40 {{ $contadorCarrito > 0 ? '' : 'hidden' }}"
                    >
                        {{ $contadorCarrito }}
                    </span>
                </button>
            </div>
        </div>

        <!-- Barra de Navegación por Categorías Temáticas Independientes -->
        <nav aria-label="Categorías principales" class="flex items-center justify-between border-t border-purple-200/60 py-2.5 text-xs font-label-md uppercase tracking-wider overflow-x-auto whitespace-nowrap no-scrollbar">
            <div class="flex items-center gap-6">
                <a href="{{ route('public.home') }}"
                   class="pb-0.5 border-b-2 transition flex items-center gap-1 {{ request()->routeIs('public.home') ? 'text-primary font-bold border-primary' : 'text-on-surface-variant hover:text-primary border-transparent hover:border-primary' }}">
                    <span>Inicio</span>
                </a>
                <a href="{{ route('public.libros') }}"
                   class="pb-0.5 border-b-2 transition flex items-center gap-1 {{ request()->routeIs('public.libros') ? 'text-primary font-bold border-primary' : 'text-on-surface-variant hover:text-primary border-transparent hover:border-primary' }}">
                    <span class="material-symbols-outlined text-[15px]">auto_stories</span>
                    <span>Libros</span>
                </a>
                <a href="{{ route('public.vinilos') }}"
                   class="pb-0.5 border-b-2 transition flex items-center gap-1 {{ (request()->routeIs('public.vinilos') || request()->routeIs('public.discos')) ? 'text-primary font-bold border-primary' : 'text-on-surface-variant hover:text-primary border-transparent hover:border-primary' }}">
                    <span class="material-symbols-outlined text-[15px]">album</span>
                    <span>Discos</span>
                </a>
                <a href="{{ route('public.memorabilia') }}"
                   class="pb-0.5 border-b-2 transition flex items-center gap-1 {{ (request()->routeIs('public.memorabilia') || request()->routeIs('public.coleccionables')) ? 'text-primary font-bold border-primary' : 'text-on-surface-variant hover:text-primary border-transparent hover:border-primary' }}">
                    <span class="material-symbols-outlined text-[15px]">history_edu</span>
                    <span>Coleccionables</span>
                </a>
                <a href="{{ route('public.adquisiciones') }}"
                   class="pb-0.5 border-b-2 transition flex items-center gap-1 {{ request()->routeIs('public.adquisiciones') ? 'text-primary font-bold border-primary' : 'text-primary hover:text-primary-hover font-bold border-transparent hover:border-primary' }}">
                    <span class="material-symbols-outlined text-[15px]">auto_awesome</span>
                    <span>Adquisiciones Selectas</span>
                </a>
                <a href="{{ route('public.nosotros') }}"
                   id="nav-tab-nosotros"
                   class="pb-0.5 border-b-2 transition flex items-center gap-1 {{ request()->routeIs('public.nosotros') ? 'text-primary font-bold border-primary' : 'text-on-surface-variant hover:text-primary border-transparent hover:border-primary' }}">
                    <span class="material-symbols-outlined text-[15px]">museum</span>
                    <span>Conoce más de nosotros</span>
                </a>
            </div>
            <div class="hidden sm:flex items-center text-xs font-normal normal-case text-on-surface-variant">
                <span class="flex items-center gap-1 text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200 text-[11px] font-label-code">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Bóveda Abierta
                </span>
            </div>
        </nav>
    </div>
</header>

{{-- Script de menús desplegables sin dependencias externas --}}
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
    });
}());
</script>