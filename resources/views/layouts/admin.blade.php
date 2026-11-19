{{-- 
    ARCHIVUM · Layout Global Administrativo
    Nota arquitectónica: No incluir tarjetas ni widgets exclusivos del dashboard (como 'Piezas en Custodia') en este layout global.
    Todos los módulos y pantallas administrativas se renderizan limpiamente dentro de {{ $slot }} o @yield('content').
--}}
@include('components.layouts.admin')
