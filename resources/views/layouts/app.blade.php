{{--
    ARCHIVUM · Layout Global de Aplicación (layouts/app.blade.php)
    Delega al layout público principal que incluye en <head> (dentro de @auth) el aislamiento
    por pestaña con sessionStorage ('archivum_tab_ok' y candado 'archivum_tab_logout_done').
--}}
@include('components.layouts.public')
