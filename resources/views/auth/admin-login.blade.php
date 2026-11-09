{{-- Vista unificada de acceso interno: renderiza la tienda pública con el Drawer Lateral (<x-public.login-drawer>) desplegado --}}
@include('public.index', ['productos' => $productos ?? collect()])
