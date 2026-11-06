<x-layouts.admin title="ARCHIVUM · Roles y Permisos" active-route="roles">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-on-surface font-quicksand">Estructura de Roles y Permisos del Sistema</h1>
            <p class="text-xs text-on-surface-variant font-label-code uppercase tracking-wider mt-1">Matriz de control de acceso aplicada en Sidebar y Middleware CheckRole</p>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-container-low border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                    <tr>
                        <th class="py-3 px-4">Módulo / Sección</th>
                        @foreach ($columnas as $rolCol)
                            <th class="py-3 px-4 text-center">{{ $rolCol }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-100">
                    @foreach ($matriz as $modulo => $rolesPermitidos)
                        <tr>
                            <td class="py-3 px-4 font-semibold">{{ $modulo }}</td>
                            @foreach ($columnas as $rolCol)
                                <td class="py-3 px-4 text-center">
                                    @if (in_array($rolCol, $rolesPermitidos, true))
                                        <span class="material-symbols-outlined text-emerald-600 text-[18px]">check_circle</span>
                                    @else
                                        <span class="material-symbols-outlined text-outline/40 text-[18px]">block</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>

