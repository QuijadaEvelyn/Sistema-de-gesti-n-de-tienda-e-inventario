@props([
    'activeBranch' => 'central',
    'userName' => null,
    'userRole' => null,
    'userEmail' => null
])

@php
    /** @var \App\Models\Usuario|null $authUser */
    $authUser = auth()->user();
    $displayName = $userName ?? $authUser?->nombre ?? 'Operador ARCHIVUM';
    $displayEmail = $userEmail ?? $authUser?->email ?? 'operador@archivum.mx';
    $displayRole = $userRole ?? strtoupper($authUser?->roles?->pluck('nombre')->implode(' · ') ?: 'Personal');
    $initials = collect(explode(' ', $displayName))
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->take(2)
        ->implode('') ?: 'AR';
@endphp

<header class="fixed top-0 left-72 right-0 h-16 bg-surface-container-lowest/95 backdrop-blur-md z-40 border-b border-purple-200/70 px-space-lg flex items-center justify-between gap-space-md shadow-xs">

    <!-- Buscador Técnico Rápido (SKU, ISBN, Matriz, Título, Autor) -->
    <div class="flex items-center gap-space-md flex-1 max-w-xl">
        <form action="{{ route('admin.inventario') }}" method="GET" class="relative w-full">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
            <input
                type="text"
                name="buscar"
                value="{{ request('buscar') }}"
                placeholder="Buscar por SKU, ISBN, catálogo vinilo, título o autor..."
                class="w-full bg-surface-container-low pl-10 pr-space-md py-1.5 rounded-lg border border-purple-200/80 font-body-sm text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary focus:bg-white transition-colors"
            />
        </form>
    </div>

    <!-- Controles de Cabecera: Retorno a Tienda, Selector de Sucursal, Tema, Perfil y Logout -->
    <div class="flex items-center gap-space-md">

        <!-- Botón de Retorno a Portal Público / Tienda -->
        <a href="{{ route('public.home') }}" class="flex items-center gap-1.5 text-xs font-label-md text-primary hover:text-primary-hover bg-purple-50 hover:bg-purple-100/80 px-3 py-1.5 rounded-lg border border-purple-200 transition">
            <span class="material-symbols-outlined text-[16px]">storefront</span>
            <span>Ver Tienda Pública</span>
        </a>

        <!-- Selector de Sucursal Operativa -->
        <div class="hidden lg:flex items-center gap-space-xs bg-surface-container-low px-space-sm py-1 rounded-lg border border-purple-200/80">
            <span class="material-symbols-outlined text-[18px] text-primary">location_on</span>
            <label class="sr-only" for="branch-select">Sucursal activa</label>
            <select id="branch-select" class="bg-transparent font-label-sm text-on-surface font-medium focus:outline-none cursor-pointer pr-space-xs text-xs">
                <option value="central" {{ $activeBranch === 'central' ? 'selected' : '' }}>Sucursal Central Bellas Artes</option>
                <option value="coyoacan" {{ $activeBranch === 'coyoacan' ? 'selected' : '' }}>Sucursal Coyoacán</option>
                <option value="almacen" {{ $activeBranch === 'almacen' ? 'selected' : '' }}>Almacén General</option>
            </select>
        </div>

        <!-- Conmutador de Tema Claro / Oscuro -->
        <button type="button" id="admin-theme-toggle" class="p-2 rounded-lg bg-surface-container-low hover:bg-surface-container border border-purple-200/60 text-on-surface-variant hover:text-on-surface transition-colors" title="Alternar Tema Claro/Oscuro">
            <span class="material-symbols-outlined text-[20px] text-primary">light_mode</span>
        </button>

        <div class="h-6 w-px bg-purple-200"></div>

        <!-- Cápsula de Usuario Operador y Cierre de Sesión -->
        <div class="flex items-center gap-space-sm pl-space-xs">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-space-sm hover:opacity-90 transition" title="Editar perfil ({{ $displayRole }})">
                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs ring-2 ring-primary/30 shadow-2xs">
                    {{ $initials }}
                </div>
                <div class="hidden xl:flex flex-col text-left">
                    <span class="font-label-md text-on-surface leading-tight font-semibold text-xs">{{ $displayName }}</span>
                    <span class="font-label-code text-secondary leading-tight text-[10px]">{{ $displayEmail }}</span>
                </div>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="p-2 rounded-lg text-outline hover:text-error hover:bg-rose-50 transition" title="Cerrar Sesión">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                </button>
            </form>
        </div>

    </div>
</header>
