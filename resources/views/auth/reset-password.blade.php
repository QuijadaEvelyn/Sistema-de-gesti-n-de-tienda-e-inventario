@extends('layouts.guest')

@section('content')
<div class="bg-surface-container rounded-2xl p-6 sm:p-8 shadow-sm border border-outline-variant/30 space-y-6">
    <div class="text-center space-y-2">
        <h1 class="text-2xl font-bold font-quicksand text-on-surface">Restablecer Contraseña</h1>
        <p class="text-sm text-on-surface-variant font-label-md">Ingresa y confirma tu nueva clave de acceso seguro</p>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-error/10 border border-error/20 text-error text-sm font-label-md space-y-1">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="space-y-1">
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Correo Electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autofocus
                class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary text-sm font-quicksand"
                placeholder="usuario@archivum.com">
        </div>

        <div class="space-y-1">
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Nueva Contraseña (Mínimo 8 caracteres)</label>
            <input id="password" type="password" name="password" required
                class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary text-sm font-quicksand"
                placeholder="••••••••">
        </div>

        <div class="space-y-1">
            <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Confirmar Nueva Contraseña</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary text-sm font-quicksand"
                placeholder="••••••••">
        </div>

        <button type="submit"
            class="w-full py-3 px-4 rounded-xl bg-primary hover:bg-primary-hover text-on-primary font-bold uppercase tracking-wider text-xs transition shadow-sm font-quicksand">
            Guardar y Actualizar Contraseña
        </button>
    </form>

    <div class="pt-4 border-t border-outline-variant/30 text-center text-xs font-label-md text-on-surface-variant">
        <a href="{{ route('login') }}" class="font-bold text-primary hover:underline">Cancelar y volver al login</a>
    </div>
</div>
@endsection

