@extends('layouts.guest')

@section('content')
<div class="bg-surface-container rounded-2xl p-6 sm:p-8 shadow-sm border border-outline-variant/30 space-y-6">
    <div class="text-center space-y-2">
        <h1 class="text-2xl font-bold font-quicksand text-on-surface">Iniciar Sesión</h1>
        <p class="text-sm text-on-surface-variant font-label-md">Acceso a clientes, personal de ventas y administración</p>
    </div>

    @if (session('status'))
        <div class="p-4 rounded-xl bg-primary/10 border border-primary/20 text-primary text-sm font-label-md">
            {{ session('status') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-900 text-sm font-label-md">
            {{ session('warning') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-error/10 border border-error/20 text-error text-sm font-label-md space-y-1">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div class="space-y-1">
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Correo Electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary text-sm font-quicksand"
                placeholder="usuario@archivum.com">
        </div>

        <div class="space-y-1">
            <div class="flex items-center justify-between">
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Contraseña</label>
                <a href="{{ route('password.request') }}" class="text-xs text-primary hover:underline font-label-md">¿Olvidaste tu contraseña?</a>
            </div>
            <input id="password" type="password" name="password" required
                class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary text-sm font-quicksand"
                placeholder="••••••••">
        </div>

        <div class="flex items-center">
            <label class="flex items-center gap-2 cursor-pointer text-xs font-label-md text-on-surface-variant">
                <input type="checkbox" name="remember" class="rounded border-outline-variant text-primary focus:ring-primary">
                <span>Recordar sesión</span>
            </label>
        </div>

        <button type="submit"
            class="w-full py-3 px-4 rounded-xl bg-primary hover:bg-primary-hover text-on-primary font-bold uppercase tracking-wider text-xs transition shadow-sm font-quicksand">
            Entrar a la Bóveda
        </button>
    </form>

    <div class="pt-4 border-t border-outline-variant/30 text-center text-xs font-label-md text-on-surface-variant">
        <span>¿No tienes una cuenta de cliente?</span>
        <a href="{{ route('register') }}" class="font-bold text-primary hover:underline ml-1">Crear cuenta gratis</a>
    </div>
</div>
@endsection

