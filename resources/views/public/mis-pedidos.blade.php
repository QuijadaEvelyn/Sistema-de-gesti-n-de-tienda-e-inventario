<x-layouts.public title="ARCHIVUM · Mi Cuenta, Pedidos y Garantías de Devolución">

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-purple-200/80 pb-5">
        <div>
            <span class="text-xs font-label-code uppercase tracking-wider text-primary font-bold">Panel del Coleccionista</span>
            <h1 class="text-2xl sm:text-3xl font-bold text-on-surface font-quicksand">Mis Adquisiciones, Envíos &amp; Garantías</h1>
            <p class="text-xs text-on-surface-variant mt-1">
                Consulta el estado de tus lotes certificados, rastrea tus envíos o solicita una devolución por garantía en caso de daño o discrepancia curatorial.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('profile.edit') }}" class="px-4 py-2 rounded-xl border border-purple-200 bg-white text-xs font-label-md text-on-surface hover:border-primary transition">
                Configurar Perfil
            </a>
            <a href="{{ route('public.home') }}" class="px-4 py-2 rounded-xl bg-primary-container text-white text-xs font-label-md font-bold transition">
                Explorar Catálogo
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Notificación y Acceso Anticipado a Preventas Exclusivas (Clientes Frecuentes >= 5 Compras) -->
    @php
        $esFrecuenteVip = (bool) ($esClienteFrecuente ?? false);
        $totalCompras = (int) ($totalComprasCliente ?? 0);
        $faltanCompras = (int) ($comprasRestantesFrecuente ?? 5);
        $listaPromocionesVip = $promocionesPreventa ?? collect();
    @endphp
    <section id="panel-preventas-vip" class="mb-8 rounded-2xl overflow-hidden border-2 {{ $esFrecuenteVip ? 'border-amber-400 bg-gradient-to-r from-purple-950 via-indigo-950 to-slate-950 text-white' : 'border-purple-200 bg-surface-container-low text-on-surface' }} p-6 shadow-md">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-5 border-b {{ $esFrecuenteVip ? 'border-purple-800/70' : 'border-purple-200/80' }}">
            <div class="space-y-1.5">
                <div class="flex flex-wrap items-center gap-2">
                    @if ($esFrecuenteVip)
                        <span class="promo-shimmer-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-400 text-slate-950 text-[11px] font-label-code font-extrabold uppercase tracking-wider">
                            <span class="material-symbols-outlined text-[14px]">workspace_premium</span>
                            <span>Notificación VIP &middot; Cliente Frecuente ({{ $totalCompras }} Compras)</span>
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-400/50 text-emerald-300 text-[10px] font-label-code font-bold uppercase">
                            Acceso Anticipado Activo
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-100 border border-purple-300 text-primary text-[11px] font-label-code font-bold uppercase">
                            <span class="material-symbols-outlined text-[14px]">loyalty</span>
                            <span>Progreso Cliente Frecuente: {{ $totalCompras }} de 5 Compras</span>
                        </span>
                    @endif
                </div>

                @if ($esFrecuenteVip)
                    <h2 class="text-xl sm:text-2xl font-bold font-quicksand text-white">
                        Tienes Acceso Anticipado a las Preventas Exclusivas de Bóveda
                    </h2>
                    <p class="text-xs text-purple-200">
                        Por haber superado las <strong>5 adquisiciones certificadas</strong>, puedes apartar y asegurar piezas de edición especial durante la <em>Fase de Preventa Exclusiva</em> antes de su apertura al público general.
                    </p>
                @else
                    <h2 class="text-lg sm:text-xl font-bold font-quicksand text-on-surface">
                        Completa {{ $faltanCompras }} {{ $faltanCompras === 1 ? 'compra más' : 'compras más' }} para desbloquear Preventas Anticipadas VIP
                    </h2>
                    <p class="text-xs text-on-surface-variant">
                        Al alcanzar <strong>5 compras</strong> en ARCHIVUM, recibirás acceso anticipado días antes del lanzamiento oficial. Las promociones en <em>Fase de Apertura</em> ya están disponibles para ti.
                    </p>
                @endif
            </div>

            <div class="flex items-center gap-3">
                <div class="px-4 py-3 rounded-xl {{ $esFrecuenteVip ? 'bg-purple-900/60 border border-amber-400/40 text-amber-300' : 'bg-white border border-purple-200 text-primary' }} text-center">
                    <span class="block text-[10px] font-label-code uppercase">Compras Completadas</span>
                    <span class="font-tabular-data text-xl font-extrabold">{{ $totalCompras }} / 5</span>
                </div>
            </div>
        </div>

        @if ($listaPromocionesVip->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-5">
                @foreach ($listaPromocionesVip as $promoItem)
                    @php
                        /** @var \App\Models\Promocion $promoItem */
                        $faseItem = $promoItem->faseActual();
                        $esPreventaVipItem = $faseItem === 'preventa_exclusiva';
                        $puedeAdquirirItem = $promoItem->puedeAdquirir(auth()->user(), $clienteActual ?? null);
                        $prodItem = $promoItem->producto;
                        $varItem = $prodItem?->variantesSku?->first();
                        $stockItem = $varItem ? (int) $varItem->existencias->sum('cantidad') : 0;
                        $precioBaseItem = (float) ($varItem?->precio ?? $prodItem?->precio ?? 0);
                        $precioPromoItem = $promoItem->calcularPrecioPromocional($precioBaseItem);
                        $targetMsItem = $promoItem->timestampObjetivoCountdownMs();
                    @endphp
                    <div
                        class="promo-card-animated rounded-xl p-4 {{ $esFrecuenteVip ? 'bg-purple-900/50 border border-purple-500/40 text-white' : 'bg-white border border-purple-200 text-on-surface' }} flex flex-col justify-between gap-3"
                        data-promo-countdown="{{ $targetMsItem }}"
                    >
                        <div class="space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <span class="promo-shimmer-badge px-2.5 py-0.5 rounded-full text-[10px] font-label-code font-bold uppercase {{ $esPreventaVipItem ? 'bg-amber-400 text-slate-950' : 'bg-emerald-600 text-white' }}">
                                    {{ $esPreventaVipItem ? 'Preventa Exclusiva VIP' : 'Fase de Apertura Pública' }}
                                </span>
                                <span class="font-tabular-data text-xs font-extrabold {{ $esFrecuenteVip ? 'text-amber-300' : 'text-primary' }}">
                                    {{ $promoItem->tipo_descuento === 'porcentaje' ? '-' . number_format((float) $promoItem->valor, 0) . '%' : '-$' . number_format((float) $promoItem->valor, 0) }}
                                </span>
                            </div>
                            <h3 class="font-bold text-sm font-quicksand leading-snug">{{ $promoItem->nombre }}</h3>
                            <p class="text-[11px] {{ $esFrecuenteVip ? 'text-purple-200' : 'text-on-surface-variant' }}">
                                {{ $promoItem->etiquetaCountdown() }}
                                <span class="font-tabular-data font-bold {{ $esFrecuenteVip ? 'text-amber-300' : 'text-primary' }}">
                                    <span data-cd-days>02</span>d : <span data-cd-hours>11</span>h : <span data-cd-minutes>45</span>m : <span data-cd-seconds>30</span>s
                                </span>
                            </p>
                        </div>

                        <div class="pt-3 border-t {{ $esFrecuenteVip ? 'border-purple-700/60' : 'border-purple-100' }} flex items-center justify-between gap-2">
                            <div>
                                @if ($precioBaseItem > 0)
                                    <span class="text-[10px] line-through opacity-75 block">${{ number_format($precioBaseItem, 2) }}</span>
                                    <span class="font-tabular-data font-bold text-sm {{ $esFrecuenteVip ? 'text-amber-300' : 'text-on-surface' }}">${{ number_format($precioPromoItem, 2) }} MXN</span>
                                @endif
                            </div>

                            @if ($varItem && $stockItem > 0 && $puedeAdquirirItem)
                                <form method="POST" action="{{ route('carrito.agregar') }}">
                                    @csrf
                                    <input type="hidden" name="variante_sku_id" value="{{ $varItem->id }}" />
                                    <input type="hidden" name="cantidad" value="1" />
                                    <button type="submit" class="px-3 py-1.5 rounded-lg {{ $esPreventaVipItem ? 'bg-amber-400 hover:bg-amber-300 text-slate-950' : 'bg-primary-container hover:bg-primary text-white' }} text-[11px] font-label-md font-extrabold uppercase tracking-wider transition cursor-pointer">
                                        Apartar Pieza
                                    </button>
                                </form>
                            @else
                                <span class="text-[10px] font-label-code uppercase px-2.5 py-1 rounded bg-purple-100 text-purple-900 font-bold">
                                    Requiere 5+ Compras
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <div class="space-y-6">
        @forelse ($pedidos as $pedido)
            @php
                $envio = $pedido->envios->first();
                $estadoEnvio = $envio?->estado_actual ?? 'en_preparacion';
                $infoEtapa = $etapasRastreo[$estadoEnvio] ?? $etapasRastreo['en_preparacion'];
                $catalogoMotivos = $motivosDevolucion ?? \App\Models\Devolucion::MOTIVOS_CATALOGO;
            @endphp
            <div class="bg-white rounded-2xl border border-purple-200/80 p-6 shadow-xs space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-purple-100 pb-4">
                    <div>
                        <span class="font-label-code text-xs font-bold text-primary">FOLIO: {{ $pedido->folio }}</span>
                        <span class="text-xs text-outline ml-2">&middot; Guía: {{ $envio?->numero_guia ?? 'En asignación' }}</span>
                        <h2 class="text-base font-bold text-on-surface font-quicksand mt-0.5">
                            Estado Actual: {{ $infoEtapa['titulo'] }}
                        </h2>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="font-tabular-data font-bold text-base text-on-surface">${{ number_format((float) $pedido->total, 2) }} MXN</span>
                        <a href="{{ route('checkout.confirmacion', $pedido) }}" class="px-4 py-2 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-bold uppercase tracking-wider transition">
                            Rastrear Paquete &rarr;
                        </a>
                    </div>
                </div>

                <!-- Barra de progreso compacta de las 4 etapas -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                    @foreach ($etapasRastreo as $clave => $etapa)
                        <div class="p-2.5 rounded-xl border {{ $etapa['paso'] <= $infoEtapa['paso'] ? 'bg-purple-50 border-primary/40 text-primary font-semibold' : 'bg-surface-container-low border-purple-100 text-outline' }}">
                            <span class="block text-[10px] font-label-code uppercase">Paso {{ $etapa['paso'] }}</span>
                            <span>{{ $etapa['titulo'] }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Desglose de Piezas del Pedido -->
                <div class="pt-3 border-t border-purple-100">
                    <h3 class="text-xs font-label-code uppercase tracking-wider text-secondary font-bold mb-2">
                        Piezas Adquiridas en este Lote
                    </h3>
                    <div class="divide-y divide-purple-100 text-xs">
                        @foreach ($pedido->detalles as $detalle)
                            <div class="py-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div>
                                    <span class="font-label-code font-bold text-primary">#{{ $detalle->varianteSku?->sku }}</span>
                                    <span class="font-bold text-on-surface ml-1">{{ $detalle->varianteSku?->producto?->nombre ?? 'Pieza Catalogada' }}</span>
                                    <span class="text-outline ml-2">({{ $detalle->cantidad }} u. &times; ${{ number_format((float) $detalle->precio_unitario, 2) }})</span>
                                </div>
                                <span class="font-tabular-data font-bold text-on-surface">${{ number_format((float) $detalle->subtotal, 2) }} MXN</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Historial de Solicitudes de Devolución de este Pedido (si existen) -->
                @if ($pedido->devoluciones && $pedido->devoluciones->isNotEmpty())
                    <div class="p-4 rounded-xl bg-purple-50/60 border border-purple-200/80 space-y-3">
                        <h3 class="text-xs font-label-code uppercase tracking-wider text-primary font-bold flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">assignment_return</span>
                            <span>Solicitudes de Devolución y Garantía Registradas ({{ $pedido->devoluciones->count() }})</span>
                        </h3>
                        <div class="space-y-2.5">
                            @foreach ($pedido->devoluciones as $dev)
                                @php
                                    $etapaDev = $dev->estadoOperativo();
                                    $badgeDev = match ($etapaDev) {
                                        'aprobada' => 'bg-blue-100 text-blue-800 border-blue-300',
                                        'producto_retornado' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                                        'reembolso_procesado' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                        'rechazada' => 'bg-rose-100 text-rose-800 border-rose-300',
                                        default => 'bg-amber-100 text-amber-800 border-amber-300',
                                    };
                                @endphp
                                <div class="p-3.5 rounded-xl bg-white border border-purple-200/70 space-y-2 text-xs">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div>
                                            <span class="font-label-code font-bold text-primary">{{ $dev->folio_devolucion ?? ('#DEV-'.$dev->id) }}</span>
                                            <span class="text-on-surface font-semibold ml-1">
                                                &middot; {{ $dev->detallePedido?->varianteSku?->producto?->nombre ?? 'Pieza del pedido' }} ({{ $dev->cantidad }} u.)
                                            </span>
                                        </div>
                                        <span class="px-2.5 py-0.5 rounded-full border text-[11px] font-label-code font-bold uppercase {{ $badgeDev }}">
                                            {{ $dev->etiquetaEstado() }}
                                        </span>
                                    </div>
                                    <p class="text-on-surface-variant">
                                        <strong>Motivo:</strong> {{ $dev->etiquetaMotivo() }}
                                        @if ($dev->comentarios_cliente)
                                            &mdash; <em>"{{ $dev->comentarios_cliente }}"</em>
                                        @endif
                                    </p>
                                    @php
                                        $evidenciasCliente = $dev->obtenerEvidenciasNormalizadas();
                                    @endphp
                                    @if (! empty($evidenciasCliente))
                                        <div class="pt-1 space-y-1.5">
                                            <span class="block text-[10px] font-label-code uppercase text-secondary font-bold">
                                                Evidencia Fotográfica Adjunta ({{ count($evidenciasCliente) }})
                                            </span>
                                            <div class="flex flex-wrap gap-2.5">
                                                @foreach ($evidenciasCliente as $ev)
                                                    @php
                                                        $urlEvCliente = ! empty($ev['ruta']) ? asset('storage/' . $ev['ruta']) : $ev['url'];
                                                    @endphp
                                                    <a href="{{ $urlEvCliente }}" target="_blank" rel="noopener"
                                                       class="group relative w-20 h-20 rounded-xl overflow-hidden border border-purple-200 bg-purple-50/40 shadow-2xs block"
                                                       title="Ver fotografía de evidencia: {{ $ev['nombre'] }}">
                                                        <img src="{{ $urlEvCliente }}"
                                                             alt="Evidencia: {{ $ev['nombre'] }}"
                                                             loading="lazy"
                                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" />
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    @if ($dev->guia_retorno)
                                        <div class="p-2.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900">
                                            <div class="font-label-code font-bold">GUÍA DE RETORNO AUTORIZADA: {{ $dev->guia_retorno }}</div>
                                            @if ($dev->instrucciones_retorno)
                                                <p class="mt-0.5 text-[11px]">{{ $dev->instrucciones_retorno }}</p>
                                            @endif
                                        </div>
                                    @endif
                                    @if ($dev->dictamen_admin)
                                        <p class="text-[11px] text-secondary">
                                            <strong>Dictamen Curatorial ARCHIVUM:</strong> {{ $dev->dictamen_admin }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Formulario Desplegable para Solicitar Devolución o Garantía por Defecto -->
                @if (in_array($pedido->estatus, ['pagado', 'enviado', 'entregado'], true))
                    <details class="group rounded-xl border border-purple-200/90 bg-surface-container-low/50 overflow-hidden">
                        <summary class="px-4 py-3 cursor-pointer flex items-center justify-between text-xs font-bold text-primary hover:bg-purple-50/70 transition list-none">
                            <span class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">verified_user</span>
                                <span>Solicitar Devolución o Garantía por Producto Defectuoso / Dañado</span>
                            </span>
                            <span class="text-[11px] font-label-code uppercase tracking-wider text-secondary group-open:rotate-180 transition-transform">&darr; Abrir Formulario</span>
                        </summary>

                        <form method="POST"
                              action="{{ route('cliente.devoluciones.store', $pedido) }}"
                              enctype="multipart/form-data"
                              class="p-4 sm:p-5 bg-white border-t border-purple-200/70 space-y-4 text-xs">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-label-code uppercase text-secondary font-bold mb-1">Pieza a Devolver *</label>
                                    <select name="detalle_pedido_id" required class="w-full px-3 py-2 rounded-xl border border-purple-200 bg-white text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/30">
                                        @foreach ($pedido->detalles as $detalle)
                                            <option value="{{ $detalle->id }}">
                                                {{ $detalle->varianteSku?->sku }} — {{ $detalle->varianteSku?->producto?->nombre ?? 'Pieza' }} (Adquiridas: {{ $detalle->cantidad }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-label-code uppercase text-secondary font-bold mb-1">Motivo de Devolución *</label>
                                    <select name="motivo" required class="w-full px-3 py-2 rounded-xl border border-purple-200 bg-white text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/30">
                                        @foreach ($catalogoMotivos as $codigoMotivo => $etiquetaMotivo)
                                            <option value="{{ $codigoMotivo }}">{{ $etiquetaMotivo }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-label-code uppercase text-secondary font-bold mb-1">Cantidad Afectada *</label>
                                    <input type="number" name="cantidad" min="1" max="10" value="1" required
                                           class="w-full px-3 py-2 rounded-xl border border-purple-200 bg-white text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/30" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-label-code uppercase text-secondary font-bold mb-1">
                                        Fotografías de Evidencia del Defecto o Empaque (JPG, PNG, WEBP)
                                    </label>
                                    <input type="file" name="evidencias[]" multiple accept="image/*"
                                           class="w-full px-3 py-2 rounded-xl border border-purple-200 bg-surface-container-low text-on-surface file:mr-3 file:px-3 file:py-1 file:rounded-lg file:border-0 file:bg-primary-container file:text-white file:text-xs file:font-semibold" />
                                    <span class="block text-[11px] text-outline mt-1">Puedes adjuntar hasta 5 fotografías mostrando el daño o discrepancia curatorial.</span>
                                </div>

                                <div>
                                    <label class="block font-label-code uppercase text-secondary font-bold mb-1">
                                        Comentarios Explicativos *
                                    </label>
                                    <textarea name="comentarios_cliente" rows="2" required
                                              placeholder="Describe el estado en que llegó la pieza, daños en el sello o diferencias respecto a la graduación certificada..."
                                              class="w-full px-3 py-2 rounded-xl border border-purple-200 bg-white text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/30"></textarea>
                                </div>
                            </div>

                            <div class="flex justify-end pt-2 border-t border-purple-100">
                                <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-bold uppercase tracking-wider transition cursor-pointer">
                                    Enviar Solicitud de Devolución y Garantía
                                </button>
                            </div>
                        </form>
                    </details>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-purple-200/80 p-12 text-center space-y-3">
                <span class="material-symbols-outlined text-[42px] text-outline">package_2</span>
                <h2 class="text-lg font-bold text-on-surface font-quicksand">Aún no tienes adquisiciones registradas</h2>
                <p class="text-xs text-on-surface-variant max-w-md mx-auto">
                    Reserva cualquier pieza del catálogo y completa el pago simulado para visualizar el seguimiento interactivo de tu paquete.
                </p>
                <a href="{{ route('public.home') }}" class="inline-flex px-5 py-2.5 rounded-xl bg-primary-container text-white text-xs font-bold uppercase tracking-wider">
                    Ir al Catálogo
                </a>
            </div>
        @endforelse
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const countdownNodes = document.querySelectorAll('[data-promo-countdown]');
                if (countdownNodes.length === 0) {
                    return;
                }

                const actualizarContadores = () => {
                    const ahoraMs = Date.now();
                    countdownNodes.forEach((nodo) => {
                        const metaMs = parseInt(nodo.getAttribute('data-promo-countdown') || '0', 10);
                        const diffSeg = Math.max(0, Math.floor((metaMs - ahoraMs) / 1000));
                        const dias = Math.floor(diffSeg / 86400);
                        const horas = Math.floor((diffSeg % 86400) / 3600);
                        const minutos = Math.floor((diffSeg % 3600) / 60);
                        const segundos = diffSeg % 60;

                        const elDias = nodo.querySelector('[data-cd-days]');
                        const elHoras = nodo.querySelector('[data-cd-hours]');
                        const elMin = nodo.querySelector('[data-cd-minutes]');
                        const elSeg = nodo.querySelector('[data-cd-seconds]');

                        if (elDias) elDias.textContent = String(dias).padStart(2, '0');
                        if (elHoras) elHoras.textContent = String(horas).padStart(2, '0');
                        if (elMin) elMin.textContent = String(minutos).padStart(2, '0');
                        if (elSeg) elSeg.textContent = String(segundos).padStart(2, '0');
                    });
                };

                actualizarContadores();
                setInterval(actualizarContadores, 1000);
            });
        </script>
    @endpush

</x-layouts.public>
