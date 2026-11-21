<x-layouts.public title="ARCHIVUM · Contacto, Sucursales &amp; Horarios">
    <div class="space-y-12 mb-16">

        {{-- Encabezado Editorial --}}
        <header class="text-center max-w-3xl mx-auto space-y-4 pt-4">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary/10 text-primary border border-primary/20 text-xs font-label-code uppercase tracking-wider">
                <span class="material-symbols-outlined text-[16px]">location_on</span>
                Atención al Cliente Directa
            </span>
            <h1 class="text-3xl sm:text-4xl font-bold font-quicksand text-on-surface">
                Contacto, Sucursales &amp; Horarios de Atención
            </h1>
            <p class="text-sm sm:text-base text-on-surface-variant leading-relaxed">
                Nuestro equipo está a tu disposición para resolver dudas sobre el catálogo, disponibilidad en sucursales, envíos y Certificados de Autenticidad.
            </p>
        </header>

        {{-- Alertas de Éxito o Error --}}
        @if (session('status'))
            <div class="max-w-5xl mx-auto p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-700/60 text-emerald-800 dark:text-emerald-200 text-sm flex items-center gap-3 shadow-xs">
                <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">check_circle</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="max-w-5xl mx-auto p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-700/60 text-rose-800 dark:text-rose-200 text-sm space-y-1 shadow-xs">
                <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wider">
                    <span class="material-symbols-outlined text-rose-600 dark:text-rose-400 text-[18px]">error</span>
                    Por favor verifique los siguientes campos:
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Contenedor Principal: 2 Columnas (Sedes/Horarios + Formulario) --}}
        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            {{-- Columna Izquierda: Red de Sedes y Horarios (7 cols) --}}
            <div class="lg:col-span-7 space-y-6">

                {{-- Tarjeta de Identidad y Canales Generales --}}
                <div class="bg-surface-container-lowest dark:bg-[#1b132a] p-6 sm:p-8 rounded-3xl border border-purple-200/80 dark:border-purple-500/30 shadow-xs space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-primary-container text-white flex items-center justify-center">
                                <span class="material-symbols-outlined text-[20px]">storefront</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold font-quicksand text-on-surface">Nuestras Sucursales</h2>
                                <p class="text-xs text-on-surface-variant font-label-code uppercase tracking-wider">Horarios oficiales de atención al público</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-[11px] font-label-code">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Sucursales Abiertas
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="p-4 rounded-2xl bg-surface-container-low dark:bg-[#211636] border border-purple-100 dark:border-purple-500/20">
                            <span class="text-xs font-label-code text-primary uppercase font-bold block mb-1">Teléfono de Atención</span>
                            <p class="text-sm font-semibold text-on-surface">+52 (55) 5512-4200</p>
                            <p class="text-xs text-on-surface-variant mt-0.5">Línea directa para clientes</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-surface-container-low dark:bg-[#211636] border border-purple-100 dark:border-purple-500/20">
                            <span class="text-xs font-label-code text-primary uppercase font-bold block mb-1">Correo de Contacto</span>
                            <p class="text-sm font-semibold text-on-surface">contacto@archivum.mx</p>
                            <p class="text-xs text-on-surface-variant mt-0.5">Respuestas en menos de 24h</p>
                        </div>
                    </div>
                </div>

                {{-- Listado de Sucursales Activas con Dirección y Horario --}}
                <div class="space-y-4">
                    <h3 class="text-xs font-label-code text-on-surface-variant uppercase tracking-wider pl-1">
                        Sedes Activas y Horarios de Atención
                    </h3>

                    @forelse ($sucursales as $sucursal)
                        <div class="bg-surface-container-lowest dark:bg-[#1b132a] p-5 sm:p-6 rounded-2xl border border-purple-200/70 dark:border-purple-500/20 shadow-2xs hover:border-primary/50 transition">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                                <div class="space-y-1.5 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-7 h-7 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">
                                            #{{ $sucursal->id }}
                                        </span>
                                        <h4 class="font-bold text-base font-quicksand text-on-surface">{{ $sucursal->nombre }}</h4>
                                    </div>
                                    <p class="text-xs text-on-surface-variant flex items-start gap-1.5 pt-1">
                                        <span class="material-symbols-outlined text-[16px] text-primary shrink-0">pin_drop</span>
                                        <span>{{ $sucursal->direccion }}</span>
                                    </p>
                                    @if ($sucursal->telefono)
                                        <p class="text-xs text-on-surface-variant flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-[16px] text-secondary shrink-0">call</span>
                                            <span>{{ $sucursal->telefono }}</span>
                                        </p>
                                    @endif
                                </div>

                                {{-- Bloque de Horarios Específicos --}}
                                <div class="bg-surface-container-low dark:bg-[#25193e] px-4 py-3 rounded-xl border border-purple-100 dark:border-purple-500/20 text-xs shrink-0 sm:min-w-[210px] space-y-1">
                                    <span class="font-bold text-primary block font-label-code uppercase text-[11px]">Horario de Atención:</span>
                                    <p class="text-on-surface font-medium">Lun – Vie: 10:00 a 20:00 hrs</p>
                                    <p class="text-on-surface-variant">Sáb: 11:00 a 18:00 hrs</p>
                                    <p class="text-[10px] text-outline font-label-code">Dom: Atención con cita previa</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-purple-200/70 text-center text-sm text-outline">
                            Actualmente no hay sucursales activas registradas en el sistema.
                        </div>
                    @endforelse
                </div>

                {{-- Información sobre Ediciones Especiales --}}
                <div class="p-5 rounded-2xl bg-purple-50/60 dark:bg-purple-950/20 border border-purple-200/60 dark:border-purple-500/20 flex items-start gap-4">
                    <span class="material-symbols-outlined text-primary text-[24px] shrink-0 mt-0.5">verified</span>
                    <div class="text-xs text-on-surface-variant leading-relaxed">
                        <strong class="text-on-surface block font-quicksand font-bold mb-0.5">Citas para Revisión de Piezas Únicas y Ediciones Especiales</strong>
                        Si deseas conocer físicamente alguna Edición Especial, primera edición autografiada o vinilo de colección en sucursal, puedes agendar una cita con 24 horas de anticipación a través de este formulario.
                    </div>
                </div>

            </div>

            {{-- Columna Derecha: Formulario de Mensaje Funcional (5 cols) --}}
            <div class="lg:col-span-5 bg-surface-container-lowest dark:bg-[#1b132a] p-6 sm:p-8 rounded-3xl border border-purple-200/80 dark:border-purple-500/30 shadow-xs space-y-5">
                <div class="space-y-1 border-b border-purple-100 dark:border-purple-500/20 pb-4">
                    <h2 class="text-xl font-bold font-quicksand text-on-surface">Envíanos un Mensaje</h2>
                    <p class="text-xs text-on-surface-variant font-label-code uppercase tracking-wider">
                        Formulario de consulta directa e informes
                    </p>
                </div>

                <form method="POST" action="{{ route('public.contacto.enviar') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="campo-nombre" class="block text-xs font-label-code uppercase text-on-surface mb-1">
                            Nombre Completo o Razón Social <span class="text-primary">*</span>
                        </label>
                        <input
                            type="text"
                            id="campo-nombre"
                            name="nombre"
                            value="{{ old('nombre', auth()->user()?->nombre ?? '') }}"
                            required
                            maxlength="150"
                            placeholder="Ej. Dra. Beatriz Espejo"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200/80 dark:border-purple-500/30 bg-surface-container-low/70 dark:bg-[#211636] text-sm text-on-surface placeholder:text-outline focus:border-primary focus:outline-none transition shadow-2xs"
                        />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="campo-email" class="block text-xs font-label-code uppercase text-on-surface mb-1">
                                Correo Electrónico <span class="text-primary">*</span>
                            </label>
                            <input
                                type="email"
                                id="campo-email"
                                name="email"
                                value="{{ old('email', auth()->user()?->email ?? '') }}"
                                required
                                maxlength="150"
                                placeholder="coleccionista@correo.com"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200/80 dark:border-purple-500/30 bg-surface-container-low/70 dark:bg-[#211636] text-sm text-on-surface placeholder:text-outline focus:border-primary focus:outline-none transition shadow-2xs"
                            />
                        </div>
                        <div>
                            <label for="campo-telefono" class="block text-xs font-label-code uppercase text-on-surface mb-1">
                                Teléfono de Contacto
                            </label>
                            <input
                                type="tel"
                                id="campo-telefono"
                                name="telefono"
                                value="{{ old('telefono') }}"
                                maxlength="50"
                                placeholder="+52 55..."
                                class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200/80 dark:border-purple-500/30 bg-surface-container-low/70 dark:bg-[#211636] text-sm text-on-surface placeholder:text-outline focus:border-primary focus:outline-none transition shadow-2xs"
                            />
                        </div>
                    </div>

                    <div>
                        <label for="campo-sucursal" class="block text-xs font-label-code uppercase text-on-surface mb-1">
                            Sede de Preferencia (Opcional)
                        </label>
                        <select
                            id="campo-sucursal"
                            name="sucursal_id"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200/80 dark:border-purple-500/30 bg-surface-container-low/70 dark:bg-[#211636] text-sm text-on-surface focus:border-primary focus:outline-none transition shadow-2xs"
                        >
                            <option value="">Atención General de Curaduría</option>
                            @foreach ($sucursales as $s)
                                <option value="{{ $s->id }}" @selected(old('sucursal_id') == $s->id)>
                                    {{ $s->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="campo-asunto" class="block text-xs font-label-code uppercase text-on-surface mb-1">
                            Asunto de la Consulta <span class="text-primary">*</span>
                        </label>
                        <input
                            type="text"
                            id="campo-asunto"
                            name="asunto"
                            value="{{ old('asunto') }}"
                            required
                            maxlength="200"
                            placeholder="Ej. Solicitud de tasación / Verificación de primera edición"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200/80 dark:border-purple-500/30 bg-surface-container-low/70 dark:bg-[#211636] text-sm text-on-surface placeholder:text-outline focus:border-primary focus:outline-none transition shadow-2xs"
                        />
                    </div>

                    <div>
                        <label for="campo-mensaje" class="block text-xs font-label-code uppercase text-on-surface mb-1">
                            Mensaje o Descripción de la Pieza <span class="text-primary">*</span>
                        </label>
                        <textarea
                            id="campo-mensaje"
                            name="mensaje"
                            rows="5"
                            required
                            minlength="10"
                            maxlength="2000"
                            placeholder="Describa el motivo de su comunicación, título de la obra o número de catálogo de su interés..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200/80 dark:border-purple-500/30 bg-surface-container-low/70 dark:bg-[#211636] text-sm text-on-surface placeholder:text-outline focus:border-primary focus:outline-none transition shadow-2xs resize-none"
                        >{{ old('mensaje') }}</textarea>
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            class="btn-glass-purple w-full py-3.5 rounded-xl font-bold font-label-md uppercase tracking-wider text-xs flex items-center justify-center gap-2 shadow-md cursor-pointer"
                        >
                            <span class="material-symbols-outlined text-[18px]">mail</span>
                            <span>Enviar Mensaje</span>
                        </button>
                    </div>

                    <p class="text-[11px] text-center text-outline font-label-code pt-1">
                        Sus datos están resguardados bajo nuestro
                        <a href="{{ route('public.privacidad') }}" class="underline text-primary hover:text-primary-hover">Aviso de Privacidad</a>.
                    </p>
                </form>
            </div>

        </div>

    </div>
</x-layouts.public>
