<x-layouts.admin title="ARCHIVUM · Envíos y Despacho de Almacén" active-route="almacen-envios">
    <div class="space-y-6">
        <!-- Encabezado Operativo -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-primary text-[11px] font-label-code font-bold uppercase tracking-wider">
                        {{ $sucursalActiva ? 'Bóveda de Despacho: '.$sucursalActiva->nombre : 'Todas las Sedes (Consolidado)' }}
                    </span>
                    <span class="text-xs text-outline font-label-code">· Logística Blindada</span>
                </div>
                <h1 class="text-2xl font-bold text-on-surface font-quicksand">Envíos y Despacho de Pedidos</h1>
                <p class="text-xs text-on-surface-variant font-label-code uppercase tracking-wider mt-1">
                    Control de embalaje curatorial, guías de transporte especializado y confirmación de entrega
                </p>
            </div>
            <a href="{{ route('admin.inventario') }}"
               class="px-4 py-2 rounded-xl border border-purple-200 bg-white hover:bg-purple-50 text-xs font-label-md text-primary transition">
                &larr; Inventario Unificado
            </a>
        </div>

        @if (session('status'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-emerald-600">local_shipping</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- Tarjetas de Resumen Logístico -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-surface-container-lowest p-4 rounded-2xl border border-purple-200/70 flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-label-code uppercase text-secondary tracking-wider">En Preparación / Bóveda</span>
                    <div class="text-2xl font-bold text-amber-700 font-tabular-data mt-1">{{ $resumenEnvios['preparacion'] ?? 0 }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">inventory</span>
                </div>
            </div>
            <div class="bg-surface-container-lowest p-4 rounded-2xl border border-purple-200/70 flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-label-code uppercase text-secondary tracking-wider">En Tránsito / Custodia</span>
                    <div class="text-2xl font-bold text-primary font-tabular-data mt-1">{{ $resumenEnvios['en_transito'] ?? 0 }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined">local_shipping</span>
                </div>
            </div>
            <div class="bg-surface-container-lowest p-4 rounded-2xl border border-purple-200/70 flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-label-code uppercase text-secondary tracking-wider">Entregados / Certificados</span>
                    <div class="text-2xl font-bold text-emerald-700 font-tabular-data mt-1">{{ $resumenEnvios['entregados'] ?? 0 }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">verified</span>
                </div>
            </div>
        </div>

        <!-- Tabla de Guías y Despachos -->
        <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 overflow-hidden shadow-2xs">
            <div class="px-4 py-3 bg-surface-container-low border-b border-purple-200/60 flex flex-wrap items-center justify-between gap-2">
                <span class="text-xs font-label-code uppercase text-secondary font-bold">Bitácora de Despachos Activos</span>
                <div class="flex items-center gap-2 text-xs">
                    <a href="{{ route('almacen.envios') }}" class="px-2.5 py-1 rounded-lg {{ !($estadoFiltro ?? null) ? 'bg-purple-100 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container' }}">Todos</a>
                    <a href="{{ route('almacen.envios', ['estado' => 'preparacion']) }}" class="px-2.5 py-1 rounded-lg {{ ($estadoFiltro ?? null) === 'preparacion' ? 'bg-amber-100 text-amber-800 font-bold' : 'text-on-surface-variant hover:bg-surface-container' }}">Preparación</a>
                    <a href="{{ route('almacen.envios', ['estado' => 'en_transito']) }}" class="px-2.5 py-1 rounded-lg {{ ($estadoFiltro ?? null) === 'en_transito' ? 'bg-purple-100 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container' }}">En Tránsito</a>
                    <a href="{{ route('almacen.envios', ['estado' => 'entregado']) }}" class="px-2.5 py-1 rounded-lg {{ ($estadoFiltro ?? null) === 'entregado' ? 'bg-emerald-100 text-emerald-800 font-bold' : 'text-on-surface-variant hover:bg-surface-container' }}">Entregados</a>
                </div>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-container-low/60 border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                    <tr>
                        <th class="py-3 px-4">Guía</th>
                        <th class="py-3 px-4">Pedido / Cliente</th>
                        <th class="py-3 px-4">Paquetería</th>
                        <th class="py-3 px-4">Estado Actual</th>
                        <th class="py-3 px-4">Entrega Estimada</th>
                        <th class="py-3 px-4 text-right">Despacho</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-100">
                    @forelse ($envios as $envio)
                        @php
                            $estadoActual = (string) ($envio->estado_actual ?? $envio->estatus ?? 'preparacion');
                            $badgeClass = match ($estadoActual) {
                                'entregado' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'en_transito', 'recolectado', 'en_ruta_entrega' => 'bg-purple-50 text-primary border-purple-200',
                                default => 'bg-amber-50 text-amber-700 border-amber-200',
                            };
                        @endphp
                        <tr class="hover:bg-surface-container-low/50 transition">
                            <td class="py-3 px-4 font-label-code font-bold text-primary">{{ $envio->numero_guia ?? 'SIN-GUIA' }}</td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-on-surface">{{ $envio->pedido?->folio ?? '#'.$envio->pedido_id }}</div>
                                <div class="text-xs text-outline">{{ $envio->pedido?->usuario?->nombre ?? 'Cliente Registrado' }}</div>
                            </td>
                            <td class="py-3 px-4 text-xs font-medium">{{ $envio->paqueteria }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded-full border text-[11px] uppercase font-label-code font-bold {{ $badgeClass }}">
                                    {{ str_replace('_', ' ', $estadoActual) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-xs font-label-code text-on-surface-variant">
                                {{ $envio->fecha_estimada_entrega?->format('Y-m-d') ?? 'Pendiente' }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                @if ($estadoActual !== 'entregado')
                                    <form method="POST" action="{{ route('almacen.envios.avanzar', $envio) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-primary-container hover:bg-primary text-white text-xs font-semibold transition cursor-pointer">
                                            Avanzar Etapa &rarr;
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs font-label-code text-emerald-700 font-semibold">Custodia Concluida</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-outline">No hay envíos registrados para los filtros seleccionados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
