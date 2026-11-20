{{-- Vista unificada de inicio de sesión: renderiza la tienda pública con el Drawer Lateral (<x-public.login-drawer>) desplegado de forma estática sin recargas automáticas --}}
@include('public.index', ['productos' => $productos ?? collect()])
<script>
document.addEventListener('DOMContentLoaded', function () {
    var loginForm = document.getElementById('drawer-login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function () {
            try {
                sessionStorage.setItem('archivum_tab_ok', '1');
                sessionStorage.removeItem('archivum_tab_logout_done');
            } catch (e) {}
        });
    }
});
</script>
