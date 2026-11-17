<x-layouts.admin title="ARCHIVUM · Directorio de Proveedores" active-route="proveedores">
    <div class="space-y-6">

        {{-- Encabezado de la Sección con Botón de Alta --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-on-surface font-quicksand">Directorio de Proveedores &amp; Casas Editoriales</h1>
                <p class="text-xs text-on-surface-variant font-label-code uppercase tracking-wider mt-1">Gestión de proveedores, distribuidores y consignaciones notariales</p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    onclick="document.getElementById('modal-crear-proveedor').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-bold font-label-md uppercase tracking-wider transition shadow-sm"
                >
                    <span class="material-symbols-outlined text-[18px]">add_business</span>
                    <span>Registrar Proveedor</span>
                </button>
                <a href="{{ route('compras.ordenes') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-purple-200/80 bg-white hover:bg-surface-container text-on-surface text-xs font-label-md font-semibold transition shadow-2xs">
                    <span class="material-symbols-outlined text-[18px]">inventory</span>
                    <span>Recepcionar Mercancía</span>
                </a>
            </div>
        </div>

        {{-- Alertas de Éxito o Error --}}
        @if (session('status'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-700/60 text-emerald-800 dark:text-emerald-200 text-sm flex items-center gap-3 shadow-xs">
                <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">check_circle</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-700/60 text-rose-800 dark:text-rose-200 text-sm space-y-1 shadow-xs">
                <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wider">
                    <span class="material-symbols-outlined text-rose-600 dark:text-rose-400 text-[18px]">error</span>
                    Ocurrió un error al procesar la información del proveedor:
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Barra de Búsqueda y Filtros de Estado --}}
        <div class="bg-surface-container-lowest p-4 rounded-2xl border border-purple-200/70 shadow-2xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            {{-- Filtros por Estado --}}
            <div class="flex items-center gap-2 text-xs font-label-md">
                <a
                    href="{{ route('compras.proveedores', ['buscar' => request('buscar')]) }}"
                    class="px-3 py-1.5 rounded-xl transition {{ !request('estado') || request('estado') === 'todos' ? 'bg-primary-container text-white font-bold' : 'bg-surface-container text-on-surface hover:bg-surface-container-high' }}"
                >
                    Todos
                </a>
                <a
                    href="{{ route('compras.proveedores', ['estado' => 'activos', 'buscar' => request('buscar')]) }}"
                    class="px-3 py-1.5 rounded-xl transition {{ request('estado') === 'activos' ? 'bg-primary-container text-white font-bold' : 'bg-surface-container text-on-surface hover:bg-surface-container-high' }}"
                >
                    Activos
                </a>
                <a
                    href="{{ route('compras.proveedores', ['estado' => 'inactivos', 'buscar' => request('buscar')]) }}"
                    class="px-3 py-1.5 rounded-xl transition {{ request('estado') === 'inactivos' ? 'bg-primary-container text-white font-bold' : 'bg-surface-container text-on-surface hover:bg-surface-container-high' }}"
                >
                    Inactivos
                </a>
            </div>

            {{-- Formulario de Búsqueda --}}
            <form method="GET" action="{{ route('compras.proveedores') }}" class="flex items-center gap-2 max-w-md w-full">
                @if (request('estado'))
                    <input type="hidden" name="estado" value="{{ request('estado') }}" />
                @endif
                <div class="relative w-full">
                    <input
                        type="search"
                        name="buscar"
                        value="{{ request('buscar') }}"
                        placeholder="Buscar por nombre, razón social, RFC, contacto..."
                        class="w-full pl-9 pr-3 py-2 rounded-xl border border-purple-200 text-xs bg-white text-on-surface focus:border-primary focus:outline-none"
                    />
                    <span class="material-symbols-outlined absolute left-2.5 top-2 text-outline text-[18px]">search</span>
                </div>
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-primary text-white text-xs font-bold uppercase tracking-wider">
                    Buscar
                </button>
                @if (request('buscar'))
                    <a href="{{ route('compras.proveedores', ['estado' => request('estado')]) }}" class="text-xs text-outline hover:text-primary">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        {{-- Tabla de Proveedores --}}
        <div class="bg-surface-container-lowest rounded-2xl border border-purple-200/70 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-container-low border-b border-purple-200/60 text-xs uppercase font-label-code text-secondary">
                        <tr>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Proveedor / Razón Social</th>
                            <th class="py-3 px-4">RFC</th>
                            <th class="py-3 px-4">Contacto Directo</th>
                            <th class="py-3 px-4">Teléfono / Ubicación</th>
                            <th class="py-3 px-4 text-center">Estado</th>
                            <th class="py-3 px-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-purple-100">
                        @forelse ($proveedores as $prov)
                            <tr class="hover:bg-purple-50/40 transition">
                                <td class="py-3.5 px-4 font-label-code text-xs">#{{ $prov->id }}</td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-on-surface">{{ $prov->nombre }}</div>
                                    @if ($prov->razon_social)
                                        <div class="text-xs text-on-surface-variant font-label-code">{{ $prov->razon_social }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-mono text-xs text-primary font-bold">
                                    {{ $prov->rfc ?: '—' }}
                                </td>
                                <td class="py-3.5 px-4 text-xs">
                                    <div class="font-medium text-on-surface">{{ $prov->contacto_nombre ?: 'Sin asignar' }}</div>
                                    @if ($prov->email)
                                        <div class="text-[11px] text-outline">{{ $prov->email }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-xs text-on-surface-variant">
                                    <div>{{ $prov->telefono ?: '—' }}</div>
                                    @if ($prov->direccion)
                                        <div class="text-[11px] text-outline truncate max-w-xs">{{ $prov->direccion }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if ($prov->activo)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-label-code font-bold border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[11px] font-label-code font-bold border border-slate-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Botón para abrir modal de edición --}}
                                        <button
                                            type="button"
                                            onclick="abrirModalEditarProveedor({{ json_encode($prov) }})"
                                            class="p-1.5 rounded-lg border border-purple-200 bg-white hover:bg-surface-container text-primary transition"
                                            title="Editar Proveedor"
                                        >
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>

                                        {{-- Botón de Alternancia de Estado --}}
                                        <form method="POST" action="{{ route('compras.proveedores.toggle', $prov) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                class="p-1.5 rounded-lg border {{ $prov->activo ? 'border-amber-200 text-amber-600 hover:bg-amber-50' : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' }} bg-white transition"
                                                title="{{ $prov->activo ? 'Desactivar Proveedor' : 'Activar Proveedor' }}"
                                            >
                                                <span class="material-symbols-outlined text-[18px]">{{ $prov->activo ? 'block' : 'check_circle' }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-outline text-xs space-y-2">
                                    <span class="material-symbols-outlined text-outline text-[36px] block">business_center</span>
                                    <p class="font-medium text-sm text-on-surface">No se encontraron proveedores registrados.</p>
                                    <p class="text-xs">Utilice el botón "Registrar Proveedor" para incorporar una nueva casa editorial o distribuidor.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($proveedores->hasPages())
                <div class="p-4 border-t border-purple-100">
                    {{ $proveedores->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- Modal para Registro de Nuevo Proveedor --}}
    <div id="modal-crear-proveedor" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest w-full max-w-lg rounded-3xl border border-purple-200/80 shadow-xl overflow-hidden">
            <div class="p-6 border-b border-purple-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-primary-container text-white flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">add_business</span>
                    </div>
                    <h3 class="text-base font-bold font-quicksand text-on-surface">Registrar Nuevo Proveedor</h3>
                </div>
                <button
                    type="button"
                    onclick="document.getElementById('modal-crear-proveedor').classList.add('hidden')"
                    class="text-outline hover:text-on-surface p-1 rounded-lg"
                >
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form method="POST" action="{{ route('compras.proveedores.store') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-label-code uppercase mb-1">Nombre Comercial <span class="text-primary">*</span></label>
                    <input type="text" name="nombre" required placeholder="Ej. Fondo de Cultura Económica" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1">Razón Social</label>
                        <input type="text" name="razon_social" placeholder="FCE S.A. de C.V." class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1">RFC</label>
                        <input type="text" name="rfc" maxlength="20" placeholder="FCE340915..." class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm font-mono uppercase" />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1">Persona de Contacto</label>
                        <input type="text" name="contacto_nombre" placeholder="Lic. Arnaldo Orfila" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1">Correo Electrónico</label>
                        <input type="email" name="email" placeholder="editorial@fce.com.mx" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1">Teléfono</label>
                        <input type="text" name="telefono" placeholder="+52 55 5227 4600" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1">Dirección / Sede</label>
                        <input type="text" name="direccion" placeholder="Carretera Picacho Ajusco 227, CDMX" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
                    </div>
                </div>
                <div class="pt-3 flex justify-end gap-3 border-t border-purple-100">
                    <button
                        type="button"
                        onclick="document.getElementById('modal-crear-proveedor').classList.add('hidden')"
                        class="px-4 py-2 rounded-xl border border-purple-200 text-xs font-semibold hover:bg-surface-container"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-bold uppercase tracking-wider shadow-sm"
                    >
                        Guardar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal para Edición de Proveedor --}}
    <div id="modal-editar-proveedor" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-surface-container-lowest w-full max-w-lg rounded-3xl border border-purple-200/80 shadow-xl overflow-hidden">
            <div class="p-6 border-b border-purple-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-secondary text-white flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">edit_note</span>
                    </div>
                    <h3 class="text-base font-bold font-quicksand text-on-surface">Actualizar Datos de Proveedor</h3>
                </div>
                <button
                    type="button"
                    onclick="document.getElementById('modal-editar-proveedor').classList.add('hidden')"
                    class="text-outline hover:text-on-surface p-1 rounded-lg"
                >
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form id="form-editar-proveedor" method="POST" action="" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-label-code uppercase mb-1">Nombre Comercial <span class="text-primary">*</span></label>
                    <input type="text" id="edit-nombre" name="nombre" required class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1">Razón Social</label>
                        <input type="text" id="edit-razon_social" name="razon_social" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1">RFC</label>
                        <input type="text" id="edit-rfc" name="rfc" maxlength="20" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm font-mono uppercase" />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1">Persona de Contacto</label>
                        <input type="text" id="edit-contacto_nombre" name="contacto_nombre" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1">Correo Electrónico</label>
                        <input type="email" id="edit-email" name="email" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1">Teléfono</label>
                        <input type="text" id="edit-telefono" name="telefono" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1">Dirección / Sede</label>
                        <input type="text" id="edit-direccion" name="direccion" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
                    </div>
                </div>
                <div class="pt-3 flex justify-end gap-3 border-t border-purple-100">
                    <button
                        type="button"
                        onclick="document.getElementById('modal-editar-proveedor').classList.add('hidden')"
                        class="px-4 py-2 rounded-xl border border-purple-200 text-xs font-semibold hover:bg-surface-container"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2 rounded-xl bg-secondary hover:bg-secondary-hover text-white text-xs font-bold uppercase tracking-wider shadow-sm"
                    >
                        Actualizar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script auxiliar para rellenar formulario de edición --}}
    <script>
        function abrirModalEditarProveedor(prov) {
            const form = document.getElementById('form-editar-proveedor');
            form.action = `/compras/proveedores/${prov.id}`;
            document.getElementById('edit-nombre').value = prov.nombre || '';
            document.getElementById('edit-razon_social').value = prov.razon_social || '';
            document.getElementById('edit-rfc').value = prov.rfc || '';
            document.getElementById('edit-contacto_nombre').value = prov.contacto_nombre || '';
            document.getElementById('edit-email').value = prov.email || '';
            document.getElementById('edit-telefono').value = prov.telefono || '';
            document.getElementById('edit-direccion').value = prov.direccion || '';
            document.getElementById('modal-editar-proveedor').classList.remove('hidden');
        }
    </script>
</x-layouts.admin>
