<x-layouts.admin title="ARCHIVUM · Estado de Terminal Operativa" active-route="dashboard">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-surface-container-lowest p-6 rounded-2xl border border-purple-200/70 shadow-xs">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary-container text-white flex items-center justify-center shadow-xs">
                    <span class="material-symbols-outlined text-[26px]">terminal</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-on-surface font-quicksand">TERMINAL ARCHIVO {{ $version }}</h1>
                    <p class="text-xs text-on-surface-variant font-label-code uppercase tracking-wider">
                        Diagnóstico Operativo · Operador: {{ $usuario->nombre }} ({{ $usuario->email }})
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('public.home') }}" class="px-4 py-2 rounded-xl border border-purple-200 bg-white hover:bg-purple-50 text-xs font-label-md text-primary flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[16px]">storefront</span>
                    <span>Ver Tienda Pública</span>
                </a>
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-label-md flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[16px]">dashboard</span>
                    <span>Volver al Panel</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-purple-200/70 shadow-xs space-y-4">
                <h2 class="text-base font-bold text-on-surface font-quicksand">Parámetros del Entorno</h2>
                <dl class="divide-y divide-purple-100 text-sm">
                    @foreach ($sistema as $clave => $valor)
                        <div class="py-2.5 flex items-center justify-between">
                            <dt class="text-on-surface-variant font-label-md">{{ $clave }}</dt>
                            <dd class="font-label-code font-semibold text-on-surface">{{ $valor }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-purple-200/70 shadow-xs space-y-4">
                <h2 class="text-base font-bold text-on-surface font-quicksand">Métricas de Base de Datos en Tiempo Real</h2>
                <dl class="divide-y divide-purple-100 text-sm">
                    @foreach ($conteos as $etiqueta => $total)
                        <div class="py-2.5 flex items-center justify-between">
                            <dt class="text-on-surface-variant font-label-md">{{ $etiqueta }}</dt>
                            <dd class="font-tabular-data font-bold text-primary">{{ number_format($total) }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </div>
</x-layouts.admin>

