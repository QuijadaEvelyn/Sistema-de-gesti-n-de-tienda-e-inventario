{{--
    ARCHIVUM · Aislamiento Real de Sesión por Pestaña (sessionStorage + tab_token)
    - Jamás ejecuta POST /logout automático al abrir una pestaña nueva.
    - Cada pestaña mantiene su propio tab_token en sessionStorage y lo transmite en enlaces
      de la misma pestaña (_tab), formularios (tab_token) y peticiones AJAX (X-Tab-Token).
    - Una pestaña nueva no tiene _tab en su URL ni tab_token autenticado, por lo que el servidor
      la renderiza como Invitado (Auth::guest()) desde el primer milisegundo sin cerrar las otras pestañas.
--}}
@php
    $archivumIsAuth = auth()->check();
    $archivumActiveTabId = (string) request()->attributes->get('archivum_resolved_tab_id', '');
@endphp
<meta name="archivum-is-authenticated" content="{{ $archivumIsAuth ? '1' : '0' }}">
<meta name="archivum-active-tab-id" content="{{ $archivumActiveTabId }}">

<script>
(function () {
    'use strict';
    try {
        var isServerAuth = {{ $archivumIsAuth ? 'true' : 'false' }};
        var serverTabId = @json($archivumActiveTabId);
        var AUTH_REQUIRED_MSG = 'Inicia sesión o regístrate para agregar productos al carrito';

        function generateTabToken() {
            var randomPart = Math.random().toString(36).substring(2, 12) + Math.random().toString(36).substring(2, 12);
            return 'tab_' + Date.now().toString(36) + '_' + randomPart;
        }

        // 1. Verificar o generar tab_token único por pestaña en sessionStorage ('archivum_tab_token').
        // window.name es vacío ("") en toda pestaña nueva o duplicada, pero persiste en la misma pestaña.
        var tabToken = sessionStorage.getItem('archivum_tab_token')
            || sessionStorage.getItem('tab_token')
            || sessionStorage.getItem('tab_session_id');
        var isNewBrowsingContext = !tabToken || !window.name || window.name.indexOf('tab_') !== 0 || window.name !== tabToken;

        if (isNewBrowsingContext) {
            tabToken = generateTabToken();
            sessionStorage.setItem('archivum_tab_token', tabToken);
            sessionStorage.setItem('tab_token', tabToken);
            sessionStorage.setItem('tab_session_id', tabToken);
            sessionStorage.removeItem('tab_authenticated');
            sessionStorage.removeItem('archivum_tab_ok');
            window.name = tabToken;
        } else {
            sessionStorage.setItem('archivum_tab_token', tabToken);
            sessionStorage.setItem('tab_token', tabToken);
            sessionStorage.setItem('tab_session_id', tabToken);
        }

        window.ensureArchivumTabToken = function () {
            var current = sessionStorage.getItem('archivum_tab_token')
                || sessionStorage.getItem('tab_token')
                || sessionStorage.getItem('tab_session_id')
                || tabToken;
            if (!current) {
                current = generateTabToken();
            }
            sessionStorage.setItem('archivum_tab_token', current);
            sessionStorage.setItem('tab_token', current);
            sessionStorage.setItem('tab_session_id', current);
            window.name = current;
            tabToken = current;
            return current;
        };

        window.getArchivumTabToken = window.ensureArchivumTabToken;
        window.getArchivumTabSessionId = window.ensureArchivumTabToken;

        var currentUrl = new URL(window.location.href);
        var urlTabParam = currentUrl.searchParams.get('_tab');

        // 2. Sincronizar estado autenticado de esta pestaña SIN hacer redirecciones ni recargas en bucle.
        if (isServerAuth && serverTabId) {
            // El servidor autenticó esta petición (ya sea tras POST /login o mediante _tab en la misma pestaña):
            // guardar el tab_token inmediatamente en sessionStorage y mantenerlo en la URL con history.replaceState.
            tabToken = serverTabId;
            sessionStorage.setItem('archivum_tab_token', serverTabId);
            sessionStorage.setItem('tab_token', serverTabId);
            sessionStorage.setItem('tab_session_id', serverTabId);
            sessionStorage.setItem('tab_authenticated', '1');
            sessionStorage.setItem('archivum_tab_ok', '1');
            window.name = serverTabId;

            if (urlTabParam !== serverTabId) {
                currentUrl.searchParams.set('_tab', serverTabId);
                window.history.replaceState(window.history.state || {}, '', currentUrl.toString());
            }
        } else {
            // El servidor renderizó esta petición como Invitado (pestaña nueva, /login o sesión cerrada):
            // limpiar banderas de autenticación locales y quitar cualquier _tab residual de la barra de direcciones
            // usando history.replaceState (NUNCA window.location.replace ni window.location.reload).
            sessionStorage.removeItem('tab_authenticated');
            sessionStorage.removeItem('archivum_tab_ok');

            if (urlTabParam) {
                currentUrl.searchParams.delete('_tab');
                var cleanUrl = currentUrl.pathname + (currentUrl.search ? currentUrl.search : '') + currentUrl.hash;
                window.history.replaceState(window.history.state || {}, '', cleanUrl);
            }
        }

        // 4. Adjuntar X-Tab-Token y X-Tab-Session-Id en todas las peticiones fetch() y XMLHttpRequest
        var originalFetch = window.fetch;
        if (typeof originalFetch === 'function') {
            window.fetch = function (input, init) {
                init = init || {};
                var headers = new Headers(init.headers || {});
                var activeToken = window.getArchivumTabToken();
                if (activeToken) {
                    if (!headers.has('X-Tab-Token')) {
                        headers.set('X-Tab-Token', activeToken);
                    }
                    if (!headers.has('X-Tab-Session-Id')) {
                        headers.set('X-Tab-Session-Id', activeToken);
                    }
                }
                init.headers = headers;
                return originalFetch.call(this, input, init);
            };
        }

        var originalXhrOpen = XMLHttpRequest.prototype.open;
        var originalXhrSend = XMLHttpRequest.prototype.send;
        XMLHttpRequest.prototype.open = function () {
            this.__archivumAttachTabHeader = true;
            return originalXhrOpen.apply(this, arguments);
        };
        XMLHttpRequest.prototype.send = function () {
            try {
                if (this.__archivumAttachTabHeader) {
                    var activeToken = window.getArchivumTabToken();
                    this.setRequestHeader('X-Tab-Token', activeToken);
                    this.setRequestHeader('X-Tab-Session-Id', activeToken);
                }
            } catch (e) {}
            return originalXhrSend.apply(this, arguments);
        };

        // 5. Inyectar tab_token en todos los formularios (incluyendo form.submit() programático)
        function injectTabTokenIntoForm(form) {
            if (!form || form.tagName !== 'FORM') return;
            var activeToken = window.getArchivumTabToken();

            ['tab_token', 'tab_session_id', '_tab'].forEach(function (fieldName) {
                var input = form.querySelector('input[name="' + fieldName + '"]');
                if (!input) {
                    input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = fieldName;
                    form.appendChild(input);
                }
                input.value = activeToken;
            });
        }

        if (typeof HTMLFormElement !== 'undefined' && HTMLFormElement.prototype) {
            var originalFormSubmit = HTMLFormElement.prototype.submit;
            HTMLFormElement.prototype.submit = function () {
                injectTabTokenIntoForm(this);
                return originalFormSubmit.apply(this, arguments);
            };
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('form').forEach(injectTabTokenIntoForm);
        });

        window.addEventListener('admin-spa-navigated', function () {
            document.querySelectorAll('form').forEach(injectTabTokenIntoForm);
        });

        // 6. Interceptar envío de formularios (Login, Logout y Carrito para invitados)
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form || form.tagName !== 'FORM') return;

            injectTabTokenIntoForm(form);
            var action = (form.getAttribute('action') || '').toLowerCase();

            if (action.indexOf('/login') !== -1 || action.indexOf('/register') !== -1 || form.id === 'drawer-login-form' || form.id === 'drawer-register-form') {
                sessionStorage.setItem('tab_authenticated', '1');
                sessionStorage.setItem('archivum_tab_ok', '1');
                return;
            }

            if (action.indexOf('/logout') !== -1) {
                sessionStorage.removeItem('tab_authenticated');
                sessionStorage.removeItem('archivum_tab_ok');
                return;
            }

            // Si el usuario es invitado en esta pestaña, bloquear envío de formularios de carrito/reserva
            if (!isServerAuth) {
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

        // 7. Interceptar clics en enlaces internos (para propagar _tab dentro de la misma pestaña autenticada)
        //    y clics de invitados en botones de RESERVAR / Carrito
        document.addEventListener('click', function (e) {
            if (!e.target || typeof e.target.closest !== 'function') return;

            var logoutForm = e.target.closest('form[action*="/logout"]');
            if (logoutForm) {
                injectTabTokenIntoForm(logoutForm);
                sessionStorage.removeItem('tab_authenticated');
                sessionStorage.removeItem('archivum_tab_ok');
                return;
            }

            if (!isServerAuth) {
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

            var link = e.target.closest('a[href]');
            if (!link) return;

            var rawHref = link.getAttribute('href') || '';
            if (!rawHref || rawHref.charAt(0) === '#' || rawHref.indexOf('javascript:') === 0) {
                return;
            }

            try {
                var targetUrl = new URL(link.href, window.location.origin);
                if (targetUrl.origin !== window.location.origin) {
                    return;
                }

                var isNewTabGesture = e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || (e.button !== undefined && e.button !== 0) || link.target === '_blank';
                if (isNewTabGesture) {
                    // Si abre el enlace en una pestaña nueva, quitar _tab para que la nueva pestaña sea Invitado
                    if (targetUrl.searchParams.has('_tab')) {
                        targetUrl.searchParams.delete('_tab');
                        link.href = targetUrl.toString();
                    }
                    return;
                }

                // Navegación normal dentro de la misma pestaña autenticada: anexar _tab
                if (isServerAuth || sessionStorage.getItem('tab_authenticated') === '1') {
                    var activeToken = window.getArchivumTabToken();
                    if (activeToken) {
                        targetUrl.searchParams.set('_tab', activeToken);
                        link.href = targetUrl.toString();
                    }
                }
            } catch (err) {}
        }, true);

        window.requireAuthForReservation = function (intendedUrl, customMsg) {
            var cleanIntended = intendedUrl || window.location.href;
            try {
                var parsedIntended = new URL(cleanIntended, window.location.origin);
                parsedIntended.searchParams.delete('_tab');
                cleanIntended = parsedIntended.toString();
                sessionStorage.setItem('intended_after_auth', cleanIntended);
            } catch (err) {}

            var msg = customMsg || AUTH_REQUIRED_MSG;

            if (typeof window.cerrarModalDetalleProducto === 'function') {
                window.cerrarModalDetalleProducto();
            }

            var closeCartBtn = document.getElementById('close-cart-btn');
            var cartDrawer = document.getElementById('cart-drawer');
            if (closeCartBtn && cartDrawer && !cartDrawer.classList.contains('translate-x-full')) {
                closeCartBtn.click();
            }

            if (typeof window.openLoginDrawer === 'function') {
                window.openLoginDrawer('login', cleanIntended, msg);
            } else if (window.location.pathname !== '/login') {
                window.location.href = '{{ route('login', [], false) }}?intended=' + encodeURIComponent(cleanIntended);
            }
        };
    } catch (e) {}
})();
</script>
