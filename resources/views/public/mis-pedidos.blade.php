<x-layouts.public title="ARCHIVUM · Mi Cuenta y Seguimiento de Adquisiciones">

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-purple-200/80 pb-5">
        <div>
            <span class="text-xs font-label-code uppercase tracking-wider text-primary font-bold">Panel del Coleccionista</span>
            <h1 class="text-2xl sm:text-3xl font-bold text-on-surface font-quicksand">Mis Adquisiciones &amp; Seguimiento de Envíos</h1>
            <p class="text-xs text-on-surface-variant mt-1">
                Consulta el estado en tiempo real de tus lotes certificados y simula el avance de custodia desde este panel.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('profile.edit') }}" class="px-4 py-2 rounded-xl border border-purple-200 bg-white text-xs font-label-md text-on-surface hover:border-primary">
                Configurar Perfil
            </a>
            <a href="{{ route('public.home') }}" class="px-4 py-2 rounded-xl bg-primary-container text-white text-xs font-label-md font-bold">
                Explorar Catálogo
            </a>
        </div>
    </div>

    <div class="space-y-6">
        @forelse ($pedidos as $pedido)
            @php
                $envio = $pedido->envios->first();
                $estadoEnvio = $envio?->estado_actual ?? 'en_preparacion';
                $infoEtapa = $etapasRastreo[$estadoEnvio] ?? $etapasRastreo['en_preparacion'];
            @endphp
            <div class="bg-white rounded-2xl border border-purple-200/80 p-6 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-purple-100 pb-4">
                    <div>
                        <span class="font-label-code text-xs font-bold text-primary">FOLIO: {{ $pedido->folio }}</span>
                        <span class="text-xs text-outline ml-2">&middot; Guía: {{ $envio?->numero_guia ?? 'En asignación' }}</span>
                        <h2 class="text-base font-bold text-on-surface font-quicksand mt-0.5">
                            Estado Actual: {{ $infoEtapa['titulo'] }}
                        </h2>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-tabular-data font-bold text-base text-on-surface">${{ number_format((float) $pedido->total, 2) }} MXN</span>
                        <a href="{{ route('checkout.confirmacion', $pedido) }}" class="px-4 py-2 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-bold uppercase tracking-wider">
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

