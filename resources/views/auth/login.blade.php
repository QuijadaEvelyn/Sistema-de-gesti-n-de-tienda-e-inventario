{{-- Vista unificada de inicio de sesión: renderiza la tienda pública con el Drawer Lateral (<x-public.login-drawer>) desplegado --}}
@include('public.index', ['productos' => $productos ?? collect()])
