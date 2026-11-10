@php
    $esRutaAuth = request()->routeIs('login', 'admin.login', 'register');
    $abrirPorDefecto = $esRutaAuth || $errors->any() || request('login') === '1';
    $tabInicial = (request()->routeIs('register') || request('tab') === 'register') ? 'register' : 'login';
@endphp

<!-- Overlay Translúcido de Acceso Lateral (Atmósfera de Bóveda Nocturna) -->
<div id="login-drawer-overlay" class="fixed inset-0 bg-[#07040d]/85 backdrop-blur-sm z-50 transition-opacity duration-300 {{ $abrirPorDefecto ? 'opacity-100' : 'opacity-0 pointer-events-none' }}" aria-hidden="true"></div>

<!-- Cajón Desplegable de Autenticación al 50% de Ancho (Login Slide-Over Drawer) -->
<aside
    id="login-drawer"
    class="fixed top-0 right-0 h-full w-full md:w-1/2 bg-surface-container-lowest dark:bg-[#171024] border-l border-purple-200/80 dark:border-purple-500/30 z-50 shadow-2xl flex flex-col justify-between transform {{ $abrirPorDefecto ? 'translate-x-0' : 'translate-x-full' }} transition-transform duration-300 ease-out"
    role="dialog"
    aria-modal="true"
    aria-labelledby="login-drawer-title"
