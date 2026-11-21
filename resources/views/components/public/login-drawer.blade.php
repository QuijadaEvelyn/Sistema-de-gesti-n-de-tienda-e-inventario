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
            <div class="flex items-center gap-3 group">
                <x-ui.archivum-logo size="lg" />
                <div>
                    <h2 id="login-drawer-title" class="font-headline-sm text-lg text-on-surface font-bold font-brand-serif tracking-[0.15em] uppercase">
                        Acceso a tu Cuenta ARCHIVUM
                    </h2>
                    <span class="font-label-code text-secondary text-[11px] uppercase tracking-wider">
                        Inicio de Sesión para Clientes &amp; Personal
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
                class="btn-glass-purple py-2 px-4 rounded-lg text-xs font-bold uppercase tracking-wider font-quicksand flex items-center justify-center gap-1.5 shadow-2xs"
            >
                <span class="material-symbols-outlined text-[16px]">login</span>
                <span>Iniciar Sesión</span>
            </button>
            <button
                type="button"
                id="tab-register-btn"
                data-drawer-tab="register"
                class="btn-glass-surface py-2 px-4 rounded-lg text-xs font-bold uppercase tracking-wider font-quicksand flex items-center justify-center gap-1.5 shadow-2xs"
            >
                <span class="material-symbols-outlined text-[16px]">person_add</span>
                <span>Crear Cuenta</span>
            </button>
        </div>
    </div>

    <!-- Cuerpo Desplazable con Formularios y Alertas de Validación en Línea -->
    <div class="flex-1 overflow-y-auto p-6 sm:p-10 space-y-6">
        <div class="max-w-lg mx-auto space-y-6">

            <!-- Contenedor de Confirmación Exitosa Dinámico (AJAX + Sesión Laravel) -->
            <div
                id="login-drawer-success"
                class="hidden p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-900 dark:text-emerald-200 text-sm font-label-md flex items-start gap-2.5"
                role="status"
            >
                <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-[20px] shrink-0 mt-0.5">check_circle</span>
                <div class="space-y-1">
                    <p id="login-drawer-success-text" class="text-xs font-semibold leading-relaxed"></p>
                </div>
            </div>

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

            <!-- Banner Informativo de Acceso Obligatorio para Reserva en Bóveda / Pasarela Notariada -->
            @php
                $avisoAutenticacion = session('auth_notice') ?: session('status') ?: session('warning');
                $mostrarBannerInformativo = request()->filled('intended')
                    || request('login') === '1'
                    || session()->has('url.intended')
                    || ! empty($avisoAutenticacion);
            @endphp
            <div
                id="checkout-auth-banner"
                class="{{ $mostrarBannerInformativo ? '' : 'hidden' }} p-4 rounded-xl bg-primary/10 border border-primary/30 flex items-start gap-3 text-xs text-on-surface"
            >
                <div class="w-8 h-8 rounded-lg bg-primary-container text-white flex items-center justify-center shrink-0 mt-0.5">
                    <span class="material-symbols-outlined text-[18px]">verified_user</span>
                </div>
                <div class="space-y-0.5">
                    <p id="checkout-auth-banner-title" class="font-bold font-quicksand text-primary text-sm">Inicio de Sesión Requerido</p>
                    <p id="checkout-auth-banner-text" class="text-on-surface-variant text-[11px] leading-relaxed font-semibold">
                        {{ $avisoAutenticacion ?: 'Inicia sesión o regístrate para agregar productos al carrito' }}
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
                    <input type="hidden" name="tab_token" id="drawer-login-tab-token" value="" />
                    <input type="hidden" name="tab_session_id" id="drawer-login-tab-id" value="" />

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
                            <a
                                href="{{ route('password.request') }}"
                                id="open-forgot-password-panel-btn"
                                data-open-login-drawer="forgot"
                                class="text-xs text-primary hover:underline font-label-md"
                            >
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
                        class="btn-glass-purple w-full py-3.5 px-5 rounded-xl font-bold uppercase tracking-wider text-xs shadow-md font-quicksand flex items-center justify-center gap-2"
                    >
                        <span class="material-symbols-outlined text-[18px]">lock_open</span>
                        <span id="drawer-login-submit-text">Iniciar Sesión</span>
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
                    <input type="hidden" name="tab_token" id="drawer-register-tab-token" value="" />
                    <input type="hidden" name="tab_session_id" id="drawer-register-tab-id" value="" />

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
                        class="btn-glass-purple w-full py-3.5 px-5 rounded-xl font-bold uppercase tracking-wider text-xs shadow-md font-quicksand flex items-center justify-center gap-2"
                    >
                        <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                        <span id="drawer-register-submit-text">Crear mi Cuenta de Cliente</span>
                    </button>
                </form>
            </div>

            <!-- Panel 3: Formulario de Recuperación de Contraseña por Correo Electrónico (SMTP Gmail) -->
            <div id="drawer-panel-forgot" class="hidden space-y-5">
                <div class="space-y-1">
                    <h3 class="text-xl font-bold font-quicksand text-on-surface">¿Olvidaste tu contraseña?</h3>
                    <p class="text-xs text-on-surface-variant dark:text-stone-300 font-label-md leading-relaxed">
                        Ingresa el correo electrónico de tu cuenta y te enviaremos un enlace seguro (válido por 60 minutos) para restablecer tu contraseña.
                    </p>
                </div>

                <form id="drawer-forgot-form" method="POST" action="{{ route('password.email') }}" class="space-y-4">
                    @csrf
                    <div class="space-y-1.5">
                        <label for="drawer-forgot-email" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">
                            Correo Electrónico Registrado
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined text-[18px] text-outline absolute left-3.5 top-1/2 -translate-y-1/2">mail</span>
                            <input
                                id="drawer-forgot-email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-purple-200/90 dark:border-purple-500/40 bg-white dark:bg-[#140d20] text-on-surface dark:text-stone-100 placeholder:text-outline/70 dark:placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary focus:dark:bg-[#1a1228] focus:dark:border-purple-400 text-sm font-quicksand"
                                placeholder="tu-correo@gmail.com"
                            />
                        </div>
                    </div>

                    <button
                        type="submit"
                        id="drawer-forgot-submit"
                        class="btn-glass-purple w-full py-3.5 px-5 rounded-xl font-bold uppercase tracking-wider text-xs shadow-md font-quicksand flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        <span id="drawer-forgot-submit-text">Enviar Enlace de Recuperación</span>
                    </button>
                </form>

                <div class="flex items-center justify-between pt-2 text-xs">
                    <button
                        type="button"
                        data-open-login-drawer="login"
                        class="text-primary hover:underline font-semibold inline-flex items-center gap-1 cursor-pointer"
                    >
                        <span class="material-symbols-outlined text-[15px]">arrow_back</span>
                        <span>Volver a Iniciar Sesión</span>
                    </button>
                    <a href="{{ route('password.request') }}" class="text-on-surface-variant hover:text-primary hover:underline">
                        Abrir en pantalla completa &rarr;
                    </a>
                </div>
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
        const panelForgot = document.getElementById('drawer-panel-forgot');
        const alertBox = document.getElementById('login-drawer-alert');
        const errorList = document.getElementById('login-drawer-error-list');
        const successBox = document.getElementById('login-drawer-success');
        const successText = document.getElementById('login-drawer-success-text');

        if (!drawer || !overlay) {
            return;
        }

        function switchTab(tab) {
            if (!panelLogin || !panelRegister || !tabLoginBtn || !tabRegisterBtn) {
                return;
            }
            if (alertBox) {
                alertBox.classList.add('hidden');
            }
            if (successBox && tab !== 'forgot') {
                successBox.classList.add('hidden');
            }
            if (tab === 'register') {
                panelLogin.classList.add('hidden');
                if (panelForgot) panelForgot.classList.add('hidden');
                panelRegister.classList.remove('hidden');
                tabRegisterBtn.classList.add('btn-glass-purple');
                tabRegisterBtn.classList.remove('btn-glass-surface');
                tabLoginBtn.classList.remove('btn-glass-purple');
                tabLoginBtn.classList.add('btn-glass-surface');
            } else if (tab === 'forgot') {
                panelLogin.classList.add('hidden');
                panelRegister.classList.add('hidden');
                if (panelForgot) panelForgot.classList.remove('hidden');
                tabLoginBtn.classList.add('btn-glass-surface');
                tabLoginBtn.classList.remove('btn-glass-purple');
                tabRegisterBtn.classList.add('btn-glass-surface');
                tabRegisterBtn.classList.remove('btn-glass-purple');
                const loginEmailVal = document.getElementById('drawer-login-email')?.value || '';
                const forgotEmailEl = document.getElementById('drawer-forgot-email');
                if (forgotEmailEl && loginEmailVal && !forgotEmailEl.value) {
                    forgotEmailEl.value = loginEmailVal;
                }
            } else {
                panelRegister.classList.add('hidden');
                if (panelForgot) panelForgot.classList.add('hidden');
                panelLogin.classList.remove('hidden');
                tabLoginBtn.classList.add('btn-glass-purple');
                tabLoginBtn.classList.remove('btn-glass-surface');
                tabRegisterBtn.classList.remove('btn-glass-purple');
                tabRegisterBtn.classList.add('btn-glass-surface');
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
        window.openLoginDrawer = function (tab, intended, customMessage) {
            const targetIntended = intended || window.location.href;
            const loginIntended = document.getElementById('drawer-login-intended');
            if (loginIntended) {
                loginIntended.value = targetIntended;
            }
            const regIntended = document.getElementById('drawer-register-intended');
            if (regIntended) {
                regIntended.value = targetIntended;
            }
            const banner = document.getElementById('checkout-auth-banner');
            const bannerText = document.getElementById('checkout-auth-banner-text');
            if (banner) {
                banner.classList.remove('hidden');
            }
            if (bannerText) {
                bannerText.textContent = customMessage || 'Inicia sesión o regístrate para reservar piezas en la bóveda';
            }
            try {
                sessionStorage.setItem('intended_after_auth', targetIntended);
            } catch (e) {}
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
                try {
                    sessionStorage.setItem('archivum_tab_ok', '1');
                    sessionStorage.removeItem('archivum_tab_logout_done');
                } catch (err) {}
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
                    let currentTabId = '';
                    if (typeof window.ensureArchivumTabToken === 'function') {
                        currentTabId = window.ensureArchivumTabToken();
                    } else if (typeof window.getArchivumTabToken === 'function') {
                        currentTabId = window.getArchivumTabToken();
                    } else {
                        currentTabId = sessionStorage.getItem('archivum_tab_token')
                            || sessionStorage.getItem('tab_token')
                            || sessionStorage.getItem('tab_session_id')
                            || '';
                        if (!currentTabId) {
                            currentTabId = 'tab_' + Date.now().toString(36) + '_' + Math.random().toString(36).substring(2, 14);
                        }
                        sessionStorage.setItem('archivum_tab_token', currentTabId);
                        sessionStorage.setItem('tab_token', currentTabId);
                        sessionStorage.setItem('tab_session_id', currentTabId);
                    }

                    ['tab_token', 'tab_session_id'].forEach(function (fieldName) {
                        let hiddenInput = form.querySelector('input[name="' + fieldName + '"]');
                        if (!hiddenInput) {
                            hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = fieldName;
                            form.appendChild(hiddenInput);
                        }
                        hiddenInput.value = currentTabId;
                    });

                    const formData = new FormData(form);
                    if (currentTabId) {
                        formData.set('tab_token', currentTabId);
                        formData.set('tab_session_id', currentTabId);
                    }
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-Tab-Token': currentTabId,
                            'X-Tab-Session-Id': currentTabId,
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok && data.status === 'success') {
                        const boundTabId = data.tab_token || data.tab_session_id || currentTabId;
                        let targetRedirect = data.redirect || '{{ route('public.home') }}';
                        try {
                            if (boundTabId) {
                                sessionStorage.setItem('archivum_tab_token', boundTabId);
                                sessionStorage.setItem('tab_token', boundTabId);
                                sessionStorage.setItem('tab_session_id', boundTabId);
                                window.name = boundTabId;
                            }
                            sessionStorage.setItem('tab_authenticated', '1');
                            sessionStorage.setItem('archivum_tab_ok', '1');
                            sessionStorage.removeItem('intended_after_auth');

                            if (boundTabId) {
                                const redirectUrlObj = new URL(targetRedirect, window.location.origin);
                                redirectUrlObj.searchParams.set('_tab', boundTabId);
                                targetRedirect = redirectUrlObj.toString();
                            }
                        } catch (err) {}
                        window.location.href = targetRedirect;
                        return;
                    }

                    // Si el backend responde con aviso informativo de sesión requerida (401 unauthenticated),
                    // mostrarlo en el banner informativo azul/púrpura y NUNCA en el cuadro rojo de credenciales incorrectas
                    if (response.status === 401 || data.status === 'unauthenticated') {
                        const banner = document.getElementById('checkout-auth-banner');
                        const bannerText = document.getElementById('checkout-auth-banner-text');
                        if (banner) {
                            banner.classList.remove('hidden');
                        }
                        if (bannerText && data.message) {
                            bannerText.textContent = data.message;
                        }
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

        bindAjaxForm('drawer-login-form', 'drawer-login-submit', 'drawer-login-submit-text', 'Iniciar Sesión');
        bindAjaxForm('drawer-register-form', 'drawer-register-submit', 'drawer-register-submit-text', 'Crear mi Cuenta de Cliente');

        // Formulario AJAX de Recuperación de Contraseña en el Drawer
        const forgotForm = document.getElementById('drawer-forgot-form');
        const forgotSubmitBtn = document.getElementById('drawer-forgot-submit');
        const forgotSubmitText = document.getElementById('drawer-forgot-submit-text');

        if (forgotForm) {
            forgotForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                if (alertBox) alertBox.classList.add('hidden');
                if (errorList) errorList.innerHTML = '';
                if (successBox) successBox.classList.add('hidden');
                if (forgotSubmitBtn) forgotSubmitBtn.disabled = true;
                if (forgotSubmitText) forgotSubmitText.textContent = 'Enviando enlace...';

                try {
                    const formData = new FormData(forgotForm);
                    const response = await fetch(forgotForm.action, {
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
                        if (successBox && successText) {
                            successText.textContent = data.message || 'Te enviamos un enlace a tu correo para restablecer tu contraseña.';
                            successBox.classList.remove('hidden');
                        }
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
                        messages.push('No pudimos enviar el correo de recuperación en este momento.');
                    }

                    if (errorList && alertBox) {
                        messages.forEach(function (msg) {
                            const li = document.createElement('li');
                            li.textContent = msg;
                            errorList.appendChild(li);
                        });
                        alertBox.classList.remove('hidden');
                    }
                } catch (err) {
                    forgotForm.submit();
                } finally {
                    if (forgotSubmitBtn) forgotSubmitBtn.disabled = false;
                    if (forgotSubmitText) forgotSubmitText.textContent = 'Enviar Enlace de Recuperación';
                }
            });
        }

        // Si estamos en /login, /register, /admin/login, ?login=1 o existen errores de validación, abrir el drawer automáticamente
        const params = new URLSearchParams(window.location.search);
        if ({{ $abrirPorDefecto ? 'true' : 'false' }} || params.get('login') === '1') {
            toggleLoginDrawer(true, params.get('tab') === 'register' ? 'register' : '{{ $tabInicial }}');
        }
    });
}());
</script>

