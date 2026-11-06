<x-layouts.admin title="ARCHIVUM · Proveedores" active-route="proveedores">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-on-surface font-quicksand">Directorio de Proveedores &amp; Casas Editoriales</h1>
                <p class="text-xs text-on-surface-variant font-label-code uppercase tracking-wider mt-1">Gestión de adquisiciones, consignaciones y distribuidores certificados</p>
            </div>
            <a href="{{ route('compras.ordenes') }}" class="px-4 py-2 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-label-md">Registrar Recepción / Orden</a>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-purple-200/70 text-sm text-on-surface-variant space-y-2">
            <p class="font-semibold text-on-surface">Módulo de Proveedores Activo</p>
            <p>Desde esta sección el área de Compras gestiona las remesas de proveedores y las vincula directamente con las órdenes de compra y preventas pendientes.</p>
        </div>
    </div>
</x-layouts.admin>

