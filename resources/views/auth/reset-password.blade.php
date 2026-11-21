@extends('layouts.guest')

@section('content')
<div class="bg-white/95 dark:bg-[#171024]/95 backdrop-blur-md rounded-3xl p-6 sm:p-8 shadow-xl border border-purple-200/80 dark:border-purple-500/30 space-y-6 transition-all duration-300">
    
    <!-- Encabezado con Icono y Tipografía -->
    <div class="text-center space-y-2">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-primary-container text-white shadow-xs mb-1">
            <span class="material-symbols-outlined text-[26px]">vpn_key</span>
        </div>
        <h1 class="text-2xl font-bold font-brand-serif tracking-[0.1em] text-on-surface uppercase">
            Restablecer Contraseña
        </h1>
        <p class="text-xs text-on-surface-variant font-quicksand max-w-sm mx-auto leading-relaxed">
            Verifica tu correo electrónico y escribe tu nueva contraseña para recuperar el acceso a tu cuenta en ARCHIVUM.
        </p>
    </div>

    <!-- Alerta de Estado / Notificación -->
    @if (session('status'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-700/60 text-emerald-800 dark:text-emerald-200 text-xs flex items-center gap-3 shadow-2xs" role="alert">
            <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-[20px]">check_circle</span>
            <span class="font-medium font-quicksand leading-relaxed">{{ session('status') }}</span>
        </div>
    @endif

    <!-- Errores de Validación -->
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-700/60 text-rose-800 dark:text-rose-200 text-xs space-y-1 shadow-2xs" role="alert">
            <div class="flex items-center gap-2 font-bold mb-1">
                <span class="material-symbols-outlined text-rose-600 text-[18px]">error</span>
                <span>Por favor corrige los siguientes aspectos:</span>
            </div>
            @foreach ($errors->all() as $error)
                <p class="pl-6 font-quicksand">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf

        <!-- 1. Correo Electrónico -->
        <div class="space-y-1.5">
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">
                Correo Electrónico
            </label>
            <div class="relative">
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $email) }}"
                    required
                    autofocus
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-purple-200/80 dark:border-purple-500/30 bg-surface-container-low/70 dark:bg-[#1f1530] text-on-surface placeholder:text-outline text-sm font-quicksand focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition"
                    placeholder="coleccionista@archivum.mx"
                />
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">
                    mail
                </span>
            </div>
        </div>

        <!-- 2. Código / Token de Verificación Temporal -->
        <div class="space-y-1.5">
            <div class="flex items-center justify-between">
                <label for="token" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">
                    Token de Verificación
                </label>
                <span class="text-[10px] text-primary dark:text-[#ccbeff] font-label-code font-bold">
                    64 caracteres &middot; 60 min
                </span>
            </div>
            <div class="relative">
                <input
                    id="token"
                    type="text"
                    name="token"
                    value="{{ old('token', $token) }}"
                    required
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-purple-200/80 dark:border-purple-500/30 bg-surface-container-low/70 dark:bg-[#1f1530] text-on-surface placeholder:text-outline text-xs font-mono focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition select-all"
                    placeholder="Pega el código recibido por correo..."
                />
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">
                    key
                </span>
            </div>
            <p class="text-[10px] text-on-surface-variant dark:text-stone-300 font-quicksand">
                Si abriste el enlace desde tu correo, este campo ya se encuentra prellenado automáticamente.
            </p>
        </div>

        <!-- 3. Nueva Contraseña -->
        <div class="space-y-1.5">
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">
                Nueva Contraseña (Mínimo 8 caracteres)
            </label>
            <div class="relative">
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-purple-200/80 dark:border-purple-500/30 bg-surface-container-low/70 dark:bg-[#1f1530] text-on-surface placeholder:text-outline text-sm font-quicksand focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition"
                    placeholder="••••••••••••"
                />
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">
                    lock
                </span>
            </div>
        </div>

        <!-- 4. Confirmación de Nueva Contraseña -->
        <div class="space-y-1.5">
            <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">
                Confirmar Nueva Contraseña
            </label>
            <div class="relative">
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-purple-200/80 dark:border-purple-500/30 bg-surface-container-low/70 dark:bg-[#1f1530] text-on-surface placeholder:text-outline text-sm font-quicksand focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition"
                    placeholder="••••••••••••"
                />
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">
                    lock_reset
                </span>
            </div>
        </div>

        <!-- Botón de Envío Glossy Glassmorphism -->
        <button
            type="submit"
            class="btn-glass-purple w-full py-3.5 px-4 rounded-2xl text-white font-bold uppercase tracking-wider text-xs transition shadow-md flex items-center justify-center gap-2 mt-2 cursor-pointer"
        >
            <span class="material-symbols-outlined text-[18px]">verified_user</span>
            <span>Restablecer Contraseña</span>
        </button>
    </form>

    <div class="pt-4 border-t border-purple-200/60 dark:border-purple-500/30 flex flex-col sm:flex-row items-center justify-between gap-2 text-center">
        <a href="{{ route('password.request') }}" class="text-xs text-primary hover:underline font-quicksand font-semibold">
            ¿No recibiste el correo? Solicitar nuevo enlace
        </a>
        <a href="{{ route('login') }}" class="btn-glass-surface inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold font-quicksand">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            <span>Volver al login</span>
        </a>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Asegurar que al enviar el formulario de nueva contraseña se incluya el tab_token de esta pestaña
    var resetForm = document.querySelector('form[action*="reset-password"]');
    if (!resetForm) return;
    resetForm.addEventListener('submit', function () {
        try {
            var token = sessionStorage.getItem('archivum_tab_token')
                || sessionStorage.getItem('tab_token')
                || ('tab_' + Date.now().toString(36) + '_' + Math.random().toString(36).substring(2, 14));
            sessionStorage.setItem('archivum_tab_token', token);
            sessionStorage.setItem('tab_token', token);
            sessionStorage.setItem('tab_session_id', token);
            sessionStorage.setItem('tab_authenticated', '1');
            sessionStorage.setItem('archivum_tab_ok', '1');
            window.name = token;

            var input = resetForm.querySelector('input[name="tab_token"]');
            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'tab_token';
                resetForm.appendChild(input);
            }
            input.value = token;
        } catch (e) {}
    });
});
</script>
@endsection
