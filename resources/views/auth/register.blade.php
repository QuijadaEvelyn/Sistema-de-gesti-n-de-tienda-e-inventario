{{-- Vista unificada de registro: renderiza la tienda pública con el Drawer Lateral (<x-public.login-drawer>) en la pestaña Crear Cuenta --}}
@include('public.index', ['productos' => $productos ?? collect()])
