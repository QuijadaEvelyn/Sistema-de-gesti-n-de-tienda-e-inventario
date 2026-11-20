<!DOCTYPE html>
<html lang="es" translate="no">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google" content="notranslate">

    <!-- Aislamiento de Sesión por Pestaña (sessionStorage) -->
    <x-auth.tab-session-isolation />

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

    <!-- Área de Trabajo Principal Desplazada (pl-72) con Contención de Desborde -->
    <div class="pl-72 min-h-screen flex flex-col min-w-0 flex-1 overflow-x-hidden">

        <!-- Barra Superior Administrativa Fija (left-72) -->
        <x-admin.header :user-name="$userName ?? null" :user-role="$userRole ?? null" />

        <!-- Lienzo de Contenido Operativo -->
        <main id="admin-main-canvas" class="relative min-w-0 flex-1 overflow-x-hidden w-full max-w-full pt-20 px-4 sm:px-6 md:px-8 py-6 bg-background transition-all duration-300 ease-out box-border">
            <!-- Fondo Ambiental Temático de Bóveda y Vinilos (Bibliotheca & Discos) -->
            <x-admin.vault-ambient-background />

            <div id="admin-main-content" class="relative z-10 admin-stage-transition min-w-0 w-full max-w-full box-border overflow-x-hidden">
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
        (function() {
            function syncAdminThemeUI(isDark) {
                const adminThemeBtn = document.getElementById('admin-theme-toggle');
                if (adminThemeBtn) {
                    const icon = adminThemeBtn.querySelector('.material-symbols-outlined');
                    if (icon) {
                        icon.textContent = isDark ? 'dark_mode' : 'light_mode';
                    }
                    adminThemeBtn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
                    adminThemeBtn.setAttribute('title', isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro');
                }
            }

            window.syncAdminThemeUI = syncAdminThemeUI;

            // Sincronizar inmediatamente el icono del botón según el estado actual de <html>
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    syncAdminThemeUI(document.documentElement.classList.contains('dark'));
                }, { once: true });
            } else {
                syncAdminThemeUI(document.documentElement.classList.contains('dark'));
            }

            // Delegación única global en document para evitar listeners duplicados en navegación SPA
            if (!window.__adminThemeListenerAttached) {
                window.__adminThemeListenerAttached = true;

                document.addEventListener('click', (event) => {
                    const btn = event.target.closest('#admin-theme-toggle');
                    if (!btn) return;

                    event.preventDefault();
                    const isDark = document.documentElement.classList.toggle('dark');
                    if (isDark) {
                        document.documentElement.classList.remove('light');
                        localStorage.setItem('theme', 'dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    }

                    syncAdminThemeUI(isDark);

                    // Disparar ambos eventos para compatibilidad total con gráficas y componentes
                    window.dispatchEvent(new CustomEvent('themeChanged', { detail: { isDark } }));
                    window.dispatchEvent(new CustomEvent('archivum-theme-change', { detail: { isDark } }));
                });

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
                        window.dispatchEvent(new CustomEvent('themeChanged', { detail: { isDark } }));
                        window.dispatchEvent(new CustomEvent('archivum-theme-change', { detail: { isDark } }));
                    }
                });

                // Sincronizar estado del conmutador tras cada transición SPA
                window.addEventListener('admin-spa-navigated', () => {
                    syncAdminThemeUI(document.documentElement.classList.contains('dark'));
                });
            }
        })();
    </script>

    @stack('scripts')
</body>
</html>
