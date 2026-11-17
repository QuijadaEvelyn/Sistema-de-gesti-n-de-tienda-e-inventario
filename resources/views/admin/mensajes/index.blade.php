<x-layouts.admin title="ARCHIVUM · Bandeja de Mensajes &amp; Consultas de Curaduría" active-route="mensajes">
    <div class="space-y-6">

        {{-- Encabezado Principal y Métricas Rápidas --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-purple-200/70 dark:border-purple-500/30 pb-5">
            <div>
                <span class="font-label-code text-[10px] uppercase tracking-widest text-primary dark:text-purple-300 font-bold block mb-1">
                    Atención al Coleccionista &middot; Bóveda Notarial
                </span>
                <h1 class="text-2xl sm:text-3xl font-bold text-on-surface font-quicksand">
                    Bandeja de Mensajes &amp; Consultas
                </h1>
                <p class="text-xs text-on-surface-variant dark:text-stone-300 mt-1 max-w-2xl leading-relaxed">
                    Comunicaciones y solicitudes de peritaje o tasación enviadas por clientes y visitantes desde el portal público de contacto.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('public.contacto') }}" target="_blank" class="btn-glass-surface inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-label-md text-xs font-bold shadow-xs">
                    <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                    <span>Ver Formulario Público</span>
                </a>
            </div>
        </div>

        {{-- Alertas de Éxito / Notificación --}}
        @if (session('status'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-700/60 text-emerald-800 dark:text-emerald-200 text-sm flex items-center gap-3 shadow-xs" role="alert">
                <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">check_circle</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        {{-- Tarjetas KPI de Estado de Consultas --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-surface-container-lowest dark:bg-[#171024] p-5 rounded-2xl border border-purple-200/70 dark:border-purple-500/30 shadow-2xs space-y-1">
                <div class="flex items-center justify-between text-secondary dark:text-purple-300">
                    <span class="text-[11px] font-label-code uppercase tracking-wider font-semibold">Total Consultas</span>
                    <span class="material-symbols-outlined text-[20px]">inbox</span>
                </div>
                <div class="text-2xl sm:text-3xl font-bold font-quicksand text-on-surface dark:text-white font-tabular-data">
                    {{ $totales['total'] }}
                </div>
                <span class="text-[10px] text-outline dark:text-stone-400 font-label-code block">Recibidas en plataforma</span>
            </div>

            <div class="bg-surface-container-lowest dark:bg-[#171024] p-5 rounded-2xl border border-amber-200/80 dark:border-amber-500/30 shadow-2xs space-y-1">
                <div class="flex items-center justify-between text-amber-600 dark:text-amber-400">
                    <span class="text-[11px] font-label-code uppercase tracking-wider font-semibold">Pendientes</span>
                    <span class="material-symbols-outlined text-[20px] {{ $totales['pendientes'] > 0 ? 'animate-pulse' : '' }}">schedule</span>
                </div>
                <div class="text-2xl sm:text-3xl font-bold font-quicksand text-amber-600 dark:text-amber-400 font-tabular-data">
                    {{ $totales['pendientes'] }}
                </div>
                <span class="text-[10px] text-outline dark:text-stone-400 font-label-code block">Requieren revisión curatorial</span>
            </div>

            <div class="bg-surface-container-lowest dark:bg-[#171024] p-5 rounded-2xl border border-purple-200/70 dark:border-purple-500/30 shadow-2xs space-y-1">
                <div class="flex items-center justify-between text-purple-600 dark:text-purple-300">
                    <span class="text-[11px] font-label-code uppercase tracking-wider font-semibold">En Revisión / Leídos</span>
                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                </div>
                <div class="text-2xl sm:text-3xl font-bold font-quicksand text-primary dark:text-purple-300 font-tabular-data">
                    {{ $totales['leidos'] }}
                </div>
                <span class="text-[10px] text-outline dark:text-stone-400 font-label-code block">En proceso de dictamen</span>
            </div>

            <div class="bg-surface-container-lowest dark:bg-[#171024] p-5 rounded-2xl border border-emerald-200/80 dark:border-emerald-500/30 shadow-2xs space-y-1">
                <div class="flex items-center justify-between text-emerald-600 dark:text-emerald-400">
                    <span class="text-[11px] font-label-code uppercase tracking-wider font-semibold">Atendidos</span>
                    <span class="material-symbols-outlined text-[20px]">mark_email_read</span>
                </div>
                <div class="text-2xl sm:text-3xl font-bold font-quicksand text-emerald-600 dark:text-emerald-400 font-tabular-data">
                    {{ $totales['respondidos'] }}
                </div>
                <span class="text-[10px] text-outline dark:text-stone-400 font-label-code block">Respuestas emitidas</span>
            </div>
        </div>

        {{-- Barra de Filtros Dinámicos --}}
        <form method="GET" action="{{ route('admin.mensajes') }}" class="bg-surface-container-lowest dark:bg-[#171024] p-4 sm:p-5 rounded-2xl border border-purple-200/70 dark:border-purple-500/30 shadow-2xs flex flex-col md:flex-row gap-3 items-stretch md:items-end">
            <div class="flex-1 min-w-[200px]">
                <label for="buscar" class="block font-label-code text-[10px] uppercase tracking-widest text-secondary dark:text-purple-300 mb-1">
                    Búsqueda Textual
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
                    <input
                        type="text"
                        id="buscar"
                        name="buscar"
                        value="{{ $filtros['buscar'] ?? '' }}"
                        placeholder="Buscar por nombre, correo, asunto o contenido..."
                        class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-purple-200 dark:border-purple-500/40 bg-surface-container-low/50 dark:bg-[#140d20] text-sm text-on-surface dark:text-stone-100 placeholder:text-outline focus:outline-none focus:border-primary"
                    />
                </div>
            </div>

            <div class="w-full md:w-44">
                <label for="estatus" class="block font-label-code text-[10px] uppercase tracking-widest text-secondary dark:text-purple-300 mb-1">
                    Estatus
                </label>
                <select
                    id="estatus"
                    name="estatus"
                    class="w-full px-3.5 py-2 rounded-xl border border-purple-200 dark:border-purple-500/40 bg-surface-container-low/50 dark:bg-[#140d20] text-sm text-on-surface dark:text-stone-100 focus:outline-none focus:border-primary cursor-pointer"
                >
                    <option value="">Todos los Estatus</option>
                    <option value="pendiente" @selected(($filtros['estatus'] ?? '') === 'pendiente')>Pendientes</option>
                    <option value="leido" @selected(($filtros['estatus'] ?? '') === 'leido')>Leídos / En Revisión</option>
                    <option value="respondido" @selected(($filtros['estatus'] ?? '') === 'respondido')>Respondidos</option>
                </select>
            </div>

            <div class="w-full md:w-56">
                <label for="sucursal_id" class="block font-label-code text-[10px] uppercase tracking-widest text-secondary dark:text-purple-300 mb-1">
                    Sede / Bóveda
                </label>
                <select
                    id="sucursal_id"
                    name="sucursal_id"
                    class="w-full px-3.5 py-2 rounded-xl border border-purple-200 dark:border-purple-500/40 bg-surface-container-low/50 dark:bg-[#140d20] text-sm text-on-surface dark:text-stone-100 focus:outline-none focus:border-primary cursor-pointer"
                >
                    <option value="">Todas las Sedes</option>
                    @foreach ($sucursales as $s)
                        <option value="{{ $s->id }}" @selected(($filtros['sucursal_id'] ?? '') == (string) $s->id)>
                            {{ $s->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 pt-1 md:pt-0">
                <button type="submit" class="btn-glass-purple px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-wider cursor-pointer shadow-sm">
                    Filtrar
                </button>
                @if (!empty($filtros['buscar']) || !empty($filtros['estatus']) || !empty($filtros['sucursal_id']))
                    <a href="{{ route('admin.mensajes') }}" class="btn-glass-surface px-4 py-2 rounded-xl text-xs font-label-code uppercase tracking-wider">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>

        {{-- Listado de Consultas --}}
        <div class="bg-surface-container-lowest dark:bg-[#171024] rounded-2xl border border-purple-200/70 dark:border-purple-500/30 overflow-hidden shadow-xs">
            <div class="px-5 py-4 border-b border-purple-200/60 dark:border-purple-500/20 bg-surface-container-low/40 dark:bg-[#1b132a] flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">forum</span>
                    <h2 class="text-sm font-bold font-quicksand uppercase tracking-wider text-on-surface dark:text-white">
                        Consultas Registradas ({{ $mensajes->total() }})
                    </h2>
                </div>
                <span class="text-xs font-label-code text-on-surface-variant dark:text-stone-400">
                    Página {{ $mensajes->currentPage() }} de {{ $mensajes->lastPage() ?: 1 }}
                </span>
            </div>

            <div class="divide-y divide-purple-100 dark:divide-purple-500/20">
                @forelse ($mensajes as $msg)
                    <article class="p-5 sm:p-6 transition hover:bg-surface-container-low/30 dark:hover:bg-[#1f1532]/40 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-label-code font-bold text-xs text-primary dark:text-purple-300">
                                        #{{ $msg->id }}
                                    </span>

                                    @if ($msg->estatus === 'pendiente')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold font-label-code uppercase bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-700/50">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Pendiente
                                        </span>
                                    @elseif ($msg->estatus === 'leido')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold font-label-code uppercase bg-purple-100 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300 border border-purple-300 dark:border-purple-700/50">
                                            <span class="material-symbols-outlined text-[12px]">visibility</span>
                                            Leído / En Dictamen
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold font-label-code uppercase bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700/50">
                                            <span class="material-symbols-outlined text-[12px]">check_circle</span>
                                            Respondido
                                        </span>
                                    @endif

                                    @if ($msg->sucursal)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-label-code text-secondary dark:text-stone-300 bg-surface-container dark:bg-[#25193e] px-2 py-0.5 rounded-md border border-purple-200/60 dark:border-purple-500/20">
                                            <span class="material-symbols-outlined text-[13px]">store</span>
                                            {{ $msg->sucursal->nombre }}
                                        </span>
                                    @else
                                        <span class="text-[11px] font-label-code text-outline dark:text-stone-400">
                                            Atención General
                                        </span>
                                    @endif

                                    <span class="text-[11px] font-label-code text-outline dark:text-stone-400">
                                        &middot; {{ $msg->created_at?->diffForHumans() }} ({{ $msg->created_at?->format('d/m/Y H:i') }})
                                    </span>
                                </div>

                                <h3 class="text-base font-bold font-quicksand text-on-surface dark:text-white pt-1">
                                    {{ $msg->asunto }}
                                </h3>
                            </div>

                            {{-- Botones de Acción de Estatus --}}
                            <div class="flex flex-wrap items-center gap-2 shrink-0">
                                @if ($msg->estatus !== 'leido')
                                    <form method="POST" action="{{ route('admin.mensajes.estatus', $msg) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="estatus" value="leido" />
                                        <button type="submit" class="btn-glass-surface px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1 shadow-2xs" title="Marcar como leído / en revisión">
                                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                                            <span>Marcar Leído</span>
                                        </button>
                                    </form>
                                @endif

                                @if ($msg->estatus !== 'respondido')
                                    <form method="POST" action="{{ route('admin.mensajes.estatus', $msg) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="estatus" value="respondido" />
                                        <button type="submit" class="btn-glass-purple px-3.5 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1 shadow-xs" title="Marcar como respondido">
                                            <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                            <span>Marcar Respondido</span>
                                        </button>
                                    </form>
                                @endif

                                @if ($msg->estatus !== 'pendiente')
                                    <form method="POST" action="{{ route('admin.mensajes.estatus', $msg) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="estatus" value="pendiente" />
                                        <button type="submit" class="btn-glass-surface px-2.5 py-1.5 rounded-lg text-[11px] font-medium text-outline hover:text-amber-600 transition" title="Reabrir a estado pendiente">
                                            <span>Reabrir</span>
                                        </button>
                                    </form>
                                @endif

                                <a
                                    href="mailto:{{ $msg->email }}?subject={{ rawurlencode('Re: [ARCHIVUM] ' . $msg->asunto) }}"
                                    class="btn-glass-surface px-3 py-1.5 rounded-lg text-xs font-semibold text-primary dark:text-purple-300 flex items-center gap-1"
                                    title="Remitir respuesta por correo electrónico"
                                >
                                    <span class="material-symbols-outlined text-[16px]">reply</span>
                                    <span>Responder</span>
                                </a>
                            </div>
                        </div>

                        {{-- Ficha del Remitente --}}
                        <div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-xs font-label-code text-on-surface-variant dark:text-stone-300 bg-surface-container-low/60 dark:bg-[#140d20] p-3 rounded-xl border border-purple-100 dark:border-purple-500/20">
                            <span class="flex items-center gap-1.5 font-bold text-on-surface dark:text-white">
                                <span class="material-symbols-outlined text-[16px] text-primary">person</span>
                                {{ $msg->nombre }}
                            </span>
                            <a href="mailto:{{ $msg->email }}" class="flex items-center gap-1 text-primary hover:underline">
                                <span class="material-symbols-outlined text-[15px]">mail</span>
                                {{ $msg->email }}
                            </a>
                            @if ($msg->telefono)
                                <a href="tel:{{ $msg->telefono }}" class="flex items-center gap-1 text-secondary dark:text-purple-300 hover:underline">
                                    <span class="material-symbols-outlined text-[15px]">call</span>
                                    {{ $msg->telefono }}
                                </a>
                            @endif
                            @if ($msg->ip)
                                <span class="text-outline dark:text-stone-400 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">desktop_windows</span>
                                    IP: {{ $msg->ip }}
                                </span>
                            @endif
                        </div>

                        {{-- Cuerpo del Mensaje --}}
                        <div class="bg-white dark:bg-[#1b132a] p-4 rounded-xl border border-purple-100 dark:border-purple-500/20">
                            <p class="text-sm text-on-surface dark:text-stone-200 leading-relaxed whitespace-pre-wrap font-quicksand">
                                {{ $msg->mensaje }}
                            </p>
                        </div>
                    </article>
                @empty
                    <div class="py-14 text-center space-y-3">
                        <div class="w-14 h-14 rounded-2xl bg-surface-container-low dark:bg-[#211636] border border-purple-200/60 dark:border-purple-500/30 flex items-center justify-center mx-auto text-outline">
                            <span class="material-symbols-outlined text-[30px] text-primary">mark_email_read</span>
                        </div>
                        <div class="space-y-1 max-w-sm mx-auto">
                            <h3 class="font-bold text-base font-quicksand text-on-surface dark:text-white">
                                No se encontraron consultas
                            </h3>
                            <p class="text-xs text-on-surface-variant dark:text-stone-400">
                                @if (!empty($filtros['buscar']) || !empty($filtros['estatus']) || !empty($filtros['sucursal_id']))
                                    No hay mensajes que coincidan con los filtros aplicados. Intenta restablecer la búsqueda.
                                @else
                                    La bandeja de curaduría está al día. Las nuevas comunicaciones de coleccionistas se reflejarán aquí de inmediato.
                                @endif
                            </p>
                        </div>
                        @if (!empty($filtros['buscar']) || !empty($filtros['estatus']) || !empty($filtros['sucursal_id']))
                            <div class="pt-2">
                                <a href="{{ route('admin.mensajes') }}" class="btn-glass-surface inline-flex items-center gap-1 px-4 py-2 rounded-xl text-xs font-semibold">
                                    <span>Ver todas las consultas</span>
                                </a>
                            </div>
                        @endif
                    </div>
                @endforelse
            </div>

            @if ($mensajes->hasPages())
                <div class="p-4 border-t border-purple-200/60 dark:border-purple-500/20 bg-surface-container-low/40 dark:bg-[#1b132a]">
                    {{ $mensajes->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.admin>
