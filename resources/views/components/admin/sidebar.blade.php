@props([
    'activeRoute' => 'dashboard'
])

@php
    /** @var \App\Models\Usuario|null $authUser */
    $authUser = auth()->user();
    $esAdmin = $authUser?->hasRole('administrador') ?? false;
    $esVentas = $authUser?->hasRole('ventas') ?? false;
    $esAlmacen = $authUser?->hasRole('almacen', 'encargado') ?? false;
    $esCompras = $authUser?->hasRole('compras') ?? false;

    $rutaInicioRol = match (true) {
        $esAdmin => route('admin.dashboard'),
        $esAlmacen => route('almacen.dashboard'),
        $esVentas => route('ventas.dashboard'),
        $esCompras => route('compras.ordenes'),
        default => route('public.home'),
    };

    $mensajesPendientesCount = \Illuminate\Support\Facades\Schema::hasTable('mensajes_contacto')
        ? \App\Models\MensajeContacto::where('estatus', 'pendiente')->count()
        : 0;
@endphp

<aside class="fixed left-0 top-0 h-full w-72 bg-surface-container-lowest dark:bg-[#140d20] z-50 flex flex-col justify-between border-r border-purple-200/70 dark:border-purple-500/30 shadow-xs">
    <div class="flex flex-col overflow-y-auto no-scrollbar">

        <!-- Cabecera del Sidebar con Logotipo de Archivo -->
        <a href="{{ $rutaInicioRol }}" class="h-16 px-space-lg flex items-center gap-space-sm border-b border-purple-100 dark:border-purple-500/30 hover:bg-surface-container-low dark:hover:bg-[#1b132a] transition shrink-0 group">
            <x-ui.archivum-logo size="md" />
            <div class="flex flex-col">
                <span class="font-brand-serif text-base text-primary dark:text-[#ccbeff] font-bold tracking-[0.2em] leading-none transition-colors group-hover:text-primary">ARCHIVUM</span>
                <span class="font-label-code text-[10px] text-secondary uppercase tracking-widest mt-1">Archival &amp; Curated</span>
            </div>
        </a>

        <!-- Navegación Administrativa Principal con Control de Acceso por Rol -->
        <div class="px-space-md py-space-sm">
            <span class="font-label-sm uppercase tracking-wider text-outline px-space-sm block mb-space-xs font-semibold">
                Gestión de Archivo &amp; Terminal
            </span>

            <nav class="flex flex-col gap-space-xs" aria-label="Menú administrativo por rol">

                {{-- 1. DASHBOARD FINANCIERO GENERAL (Exclusivo Administrador) O PANEL OPERATIVO DE SEDE (Empleados) --}}
                @if ($esAdmin)
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-space-sm px-space-md py-2.5 rounded-xl transition-all duration-200 active:scale-95 {{ $activeRoute === 'dashboard' || request()->routeIs('admin.dashboard') ? 'sidebar-link-glossy-active font-label-md shadow-xs' : 'sidebar-link-glossy-idle font-body-md text-sm' }}">
                        <span class="material-symbols-outlined text-[20px]">dashboard</span>
                        <span>Dashboard General</span>
                    </a>

                    {{-- 1.B REPORTES FINANCIEROS Y EXPORTACIÓN DE DATOS (CSV Y PDF): Exclusivo Administrador --}}
                    <a href="{{ route('admin.reportes') }}"
                       class="flex items-center gap-space-sm px-space-md py-2.5 rounded-xl transition-all duration-200 active:scale-95 {{ $activeRoute === 'reportes' || request()->routeIs('admin.reportes*') ? 'sidebar-link-glossy-active font-label-md shadow-xs' : 'sidebar-link-glossy-idle font-body-md text-sm' }}">
                        <span class="material-symbols-outlined text-[20px]">analytics</span>
                        <span>Reportes &amp; Exportación</span>
                    </a>
                @elseif ($esAlmacen || $esVentas)
                    <a href="{{ $rutaInicioRol }}"
                       class="flex items-center gap-space-sm px-space-md py-2.5 rounded-xl transition-all duration-200 active:scale-95 {{ request()->routeIs('almacen.dashboard', 'ventas.dashboard') ? 'sidebar-link-glossy-active font-label-md shadow-xs' : 'sidebar-link-glossy-idle font-body-md text-sm' }}">
                        <span class="material-symbols-outlined text-[20px]">storefront</span>
                        <span>Panel Operativo de Sede</span>
                    </a>
                @endif

                {{-- 2. INVENTARIO UNIFICADO: Personal Operativo y Administrador --}}
                @if ($esAdmin || $esAlmacen || $esCompras || $esVentas || $authUser?->esEmpleado())
                    <a href="{{ route('admin.productos.index') }}"
                       class="flex items-center gap-space-sm px-space-md py-2.5 rounded-xl transition-all duration-200 active:scale-95 {{ $activeRoute === 'productos' || request()->routeIs('admin.productos*') ? 'sidebar-link-glossy-active font-label-md shadow-xs' : 'sidebar-link-glossy-idle font-body-md text-sm' }}">
                        <span class="material-symbols-outlined text-[20px]">library_add</span>
                        <span>Altas &amp; Bajas de Productos</span>
                    </a>

                    <a href="{{ route('admin.inventario') }}"
                       class="flex items-center gap-space-sm px-space-md py-2.5 rounded-xl transition-all duration-200 active:scale-95 {{ $activeRoute === 'inventario' || request()->routeIs('admin.inventario', 'almacen.dashboard') ? 'sidebar-link-glossy-active font-label-md shadow-xs' : 'sidebar-link-glossy-idle font-body-md text-sm' }}">
                        <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                        <span>Inventario Unificado</span>
                    </a>
                @endif

                {{-- Submódulos de Almacén: Ajustes de Stock, Recepciones, Transferencias y Envíos --}}
                @if ($esAdmin || $esAlmacen)
                    <a href="{{ route('almacen.ajustes') }}"
                       class="flex items-center gap-space-sm px-space-md py-2 rounded-xl transition-all duration-200 active:scale-95 pl-8 {{ $activeRoute === 'almacen-ajustes' || request()->routeIs('almacen.ajustes*', 'almacen.recepciones*', 'admin.ajustes*', 'admin.recepciones*') ? 'sidebar-link-glossy-active font-label-md shadow-xs text-xs' : 'sidebar-link-glossy-idle font-body-md text-xs' }}">
                        <span class="material-symbols-outlined text-[18px]">tune</span>
                        <span>Ajustes &amp; Recepciones</span>
                    </a>

                    <a href="{{ route('almacen.transferencias') }}"
                       class="flex items-center gap-space-sm px-space-md py-2 rounded-xl transition-all duration-200 active:scale-95 pl-8 {{ $activeRoute === 'almacen-transferencias' || request()->routeIs('almacen.transferencias*', 'admin.transferencias*') ? 'sidebar-link-glossy-active font-label-md shadow-xs text-xs' : 'sidebar-link-glossy-idle font-body-md text-xs' }}">
                        <span class="material-symbols-outlined text-[18px]">swap_horiz</span>
                        <span>Transferencias de Stock</span>
                    </a>

                    <a href="{{ route('almacen.envios') }}"
                       class="flex items-center gap-space-sm px-space-md py-2 rounded-xl transition-all duration-200 active:scale-95 pl-8 {{ $activeRoute === 'almacen-envios' || request()->routeIs('almacen.envios*', 'admin.envios*') ? 'sidebar-link-glossy-active font-label-md shadow-xs text-xs' : 'sidebar-link-glossy-idle font-body-md text-xs' }}">
                        <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                        <span>Envíos &amp; Despacho</span>
                    </a>

                    @if (! $esAdmin && ! $esVentas)
                        <a href="{{ route('ventas.devoluciones') }}"
                           class="flex items-center gap-space-sm px-space-md py-2 rounded-xl transition-all duration-200 active:scale-95 pl-8 {{ $activeRoute === 'ventas-devoluciones' || request()->routeIs('ventas.devoluciones*', 'admin.devoluciones*') ? 'sidebar-link-glossy-active font-label-md shadow-xs text-xs' : 'sidebar-link-glossy-idle font-body-md text-xs' }}">
                            <span class="material-symbols-outlined text-[18px]">assignment_return</span>
                            <span>Devoluciones &amp; Garantías</span>
                        </a>
                    @endif
                @endif

                {{-- 3. ADMINISTRACIÓN DE VENTAS: Administrador y Ventas --}}
                @if ($esAdmin || $esVentas)
                    <a href="{{ route('admin.ventas') }}"
                       class="flex items-center gap-space-sm px-space-md py-2.5 rounded-xl transition-all duration-200 active:scale-95 {{ $activeRoute === 'ventas' && ! request()->routeIs('ventas.devoluciones*', 'admin.devoluciones*', 'ventas.pagos*', 'ventas.promociones*') ? 'sidebar-link-glossy-active font-label-md shadow-xs' : 'sidebar-link-glossy-idle font-body-md text-sm' }}">
                        <span class="material-symbols-outlined text-[20px]">point_of_sale</span>
                        <span>Administración de Ventas</span>
                    </a>

                    <a href="{{ route('admin.mensajes') }}"
                       class="flex items-center justify-between gap-space-sm px-space-md py-2.5 rounded-xl transition-all duration-200 active:scale-95 {{ $activeRoute === 'mensajes' || request()->routeIs('admin.mensajes*') ? 'sidebar-link-glossy-active font-label-md shadow-xs' : 'sidebar-link-glossy-idle font-body-md text-sm' }}">
                        <div class="flex items-center gap-space-sm">
                            <span class="material-symbols-outlined text-[20px]">mark_chat_unread</span>
                            <span>Mensajes &amp; Consultas</span>
                        </div>
                        @if ($mensajesPendientesCount > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold font-tabular-data {{ $activeRoute === 'mensajes' || request()->routeIs('admin.mensajes*') ? 'bg-white text-primary' : 'bg-primary-container text-white' }} shadow-2xs">
                                {{ $mensajesPendientesCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('ventas.pagos') }}"
                       class="flex items-center gap-space-sm px-space-md py-2 rounded-xl transition-all duration-200 active:scale-95 pl-8 {{ $activeRoute === 'ventas-pagos' || request()->routeIs('ventas.pagos*') ? 'sidebar-link-glossy-active font-label-md shadow-xs text-xs' : 'sidebar-link-glossy-idle font-body-md text-xs' }}">
                        <span class="material-symbols-outlined text-[18px]">payments</span>
                        <span>Validación de Pagos</span>
                    </a>

                    <a href="{{ route('ventas.devoluciones') }}"
                       class="flex items-center gap-space-sm px-space-md py-2 rounded-xl transition-all duration-200 active:scale-95 pl-8 {{ $activeRoute === 'ventas-devoluciones' || request()->routeIs('ventas.devoluciones*', 'admin.devoluciones*') ? 'sidebar-link-glossy-active font-label-md shadow-xs text-xs' : 'sidebar-link-glossy-idle font-body-md text-xs' }}">
                        <span class="material-symbols-outlined text-[18px]">assignment_return</span>
                        <span>Devoluciones &amp; Garantías</span>
                    </a>

                    <a href="{{ route('ventas.promociones') }}"
                       class="flex items-center gap-space-sm px-space-md py-2 rounded-xl transition-all duration-200 active:scale-95 pl-8 {{ $activeRoute === 'ventas-promociones' || request()->routeIs('ventas.promociones*') ? 'sidebar-link-glossy-active font-label-md shadow-xs text-xs' : 'sidebar-link-glossy-idle font-body-md text-xs' }}">
                        <span class="material-symbols-outlined text-[18px]">sell</span>
                        <span>Precios &amp; Promociones</span>
                    </a>
                @endif

                {{-- 4. COMPRAS Y PROVEEDORES: Administrador y Compras --}}
                @if ($esAdmin || $esCompras)
                    <a href="{{ route('compras.proveedores') }}"
                       class="flex items-center gap-space-sm px-space-md py-2.5 rounded-xl transition-all duration-200 active:scale-95 {{ $activeRoute === 'proveedores' || request()->routeIs('compras.proveedores*') ? 'sidebar-link-glossy-active font-label-md shadow-xs' : 'sidebar-link-glossy-idle font-body-md text-sm' }}">
                        <span class="material-symbols-outlined text-[20px]">handshake</span>
                        <span>Proveedores</span>
                    </a>

                    <a href="{{ route('compras.ordenes') }}"
                       class="flex items-center gap-space-sm px-space-md py-2.5 rounded-xl transition-all duration-200 active:scale-95 {{ $activeRoute === 'ordenes-compra' || request()->routeIs('compras.ordenes', 'compras.dashboard') ? 'sidebar-link-glossy-active font-label-md shadow-xs' : 'sidebar-link-glossy-idle font-body-md text-sm' }}">
                        <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                        <span>Órdenes de Compra</span>
                    </a>

                    <a href="{{ route('compras.productos') }}"
                       class="flex items-center gap-space-sm px-space-md py-2 rounded-xl transition-all duration-200 active:scale-95 pl-8 {{ $activeRoute === 'compras-productos' || request()->routeIs('compras.productos*') ? 'sidebar-link-glossy-active font-label-md shadow-xs text-xs' : 'sidebar-link-glossy-idle font-body-md text-xs' }}">
                        <span class="material-symbols-outlined text-[18px]">library_add</span>
                        <span>Catálogo &amp; Nuevos SKUs</span>
                    </a>
                @endif

                {{-- 5. OPCIONES EXCLUSIVAS DE ADMINISTRADOR --}}
                @if ($esAdmin)
                    <!-- Alta de Empleado -->
                    <a href="{{ route('admin.empleados.alta') }}"
                       class="flex items-center gap-space-sm px-space-md py-2.5 rounded-xl transition-all duration-200 active:scale-95 {{ $activeRoute === 'alta-empleado' ? 'sidebar-link-glossy-active font-label-md shadow-xs' : 'sidebar-link-glossy-idle font-body-md text-sm' }}">
                        <span class="material-symbols-outlined text-[20px]">person_add</span>
                        <span>Alta de Empleado</span>
                    </a>

                    <!-- Baja / Registro de Personal -->
                    <a href="{{ route('admin.empleados.baja') }}"
                       class="flex items-center gap-space-sm px-space-md py-2.5 rounded-xl transition-all duration-200 active:scale-95 {{ $activeRoute === 'baja-empleado' ? 'sidebar-link-glossy-active font-label-md shadow-xs' : 'sidebar-link-glossy-idle font-body-md text-sm' }}">
                        <span class="material-symbols-outlined text-[20px]">person_remove</span>
                        <span>Baja de Empleado</span>
                    </a>

                    <!-- Gestión de Sucursales -->
                    <a href="{{ route('admin.sucursales') }}"
                       class="flex items-center gap-space-sm px-space-md py-2.5 rounded-xl transition-all duration-200 active:scale-95 {{ $activeRoute === 'sucursales' ? 'sidebar-link-glossy-active font-label-md shadow-xs' : 'sidebar-link-glossy-idle font-body-md text-sm' }}">
                        <span class="material-symbols-outlined text-[20px]">store</span>
                        <span>Sucursales &amp; Sedes</span>
                    </a>

                    <!-- Bitácora del Sistema (Audit Log) -->
                    <a href="{{ route('admin.bitacora') }}"
                       class="flex items-center gap-space-sm px-space-md py-2.5 rounded-xl transition-all duration-200 active:scale-95 {{ $activeRoute === 'bitacora' ? 'sidebar-link-glossy-active font-label-md shadow-xs' : 'sidebar-link-glossy-idle font-body-md text-sm' }}">
                        <span class="material-symbols-outlined text-[20px]">history_edu</span>
                        <span>Bitácora del Sistema</span>
                    </a>

                    <!-- Roles y Permisos -->
                    <a href="{{ route('admin.roles') }}"
                       class="flex items-center gap-space-sm px-space-md py-2.5 rounded-xl transition-all duration-200 active:scale-95 {{ $activeRoute === 'roles' ? 'sidebar-link-glossy-active font-label-md shadow-xs' : 'sidebar-link-glossy-idle font-body-md text-sm' }}">
                        <span class="material-symbols-outlined text-[20px]">admin_panel_settings</span>
                        <span>Roles y Permisos</span>
                    </a>
                @endif

            </nav>
        </div>
    </div>

    <!-- Pie del Sidebar: Estado de Terminal Operativa v4.8.2-PROD (Enlazado y Funcional) -->
    <div class="p-space-md border-t border-purple-100 dark:border-purple-500/30 bg-surface-container-lowest dark:bg-[#140d20]">
        <a href="{{ route('admin.terminal') }}"
           class="sidebar-terminal-glossy p-space-sm rounded-xl flex items-center justify-between group"
           title="Inspeccionar estado de TERMINAL ARCHIVO v4.8.2-PROD">
            <div class="flex items-center gap-space-sm">
                <span class="material-symbols-outlined text-primary text-[20px] group-hover:scale-105 transition-transform">security</span>
                <div>
                    <div class="font-label-sm text-on-surface dark:text-stone-100 font-semibold group-hover:text-primary transition-colors">TERMINAL ARCHIVO</div>
                    <div class="font-label-code text-on-surface-variant dark:text-stone-400 text-[10px]">v4.8.2-PROD</div>
                </div>
            </div>
            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-emerald-100 dark:ring-emerald-950" title="Terminal en línea y sincronizada"></span>
        </a>
    </div>
</aside>
