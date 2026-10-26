@props([
    'activeBranch' => 'central',
    'userName' => 'Mateo Vargas',
    'userRole' => 'Curador en Jefe',
    'userEmail' => 'admin@archivum.mx'
])

<header class="fixed top-0 left-72 right-0 h-16 bg-surface-container-lowest/95 backdrop-blur-md z-40 border-b border-purple-200/70 px-space-lg flex items-center justify-between gap-space-md shadow-xs">
    
    <!-- Buscador Técnico Rápido (SKU, ISBN, Matriz, Título, Autor) -->
    <div class="flex items-center gap-space-md flex-1 max-w-xl">
        <div class="relative w-full">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
            <input 
                type="text" 
                placeholder="Buscar por SKU, ISBN, catálogo vinilo, título o autor..." 
                class="w-full bg-surface-container-low pl-10 pr-space-md py-1.5 rounded-lg border border-purple-200/80 font-body-sm text-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary focus:bg-white transition-colors" 
            />
        </div>
    </div>

    <!-- Controles de Cabecera: Selector de Sucursal, Alertas, Tema, Perfil -->
    <div class="flex items-center gap-space-md">
        
        <!-- Enlace Directo a Portal Público / Tienda -->
        <a href="{{ url('/') }}" target="_blank" class="hidden md:flex items-center gap-1.5 text-xs font-label-md text-primary hover:text-primary-hover bg-purple-50 px-3 py-1.5 rounded-lg border border-purple-200 transition">
            <span class="material-symbols-outlined text-[16px]">visibility</span>
            <span>Ver Tienda Pública</span>
        </a>

        <!-- Selector de Sucursal Operativa -->
        <div class="flex items-center gap-space-xs bg-surface-container-low px-space-sm py-1 rounded-lg border border-purple-200/80">
            <span class="material-symbols-outlined text-[18px] text-primary">location_on</span>
            <label class="sr-only" for="branch-select">Sucursal activa</label>
            <select id="branch-select" class="bg-transparent font-label-sm text-on-surface font-medium focus:outline-none cursor-pointer pr-space-xs text-xs">
                <option value="central" {{ $activeBranch === 'central' ? 'selected' : '' }}>Sucursal Central Bellas Artes</option>
                <option value="coyoacan" {{ $activeBranch === 'coyoacan' ? 'selected' : '' }}>Sucursal Coyoacán</option>
                <option value="almacen" {{ $activeBranch === 'almacen' ? 'selected' : '' }}>Almacén General</option>
            </select>
        </div>

        <!-- Alertas de Existencias Críticas -->
        <button type="button" class="relative p-2 rounded-lg bg-surface-container-low hover:bg-surface-container border border-purple-200/60 text-on-surface-variant hover:text-on-surface transition-colors" title="Existencias Críticas en Bóveda">
            <span class="material-symbols-outlined text-[20px]">notifications</span>
            <span class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-error ring-2 ring-white animate-ping"></span>
            <span class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-error ring-2 ring-white"></span>
        </button>

        <!-- Conmutador de Tema Claro / Oscuro -->
        <button type="button" id="admin-theme-toggle" class="p-2 rounded-lg bg-surface-container-low hover:bg-surface-container border border-purple-200/60 text-on-surface-variant hover:text-on-surface transition-colors" title="Alternar Tema Claro/Oscuro">
            <span class="material-symbols-outlined text-[20px] text-primary">light_mode</span>
        </button>

        <div class="h-6 w-px bg-purple-200"></div>

        <!-- Cápsula de Usuario Administrador / Curador en Jefe -->
        <div class="flex items-center gap-space-sm pl-space-xs">
            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs ring-2 ring-primary/30 shadow-2xs">
                MV
            </div>
            <div class="hidden xl:flex flex-col text-left">
                <span class="font-label-md text-on-surface leading-tight font-semibold text-xs">{{ $userName }}</span>
                <span class="font-label-code text-secondary leading-tight text-[10px]">{{ $userEmail }}</span>
            </div>
        </div>

    </div>
</header>
