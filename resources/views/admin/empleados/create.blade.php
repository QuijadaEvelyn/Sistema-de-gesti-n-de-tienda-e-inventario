<x-layouts.admin title="ARCHIVUM · Alta de Empleado" active-route="alta-empleado">
    <div class="max-w-2xl space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-on-surface font-quicksand">Alta de Nuevo Empleado</h1>
            <a href="{{ route('admin.empleados.index') }}" class="text-xs font-label-md text-primary hover:underline">&larr; Ver Plantilla</a>
        </div>

        <form method="POST" action="{{ route('admin.empleados.store') }}" class="bg-surface-container-lowest p-6 rounded-2xl border border-purple-200/70 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-label-code uppercase mb-1">Nombre Completo</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
            </div>
            <div>
                <label class="block text-xs font-label-code uppercase mb-1">Correo Institucional</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-label-code uppercase mb-1">Contraseña Inicial (Mín. 8)</label>
                    <input type="password" name="password" required class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
                </div>
                <div>
                    <label class="block text-xs font-label-code uppercase mb-1">Rol Operativo</label>
                    <select name="rol" required class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm bg-white">
                        @foreach (\App\Http\Controllers\Admin\EmpleadoController::ROLES_EMPLEADOS as $rolNombre)
                            <option value="{{ $rolNombre }}">{{ ucfirst($rolNombre) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <input type="hidden" name="activo" value="1" />
            <button type="submit" class="px-6 py-3 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-bold uppercase tracking-wider">Registrar Empleado</button>
        </form>
    </div>
</x-layouts.admin>

