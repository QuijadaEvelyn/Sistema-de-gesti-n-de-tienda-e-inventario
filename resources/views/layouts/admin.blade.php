<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
        <x-admin.header :user-name="$userName ?? 'Mateo Vargas'" :user-role="$userRole ?? 'Curador en Jefe'" />

        <!-- Lienzo de Contenido Operativo -->
        <main class="w-full pt-20 px-space-lg py-space-lg bg-background flex-1">
            {{ $slot ?? '' }}
            @yield('content')
        </main>
        
        <!-- Micro-Pie de Página Administrativo -->
        <footer class="px-space-lg py-4 border-t border-purple-200/50 bg-surface-container-lowest/70 text-xs font-label-code text-on-surface-variant flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                <span>Base de Datos de Colecciones: Sincronizada con Almacén Central</span>
            </div>
            <div>
                Archivum Admin Suite v4.8.2-PROD · Protocolo de Seguridad Notarial Activo
            </div>
        </footer>

    </div>

    <!-- Script de Modo Claro/Oscuro en Administración -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const adminThemeBtn = document.getElementById('admin-theme-toggle');
            if (adminThemeBtn) {
                adminThemeBtn.addEventListener('click', () => {
                    const isDark = document.documentElement.classList.toggle('dark');
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
