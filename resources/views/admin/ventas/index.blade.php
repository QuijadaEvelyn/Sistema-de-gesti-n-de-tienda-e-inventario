<x-layouts.admin title="ARCHIVUM · Administración de Ventas" active-route="ventas">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-on-surface font-quicksand">Administración de Ventas · {{ ucfirst($seccion) }}</h1>
                <p class="text-xs text-on-surface-variant font-label-code uppercase tracking-wider mt-1">Control de pedidos, pagos, devoluciones y promociones</p>
            </div>
            <div class="flex flex-wrap gap-2 text-xs font-label-md">
                <a href="{{ route('ventas.pedidos') }}" class="px-3.5 py-2 rounded-xl border {{ $seccion === 'pedidos' ? 'bg-primary-container text-white border-primary' : 'bg-white border-purple-200 text-on-surface' }}">Pedidos</a>
                <a href="{{ route('ventas.pagos') }}" class="px-3.5 py-2 rounded-xl border {{ $seccion === 'pagos' ? 'bg-primary-container text-white border-primary' : 'bg-white border-purple-200 text-on-surface' }}">Pagos</a>
                <a href="{{ route('ventas.devoluciones') }}" class="px-3.5 py-2 rounded-xl border {{ $seccion === 'devoluciones' ? 'bg-primary-container text-white border-primary' : 'bg-white border-purple-200 text-on-surface' }}">Devoluciones</a>
                <a href="{{ route('ventas.promociones') }}" class="px-3.5 py-2 rounded-xl border {{ $seccion === 'promociones' ? 'bg-primary-container text-white border-primary' : 'bg-white border-purple-200 text-on-surface' }}">Promociones</a>
            </div>
        </div>

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
            @elseif ($seccion === 'devoluciones')
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-container-low border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                        <tr>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Pedido</th>
                            <th class="py-3 px-4">Motivo</th>
                            <th class="py-3 px-4">Estatus</th>
                            <th class="py-3 px-4 text-right">Reembolso</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-purple-100">
                        @forelse ($registros as $dev)
                            <tr>
                                <td class="py-3 px-4 font-label-code">#{{ $dev->id }}</td>
                                <td class="py-3 px-4">{{ $dev->pedido?->folio ?? '#'.$dev->pedido_id }}</td>
                                <td class="py-3 px-4">{{ $dev->motivo }}</td>
                                <td class="py-3 px-4 uppercase text-xs font-label-code font-bold">{{ $dev->estatus }}</td>
                                <td class="py-3 px-4 text-right font-tabular-data font-bold">${{ number_format((float) $dev->monto_reembolso, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-6 text-center text-outline">No hay devoluciones registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-container-low border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                        <tr>
                            <th class="py-3 px-4">Promoción</th>
                            <th class="py-3 px-4">Tipo</th>
                            <th class="py-3 px-4">Valor</th>
                            <th class="py-3 px-4">Vigencia</th>
                            <th class="py-3 px-4">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-purple-100">
                        @forelse ($registros as $promo)
                            <tr>
                                <td class="py-3 px-4 font-bold">{{ $promo->nombre }}</td>
                                <td class="py-3 px-4 uppercase text-xs font-label-code">{{ $promo->tipo_descuento }}</td>
                                <td class="py-3 px-4 font-tabular-data font-bold">{{ $promo->valor }}</td>
                                <td class="py-3 px-4 text-xs">{{ $promo->inicia_en?->format('Y-m-d') }} &rarr; {{ $promo->termina_en?->format('Y-m-d') }}</td>
                                <td class="py-3 px-4">{{ $promo->activa ? 'Activa' : 'Inactiva' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-6 text-center text-outline">No hay promociones registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-layouts.admin>

