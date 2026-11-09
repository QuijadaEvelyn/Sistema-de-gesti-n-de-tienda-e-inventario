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

</x-layouts.public>
