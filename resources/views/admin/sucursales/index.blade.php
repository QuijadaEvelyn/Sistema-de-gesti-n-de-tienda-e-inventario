<x-layouts.admin title="ARCHIVUM · Sucursales y Sedes" active-route="sucursales">
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-on-surface font-quicksand">Sucursales &amp; Sedes Operativas</h1>

        <form method="POST" action="{{ route('admin.sucursales.store') }}" class="bg-surface-container-lowest p-6 rounded-2xl border border-purple-200/70 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-xs font-label-code uppercase mb-1">Nombre de Sucursal</label>
                <input type="text" name="nombre" required placeholder="Ej. Sucursal Roma Norte" class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm" />
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-label-code uppercase mb-1">Dirección</label>
                <input type="text" name="direccion" placeholder="Calle, número y colonia" class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm" />
            </div>
            <div>
                <label class="block text-xs font-label-code uppercase mb-1">Teléfono</label>
                <input type="text" name="telefono" placeholder="5512345678" class="w-full px-3 py-2 rounded-xl border border-purple-200 text-sm" />
            </div>
            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-bold uppercase tracking-wider">Registrar Sucursal</button>
            </div>
        </form>

        <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-container-low border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                    <tr>
                        <th class="py-3 px-4">Sucursal</th>
                        <th class="py-3 px-4">Dirección</th>
                        <th class="py-3 px-4 text-right">Unidades en Custodia</th>
                        <th class="py-3 px-4 text-right">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-100">
                    @forelse ($sucursales as $suc)
                        <tr>
                            <td class="py-3 px-4 font-bold">{{ $suc->nombre }}</td>
                            <td class="py-3 px-4 text-xs text-on-surface-variant">{{ $suc->direccion ?? 'Sin dirección' }}</td>
                            <td class="py-3 px-4 text-right font-tabular-data font-bold">{{ (int) $suc->unidades_totales }}</td>
                            <td class="py-3 px-4 text-right">
                                <form method="POST" action="{{ route('admin.sucursales.toggle', $suc) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1 rounded-lg text-xs font-label-code {{ $suc->activa ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $suc->activa ? 'Activa' : 'Inactiva' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-outline">No hay sucursales registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>

