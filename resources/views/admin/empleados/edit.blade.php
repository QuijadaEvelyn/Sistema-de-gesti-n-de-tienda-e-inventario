<x-layouts.admin title="ARCHIVUM · Editar Empleado" active-route="baja-empleado">
    <div class="max-w-2xl space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-on-surface font-quicksand">Editar Empleado: {{ $empleado->nombre }}</h1>
            <a href="{{ route('admin.empleados.index') }}" class="text-xs font-label-md text-primary hover:underline">&larr; Volver</a>
        </div>

        <form method="POST" action="{{ route('admin.empleados.update', $empleado) }}" class="bg-surface-container-lowest p-6 rounded-2xl border border-purple-200/70 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-label-code uppercase mb-1">Nombre Completo</label>
                <input type="text" name="nombre" value="{{ old('nombre', $empleado->nombre) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
            </div>
            <div>
                <label class="block text-xs font-label-code uppercase mb-1">Correo Institucional</label>
                <input type="email" name="email" value="{{ old('email', $empleado->email) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-label-code uppercase mb-1">Nueva Contraseña (Opcional)</label>
                    <input type="password" name="password" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
                </div>
                <div>
                    <label class="block text-xs font-label-code uppercase mb-1">Rol Operativo</label>
                    <select name="rol" required class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm bg-white">
                        @foreach (\App\Http\Controllers\Admin\EmpleadoController::ROLES_EMPLEADOS as $rolNombre)
                            <option value="{{ $rolNombre }}" @selected($empleado->hasRole($rolNombre))>{{ ucfirst($rolNombre) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-label-code uppercase mb-1">Estado de Cuenta</label>
                <select name="activo" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm bg-white">
                    <option value="1" @selected($empleado->activo)>Activo</option>
                    <option value="0" @selected(! $empleado->activo)>Inactivo</option>
                </select>
            </div>
            <button type="submit" class="px-6 py-3 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-bold uppercase tracking-wider">Guardar Cambios</button>
        </form>
    </div>
</x-layouts.admin>

