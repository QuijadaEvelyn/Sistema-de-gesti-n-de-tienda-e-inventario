<x-layouts.admin title="ARCHIVUM · Bitácora del Sistema" active-route="bitacora">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-on-surface font-quicksand">Bitácora de Auditoría Notarial</h1>
            <p class="text-xs text-on-surface-variant font-label-code uppercase tracking-wider mt-1">Registro inmutable de acciones operativas y de seguridad</p>
        </div>

        <form method="GET" action="{{ route('admin.bitacora') }}" class="bg-surface-container-lowest p-4 rounded-2xl border border-purple-200/70 flex flex-wrap gap-3 items-center">
            <input type="text" name="accion" value="{{ request('accion') }}" placeholder="Filtrar por acción (ej. LOGIN)" class="px-3.5 py-2 rounded-xl border border-purple-200 text-sm" />
            <select name="tabla" class="px-3.5 py-2 rounded-xl border border-purple-200 text-sm bg-white">
                <option value="">Todas las tablas</option>
                @foreach ($tablas as $tabla)
                    <option value="{{ $tabla }}" @selected(request('tabla') === $tabla)>{{ $tabla }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold uppercase tracking-wider">Filtrar</button>
        </form>

        <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-container-low border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                    <tr>
                        <th class="py-3 px-4">Fecha</th>
                        <th class="py-3 px-4">Acción</th>
                        <th class="py-3 px-4">Usuario</th>
                        <th class="py-3 px-4">Tabla</th>
                        <th class="py-3 px-4">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-100">
                    @forelse ($eventos as $ev)
                        <tr>
                            <td class="py-3 px-4 font-label-code text-xs">{{ $ev->created_at }}</td>
                            <td class="py-3 px-4 font-label-code font-bold text-primary text-xs">{{ $ev->accion }}</td>
                            <td class="py-3 px-4 text-xs">{{ $ev->usuario?->email ?? 'Sistema / Invitado' }}</td>
                            <td class="py-3 px-4 font-label-code text-xs">{{ $ev->tabla_afectada }}</td>
                            <td class="py-3 px-4 font-label-code text-xs text-outline">{{ $ev->ip }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-outline">Sin registros en la bitácora.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>

