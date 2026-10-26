<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'ARCHIVUM · Portal de Autenticación Segura' }}</title>

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
<body class="min-h-screen bg-background text-on-surface antialiased font-quicksand flex flex-col justify-between p-4 sm:p-8">
    
    <!-- Barra Superior de Navegación de Acceso -->
    <div class="max-w-6xl w-full mx-auto flex items-center justify-between">
        <a href="{{ url('/') }}" class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary-container text-white flex items-center justify-center shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                    <polyline points="2 17 12 22 22 17"></polyline>
                    <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
            </div>
            <div class="flex flex-col">
                <span class="text-lg font-bold tracking-wider font-quicksand uppercase text-on-surface">Archivum</span>
                <span class="font-label-code text-primary uppercase tracking-widest text-[10px]">Autenticación de Bóveda</span>
            </div>
        </a>
        <a href="{{ url('/') }}" class="flex items-center gap-1 text-xs font-label-md text-on-surface-variant hover:text-primary transition">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            <span>Volver a la Galería</span>
        </a>
    </div>

    <!-- Contenedor Central de Acceso -->
    <div class="max-w-md w-full mx-auto my-8">
        {{ $slot ?? '' }}
        @yield('content')
    </div>

    <!-- Pie Mínimo de Seguridad Notarial -->
    <div class="max-w-md w-full mx-auto text-center text-xs text-outline font-label-code space-y-1">
        <p>&copy; {{ date('Y') }} ARCHIVUM · Conexión Cifrada SSL / TLS 1.3 con Auditoría Notarial</p>
    </div>

</body>
</html>
