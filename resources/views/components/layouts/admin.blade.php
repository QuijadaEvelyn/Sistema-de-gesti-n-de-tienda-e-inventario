<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Script de Inicialización Inmediata de Tema (Zero-FOUC Dark Mode) -->
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                    document.documentElement.classList.add('dark');
                    document.documentElement.classList.remove('light');
                } else {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.classList.add('light');
                }
            } catch (e) {}
        })();
    </script>

    <title>{{ $title ?? 'ARCHIVUM · Terminal Administrativa y de Catalogación' }}</title>

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
<body class="bg-background text-on-surface antialiased font-quicksand selection:bg-primary-container selection:text-white">

    <!-- Sidebar de Navegación Fija (280px / w-72) -->
    <x-admin.sidebar :active-route="$activeRoute ?? 'dashboard'" />

    <!-- Área de Trabajo Principal Desplazada (pl-72) -->
    <div class="pl-72 min-h-screen flex flex-col">

        <!-- Barra Superior Administrativa Fija (left-72) -->
        <x-admin.header :user-name="$userName ?? null" :user-role="$userRole ?? null" />

        <!-- Lienzo de Contenido Operativo -->
        <main id="admin-main-canvas" class="w-full pt-20 px-space-lg py-space-lg bg-background flex-1 transition-all duration-300 ease-out">
            <div id="admin-main-content" class="admin-stage-transition w-full">
                @if (session('status'))
                    <div class="mb-6 flex items-center gap-3 p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-900 rounded-xl shadow-xs" role="alert">
                        <span class="material-symbols-outlined text-emerald-600 text-xl">check_circle</span>
                        <p class="text-sm font-medium">{{ session('status') }}</p>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="mb-6 flex items-center gap-3 p-4 bg-amber-500/10 border border-amber-500/30 text-amber-900 rounded-xl shadow-xs" role="alert">
                        <span class="material-symbols-outlined text-amber-600 text-xl">warning</span>
                        <p class="text-sm font-medium">{{ session('warning') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 text-rose-900 rounded-xl shadow-xs space-y-1" role="alert">
                        @foreach ($errors->all() as $error)
                            <p class="text-sm font-medium">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </main>

        <!-- Micro-Pie de Página Administrativo -->
        <footer class="px-space-lg py-4 border-t border-purple-200/50 bg-surface-container-lowest/70 text-xs font-label-code text-on-surface-variant flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                <span>Base de Datos de Colecciones: Sincronizada con Almacén Central</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.terminal') }}" class="hover:text-primary font-semibold transition">
                    TERMINAL ARCHIVO v4.8.2-PROD
                </a>
                <span>&middot;</span>
                <a href="{{ route('public.home') }}" class="hover:text-primary transition">
                    Ver Tienda Pública
                </a>
            </div>
        </footer>

    </div>

    <!-- Script de Modo Claro/Oscuro en Administración -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const adminThemeBtn = document.getElementById('admin-theme-toggle');

            function syncAdminThemeUI(isDark) {
                if (adminThemeBtn) {
                    const icon = adminThemeBtn.querySelector('.material-symbols-outlined');
                    if (icon) {
                        icon.textContent = isDark ? 'dark_mode' : 'light_mode';
                    }
                    adminThemeBtn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
                    adminThemeBtn.setAttribute('title', isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro');
                }
            }

            if (adminThemeBtn) {
                adminThemeBtn.addEventListener('click', () => {
                    const isDark = document.documentElement.classList.toggle('dark');
                    if (isDark) {
                        document.documentElement.classList.remove('light');
                        localStorage.setItem('theme', 'dark');
                    } else {
                        document.documentElement.classList.add('light');
                        localStorage.setItem('theme', 'light');
                    }
                    syncAdminThemeUI(isDark);
                });
            }

            syncAdminThemeUI(document.documentElement.classList.contains('dark'));

            // Sincronización en tiempo real al cambiar entre pestañas del navegador
            window.addEventListener('storage', (event) => {
                if (event.key === 'theme') {
                    const isDark = event.newValue === 'dark';
                    if (isDark) {
                        document.documentElement.classList.add('dark');
                        document.documentElement.classList.remove('light');
                    } else {
                        document.documentElement.classList.remove('dark');
                        document.documentElement.classList.add('light');
                    }
                    syncAdminThemeUI(isDark);
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
