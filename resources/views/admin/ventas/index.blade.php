<x-layouts.admin
    :title="$seccion === 'devoluciones' ? 'ARCHIVUM · Gestión de Devoluciones y Garantías' : 'ARCHIVUM · Administración de Ventas'"
    :active-route="$seccion === 'devoluciones' ? 'ventas-devoluciones' : ($seccion === 'pagos' ? 'ventas-pagos' : ($seccion === 'promociones' ? 'ventas-promociones' : 'ventas'))">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-on-surface font-quicksand">
                    @if ($seccion === 'devoluciones')
                        Gestión de Devoluciones &amp; Garantías por Defectos
                    @else
                        Administración de Ventas · {{ ucfirst($seccion) }}
                    @endif
                </h1>
                <p class="text-xs text-on-surface-variant font-label-code uppercase tracking-wider mt-1">
                    @if ($seccion === 'devoluciones')
                        Revisión de evidencia fotográfica, emisión de guías de retorno y reintegración a stock o baja por merma
                    @else
                        Control de pedidos, pagos, devoluciones y promociones
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2 text-xs font-label-md">
                <a href="{{ route('ventas.pedidos') }}" class="px-3.5 py-2 rounded-xl border {{ $seccion === 'pedidos' ? 'bg-primary-container text-white border-primary' : 'bg-white border-purple-200 text-on-surface hover:bg-purple-50' }} transition">Pedidos</a>
                <a href="{{ route('ventas.pagos') }}" class="px-3.5 py-2 rounded-xl border {{ $seccion === 'pagos' ? 'bg-primary-container text-white border-primary' : 'bg-white border-purple-200 text-on-surface hover:bg-purple-50' }} transition">Pagos</a>
                <a href="{{ route('ventas.devoluciones') }}" class="px-3.5 py-2 rounded-xl border {{ $seccion === 'devoluciones' ? 'bg-primary-container text-white border-primary' : 'bg-white border-purple-200 text-on-surface hover:bg-purple-50' }} transition">Devoluciones &amp; Garantías</a>
                <a href="{{ route('ventas.promociones') }}" class="px-3.5 py-2 rounded-xl border {{ $seccion === 'promociones' ? 'bg-primary-container text-white border-primary' : 'bg-white border-purple-200 text-on-surface hover:bg-purple-50' }} transition">Promociones</a>
            </div>
        </div>

        @if (session('status'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-rose-600">error</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($seccion === 'devoluciones')
            <!-- Resumen de Estados de Devolución -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                <a href="{{ route('ventas.devoluciones', ['etapa' => 'solicitada']) }}"
                   class="p-4 rounded-2xl border transition {{ ($filtroEstado ?? null) === 'solicitada' ? 'bg-amber-50 border-amber-400 shadow-xs' : 'bg-surface-container-lowest border-purple-200/70 hover:border-amber-300' }}">
                    <span class="text-[10px] font-label-code uppercase text-amber-800 font-bold tracking-wider">Pendiente de revisión</span>
                    <div class="text-2xl font-bold text-amber-700 font-tabular-data mt-1">{{ $resumenDevoluciones['solicitada'] ?? 0 }}</div>
                </a>
                <a href="{{ route('ventas.devoluciones', ['etapa' => 'aprobada']) }}"
                   class="p-4 rounded-2xl border transition {{ ($filtroEstado ?? null) === 'aprobada' ? 'bg-blue-50 border-blue-400 shadow-xs' : 'bg-surface-container-lowest border-purple-200/70 hover:border-blue-300' }}">
                    <span class="text-[10px] font-label-code uppercase text-blue-800 font-bold tracking-wider">Aprobada · Guía emitida</span>
                    <div class="text-2xl font-bold text-blue-700 font-tabular-data mt-1">{{ $resumenDevoluciones['aprobada'] ?? 0 }}</div>
                </a>
                <a href="{{ route('ventas.devoluciones', ['etapa' => 'producto_retornado']) }}"
                   class="p-4 rounded-2xl border transition {{ ($filtroEstado ?? null) === 'producto_retornado' ? 'bg-indigo-50 border-indigo-400 shadow-xs' : 'bg-surface-container-lowest border-purple-200/70 hover:border-indigo-300' }}">
                    <span class="text-[10px] font-label-code uppercase text-indigo-800 font-bold tracking-wider">Producto retornado</span>
                    <div class="text-2xl font-bold text-indigo-700 font-tabular-data mt-1">{{ $resumenDevoluciones['producto_retornado'] ?? 0 }}</div>
                </a>
                <a href="{{ route('ventas.devoluciones', ['etapa' => 'reembolso_procesado']) }}"
                   class="p-4 rounded-2xl border transition {{ ($filtroEstado ?? null) === 'reembolso_procesado' ? 'bg-emerald-50 border-emerald-400 shadow-xs' : 'bg-surface-container-lowest border-purple-200/70 hover:border-emerald-300' }}">
                    <span class="text-[10px] font-label-code uppercase text-emerald-800 font-bold tracking-wider">Reembolso procesado</span>
                    <div class="text-2xl font-bold text-emerald-700 font-tabular-data mt-1">{{ $resumenDevoluciones['reembolso_procesado'] ?? 0 }}</div>
                </a>
                <a href="{{ route('ventas.devoluciones', ['etapa' => 'rechazada']) }}"
                   class="p-4 rounded-2xl border transition {{ ($filtroEstado ?? null) === 'rechazada' ? 'bg-rose-50 border-rose-400 shadow-xs' : 'bg-surface-container-lowest border-purple-200/70 hover:border-rose-300' }}">
                    <span class="text-[10px] font-label-code uppercase text-rose-800 font-bold tracking-wider">Rechazada</span>
                    <div class="text-2xl font-bold text-rose-700 font-tabular-data mt-1">{{ $resumenDevoluciones['rechazada'] ?? 0 }}</div>
                </a>
            </div>

            <!-- Barra de Filtros y Búsqueda -->
            <div class="bg-surface-container-lowest p-4 rounded-2xl border border-purple-200/70 flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="text-outline font-label-code uppercase">Estado:</span>
                    <a href="{{ route('ventas.devoluciones') }}" class="px-3 py-1.5 rounded-lg {{ empty($filtroEstado) ? 'bg-primary text-white font-bold' : 'text-on-surface-variant hover:bg-surface-container' }}">Todas</a>
                    <a href="{{ route('ventas.devoluciones', ['etapa' => 'solicitada']) }}" class="px-3 py-1.5 rounded-lg {{ ($filtroEstado ?? null) === 'solicitada' ? 'bg-amber-600 text-white font-bold' : 'text-on-surface-variant hover:bg-surface-container' }}">Pendiente de revisión</a>
                    <a href="{{ route('ventas.devoluciones', ['etapa' => 'aprobada']) }}" class="px-3 py-1.5 rounded-lg {{ ($filtroEstado ?? null) === 'aprobada' ? 'bg-blue-600 text-white font-bold' : 'text-on-surface-variant hover:bg-surface-container' }}">Aprobada</a>
                    <a href="{{ route('ventas.devoluciones', ['etapa' => 'producto_retornado']) }}" class="px-3 py-1.5 rounded-lg {{ ($filtroEstado ?? null) === 'producto_retornado' ? 'bg-indigo-600 text-white font-bold' : 'text-on-surface-variant hover:bg-surface-container' }}">Producto retornado</a>
                    <a href="{{ route('ventas.devoluciones', ['etapa' => 'reembolso_procesado']) }}" class="px-3 py-1.5 rounded-lg {{ ($filtroEstado ?? null) === 'reembolso_procesado' ? 'bg-emerald-600 text-white font-bold' : 'text-on-surface-variant hover:bg-surface-container' }}">Reembolso procesado</a>
                    <a href="{{ route('ventas.devoluciones', ['etapa' => 'rechazada']) }}" class="px-3 py-1.5 rounded-lg {{ ($filtroEstado ?? null) === 'rechazada' ? 'bg-rose-600 text-white font-bold' : 'text-on-surface-variant hover:bg-surface-container' }}">Rechazada</a>
                </div>
                <form method="GET" action="{{ route('ventas.devoluciones') }}" class="flex items-center gap-2">
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Folio devolución, pedido o guía..."
                           class="px-3 py-1.5 rounded-xl border border-purple-200 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-primary/30" />
                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-primary-container text-white text-xs font-bold uppercase tracking-wider cursor-pointer">
                        Buscar
                    </button>
                </form>
            </div>

            <!-- Listado de Solicitudes de Devolución y Garantía -->
            <div class="space-y-4">
                @forelse ($registros as $dev)
                    @php
                        $etapaActual = $dev->estadoOperativo();
                        $badgeColor = match ($etapaActual) {
                            'aprobada' => 'bg-blue-100 text-blue-800 border-blue-300',
                            'producto_retornado' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                            'reembolso_procesado' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                            'rechazada' => 'bg-rose-100 text-rose-800 border-rose-300',
                            default => 'bg-amber-100 text-amber-800 border-amber-300',
                        };
                        $variante = $dev->detallePedido?->varianteSku;
                        $producto = $variante?->producto;
                        $evidencias = $dev->obtenerEvidenciasNormalizadas();
                    @endphp

                    <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/80 p-5 shadow-2xs space-y-4">
                        <!-- Cabecera de la Solicitud -->
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-purple-100 pb-3">
                            <div class="space-y-0.5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-label-code text-sm font-bold text-primary">
                                        {{ $dev->folio_devolucion ?? ('#DEV-'.str_pad((string) $dev->id, 4, '0', STR_PAD_LEFT)) }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full border text-[11px] font-label-code font-bold uppercase {{ $badgeColor }}">
                                        {{ $dev->etiquetaEstado() }}
                                    </span>
                                    @if ($dev->destino_inventario)
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-label-code font-bold uppercase {{ $dev->destino_inventario === 'reintegrar_stock' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                            {{ $dev->destino_inventario === 'reintegrar_stock' ? 'Reintegrado a Stock' : 'Enviado a Mermas / Baja' }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-on-surface-variant">
                                    Pedido: <strong class="font-label-code text-on-surface">{{ $dev->pedido?->folio ?? '#'.$dev->pedido_id }}</strong>
                                    &middot; Cliente: <strong>{{ $dev->cliente?->nombre }} {{ $dev->cliente?->apellidos }}</strong> ({{ $dev->cliente?->email }})
                                    &middot; Solicitada: {{ $dev->created_at?->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <div class="text-left md:text-right">
                                <span class="text-[10px] font-label-code uppercase text-outline block">Monto Estimado de Reembolso</span>
                                <span class="text-lg font-bold font-tabular-data text-primary">${{ number_format((float) $dev->monto_reembolso, 2) }} MXN</span>
                            </div>
                        </div>

                        <!-- Información de la Pieza, Motivo y Evidencia Fotográfica -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 text-xs">
                            <div class="p-3.5 rounded-xl bg-surface-container-low border border-purple-100 space-y-1">
                                <span class="text-[10px] font-label-code uppercase text-secondary font-bold">Pieza Objeto de Garantía</span>
                                <div class="font-bold text-on-surface text-sm">{{ $producto?->nombre ?? 'Pieza Catalogada' }}</div>
                                <div class="font-label-code text-primary">SKU: {{ $variante?->sku ?? 'N/D' }} &middot; Cantidad: {{ $dev->cantidad }} u.</div>
                                <div class="text-outline">Graduación actual: {{ $dev->nueva_graduacion ?? $variante?->descripcion ?? 'Estándar' }}</div>
                            </div>

                            <div class="p-3.5 rounded-xl bg-surface-container-low border border-purple-100 space-y-1">
                                <span class="text-[10px] font-label-code uppercase text-secondary font-bold">Motivo &amp; Comentarios del Cliente</span>
                                <div class="font-bold text-on-surface">{{ $dev->etiquetaMotivo() }}</div>
                                @if ($dev->comentarios_cliente)
                                    <p class="text-on-surface-variant italic">"{{ $dev->comentarios_cliente }}"</p>
                                @else
                                    <p class="text-outline">Sin comentarios adicionales.</p>
                                @endif
                                @if ($dev->guia_retorno)
                                    <div class="pt-1.5 mt-1.5 border-t border-purple-200/60 text-emerald-800">
                                        <span class="font-label-code font-bold">Guía Retorno: {{ $dev->guia_retorno }}</span>
                                        @if ($dev->instrucciones_retorno)
                                            <p class="text-[11px] text-on-surface-variant">{{ $dev->instrucciones_retorno }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="p-3.5 rounded-xl bg-surface-container-low border border-purple-100 space-y-2">
                                <span class="text-[10px] font-label-code uppercase text-secondary font-bold">
                                    Evidencia Fotográfica Adjunta ({{ count($evidencias) }})
                                </span>
                                @if (! empty($evidencias))
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 pt-1">
                                        @foreach ($evidencias as $idx => $ev)
                                            @php
                                                $rutaEv = $ev['ruta'] ?? '';
                                                $urlEv = $rutaEv !== '' ? asset('storage/' . $rutaEv) : ($ev['url'] ?? '');
                                                $nombreEv = $ev['nombre'] ?? ('Evidencia #'.($idx + 1));
                                            @endphp
                                            <div class="group relative rounded-xl overflow-hidden border border-purple-200 bg-white shadow-2xs flex flex-col">
                                                <button type="button"
                                                        onclick="abrirModalEvidenciaArchivum('{{ e($urlEv) }}', '{{ e($nombreEv) }}')"
                                                        class="relative block w-full h-24 bg-purple-50/50 overflow-hidden cursor-pointer focus:outline-none"
                                                        title="Haz clic para ampliar evidencia: {{ $nombreEv }}">
                                                    <img src="{{ $urlEv }}"
                                                         alt="Evidencia de devolución: {{ $nombreEv }}"
                                                         loading="lazy"
                                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" />
                                                    <span class="absolute inset-0 bg-black/0 group-hover:bg-black/35 transition flex items-center justify-center">
                                                        <span class="material-symbols-outlined text-white opacity-0 group-hover:opacity-100 text-[22px] drop-shadow">
                                                            zoom_in
                                                        </span>
                                                    </span>
                                                </button>
                                                <div class="px-2 py-1.5 bg-white border-t border-purple-100 flex items-center justify-between gap-1">
                                                    <span class="font-label-code text-[10px] text-on-surface truncate" title="{{ $nombreEv }}">
                                                        {{ \Illuminate\Support\Str::limit($nombreEv, 16) }}
                                                    </span>
                                                    <a href="{{ $urlEv }}" target="_blank" rel="noopener"
                                                       class="text-primary hover:underline text-[10px] font-bold shrink-0"
                                                       title="Abrir original en pestaña nueva">
                                                        Ver
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-outline">El cliente no adjuntó archivos fotográficos en esta solicitud.</p>
                                @endif

                                @if ($dev->dictamen_admin)
                                    <div class="pt-1.5 border-t border-purple-200/60 text-on-surface-variant">
                                        <strong>Dictamen Interno:</strong> {{ $dev->dictamen_admin }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Controles Operativos: 1) Dictaminar Solicitud (Aprobar / Rechazar) y 2) Recepción Física y Ajuste de Inventario -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 pt-2 border-t border-purple-100 text-xs">
                            <!-- Paso 1: Autorización o Rechazo y Emisión de Guía de Retorno -->
                            <form method="POST" action="{{ route('ventas.devoluciones.dictaminar', $dev) }}"
                                  class="p-4 rounded-xl bg-purple-50/40 border border-purple-200/70 space-y-3">
                                @csrf
                                <div class="flex items-center justify-between">
                                    <span class="font-label-code uppercase text-primary font-bold">
                                        1. Dictamen de Garantía &amp; Guía de Retorno
                                    </span>
                                    <span class="text-[11px] text-outline">Autorizar o Rechazar</span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-label-code uppercase text-secondary mb-1">Guía de Retorno (Opcional)</label>
                                        <input type="text" name="guia_retorno" value="{{ $dev->guia_retorno }}" placeholder="Se autogenera si se deja vacío"
                                               class="w-full px-3 py-1.5 rounded-lg border border-purple-200 bg-white text-xs" />
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-label-code uppercase text-secondary mb-1">Dictamen / Observación</label>
                                        <input type="text" name="dictamen_admin" value="{{ $dev->dictamen_admin }}" placeholder="Ej. Daño en traslado verificado en fotos"
                                               class="w-full px-3 py-1.5 rounded-lg border border-purple-200 bg-white text-xs" />
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-label-code uppercase text-secondary mb-1">Indicaciones para el Retorno de la Pieza</label>
                                    <input type="text" name="instrucciones_retorno" value="{{ $dev->instrucciones_retorno }}"
                                           placeholder="Instrucciones de embalaje y recolección para el cliente..."
                                           class="w-full px-3 py-1.5 rounded-lg border border-purple-200 bg-white text-xs" />
                                </div>

                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <button type="submit" name="decision" value="rechazar"
                                            class="px-3.5 py-1.5 rounded-lg border border-rose-300 bg-rose-50 hover:bg-rose-100 text-rose-800 font-bold uppercase tracking-wider text-[11px] transition cursor-pointer">
                                        Rechazar Solicitud
                                    </button>
                                    <button type="submit" name="decision" value="aprobar"
                                            class="px-4 py-1.5 rounded-lg bg-primary-container hover:bg-primary text-white font-bold uppercase tracking-wider text-[11px] transition cursor-pointer">
                                        Autorizar &amp; Generar Guía
                                    </button>
                                </div>
                            </form>

                            <!-- Paso 2: Recepción Física del Producto Retornado y Ajuste Automático de Stock / Mermas -->
                            <form method="POST" action="{{ route('ventas.devoluciones.recepcion', $dev) }}"
                                  class="p-4 rounded-xl bg-emerald-50/40 border border-emerald-200/80 space-y-3">
                                @csrf
                                <div class="flex items-center justify-between">
                                    <span class="font-label-code uppercase text-emerald-900 font-bold">
                                        2. Recepción Física, Destino de Stock &amp; Reembolso
                                    </span>
                                    @if ($dev->stock_procesado)
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-label-code font-bold">
                                            Inventario Actualizado
                                        </span>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-label-code uppercase text-secondary mb-1">Destino en Inventario *</label>
                                        <select name="destino_inventario" required class="w-full px-3 py-1.5 rounded-lg border border-purple-200 bg-white text-xs">
                                            <option value="reintegrar_stock" @selected($dev->destino_inventario === 'reintegrar_stock')>
                                                Regresar al Stock de Sucursal (Reparable / Revendible)
                                            </option>
                                            <option value="merma_baja" @selected($dev->destino_inventario === 'merma_baja')>
                                                Enviar a Mermas / Baja de Inventario (Defectuoso)
                                            </option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-label-code uppercase text-secondary mb-1">Sucursal Receptor</label>
                                        <select name="sucursal_destino_id" class="w-full px-3 py-1.5 rounded-lg border border-purple-200 bg-white text-xs">
                                            @foreach ($sucursales ?? [] as $suc)
                                                <option value="{{ $suc->id }}" @selected(($dev->sucursal_destino_id ?? $sucursalActiva?->id) === $suc->id)>
                                                    {{ $suc->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-end">
                                    <div>
                                        <label class="block text-[11px] font-label-code uppercase text-secondary mb-1">
                                            Nueva Graduación (Si regresa a stock)
                                        </label>
                                        <input type="text" name="nueva_graduacion" value="{{ $dev->nueva_graduacion }}"
                                               placeholder="Ej. VERY GOOD (VG) · Reacondicionado"
                                               class="w-full px-3 py-1.5 rounded-lg border border-purple-200 bg-white text-xs" />
                                    </div>

                                    <label class="inline-flex items-center gap-2 text-xs text-on-surface cursor-pointer pb-1">
                                        <input type="checkbox" name="procesar_reembolso" value="1" checked class="rounded border-purple-300 text-primary" />
                                        <span>Marcar Reembolso Procesado (${{ number_format((float) $dev->monto_reembolso, 2) }})</span>
                                    </label>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit"
                                            class="px-4 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold uppercase tracking-wider text-[11px] transition cursor-pointer">
                                        Confirmar Retorno Físico &amp; Ajustar Stock
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 p-10 text-center text-outline">
                        No hay solicitudes de devolución registradas para el filtro actual.
                    </div>
                @endforelse
            </div>

            <!-- Modal Lightbox para Ampliar Fotografía de Evidencia en Panel Administrativo -->
            <div id="modal-evidencia-archivum"
                 class="fixed inset-0 z-50 hidden items-center justify-center bg-black/75 dark:bg-[#07040d]/90 backdrop-blur-sm p-4"
                 onclick="cerrarModalEvidenciaArchivum(event)">
                <div class="relative max-w-3xl w-full bg-white dark:bg-[#171024] rounded-2xl border border-purple-200 dark:border-purple-500/30 shadow-2xl overflow-hidden"
                     onclick="event.stopPropagation()">
                    <div class="px-5 py-3.5 bg-surface-container-low dark:bg-[#1b132a] border-b border-purple-100 dark:border-purple-500/30 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[20px]">photo_camera</span>
                            <span id="modal-evidencia-titulo" class="font-label-code text-xs font-bold text-on-surface uppercase">Evidencia Fotográfica</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <a id="modal-evidencia-link" href="#" target="_blank" rel="noopener"
                               class="text-xs font-label-code font-bold text-primary hover:underline">
                                Abrir archivo original &nearr;
                            </a>
                            <button type="button" onclick="cerrarModalEvidenciaArchivum()"
                                    class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/60 hover:bg-purple-200 dark:hover:bg-purple-800 text-primary flex items-center justify-center font-bold cursor-pointer">
                                &times;
                            </button>
                        </div>
                    </div>
                    <div class="p-4 bg-neutral-950 flex items-center justify-center max-h-[75vh] overflow-auto">
                        <img id="modal-evidencia-img" src="" alt="Vista ampliada de evidencia" class="max-w-full max-h-[70vh] object-contain rounded-lg" />
                    </div>
                </div>
            </div>

            <script>
                function abrirModalEvidenciaArchivum(url, nombre) {
                    const modal = document.getElementById('modal-evidencia-archivum');
                    const img = document.getElementById('modal-evidencia-img');
                    const titulo = document.getElementById('modal-evidencia-titulo');
                    const link = document.getElementById('modal-evidencia-link');
                    if (!modal || !img) return;
                    img.src = url;
                    if (titulo) titulo.textContent = nombre || 'Evidencia Fotográfica';
                    if (link) link.href = url;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }

                function cerrarModalEvidenciaArchivum() {
                    const modal = document.getElementById('modal-evidencia-archivum');
                    if (!modal) return;
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            </script>
        @else
            <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 overflow-hidden shadow-xs">
                @if ($seccion === 'pedidos')
                    <table class="w-full text-left text-sm">
                        <thead class="bg-surface-container-low border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                            <tr>
                                <th class="py-3 px-4">Folio</th>
                                <th class="py-3 px-4">Cliente</th>
                                <th class="py-3 px-4">Estatus</th>
                                <th class="py-3 px-4 text-right">Total</th>
                                <th class="py-3 px-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-purple-100">
                            @forelse ($registros as $pedido)
                                <tr>
                                    <td class="py-3 px-4 font-label-code font-bold text-primary">{{ $pedido->folio }}</td>
                                    <td class="py-3 px-4">{{ $pedido->cliente?->nombre }} {{ $pedido->cliente?->apellidos }}</td>
                                    <td class="py-3 px-4 uppercase font-label-code text-xs font-bold">{{ $pedido->estatus }}</td>
                                    <td class="py-3 px-4 text-right font-tabular-data font-bold">${{ number_format((float) $pedido->total, 2) }}</td>
                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('ventas.pedidos.show', $pedido) }}" class="text-xs text-primary font-semibold hover:underline">Inspeccionar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-6 text-center text-outline">No hay pedidos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @elseif ($seccion === 'pagos')
                    <table class="w-full text-left text-sm">
                        <thead class="bg-surface-container-low border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                            <tr>
                                <th class="py-3 px-4">ID</th>
                                <th class="py-3 px-4">Pedido</th>
                                <th class="py-3 px-4">Método</th>
                                <th class="py-3 px-4">Estatus</th>
                                <th class="py-3 px-4 text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-purple-100">
                            @forelse ($registros as $pago)
                                <tr>
                                    <td class="py-3 px-4 font-label-code">#{{ $pago->id }}</td>
                                    <td class="py-3 px-4">{{ $pago->pedido?->folio ?? '#'.$pago->pedido_id }}</td>
                                    <td class="py-3 px-4 uppercase text-xs font-label-code">{{ $pago->metodo_pago ?? $pago->metodo }}</td>
                                    <td class="py-3 px-4 uppercase text-xs font-label-code font-bold">{{ $pago->estatus }}</td>
                                    <td class="py-3 px-4 text-right font-tabular-data font-bold">${{ number_format((float) $pago->monto, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-6 text-center text-outline">No hay pagos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @else
                    <!-- Formulario para Programar Promociones Exclusivas y Preventas Anticipadas -->
                    <div class="p-6 border-b border-purple-100 bg-surface-container-low">
                        <div class="mb-4">
                            <span class="text-[11px] font-label-code uppercase tracking-wider text-primary font-bold">Programación de Lanzamientos &amp; Preventas VIP</span>
                            <h2 class="text-base font-bold text-on-surface font-quicksand">Programar Nueva Promoción con Preventa Anticipada (Clientes Frecuentes &ge; 5 Compras)</h2>
                        </div>
                        <form method="POST" action="{{ route('ventas.promociones.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                            @csrf
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-label-code uppercase text-outline mb-1">Nombre de la Promoción / Lanzamiento *</label>
                                <input type="text" name="nombre" required placeholder="Ej. Preventa Exclusiva VIP: Incunable..." class="w-full px-3 py-2 rounded-xl border border-purple-200 bg-white text-on-surface" />
                            </div>
                            <div>
                                <label class="block text-[10px] font-label-code uppercase text-outline mb-1">Pieza Vinculada (Opcional)</label>
                                <select name="producto_id" class="w-full px-3 py-2 rounded-xl border border-purple-200 bg-white text-on-surface">
                                    <option value="">Catálogo General</option>
                                    @foreach ($productosCatalogo ?? [] as $prodCat)
                                        <option value="{{ $prodCat->id }}">{{ $prodCat->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[10px] font-label-code uppercase text-outline mb-1">Tipo *</label>
                                    <select name="tipo_descuento" class="w-full px-2.5 py-2 rounded-xl border border-purple-200 bg-white text-on-surface">
                                        <option value="porcentaje">% Porcentaje</option>
                                        <option value="monto">$ Monto Fijo</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-label-code uppercase text-outline mb-1">Valor *</label>
                                    <input type="number" step="0.01" min="0.01" name="valor" required value="15" class="w-full px-2.5 py-2 rounded-xl border border-purple-200 bg-white text-on-surface font-tabular-data" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-label-code uppercase text-outline mb-1">Inicio Preventa VIP (5+ compras) *</label>
                                <input type="date" name="preventa_inicia_en" required value="{{ now()->toDateString() }}" class="w-full px-3 py-2 rounded-xl border border-purple-200 bg-white text-on-surface font-tabular-data" />
                            </div>
                            <div>
                                <label class="block text-[10px] font-label-code uppercase text-outline mb-1">Apertura Público General *</label>
                                <input type="date" name="apertura_publica_en" required value="{{ now()->addDays(3)->toDateString() }}" class="w-full px-3 py-2 rounded-xl border border-purple-200 bg-white text-on-surface font-tabular-data" />
                            </div>
                            <div>
                                <label class="block text-[10px] font-label-code uppercase text-outline mb-1">Cierre de Promoción *</label>
                                <input type="date" name="termina_en" required value="{{ now()->addDays(10)->toDateString() }}" class="w-full px-3 py-2 rounded-xl border border-purple-200 bg-white text-on-surface font-tabular-data" />
                            </div>
                            <div class="flex items-end">
                                <input type="hidden" name="es_preventa_exclusiva" value="1" />
                                <button type="submit" class="w-full px-4 py-2 rounded-xl bg-primary-container hover:bg-primary text-white font-bold uppercase tracking-wider transition cursor-pointer">
                                    Programar Preventa
                                </button>
                            </div>
                        </form>
                    </div>

                    <table class="w-full text-left text-sm">
                        <thead class="bg-surface-container-low border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                            <tr>
                                <th class="py-3 px-4">Promoción</th>
                                <th class="py-3 px-4">Tipo</th>
                                <th class="py-3 px-4">Valor</th>
                                <th class="py-3 px-4">Vigencia &amp; Fases</th>
                                <th class="py-3 px-4">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-purple-100">
                            @forelse ($registros as $promo)
                                @php
                                    /** @var \App\Models\Promocion $promo */
                                    $faseTabla = $promo->faseActual();
                                @endphp
                                <tr>
                                    <td class="py-3 px-4">
                                        <span class="font-bold block">{{ $promo->nombre }}</span>
                                        @if ($promo->producto)
                                            <span class="text-xs text-primary font-label-code">Pieza: {{ $promo->producto->nombre }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 uppercase text-xs font-label-code">{{ $promo->tipo_descuento }}</td>
                                    <td class="py-3 px-4 font-tabular-data font-bold">{{ $promo->valor }}</td>
                                    <td class="py-3 px-4 text-xs">
                                        <div>VIP: {{ $promo->fechaInicioPreventa()->format('Y-m-d') }} &middot; Público: {{ $promo->fechaAperturaPublica()->format('Y-m-d') }} &rarr; {{ $promo->fechaCierre()->format('Y-m-d') }}</div>
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-label-code font-bold uppercase {{ $faseTabla === 'preventa_exclusiva' ? 'bg-amber-100 text-amber-900 border border-amber-300' : ($faseTabla === 'abierta_publico' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-purple-100 text-purple-900') }}">
                                            {{ $promo->etiquetaFase() }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">{{ $promo->activa ? 'Activa' : 'Inactiva' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-6 text-center text-outline">No hay promociones registradas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif
            </div>
        @endif
    </div>
</x-layouts.admin>