>
    <!-- Encabezado del Slide-Over tipo Bóveda -->
    <div class="p-6 sm:p-8 border-b border-purple-200/70 dark:border-purple-500/30 bg-surface-container-low/50 dark:bg-[#1b132a]">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-primary-container text-white flex items-center justify-center shadow-sm border border-purple-300/40">
                    <span class="material-symbols-outlined text-[24px]">passkey</span>
                </div>
                <div>
                    <h2 id="login-drawer-title" class="font-headline-sm text-lg text-on-surface font-bold font-quicksand">
                        Bóveda de Autenticación ARCHIVUM
                    </h2>
                    <span class="font-label-code text-secondary text-[11px] uppercase tracking-wider">
                        Acceso Cifrado para Coleccionistas &amp; Personal
                    </span>
                </div>
            </div>
            <button
                type="button"
                id="close-login-drawer-btn"
                class="p-2 rounded-xl text-outline hover:text-on-surface hover:bg-surface-container dark:hover:bg-[#221834] transition focus:outline-none"
                aria-label="Cerrar panel de acceso"
            >
                <span class="material-symbols-outlined text-[22px]">close</span>
            </button>
        </div>

        <!-- Selector de Pestañas: Iniciar Sesión / Crear Cuenta -->
        <div class="mt-5 grid grid-cols-2 gap-2 p-1 rounded-xl bg-surface-container dark:bg-[#140d20] border border-purple-200/60 dark:border-purple-500/30">
            <button
                type="button"
                id="tab-login-btn"
                data-drawer-tab="login"
                class="py-2 px-4 rounded-lg text-xs font-bold uppercase tracking-wider font-quicksand transition bg-primary-container text-white shadow-2xs flex items-center justify-center gap-1.5"
            >
                <span class="material-symbols-outlined text-[16px]">login</span>
                <span>Iniciar Sesión</span>
            </button>
            <button
                type="button"
                id="tab-register-btn"
                data-drawer-tab="register"
                class="py-2 px-4 rounded-lg text-xs font-bold uppercase tracking-wider font-quicksand transition text-on-surface-variant hover:text-on-surface flex items-center justify-center gap-1.5"
            >
                <span class="material-symbols-outlined text-[16px]">person_add</span>
                <span>Crear Cuenta</span>
            </button>
        </div>
    </div>

    <!-- Cuerpo Desplazable con Formularios y Alertas de Validación en Línea -->
    <div class="flex-1 overflow-y-auto p-6 sm:p-10 space-y-6">
        <div class="max-w-lg mx-auto space-y-6">

            <!-- Contenedor de Errores Dinámico (AJAX + Sesión Laravel) -->
            <div
                id="login-drawer-alert"
                class="{{ $errors->any() ? '' : 'hidden' }} p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-900 text-sm font-label-md space-y-1"
                role="alert"
            >
                <div class="flex items-center gap-2 font-bold text-rose-800">
                    <span class="material-symbols-outlined text-[18px]">error</span>
                    <span>Verifica los datos ingresados:</span>
                </div>
                <ul id="login-drawer-error-list" class="list-disc list-inside text-xs space-y-0.5 pl-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- Banner Informativo de Acceso para Pasarela Notariada -->
            <div
                id="checkout-auth-banner"
                class="{{ (request('intended') === 'checkout' || request('login') === '1' || session('url.intended') === route('checkout.index')) ? '' : 'hidden' }} p-4 rounded-xl bg-primary/10 border border-primary/30 flex items-start gap-3 text-xs text-on-surface"
            >
                <div class="w-8 h-8 rounded-lg bg-primary-container text-white flex items-center justify-center shrink-0 mt-0.5">
                    <span class="material-symbols-outlined text-[18px]">verified_user</span>
                </div>
                <div class="space-y-0.5">
                    <p class="font-bold font-quicksand text-primary text-sm">Identificación para Pasarela Notariada</p>
                    <p class="text-on-surface-variant text-[11px] leading-relaxed">
                        Inicia sesión o crea tu cuenta para formalizar la adquisición. <strong>Tus piezas en custodia y reserva temporal se mantienen intactas.</strong>
                    </p>
                </div>
            </div>

            <!-- Panel 1: Formulario de Inicio de Sesión -->
            <div id="drawer-panel-login" class="space-y-5">
                <div class="space-y-1">
                    <h3 class="text-xl font-bold font-quicksand text-on-surface">Bienvenido de nuevo</h3>
                    <p class="text-xs text-on-surface-variant font-label-md">
                        Ingresa tus credenciales para gestionar tus adquisiciones, reservas o acceder al panel operativo según tu rol.
                    </p>
                </div>

                <form id="drawer-login-form" method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="intended" id="drawer-login-intended" value="{{ request('intended') ?? session('url.intended') }}" />

                    <div class="space-y-1.5">
                        <label for="drawer-login-email" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">
                            Correo Electrónico
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined text-[18px] text-outline absolute left-3.5 top-1/2 -translate-y-1/2">mail</span>
                            <input
                                id="drawer-login-email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-purple-200/90 dark:border-purple-500/40 bg-white dark:bg-[#140d20] text-on-surface dark:text-stone-100 placeholder:text-outline/70 dark:placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary focus:dark:bg-[#1a1228] focus:dark:border-purple-400 text-sm font-quicksand"
                                placeholder="coleccionista@archivum.com"
                            />
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="drawer-login-password" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">
                                Contraseña
                            </label>
                            <a href="{{ route('password.request') }}" class="text-xs text-primary hover:underline font-label-md">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                        <div class="relative">
                            <span class="material-symbols-outlined text-[18px] text-outline absolute left-3.5 top-1/2 -translate-y-1/2">key</span>
                            <input
                                id="drawer-login-password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-purple-200/90 dark:border-purple-500/40 bg-white dark:bg-[#140d20] text-on-surface dark:text-stone-100 placeholder:text-outline/70 dark:placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary focus:dark:bg-[#1a1228] focus:dark:border-purple-400 text-sm font-quicksand"
                                placeholder="••••••••"
                            />
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-label-md text-on-surface-variant dark:text-stone-300">
                            <input type="checkbox" name="remember" value="1" class="rounded border-purple-300 dark:border-purple-500 text-primary focus:ring-primary dark:bg-[#140d20]" />
                            <span>Mantener sesión activa en este dispositivo</span>
                        </label>
                    </div>

                    <button
                        type="submit"
                        id="drawer-login-submit"
                        class="w-full py-3.5 px-5 rounded-xl bg-primary-container hover:bg-primary text-white font-bold uppercase tracking-wider text-xs transition shadow-sm font-quicksand flex items-center justify-center gap-2"
                    >
                        <span class="material-symbols-outlined text-[18px]">lock_open</span>
                        <span id="drawer-login-submit-text">Entrar a la Bóveda</span>
                    </button>
                </form>

                <!-- Tarjeta Informativa de Roles -->
                <div class="p-4 rounded-xl bg-surface-container-low dark:bg-[#1b132a] border border-purple-200/60 dark:border-purple-500/30 space-y-2 text-xs text-on-surface-variant dark:text-stone-300">
                    <div class="flex items-center gap-2 text-primary font-bold font-label-code uppercase tracking-wider">
                        <span class="material-symbols-outlined text-[16px]">verified_user</span>
                        <span>Redirección Inteligente por Rol</span>
                    </div>
                    <p class="leading-relaxed">
                        Los <strong class="text-on-surface dark:text-white">clientes</strong> continúan en la galería pública con su carrito activo. El <strong class="text-on-surface dark:text-white">personal autorizado</strong> (Administración, Ventas, Almacén y Compras) ingresa directamente a su terminal operativa.
                    </p>
                </div>
            </div>

            <!-- Panel 2: Formulario de Registro Público de Clientes -->
            <div id="drawer-panel-register" class="hidden space-y-5">
                <div class="space-y-1">
                    <h3 class="text-xl font-bold font-quicksand text-on-surface">Registro de Coleccionista</h3>
                    <p class="text-xs text-on-surface-variant dark:text-stone-300 font-label-md">
                        Crea tu cuenta personal de cliente para apartar piezas únicas y gestionar tus envíos certificados.
                    </p>
                </div>

                <form id="drawer-register-form" method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="intended" id="drawer-register-intended" value="{{ request('intended') ?? session('url.intended') }}" />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label for="drawer-reg-nombre" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Nombre(s)</label>
                            <input
                                id="drawer-reg-nombre"
                                type="text"
                                name="nombre"
                                value="{{ old('nombre') }}"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200/90 dark:border-purple-500/40 bg-white dark:bg-[#140d20] text-on-surface dark:text-stone-100 placeholder:text-outline/70 dark:placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-primary focus:dark:bg-[#1a1228] focus:dark:border-purple-400 text-sm font-quicksand"
                                placeholder="Ej. Elena"
                            />
                        </div>
                        <div class="space-y-1">
                            <label for="drawer-reg-apellidos" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Apellidos</label>
                            <input
                                id="drawer-reg-apellidos"
                                type="text"
                                name="apellidos"
                                value="{{ old('apellidos') }}"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200/90 dark:border-purple-500/40 bg-white dark:bg-[#140d20] text-on-surface dark:text-stone-100 placeholder:text-outline/70 dark:placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-primary focus:dark:bg-[#1a1228] focus:dark:border-purple-400 text-sm font-quicksand"
                                placeholder="Ej. Ramírez"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label for="drawer-reg-email" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Correo Electrónico</label>
                            <input
                                id="drawer-reg-email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200/90 dark:border-purple-500/40 bg-white dark:bg-[#140d20] text-on-surface dark:text-stone-100 placeholder:text-outline/70 dark:placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-primary focus:dark:bg-[#1a1228] focus:dark:border-purple-400 text-sm font-quicksand"
                                placeholder="correo@ejemplo.com"
                            />
                        </div>
                        <div class="space-y-1">
                            <label for="drawer-reg-telefono" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Teléfono (Opcional)</label>
                            <input
                                id="drawer-reg-telefono"
                                type="tel"
                                name="telefono"
                                value="{{ old('telefono') }}"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200/90 dark:border-purple-500/40 bg-white dark:bg-[#140d20] text-on-surface dark:text-stone-100 placeholder:text-outline/70 dark:placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-primary focus:dark:bg-[#1a1228] focus:dark:border-purple-400 text-sm font-quicksand"
                                placeholder="5512345678"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label for="drawer-reg-password" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Contraseña (Mín. 8)</label>
                            <input
                                id="drawer-reg-password"
                                type="password"
                                name="password"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200/90 dark:border-purple-500/40 bg-white dark:bg-[#140d20] text-on-surface dark:text-stone-100 placeholder:text-outline/70 dark:placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-primary focus:dark:bg-[#1a1228] focus:dark:border-purple-400 text-sm font-quicksand"
                                placeholder="••••••••"
                            />
                        </div>
                        <div class="space-y-1">
                            <label for="drawer-reg-password-confirm" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Confirmar</label>
                            <input
                                id="drawer-reg-password-confirm"
                                type="password"
                                name="password_confirmation"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200/90 dark:border-purple-500/40 bg-white dark:bg-[#140d20] text-on-surface dark:text-stone-100 placeholder:text-outline/70 dark:placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-primary focus:dark:bg-[#1a1228] focus:dark:border-purple-400 text-sm font-quicksand"
                                placeholder="••••••••"
                            />
                        </div>
                    </div>

                    <button
                        type="submit"
                        id="drawer-register-submit"
                        class="w-full py-3.5 px-5 rounded-xl bg-primary-container hover:bg-primary text-white font-bold uppercase tracking-wider text-xs transition shadow-sm font-quicksand flex items-center justify-center gap-2"
                    >
                        <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                        <span id="drawer-register-submit-text">Crear mi Cuenta de Cliente</span>
                    </button>
                </form>
            </div>

        </div>
    </div>

        <!-- Pie del Cajón de Autenticación -->
    <div class="p-6 border-t border-purple-200/70 dark:border-purple-500/30 bg-surface-container-lowest dark:bg-[#140d20] flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] text-outline dark:text-stone-400 font-label-code">
        <div class="flex items-center gap-1.5">
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
            <span>Protocolo SSL / TLS 1.3 · Sesión Protegida</span>
        </div>
        <button type="button" data-open-login-drawer="login" class="text-primary hover:underline font-semibold cursor-pointer">
            Acceso Unificado (Clientes &amp; Personal Interno) &rarr;
        </button>
    </div>
</aside>

<script>
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const drawer = document.getElementById('login-drawer');
        const overlay = document.getElementById('login-drawer-overlay');
        const closeBtn = document.getElementById('close-login-drawer-btn');
        const tabLoginBtn = document.getElementById('tab-login-btn');
        const tabRegisterBtn = document.getElementById('tab-register-btn');
        const panelLogin = document.getElementById('drawer-panel-login');
        const panelRegister = document.getElementById('drawer-panel-register');
        const alertBox = document.getElementById('login-drawer-alert');
        const errorList = document.getElementById('login-drawer-error-list');

        if (!drawer || !overlay) {
            return;
        }

        function switchTab(tab) {
            if (!panelLogin || !panelRegister || !tabLoginBtn || !tabRegisterBtn) {
                return;
            }
            if (tab === 'register') {
                panelLogin.classList.add('hidden');
                panelRegister.classList.remove('hidden');
                tabRegisterBtn.classList.add('bg-primary-container', 'text-white', 'shadow-2xs');
                tabRegisterBtn.classList.remove('text-on-surface-variant');
                tabLoginBtn.classList.remove('bg-primary-container', 'text-white', 'shadow-2xs');
                tabLoginBtn.classList.add('text-on-surface-variant');
            } else {
                panelRegister.classList.add('hidden');
                panelLogin.classList.remove('hidden');
                tabLoginBtn.classList.add('bg-primary-container', 'text-white', 'shadow-2xs');
                tabLoginBtn.classList.remove('text-on-surface-variant');
                tabRegisterBtn.classList.remove('bg-primary-container', 'text-white', 'shadow-2xs');
                tabRegisterBtn.classList.add('text-on-surface-variant');
            }
        }

        function toggleLoginDrawer(open, tab) {
            if (tab) {
                switchTab(tab);
            }
            if (open) {
                drawer.classList.remove('translate-x-full');
                overlay.classList.remove('opacity-0', 'pointer-events-none');
                overlay.classList.add('opacity-100');
                document.body.style.overflow = 'hidden';
                const emailInput = document.getElementById(tab === 'register' ? 'drawer-reg-nombre' : 'drawer-login-email');
                if (emailInput) {
                    setTimeout(() => emailInput.focus(), 150);
                }
            } else {
                drawer.classList.remove('translate-x-0');
                drawer.classList.add('translate-x-full');
                overlay.classList.add('opacity-0', 'pointer-events-none');
                overlay.classList.remove('opacity-100');
                document.body.style.overflow = '';
                if (['/login', '/register', '/admin/login'].includes(window.location.pathname)) {
                    window.history.replaceState({}, '', '{{ route('public.home') }}');
                }
            }
        }

        // Exponer globalmente para cualquier disparador en la página
        window.openLoginDrawer = function (tab, intended) {
            if (intended) {
                const loginIntended = document.getElementById('drawer-login-intended');
                if (loginIntended) {
                    loginIntended.value = intended;
                }
                const regIntended = document.getElementById('drawer-register-intended');
                if (regIntended) {
                    regIntended.value = intended;
                }
                const banner = document.getElementById('checkout-auth-banner');
                if (banner) {
                    banner.classList.remove('hidden');
                }
                sessionStorage.setItem('intended_after_auth', intended);
            }
            toggleLoginDrawer(true, tab || 'login');
        };

        // Vincular todos los botones con atributo data-open-login-drawer
        document.querySelectorAll('[data-open-login-drawer]').forEach(function (trigger) {
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                const tab = trigger.getAttribute('data-open-login-drawer') || 'login';
                toggleLoginDrawer(true, tab);
            });
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', () => toggleLoginDrawer(false));
        }
        if (overlay) {
            overlay.addEventListener('click', () => toggleLoginDrawer(false));
        }
        if (tabLoginBtn) {
            tabLoginBtn.addEventListener('click', () => switchTab('login'));
        }
        if (tabRegisterBtn) {
            tabRegisterBtn.addEventListener('click', () => switchTab('register'));
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !drawer.classList.contains('translate-x-full')) {
                toggleLoginDrawer(false);
            }
        });

        // Envío AJAX para mostrar errores dentro del mismo modal lateral sin recargar
        function bindAjaxForm(formId, submitBtnId, submitTextId, defaultLabel) {
            const form = document.getElementById(formId);
            const submitBtn = document.getElementById(submitBtnId);
            const submitText = document.getElementById(submitTextId);

            if (!form) {
                return;
            }

            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                if (alertBox) {
                    alertBox.classList.add('hidden');
                }
                if (errorList) {
                    errorList.innerHTML = '';
                }
                if (submitBtn) {
                    submitBtn.disabled = true;
                }
                if (submitText) {
                    submitText.textContent = 'Verificando...';
                }

                try {
                    const formData = new FormData(form);
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok && data.status === 'success') {
                        const intendedAfterAuth = sessionStorage.getItem('intended_after_auth');
                        if (intendedAfterAuth) {
                            sessionStorage.removeItem('intended_after_auth');
                            window.location.href = intendedAfterAuth;
                            return;
                        }
                        window.location.href = data.redirect || '{{ route('public.home') }}';
                        return;
                    }

                    const messages = [];
                    if (data.errors) {
                        Object.values(data.errors).forEach(function (errArr) {
                            errArr.forEach(function (msg) {
                                messages.push(msg);
                            });
                        });
                    } else if (data.message) {
                        messages.push(data.message);
                    } else {
                        messages.push('No fue posible procesar la solicitud. Verifica tus datos.');
                    }

                    if (errorList && alertBox) {
                        messages.forEach(function (msg) {
                            const li = document.createElement('li');
                            li.textContent = msg;
                            errorList.appendChild(li);
                        });
                        alertBox.classList.remove('hidden');
                    }
                } catch (error) {
                    form.submit();
                    return;
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                    }
                    if (submitText) {
                        submitText.textContent = defaultLabel;
                    }
                }
            });
        }

        bindAjaxForm('drawer-login-form', 'drawer-login-submit', 'drawer-login-submit-text', 'Entrar a la Bóveda');
        bindAjaxForm('drawer-register-form', 'drawer-register-submit', 'drawer-register-submit-text', 'Crear mi Cuenta de Cliente');

        // Si estamos en /login, /register, /admin/login, ?login=1 o existen errores de validación, abrir el drawer automáticamente
        const params = new URLSearchParams(window.location.search);
        if ({{ $abrirPorDefecto ? 'true' : 'false' }} || params.get('login') === '1') {
            toggleLoginDrawer(true, params.get('tab') === 'register' ? 'register' : '{{ $tabInicial }}');
        }
    });
}());
</script>

