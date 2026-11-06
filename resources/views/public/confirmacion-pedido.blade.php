<x-layouts.public :title="'ARCHIVUM · Confirmación y Rastreo de Pedido ' . $pedido->folio">

    @php
        $estadoActualEnvio = $envio?->estado_actual ?? 'en_preparacion';
        $pasoActual = $etapasRastreo[$estadoActualEnvio]['paso'] ?? 1;
        $detalleTarjeta = $pago?->detalles?->first();
    @endphp

    <!-- Modal / Banner Destacado de Confirmación de Pago Exitoso -->
    <section class="rounded-2xl bg-gradient-to-r from-emerald-900 to-teal-950 text-white p-6 sm:p-8 mb-8 shadow-lg border border-emerald-500/40">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 border border-emerald-400/40 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-emerald-300 text-[34px]">check_circle</span>
                </div>
                <div class="space-y-1">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-400/20 text-emerald-200 text-[11px] font-label-code uppercase tracking-wider">
                        Transacción Aprobada · Pasarela Simulada ARCHIVUM
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-bold font-quicksand">
                        ¡Pago Exitoso! Tu Adquisición está en Custodia
                    </h1>
                    <p class="text-xs sm:text-sm text-emerald-100/90">
                        Folio de Pedido: <strong class="font-label-code">{{ $pedido->folio }}</strong>
                        &middot; Guía de Custodia: <strong class="font-label-code">{{ $envio?->numero_guia ?? 'GUA-PENDIENTE' }}</strong>
                        @if ($detalleTarjeta)
                            &middot; Tarjeta terminación <strong>&bull;&bull;&bull;&bull; {{ $detalleTarjeta->ultimos_digitos }}</strong> (Aut. {{ $detalleTarjeta->autorizacion }})
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('public.home') }}" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/20 text-xs font-bold uppercase tracking-wider transition">
                    Volver a la Galería
                </a>
                @auth
                    <a href="{{ route('cliente.pedidos') }}" class="px-4 py-2.5 rounded-xl bg-emerald-400 text-emerald-950 hover:bg-emerald-300 text-xs font-bold uppercase tracking-wider transition">
                        Ver Mis Pedidos
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- Simulación Interactiva de Seguimiento de Paquete (Rastreo Tipo Bóveda) -->
    <section class="bg-white rounded-2xl border border-purple-200/80 p-6 sm:p-8 shadow-xs mb-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-purple-100 pb-4">
            <div>
                <span class="text-[11px] font-label-code uppercase tracking-wider text-primary font-bold">
                    Seguimiento de Paquete en Tiempo Real
                </span>
                <h2 class="text-xl font-bold text-on-surface font-quicksand">
                    Estado del Envío: {{ $etapasRastreo[$estadoActualEnvio]['titulo'] ?? 'Bóveda Preparando Lote' }}
                </h2>
                <p class="text-xs text-on-surface-variant">
                    Transportista: {{ $envio?->paqueteria ?? 'Custodia Climatizada ARCHIVUM' }} &middot; Entrega estimada: {{ $envio?->fecha_estimada_entrega?->format('d/m/Y') ?? now()->addDays(3)->format('d/m/Y') }}
                </p>
            </div>

            <!-- Controles de Simulación Interactiva de Etapas -->
            <div class="flex flex-wrap items-center gap-2">
                @if ($estadoActualEnvio !== 'entregado')
                    <form method="POST" action="{{ route('checkout.simular-envio', $pedido) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 shadow-xs transition">
                            <span class="material-symbols-outlined text-[16px]">fast_forward</span>
                            <span>Simular Siguiente Etapa de Envío</span>
                        </button>
                    </form>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">
                        <span class="material-symbols-outlined text-[16px]">verified</span>
                        <span>Entrega Completada</span>
                    </span>
                @endif
            </div>
        </div>

        <!-- Línea de Tiempo de las 4 Etapas Curatoriales -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @foreach ($etapasRastreo as $claveEtapa => $infoEtapa)
                @php
                    $completada = $infoEtapa['paso'] <= $pasoActual;
                    $esActual = $claveEtapa === $estadoActualEnvio;
                @endphp
                <div class="p-4 rounded-xl border transition {{ $esActual ? 'border-primary bg-purple-50/70 shadow-xs' : ($completada ? 'border-emerald-300 bg-emerald-50/40' : 'border-purple-100 bg-surface-container-low/40 opacity-70') }}">
                    <div class="flex items-center justify-between mb-2">
                        <span class="w-9 h-9 rounded-xl flex items-center justify-center {{ $completada ? 'bg-primary-container text-white' : 'bg-surface-container text-outline' }}">
                            <span class="material-symbols-outlined text-[20px]">{{ $infoEtapa['icono'] }}</span>
                        </span>
                        <span class="text-[10px] font-label-code uppercase px-2 py-0.5 rounded-full {{ $esActual ? 'bg-primary text-white font-bold' : ($completada ? 'bg-emerald-100 text-emerald-800' : 'bg-surface-container text-outline') }}">
                            Paso {{ $infoEtapa['paso'] }} de 4
                        </span>
                    </div>
                    <h3 class="font-bold text-sm text-on-surface font-quicksand">{{ $infoEtapa['titulo'] }}</h3>
                    <p class="text-[11px] font-label-code text-secondary mt-0.5">{{ $infoEtapa['ubicacion'] }}</p>
                    <p class="text-xs text-on-surface-variant mt-2 leading-relaxed">{{ $infoEtapa['descripcion'] }}</p>

                    <form method="POST" action="{{ route('checkout.simular-envio', $pedido) }}" class="mt-3">
                        @csrf
                        <input type="hidden" name="etapa" value="{{ $claveEtapa }}" />
                        <button type="submit" class="text-[11px] font-semibold text-primary hover:underline">
                            {{ $esActual ? 'Etapa actual' : 'Saltar a esta etapa &rarr;' }}
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <!-- Bitácora Cronológica de Hitos del Envío -->
        @if ($envio && $envio->historial->isNotEmpty())
            <div class="pt-4 border-t border-purple-100 space-y-3">
                <h3 class="text-xs font-label-code uppercase tracking-wider text-outline font-bold">
                    Bitácora Cronológica de Custodia ({{ $envio->historial->count() }} eventos)
                </h3>
                <div class="space-y-2">
                    @foreach ($envio->historial as $hito)
                        <div class="p-3 rounded-xl bg-surface-container-low border border-purple-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
                            <div>
                                <span class="font-bold text-on-surface">{{ $hito->descripcion }}</span>
                                <span class="text-on-surface-variant block sm:inline sm:ml-2">&middot; {{ $hito->ubicacion }}</span>
                            </div>
                            <span class="font-label-code text-outline shrink-0">{{ $hito->created_at?->format('d/m/Y H:i:s') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    <!-- Detalle de Piezas Adquiridas -->
    <section class="bg-white rounded-2xl border border-purple-200/80 p-6 sm:p-8 shadow-xs space-y-4">
        <h2 class="text-lg font-bold text-on-surface font-quicksand border-b border-purple-100 pb-3">
            Piezas Incluidas en el Pedido {{ $pedido->folio }}
        </h2>
        <div class="divide-y divide-purple-100 text-sm">
            @foreach ($pedido->detalles as $detalle)
                <div class="py-3 flex items-center justify-between">
                    <div>
                        <span class="font-label-code text-xs text-primary font-bold">SKU: #{{ $detalle->varianteSku?->sku }}</span>
                        <h3 class="font-bold text-on-surface">{{ $detalle->varianteSku?->producto?->nombre ?? 'Pieza Catalogada' }}</h3>
                        <p class="text-xs text-on-surface-variant">Cantidad: {{ $detalle->cantidad }} &middot; Precio unitario: ${{ number_format((float) $detalle->precio_unitario, 2) }} MXN</p>
                    </div>
                    <span class="font-tabular-data font-bold text-base text-on-surface">${{ number_format((float) $detalle->subtotal, 2) }} MXN</span>
                </div>
            @endforeach
        </div>
        <div class="pt-3 border-t border-purple-200 flex items-center justify-between text-base font-bold">
            <span>Total Pagado:</span>
            <span class="font-tabular-data text-primary">${{ number_format((float) $pedido->total, 2) }} MXN</span>
        </div>
    </section>

</x-layouts.public>

