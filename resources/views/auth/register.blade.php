@extends('layouts.guest')

@section('content')
<div class="bg-surface-container rounded-2xl p-6 sm:p-8 shadow-sm border border-outline-variant/30 space-y-6">
    <div class="text-center space-y-2">
        <h1 class="text-2xl font-bold font-quicksand text-on-surface">Registro de Cliente</h1>
        <p class="text-sm text-on-surface-variant font-label-md">Crea tu cuenta de cliente para adquirir piezas exclusivas</p>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-error/10 border border-error/20 text-error text-sm font-label-md space-y-1">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1">
                <label for="nombre" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Nombre(s)</label>
                <input id="nombre" type="text" name="nombre" value="{{ old('nombre') }}" required autofocus
                    class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary text-sm font-quicksand"
                    placeholder="Ej. Carlos">
            </div>

            <div class="space-y-1">
                <label for="apellidos" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Apellidos</label>
                <input id="apellidos" type="text" name="apellidos" value="{{ old('apellidos') }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary text-sm font-quicksand"
                    placeholder="Ej. Mendoza">
            </div>
        </div>

        <div class="space-y-1">
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Correo Electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary text-sm font-quicksand"
                placeholder="cliente@ejemplo.com">
        </div>

        <div class="space-y-1">
            <label for="telefono" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Teléfono (opcional)</label>
            <input id="telefono" type="tel" name="telefono" value="{{ old('telefono') }}"
                class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary text-sm font-quicksand"
                placeholder="5512345678">
        </div>

        <div class="space-y-1">
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Contraseña (Mínimo 8 caracteres)</label>
            <input id="password" type="password" name="password" required
                class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary text-sm font-quicksand"
                placeholder="••••••••">
        </div>

        <div class="space-y-1">
            <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Confirmar Contraseña</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary text-sm font-quicksand"
                placeholder="••••••••">
        </div>

        <button type="submit"
            class="w-full py-3 px-4 rounded-xl bg-primary hover:bg-primary-hover text-on-primary font-bold uppercase tracking-wider text-xs transition shadow-sm font-quicksand">
            Crear mi Cuenta de Cliente
        </button>
    </form>

    <div class="pt-4 border-t border-outline-variant/30 text-center text-xs font-label-md text-on-surface-variant">
        <span>¿Ya eres miembro de ARCHIVUM?</span>
        <a href="{{ route('login') }}" class="font-bold text-primary hover:underline ml-1">Iniciar sesión</a>
    </div>
</div>
@endsection

