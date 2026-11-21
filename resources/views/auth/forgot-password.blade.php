@extends('layouts.guest')

@section('content')
<div class="bg-white/95 dark:bg-[#171024]/95 backdrop-blur-md rounded-3xl p-6 sm:p-8 shadow-xl border border-purple-200/80 dark:border-purple-500/30 space-y-6 transition-all duration-300">
    
    <!-- Encabezado con Icono y Tipografía -->
    <div class="text-center space-y-2">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-primary-container text-white shadow-xs mb-1">
            <span class="material-symbols-outlined text-[26px]">lock_reset</span>
        </div>
        <h1 class="text-2xl font-bold font-brand-serif tracking-[0.1em] text-on-surface uppercase">
            ¿Olvidaste tu Contraseña?
        </h1>
        <p class="text-xs text-on-surface-variant font-quicksand max-w-sm mx-auto leading-relaxed">
            Ingresa el correo electrónico de tu cuenta y te enviaremos un enlace seguro (válido por 60 minutos) para restablecer tu contraseña.
        </p>
    </div>

    <!-- Alertas de Estado -->
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
                <span>Atención:</span>
            </div>
            @foreach ($errors->all() as $error)
                <p class="pl-6 font-quicksand">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div class="space-y-1.5">
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">
                Correo Electrónico
            </label>
            <div class="relative">
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
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

        <button
            type="submit"
            class="btn-glass-purple w-full py-3.5 px-4 rounded-2xl text-white font-bold uppercase tracking-wider text-xs transition shadow-md flex items-center justify-center gap-2 mt-2 cursor-pointer"
        >
            <span class="material-symbols-outlined text-[18px]">send</span>
            <span>Enviar Enlace de Recuperación</span>
        </button>
    </form>

    <div class="pt-4 border-t border-purple-200/60 dark:border-purple-500/30 text-center">
        <a href="{{ route('login') }}" class="btn-glass-surface inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold font-quicksand">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            <span>Volver al inicio de sesión</span>
        </a>
    </div>

</div>
@endsection
