<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'ARCHIVUM · Bibliotheca & Vinilos Históricos' }}</title>
    <meta name="description" content="{{ $description ?? 'Catálogo selecto de libros raros, primeras ediciones, discos de vinilo 180g y memorabilia con procedencia certificada.' }}">

    <!-- Google Fonts: Quicksand, Barlow Condensed, Newsreader, Roboto Flex -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;500;600;700&family=Newsreader:ital,wght@0,400;0,500;1,400;1,500&family=Quicksand:wght@400;500;600;700&family=Roboto+Flex:wght@400;500;600&display=swap" rel="stylesheet" />

    <!-- Google Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    <!-- Vite Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="min-h-screen flex flex-col bg-background text-on-surface antialiased font-quicksand selection:bg-primary-container selection:text-white">
    
    <!-- Barra de Navegación Pública Global -->
    <x-public.header :active-branch="$activeBranch ?? 'Sucursal Central Bellas Artes'" :cart-count="$cartCount ?? 3" />

    <!-- Contenedor Principal Dinámico -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Pie de Página Curatorial -->
    <x-public.footer />

    <!-- Cajón Desplegable de Carrito y Reserva Temporal -->
    <x-public.cart-drawer />

    <!-- Scripts Esenciales de Interacción Pública (Cart Drawer & Modo Claro/Oscuro) -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const drawer = document.getElementById('cart-drawer');
            const overlay = document.getElementById('drawer-overlay');
            const openBtn = document.getElementById('open-cart-btn');
            const closeBtn = document.getElementById('close-cart-btn');

            function toggleDrawer(open) {
                if (!drawer || !overlay) return;
                if (open) {
                    drawer.classList.remove('translate-x-full');
                    overlay.classList.remove('opacity-0', 'pointer-events-none');
                    overlay.classList.add('opacity-100');
                    document.body.style.overflow = 'hidden';
                } else {
                    drawer.classList.add('translate-x-full');
                    overlay.classList.add('opacity-0', 'pointer-events-none');
                    overlay.classList.remove('opacity-100');
                    document.body.style.overflow = '';
                }
            }

            if (openBtn) openBtn.addEventListener('click', () => toggleDrawer(true));
            if (closeBtn) closeBtn.addEventListener('click', () => toggleDrawer(false));
            if (overlay) overlay.addEventListener('click', () => toggleDrawer(false));

            // Conmutador de Tema Claro / Oscuro
            const themeBtn = document.getElementById('theme-toggle-btn');
            if (themeBtn) {
                themeBtn.addEventListener('click', () => {
                    const isDark = document.documentElement.classList.toggle('dark');
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
