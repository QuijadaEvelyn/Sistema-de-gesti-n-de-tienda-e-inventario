<x-layouts.admin title="ARCHIVUM · Detalle de Pedido" active-route="ventas">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-on-surface font-quicksand">Pedido {{ $pedido->folio }}</h1>
                <p class="text-xs text-on-surface-variant font-label-code uppercase">Estatus actual: {{ $pedido->estatus }} &middot; Total: ${{ number_format((float) $pedido->total, 2) }}</p>
            </div>
            <a href="{{ route('ventas.pedidos') }}" class="px-4 py-2 rounded-xl border border-purple-200 bg-white text-xs font-label-md text-primary">&larr; Volver a Pedidos</a>
        </div>

        @if (! empty($transicionesDisponibles))
            <div class="bg-surface-container-lowest p-4 rounded-2xl border border-purple-200/70 flex items-center gap-3">
                <span class="text-xs font-label-code uppercase text-outline">Cambiar estatus:</span>
                @foreach ($transicionesDisponibles as $nuevoEstatus)
                    <form method="POST" action="{{ route('ventas.pedidos.estatus', $pedido) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="estatus" value="{{ $nuevoEstatus }}" />
                        <button type="submit" class="px-4 py-2 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-bold uppercase tracking-wider">
                            Marcar como {{ $nuevoEstatus }}
                        </button>
                    </form>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.admin>

