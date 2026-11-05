@extends('layouts.guest')

@section('content')
<div class="bg-surface-container rounded-2xl p-6 sm:p-8 shadow-sm border border-outline-variant/30 space-y-6">
    <div class="text-center space-y-2">
        <h1 class="text-2xl font-bold font-quicksand text-on-surface">Recuperar Contraseña</h1>
        <p class="text-sm text-on-surface-variant font-label-md">Ingresa tu correo para recibir un enlace temporal de restablecimiento</p>
    </div>

    @if (session('status'))
        <div class="p-4 rounded-xl bg-primary/10 border border-primary/20 text-primary text-sm font-label-md">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-error/10 border border-error/20 text-error text-sm font-label-md space-y-1">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div class="space-y-1">
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Correo Electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary text-sm font-quicksand"
                placeholder="usuario@archivum.com">
        </div>

        <button type="submit"
            class="w-full py-3 px-4 rounded-xl bg-primary hover:bg-primary-hover text-on-primary font-bold uppercase tracking-wider text-xs transition shadow-sm font-quicksand">
            Enviar Enlace de Recuperación
        </button>
    </form>

    <div class="pt-4 border-t border-outline-variant/30 text-center text-xs font-label-md text-on-surface-variant">
        <a href="{{ route('login') }}" class="font-bold text-primary hover:underline">Volver al inicio de sesión</a>
    </div>
</div>
@endsection

