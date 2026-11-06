<x-layouts.admin title="ARCHIVUM · Gestión y Baja de Empleados" active-route="baja-empleado">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-on-surface font-quicksand">Plantilla de Personal &amp; Baja de Empleados</h1>
                <p class="text-xs text-on-surface-variant font-label-code uppercase tracking-wider mt-1">Administración de cuentas operativas de Ventas, Almacén, Compras y Administración</p>
            </div>
            <a href="{{ route('admin.empleados.alta') }}" class="px-4 py-2 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-label-md flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">person_add</span>
                <span>Alta de Empleado</span>
            </a>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-container-low border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                    <tr>
                        <th class="py-3 px-4">Nombre</th>
                        <th class="py-3 px-4">Correo</th>
                        <th class="py-3 px-4">Rol</th>
                        <th class="py-3 px-4">Estado</th>
                        <th class="py-3 px-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-100">
                    @forelse ($empleados as $emp)
                        <tr>
                            <td class="py-3 px-4 font-bold">{{ $emp->nombre }}</td>
                            <td class="py-3 px-4 font-label-code text-xs">{{ $emp->email }}</td>
                            <td class="py-3 px-4 uppercase font-label-code text-xs text-primary font-bold">{{ $emp->roles->pluck('nombre')->implode(', ') }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-label-code {{ $emp->activo ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $emp->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <a href="{{ route('admin.empleados.edit', $emp) }}" class="text-xs text-primary font-semibold hover:underline">Editar</a>
                                <form method="POST" action="{{ route('admin.empleados.destroy', $emp) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold {{ $emp->activo ? 'text-error' : 'text-emerald-700' }} hover:underline">
                                        {{ $emp->activo ? 'Dar de Baja' : 'Reactivar' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-outline">No hay empleados registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>

