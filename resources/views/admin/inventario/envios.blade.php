<x-layouts.admin title="ARCHIVUM · Envíos y Despacho de Almacén" active-route="almacen-envios">
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-on-surface font-quicksand">Envíos y Despacho de Pedidos</h1>
        <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-container-low border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                    <tr>
                        <th class="py-3 px-4">Guía</th>
                        <th class="py-3 px-4">Pedido</th>
                        <th class="py-3 px-4">Paquetería</th>
                        <th class="py-3 px-4">Estado Actual</th>
                        <th class="py-3 px-4">Entrega Estimada</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-100">
                    @forelse ($envios as $envio)
                        <tr>
                            <td class="py-3 px-4 font-label-code font-bold text-primary">{{ $envio->numero_guia ?? 'SIN-GUIA' }}</td>
                            <td class="py-3 px-4">{{ $envio->pedido?->folio ?? '#'.$envio->pedido_id }}</td>
                            <td class="py-3 px-4">{{ $envio->paqueteria }}</td>
                            <td class="py-3 px-4 uppercase font-label-code text-xs">{{ $envio->estado_actual ?? $envio->estatus }}</td>
                            <td class="py-3 px-4 text-xs">{{ $envio->fecha_estimada_entrega?->format('Y-m-d') ?? 'Pendiente' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-outline">No hay envíos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>

