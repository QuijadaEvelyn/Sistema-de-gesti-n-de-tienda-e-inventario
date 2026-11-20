{{--
    ARCHIVUM · Control de Aislamiento de Sesión por Pestaña (sessionStorage)
    e Interceptor de Inicio de Sesión Obligatorio para Reservar en Bóveda.
    Sin redirecciones ni recargas automáticas en /login.
--}}
@auth
<script>
(function () {
    'use strict';
    try {
        if (window.location.pathname !== '/login' && !sessionStorage.getItem('archivum_tab_ok')) {
            if (!sessionStorage.getItem('archivum_tab_logout_done')) {
                sessionStorage.setItem('archivum_tab_logout_done', '1');
                var csrfMeta = document.querySelector('meta[name=csrf-token]');
                var csrfToken = csrfMeta ? csrfMeta.content : '';
                fetch('{{ route('logout', [], false) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                }).finally(function () {
                    window.location.reload();
                });
            }
        } else if (sessionStorage.getItem('archivum_tab_ok')) {
            sessionStorage.removeItem('archivum_tab_logout_done');
        }
    } catch (e) {}
})();
</script>
@endauth

<script>
(function () {
    'use strict';
    try {
        var isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
        var AUTH_REQUIRED_MSG = 'Inicia sesión o regístrate para reservar piezas en la bóveda';

        // 1. Marcar pestaña autenticada al enviar login/registro y limpiar al cerrar sesión (logout)
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form || form.tagName !== 'FORM') return;

            var action = (form.getAttribute('action') || '').toLowerCase();

            if (action.indexOf('/login') !== -1 || action.indexOf('/register') !== -1 || form.id === 'drawer-login-form' || form.id === 'drawer-register-form') {
                sessionStorage.setItem('archivum_tab_ok', '1');
                sessionStorage.removeItem('archivum_tab_logout_done');
                return;
            }

            if (action.indexOf('/logout') !== -1) {
                sessionStorage.removeItem('archivum_tab_ok');
                sessionStorage.removeItem('archivum_tab_logout_done');
                return;
            }

            // 2. Si el usuario es invitado (sin sesión activa), interceptar cualquier formulario de carrito/reserva
            if (!isAuthenticated) {
                var isCartForm =
                    action.indexOf('/carrito/agregar') !== -1 ||
                    action.indexOf('/carrito/actualizar') !== -1 ||
                    action.indexOf('/carrito/remover') !== -1 ||
                    action.indexOf('/checkout') !== -1 ||
                    form.hasAttribute('data-requires-auth-reserve');

                if (isCartForm) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    window.requireAuthForReservation(window.location.href, AUTH_REQUIRED_MSG);
                    return false;
                }
            }
        }, true);

        document.addEventListener('click', function (e) {
            if (!e.target || typeof e.target.closest !== 'function') return;

            // Limpiar banderas de pestaña al hacer clic en botón de logout
            var logoutForm = e.target.closest('form[action*="/logout"]');
            if (logoutForm) {
                sessionStorage.removeItem('archivum_tab_ok');
                sessionStorage.removeItem('archivum_tab_logout_done');
                return;
            }

            // Si es invitado (sin sesión activa), interceptar clic en botones de RESERVAR o ícono del carrito
            if (!isAuthenticated) {
                var guestTrigger = e.target.closest(
                    '#open-cart-btn, #btn-proceder-pasarela-guest, [data-requires-auth-reserve], ' +
                    'form[action*="/carrito/agregar"] button[type="submit"], ' +
                    'form[action*="/carrito/agregar"] input[type="submit"]'
                );

                if (guestTrigger) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    window.requireAuthForReservation(window.location.href, AUTH_REQUIRED_MSG);
                    return false;
                }
            }
        }, true);

        window.requireAuthForReservation = function (intendedUrl, customMsg) {
            var targetIntended = intendedUrl || window.location.href;
            var msg = customMsg || AUTH_REQUIRED_MSG;

            try {
                sessionStorage.setItem('intended_after_auth', targetIntended);
            } catch (err) {}

            if (typeof window.cerrarModalDetalleProducto === 'function') {
                window.cerrarModalDetalleProducto();
            }

            var closeCartBtn = document.getElementById('close-cart-btn');
            var cartDrawer = document.getElementById('cart-drawer');
            if (closeCartBtn && cartDrawer && !cartDrawer.classList.contains('translate-x-full')) {
                closeCartBtn.click();
            }

            if (typeof window.openLoginDrawer === 'function') {
                window.openLoginDrawer('login', targetIntended, msg);
            } else if (window.location.pathname !== '/login') {
                window.location.href = '{{ route('login', [], false) }}?intended=' + encodeURIComponent(targetIntended);
            }
        };
    } catch (e) {}
})();
</script>
