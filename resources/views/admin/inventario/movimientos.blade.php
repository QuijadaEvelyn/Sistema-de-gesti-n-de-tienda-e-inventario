<x-layouts.admin title="ARCHIVUM · Ajustes y Recepciones de Inventario" active-route="almacen-ajustes">
    <div class="space-y-6">
        <!-- Encabezado Operativo de Sede -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-primary text-[11px] font-label-code font-bold uppercase tracking-wider">
                        {{ $sucursalActiva ? 'Bóveda: '.$sucursalActiva->nombre : 'Todas las Sedes (Consolidado)' }}
                    </span>
                    <span class="text-xs text-outline font-label-code">· Control Físico de Existencias</span>
                </div>
                <h1 class="text-2xl font-bold text-on-surface font-quicksand">Ajustes de Stock &amp; Recepciones</h1>
                <p class="text-xs text-on-surface-variant font-label-code uppercase tracking-wider mt-1">
                    Registro auditable de entradas de mercancía, mermas y conteos cíclicos de bóveda
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('almacen.recepciones') }}"
                   class="px-3.5 py-2 rounded-xl border {{ $tipoPreseleccionado === 'entrada' && request()->routeIs('*recepciones*') ? 'bg-primary text-white border-primary' : 'border-purple-200 bg-white text-primary hover:bg-purple-50' }} text-xs font-label-md transition">
                    Recepciones de Lote
                </a>
                <a href="{{ route('admin.inventario') }}"
                   class="px-4 py-2 rounded-xl border border-purple-200 bg-white hover:bg-purple-50 text-xs font-label-md text-primary transition">
                    &larr; Inventario Unificado
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span>
                <span>{{ session('status') }}</span>
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

        <!-- Tarjetas de Resumen de Movimientos -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-surface-container-lowest p-4 rounded-2xl border border-purple-200/70 flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-label-code uppercase text-secondary tracking-wider">Entradas / Recepciones</span>
                    <div class="text-2xl font-bold text-emerald-700 font-tabular-data mt-1">{{ $resumenMovimientos['entradas'] ?? 0 }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">south_west</span>
                </div>
            </div>
            <div class="bg-surface-container-lowest p-4 rounded-2xl border border-purple-200/70 flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-label-code uppercase text-secondary tracking-wider">Salidas / Despachos</span>
                    <div class="text-2xl font-bold text-amber-700 font-tabular-data mt-1">{{ $resumenMovimientos['salidas'] ?? 0 }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined">north_east</span>
                </div>
            </div>
            <div class="bg-surface-container-lowest p-4 rounded-2xl border border-purple-200/70 flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-label-code uppercase text-secondary tracking-wider">Ajustes / Auditorías</span>
                    <div class="text-2xl font-bold text-primary font-tabular-data mt-1">{{ $resumenMovimientos['ajustes'] ?? 0 }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined">tune</span>
                </div>
            </div>
        </div>

        <!-- Formulario de Registro de Movimiento -->
        <form method="POST" action="{{ route('almacen.ajustes.store') }}" class="bg-surface-container-lowest p-6 rounded-2xl border border-purple-200/70 grid grid-cols-1 md:grid-cols-4 gap-4 items-end shadow-2xs">
            @csrf
            <div>
                <label class="block text-xs font-label-code uppercase mb-1 text-secondary">Existencia / SKU en Bóveda</label>
                <select name="existencia_id" required class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary/30">
                    @foreach ($existencias as $ex)
                        <option value="{{ $ex->id }}">
                            {{ $ex->varianteSku?->sku }} · {{ $ex->varianteSku?->catalogo?->titulo ?? $ex->varianteSku?->descripcion }} · {{ $ex->sucursal?->nombre }} (Actual: {{ $ex->cantidad }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-label-code uppercase mb-1 text-secondary">Tipo de Movimiento</label>
                <select name="tipo" required class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary/30">
                    <option value="entrada" @selected($tipoPreseleccionado === 'entrada')>Entrada / Recepción</option>
                    <option value="salida" @selected($tipoPreseleccionado === 'salida')>Salida / Merma</option>
                    <option value="ajuste" @selected($tipoPreseleccionado === 'ajuste')>Ajuste Físico</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-label-code uppercase mb-1 text-secondary">Cantidad</label>
                <input type="number" name="cantidad" min="1" value="1" required class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30" />
            </div>
            <div>
                <label class="block text-xs font-label-code uppercase mb-1 text-secondary">Motivo / Dictamen</label>
                <input type="text" name="motivo" required placeholder="Ej. Conteo cíclico o recepción certificada" class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30" />
            </div>
            <div class="md:col-span-4 flex justify-between items-center pt-2 border-t border-purple-100">
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-outline font-label-code uppercase">Filtrar bitácora:</span>
                    <a href="{{ route('almacen.ajustes') }}" class="px-2.5 py-1 rounded-lg {{ !request('tipo') ? 'bg-purple-100 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container' }}">Todos</a>
                    <a href="{{ route('almacen.ajustes', ['tipo' => 'entrada']) }}" class="px-2.5 py-1 rounded-lg {{ request('tipo') === 'entrada' ? 'bg-emerald-100 text-emerald-800 font-bold' : 'text-on-surface-variant hover:bg-surface-container' }}">Entradas</a>
                    <a href="{{ route('almacen.ajustes', ['tipo' => 'salida']) }}" class="px-2.5 py-1 rounded-lg {{ request('tipo') === 'salida' ? 'bg-amber-100 text-amber-800 font-bold' : 'text-on-surface-variant hover:bg-surface-container' }}">Salidas</a>
                    <a href="{{ route('almacen.ajustes', ['tipo' => 'ajuste']) }}" class="px-2.5 py-1 rounded-lg {{ request('tipo') === 'ajuste' ? 'bg-purple-100 text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container' }}">Ajustes</a>
                </div>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-bold uppercase tracking-wider transition cursor-pointer">
                    Registrar Movimiento
                </button>
            </div>
        </form>

        <!-- Bitácora de Movimientos de Inventario -->
        <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 overflow-hidden shadow-2xs">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-container-low border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                    <tr>
                        <th class="py-3 px-4">Fecha</th>
                        <th class="py-3 px-4">Tipo</th>
                        <th class="py-3 px-4">SKU / Pieza / Sucursal</th>
                        <th class="py-3 px-4 text-right">Cantidad</th>
                        <th class="py-3 px-4">Motivo &amp; Auditoría</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-100">
                    @forelse ($movimientos as $mov)
                        <tr class="hover:bg-surface-container-low/50 transition">
                            <td class="py-3 px-4 font-label-code text-xs text-on-surface-variant">{{ $mov->created_at }}</td>
                            <td class="py-3 px-4">
                                @php
                                    $badgeClass = match ($mov->tipo) {
                                        'entrada' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'salida' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        default => 'bg-purple-50 text-primary border-purple-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full border text-[11px] uppercase font-label-code font-bold {{ $badgeClass }}">
                                    {{ $mov->tipo }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-on-surface">{{ $mov->existencia?->varianteSku?->sku }} · {{ $mov->existencia?->varianteSku?->catalogo?->titulo ?? $mov->existencia?->varianteSku?->descripcion }}</div>
                                <div class="text-xs text-outline font-label-code">{{ $mov->existencia?->sucursal?->nombre }}</div>
                            </td>
                            <td class="py-3 px-4 text-right font-tabular-data font-bold {{ $mov->cantidad >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $mov->cantidad > 0 ? '+'.$mov->cantidad : $mov->cantidad }}
                            </td>
                            <td class="py-3 px-4 text-xs text-on-surface-variant">{{ $mov->motivo }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-outline">Sin movimientos registrados para los criterios actuales.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
