@extends('layouts.guest')

@section('content')
<div class="space-y-6">
    <div class="bg-surface-container rounded-2xl p-6 sm:p-8 shadow-sm border border-outline-variant/30 space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-outline-variant/30">
            <div>
                <h1 class="text-2xl font-bold font-quicksand text-on-surface">Mi Cuenta y Perfil</h1>
                <p class="text-sm text-on-surface-variant font-label-md">Actualiza tus datos personales y credenciales de acceso</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('cliente.mis-pedidos') }}" class="px-3.5 py-1.5 rounded-xl bg-primary-container text-white text-xs font-bold uppercase tracking-wider hover:bg-primary transition">
                    Mis Pedidos &amp; Devoluciones
                </a>
                @foreach ($usuario->roles as $rol)
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-primary/10 text-primary font-label-code">
                        {{ $rol->nombre }}
                    </span>
                @endforeach
            </div>
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

        <!-- Formulario 1: Datos Personales -->
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <h2 class="text-sm font-bold uppercase tracking-wider text-on-surface font-label-code border-b border-outline-variant/20 pb-2">Información Personal</h2>

            <div class="space-y-1">
                <label for="nombre" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Nombre(s)</label>
                <input id="nombre" type="text" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary text-sm font-quicksand">
            </div>

            @if ($cliente)
                <div class="space-y-1">
                    <label for="apellidos" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Apellidos</label>
                    <input id="apellidos" type="text" name="apellidos" value="{{ old('apellidos', $cliente->apellidos) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary text-sm font-quicksand">
                </div>

                <div class="space-y-1">
                    <label for="telefono" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Teléfono</label>
                    <input id="telefono" type="tel" name="telefono" value="{{ old('telefono', $cliente->telefono) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary text-sm font-quicksand">
                </div>
            @endif

            <div class="space-y-1">
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Correo Electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email', $usuario->email) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary text-sm font-quicksand">
            </div>

            <button type="submit"
                class="w-full py-3 px-4 rounded-xl bg-primary hover:bg-primary-hover text-on-primary font-bold uppercase tracking-wider text-xs transition shadow-sm font-quicksand">
                Actualizar Perfil
            </button>
        </form>

        <!-- Formulario 2: Seguridad y Cambio de Contraseña -->
        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4 pt-6 border-t border-outline-variant/30">
            @csrf
            @method('PUT')

            <h2 class="text-sm font-bold uppercase tracking-wider text-on-surface font-label-code border-b border-outline-variant/20 pb-2">Seguridad y Cambio de Contraseña</h2>

            <div class="space-y-1">
                <label for="current_password" class="block text-xs font-semibold uppercase tracking-wider text-on-surface font-label-code">Contraseña Actual</label>
                <input id="current_password" type="password" name="current_password" required
                    class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface text-on-surface focus:outline-none focus:ring-2 focus:ring-primary text-sm font-quicksand"
                    placeholder="••••••••">
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
                class="w-full py-3 px-4 rounded-xl bg-outline hover:bg-outline-variant text-white font-bold uppercase tracking-wider text-xs transition shadow-sm font-quicksand">
                Cambiar Contraseña
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="pt-4 border-t border-outline-variant/30 text-center">
            @csrf
            <button type="submit" class="text-xs font-bold text-error hover:underline uppercase tracking-wider font-label-code">
                Cerrar Sesión Activa
            </button>
        </form>
    </div>
</div>
@endsection

