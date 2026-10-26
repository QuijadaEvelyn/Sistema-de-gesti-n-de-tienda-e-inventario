@props([
    'activeRoute' => 'dashboard'
])

<aside class="fixed left-0 top-0 h-full w-72 bg-surface-container-lowest z-50 flex flex-col justify-between border-r border-purple-200/70 shadow-xs">
    <div class="flex flex-col">
        
        <!-- Cabecera del Sidebar con Logotipo de Archivo -->
        <div class="h-16 px-space-lg flex items-center gap-space-sm border-b border-purple-100">
            <div class="w-9 h-9 rounded-xl bg-primary-container flex items-center justify-center text-white shrink-0 shadow-2xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                    <polyline points="2 17 12 22 22 17"></polyline>
                    <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
            </div>
            <div class="flex flex-col">
                <span class="font-headline-sm text-base text-primary font-bold tracking-wide leading-none">ARCHIVUM</span>
                <span class="font-label-code text-[10px] text-secondary uppercase tracking-widest mt-1">Archival &amp; Curated</span>
            </div>
        </div>

        <!-- Navegación Administrativa Principal -->
        <div class="px-space-md py-space-sm">
            <span class="font-label-sm uppercase tracking-wider text-outline px-space-sm block mb-space-xs font-semibold">
                Gestión de Archivo &amp; Terminal
            </span>
            
            <nav class="flex flex-col gap-space-xs">
                
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') ?? url('/admin') }}" 
                   class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg transition-colors {{ $activeRoute === 'dashboard' ? 'bg-primary-container text-white font-label-md shadow-xs' : 'font-body-md text-sm text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}">
                    <span class="material-symbols-outlined text-[20px]">dashboard</span>
                    <span>Dashboard General</span>
                </a>

                <!-- Inventario Unificado -->
                <a href="{{ route('admin.inventario') ?? url('/admin/inventario') }}" 
                   class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg transition-colors {{ $activeRoute === 'inventario' ? 'bg-primary-container text-white font-label-md shadow-xs' : 'font-body-md text-sm text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}">
                    <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                    <span>Inventario Unificado</span>
                </a>

                <!-- Administración de Ventas & Pedidos -->
                <a href="{{ url('/admin/ventas') }}" 
                   class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg transition-colors {{ $activeRoute === 'ventas' ? 'bg-primary-container text-white font-label-md shadow-xs' : 'font-body-md text-sm text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}">
                    <span class="material-symbols-outlined text-[20px]">point_of_sale</span>
                    <span>Administración de Ventas</span>
                </a>

                <!-- Alta de Empleado -->
                <a href="{{ url('/admin/empleados/alta') }}" 
                   class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg transition-colors {{ $activeRoute === 'alta-empleado' ? 'bg-primary-container text-white font-label-md shadow-xs' : 'font-body-md text-sm text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}">
                    <span class="material-symbols-outlined text-[20px]">person_add</span>
                    <span>Alta de Empleado</span>
                </a>

                <!-- Baja / Registro de Personal -->
                <a href="{{ url('/admin/empleados/baja') }}" 
                   class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg transition-colors {{ $activeRoute === 'baja-empleado' ? 'bg-primary-container text-white font-label-md shadow-xs' : 'font-body-md text-sm text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}">
                    <span class="material-symbols-outlined text-[20px]">person_remove</span>
                    <span>Baja de Empleado</span>
                </a>

                <!-- Gestión de Sucursales -->
                <a href="{{ url('/admin/sucursales') }}" 
                   class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg transition-colors {{ $activeRoute === 'sucursales' ? 'bg-primary-container text-white font-label-md shadow-xs' : 'font-body-md text-sm text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}">
                    <span class="material-symbols-outlined text-[20px]">store</span>
                    <span>Sucursales &amp; Sedes</span>
                </a>

                <!-- Bitácora del Sistema (Audit Log) -->
                <a href="{{ url('/admin/bitacora') }}" 
                   class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg transition-colors {{ $activeRoute === 'bitacora' ? 'bg-primary-container text-white font-label-md shadow-xs' : 'font-body-md text-sm text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}">
                    <span class="material-symbols-outlined text-[20px]">history_edu</span>
                    <span>Bitácora del Sistema</span>
                </a>

                <!-- Roles y Permisos -->
                <a href="{{ url('/admin/roles') }}" 
                   class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg transition-colors {{ $activeRoute === 'roles' ? 'bg-primary-container text-white font-label-md shadow-xs' : 'font-body-md text-sm text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}">
                    <span class="material-symbols-outlined text-[20px]">admin_panel_settings</span>
                    <span>Roles y Permisos</span>
                </a>

            </nav>
        </div>
    </div>

    <!-- Pie del Sidebar: Estado de Terminal Operativa v4.8.2-PROD -->
    <div class="p-space-md border-t border-purple-100 bg-surface-container-lowest">
        <div class="bg-surface-container-low border border-purple-200/70 p-space-sm rounded-lg flex items-center justify-between">
            <div class="flex items-center gap-space-sm">
                <span class="material-symbols-outlined text-primary text-[20px]">security</span>
                <div>
                    <div class="font-label-sm text-on-surface font-semibold">Terminal Archivo</div>
                    <div class="font-label-code text-on-surface-variant text-[10px]">v4.8.2-PROD</div>
                </div>
            </div>
            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-emerald-100" title="Terminal en línea y sincronizada"></span>
        </div>
    </div>
</aside>
