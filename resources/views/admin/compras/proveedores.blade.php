<x-layouts.admin title="ARCHIVUM · Directorio de Proveedores" active-route="proveedores">
    <div class="space-y-6">

        {{-- Encabezado de la Sección con Botones Glossy --}}
        <div class="bento-reveal bento-delay-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-on-surface font-quicksand">Directorio de Proveedores &amp; Casas Editoriales</h1>
                <p class="text-xs text-on-surface-variant font-label-code uppercase tracking-wider mt-1">Gestión de proveedores, distribuidores y consignaciones notariales</p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    onclick="document.getElementById('modal-crear-proveedor').classList.remove('hidden')"
                    class="btn-glass-purple px-4 py-2.5 rounded-xl text-white text-xs font-bold font-label-md uppercase tracking-wider shadow-xs inline-flex items-center gap-2"
                >
                    <span class="material-symbols-outlined text-[18px]">add_business</span>
                    <span>Registrar Proveedor</span>
                </button>
                <a href="{{ route('compras.ordenes') }}" class="btn-glass-surface px-4 py-2.5 rounded-xl text-xs font-label-md font-semibold text-primary dark:text-[#ccbeff] shadow-xs inline-flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">inventory</span>
                    <span>Recepcionar Mercancía</span>
                </a>
            </div>
        </div>

        {{-- Alertas de Éxito o Error --}}
        @if (session('status'))
            <div class="bento-reveal bento-delay-0 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-700/60 text-emerald-800 dark:text-emerald-200 text-sm flex items-center gap-3 shadow-xs">
                <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">check_circle</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bento-reveal bento-delay-0 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-700/60 text-rose-800 dark:text-rose-200 text-sm space-y-1 shadow-xs">
                <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wider">
                    <span class="material-symbols-outlined text-rose-600 dark:text-rose-400 text-[18px]">error</span>
                    <span>Ocurrió un error al procesar la información del proveedor:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Barra de Búsqueda y Filtros de Estado Glassmorphism --}}
        <div class="bento-reveal bento-delay-60 bg-white/90 dark:bg-[#1a1626]/90 backdrop-blur-md p-4 rounded-2xl border border-purple-200/70 dark:border-white/10 shadow-2xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 transition-all duration-300 hover:border-purple-300 dark:hover:border-purple-500/40">
            {{-- Filtros por Estado --}}
            <div class="flex items-center gap-2 text-xs font-label-md">
                <a
                    href="{{ route('compras.proveedores', ['buscar' => request('buscar')]) }}"
                    class="px-3.5 py-1.5 rounded-xl transition-all duration-200 text-xs font-bold {{ !request('estado') || request('estado') === 'todos' ? 'btn-glass-purple text-white shadow-xs' : 'btn-glass-surface text-on-surface' }}"
                >
                    Todos
                </a>
                <a
                    href="{{ route('compras.proveedores', ['estado' => 'activos', 'buscar' => request('buscar')]) }}"
                    class="px-3.5 py-1.5 rounded-xl transition-all duration-200 text-xs font-bold {{ request('estado') === 'activos' ? 'btn-glass-purple text-white shadow-xs' : 'btn-glass-surface text-on-surface' }}"
                >
                    Activos
                </a>
                <a
                    href="{{ route('compras.proveedores', ['estado' => 'inactivos', 'buscar' => request('buscar')]) }}"
                    class="px-3.5 py-1.5 rounded-xl transition-all duration-200 text-xs font-bold {{ request('estado') === 'inactivos' ? 'btn-glass-purple text-white shadow-xs' : 'btn-glass-surface text-on-surface' }}"
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
                        class="w-full pl-9 pr-3 py-2 rounded-xl border border-purple-200 dark:border-purple-800/60 text-xs bg-white dark:bg-[#1f1a2e] text-on-surface focus:border-primary focus:outline-hidden transition-all shadow-2xs"
                    />
                    <span class="material-symbols-outlined absolute left-2.5 top-2 text-outline dark:text-purple-300/60 text-[18px]">search</span>
                </div>
                <button type="submit" class="btn-glass-purple px-4 py-2 rounded-xl text-white text-xs font-bold uppercase tracking-wider shadow-xs shrink-0">
                    Buscar
                </button>
                @if (request('buscar'))
                    <a href="{{ route('compras.proveedores', ['estado' => request('estado')]) }}" class="btn-glass-surface px-3 py-2 rounded-xl text-xs text-outline hover:text-primary dark:hover:text-[#ccbeff] shrink-0">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        {{-- Tabla de Proveedores Glassmorphic --}}
        <div class="bento-reveal bento-delay-120 bg-white/90 dark:bg-[#1a1626]/90 backdrop-blur-md rounded-2xl border border-purple-200/70 dark:border-white/10 shadow-2xs overflow-hidden transition-all duration-300 hover:border-purple-300 dark:hover:border-purple-500/40">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-container-low/80 dark:bg-[#201930]/80 backdrop-blur-xs border-b border-purple-200/60 dark:border-white/10 text-xs uppercase font-label-code text-secondary dark:text-purple-300">
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
                    <tbody class="divide-y divide-purple-100 dark:divide-white/5">
                        @forelse ($proveedores as $prov)
                            <tr class="hover:bg-purple-50/50 dark:hover:bg-purple-900/20 transition-all">
                                <td class="py-3.5 px-4 font-label-code text-xs text-on-surface-variant dark:text-purple-200/70">#{{ $prov->id }}</td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-on-surface dark:text-white">{{ $prov->nombre }}</div>
                                    @if ($prov->razon_social)
                                        <div class="text-xs text-on-surface-variant dark:text-purple-200/70 font-label-code">{{ $prov->razon_social }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-mono text-xs text-primary dark:text-[#ccbeff] font-bold">
                                    {{ $prov->rfc ?: '—' }}
                                </td>
                                <td class="py-3.5 px-4 text-xs">
                                    <div class="font-medium text-on-surface dark:text-white">{{ $prov->contacto_nombre ?: 'Sin asignar' }}</div>
                                    @if ($prov->email)
                                        <div class="text-[11px] text-outline dark:text-purple-300/60">{{ $prov->email }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-xs text-on-surface-variant dark:text-purple-200/70">
                                    <div>{{ $prov->telefono ?: '—' }}</div>
                                    @if ($prov->direccion)
                                        <div class="text-[11px] text-outline dark:text-purple-300/60 truncate max-w-xs">{{ $prov->direccion }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if ($prov->activo)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-[11px] font-label-code font-bold border border-emerald-200 dark:border-emerald-700/60 shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800/40 text-slate-600 dark:text-slate-400 text-[11px] font-label-code font-bold border border-slate-300 dark:border-slate-700/60 shadow-2xs">
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
                                            class="btn-glass-surface p-2 rounded-xl text-primary dark:text-[#ccbeff] shadow-xs"
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
                                                class="btn-glass-surface p-2 rounded-xl {{ $prov->activo ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }} shadow-xs"
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
                                <td colspan="7" class="py-12 text-center text-outline dark:text-purple-300/60 text-xs space-y-2">
                                    <span class="material-symbols-outlined text-outline dark:text-purple-300/40 text-[36px] block">business_center</span>
                                    <p class="font-medium text-sm text-on-surface dark:text-white">No se encontraron proveedores registrados.</p>
                                    <p class="text-xs">Utilice el botón "Registrar Proveedor" para incorporar una nueva casa editorial o distribuidor.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($proveedores->hasPages())
                <div class="p-4 border-t border-purple-100 dark:border-purple-900/40">
                    {{ $proveedores->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- Modal para Registro de Nuevo Proveedor Glassmorphic --}}
    <div id="modal-crear-proveedor" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white/95 dark:bg-[#1a1626]/95 backdrop-blur-md w-full max-w-lg rounded-3xl border border-purple-200/80 dark:border-white/10 shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-purple-100 dark:border-purple-900/40 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary-container text-white flex items-center justify-center shadow-xs">
                        <span class="material-symbols-outlined text-[20px]">add_business</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold font-quicksand text-on-surface dark:text-white">Registrar Nuevo Proveedor</h3>
                        <p class="text-xs text-on-surface-variant dark:text-purple-200/70 font-label-code">Alta de casa editorial o distribuidor</p>
                    </div>
                </div>
                <button
                    type="button"
                    onclick="document.getElementById('modal-crear-proveedor').classList.add('hidden')"
                    class="btn-glass-surface p-1.5 rounded-xl text-outline hover:text-on-surface"
                >
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form method="POST" action="{{ route('compras.proveedores.store') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-label-code uppercase mb-1.5 text-on-surface dark:text-white font-semibold">Nombre Comercial <span class="text-primary">*</span></label>
                    <input type="text" name="nombre" required placeholder="Ej. Fondo de Cultura Económica" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface focus:border-primary focus:outline-hidden shadow-2xs" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1.5 text-on-surface dark:text-white font-semibold">Razón Social</label>
                        <input type="text" name="razon_social" placeholder="FCE S.A. de C.V." class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface focus:border-primary focus:outline-hidden shadow-2xs" />
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1.5 text-on-surface dark:text-white font-semibold">RFC</label>
                        <input type="text" name="rfc" maxlength="20" placeholder="FCE340915..." class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface font-mono uppercase focus:border-primary focus:outline-hidden shadow-2xs" />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1.5 text-on-surface dark:text-white font-semibold">Persona de Contacto</label>
                        <input type="text" name="contacto_nombre" placeholder="Lic. Arnaldo Orfila" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface focus:border-primary focus:outline-hidden shadow-2xs" />
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1.5 text-on-surface dark:text-white font-semibold">Correo Electrónico</label>
                        <input type="email" name="email" placeholder="editorial@fce.com.mx" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface focus:border-primary focus:outline-hidden shadow-2xs" />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1.5 text-on-surface dark:text-white font-semibold">Teléfono</label>
                        <input type="text" name="telefono" placeholder="+52 55 5227 4600" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface focus:border-primary focus:outline-hidden shadow-2xs" />
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1.5 text-on-surface dark:text-white font-semibold">Dirección / Sede</label>
                        <input type="text" name="direccion" placeholder="Carretera Picacho Ajusco 227, CDMX" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface focus:border-primary focus:outline-hidden shadow-2xs" />
                    </div>
                </div>
                <div class="pt-4 flex justify-end gap-3 border-t border-purple-100 dark:border-purple-900/40">
                    <button
                        type="button"
                        onclick="document.getElementById('modal-crear-proveedor').classList.add('hidden')"
                        class="btn-glass-surface px-4 py-2.5 rounded-xl text-xs font-semibold text-on-surface"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="btn-glass-purple px-5 py-2.5 rounded-xl text-white text-xs font-bold uppercase tracking-wider shadow-xs"
                    >
                        Guardar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal para Edición de Proveedor Glassmorphic --}}
    <div id="modal-editar-proveedor" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white/95 dark:bg-[#1a1626]/95 backdrop-blur-md w-full max-w-lg rounded-3xl border border-purple-200/80 dark:border-white/10 shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-purple-100 dark:border-purple-900/40 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-secondary text-white flex items-center justify-center shadow-xs">
                        <span class="material-symbols-outlined text-[20px]">edit_note</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold font-quicksand text-on-surface dark:text-white">Actualizar Datos de Proveedor</h3>
                        <p class="text-xs text-on-surface-variant dark:text-purple-200/70 font-label-code">Modificación de ficha comercial y fiscal</p>
                    </div>
                </div>
                <button
                    type="button"
                    onclick="document.getElementById('modal-editar-proveedor').classList.add('hidden')"
                    class="btn-glass-surface p-1.5 rounded-xl text-outline hover:text-on-surface"
                >
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form id="form-editar-proveedor" method="POST" action="" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-label-code uppercase mb-1.5 text-on-surface dark:text-white font-semibold">Nombre Comercial <span class="text-primary">*</span></label>
                    <input type="text" id="edit-nombre" name="nombre" required class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface focus:border-primary focus:outline-hidden shadow-2xs" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1.5 text-on-surface dark:text-white font-semibold">Razón Social</label>
                        <input type="text" id="edit-razon_social" name="razon_social" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface focus:border-primary focus:outline-hidden shadow-2xs" />
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1.5 text-on-surface dark:text-white font-semibold">RFC</label>
                        <input type="text" id="edit-rfc" name="rfc" maxlength="20" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface font-mono uppercase focus:border-primary focus:outline-hidden shadow-2xs" />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1.5 text-on-surface dark:text-white font-semibold">Persona de Contacto</label>
                        <input type="text" id="edit-contacto_nombre" name="contacto_nombre" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface focus:border-primary focus:outline-hidden shadow-2xs" />
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1.5 text-on-surface dark:text-white font-semibold">Correo Electrónico</label>
                        <input type="email" id="edit-email" name="email" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface focus:border-primary focus:outline-hidden shadow-2xs" />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1.5 text-on-surface dark:text-white font-semibold">Teléfono</label>
                        <input type="text" id="edit-telefono" name="telefono" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface focus:border-primary focus:outline-hidden shadow-2xs" />
                    </div>
                    <div>
                        <label class="block text-xs font-label-code uppercase mb-1.5 text-on-surface dark:text-white font-semibold">Dirección / Sede</label>
                        <input type="text" id="edit-direccion" name="direccion" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800/60 text-sm bg-white dark:bg-[#1f1a2e] text-on-surface focus:border-primary focus:outline-hidden shadow-2xs" />
                    </div>
                </div>
                <div class="pt-4 flex justify-end gap-3 border-t border-purple-100 dark:border-purple-900/40">
                    <button
                        type="button"
                        onclick="document.getElementById('modal-editar-proveedor').classList.add('hidden')"
                        class="btn-glass-surface px-4 py-2.5 rounded-xl text-xs font-semibold text-on-surface"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="btn-glass-purple px-5 py-2.5 rounded-xl text-white text-xs font-bold uppercase tracking-wider shadow-xs"
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
