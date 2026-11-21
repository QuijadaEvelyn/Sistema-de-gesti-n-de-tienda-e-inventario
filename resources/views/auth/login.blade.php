{{-- Vista unificada de inicio de sesión: renderiza la tienda pública con el Drawer Lateral (<x-public.login-drawer>) desplegado de forma estática sin recargas automáticas --}}
@include('public.index', ['productos' => $productos ?? collect()])
<script>
document.addEventListener('DOMContentLoaded', function () {
    ['drawer-login-form', 'drawer-register-form'].forEach(function (formId) {
        var authForm = document.getElementById(formId);
        if (!authForm) return;
        authForm.addEventListener('submit', function () {
            try {
                var token = sessionStorage.getItem('archivum_tab_token')
                    || sessionStorage.getItem('tab_token')
                    || ('tab_' + Date.now().toString(36) + '_' + Math.random().toString(36).substring(2, 14));
                sessionStorage.setItem('archivum_tab_token', token);
                sessionStorage.setItem('tab_token', token);
                sessionStorage.setItem('tab_session_id', token);
                sessionStorage.setItem('archivum_tab_ok', '1');
                sessionStorage.removeItem('archivum_tab_logout_done');

                var input = authForm.querySelector('input[name="tab_token"]');
                if (!input) {
                    input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'tab_token';
                    authForm.appendChild(input);
                }
                input.value = token;
            } catch (e) {}
        });
    });
});
</script>
