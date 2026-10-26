<!-- Overlay Translúcido de Bóveda -->
<div id="drawer-overlay" class="fixed inset-0 bg-purple-950/40 backdrop-blur-xs z-50 transition-opacity duration-300 opacity-0 pointer-events-none" aria-hidden="true"></div>

<!-- Cajón Desplegable del Carrito (Cart Drawer) -->
<aside id="cart-drawer" class="fixed top-0 right-0 h-full w-full max-w-md bg-surface-container-lowest border-l border-purple-200/80 z-50 shadow-2xl flex flex-col justify-between transform translate-x-full transition-transform duration-300 ease-out" role="dialog" aria-modal="true" aria-label="Bóveda de Adquisiciones">
    
    <!-- Encabezado del Cajón -->
    <div class="p-space-lg border-b border-purple-200/70 bg-surface-container-low/50">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[24px]">lock</span>
                <div>
                    <h2 class="font-headline-sm text-base text-on-surface font-bold">Bóveda de Adquisición</h2>
                    <span class="font-label-code text-secondary text-[11px] uppercase tracking-wider">3 ítems en custodia temporal</span>
                </div>
            </div>
            <button type="button" id="close-cart-btn" class="p-1.5 rounded-lg text-outline hover:text-on-surface hover:bg-surface-container transition" aria-label="Cerrar Bóveda">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Temporizador de Reserva Exclusiva de Lote Único -->
        <div class="mt-3 bg-primary-container/10 border border-primary/20 rounded-lg p-2.5 flex items-center justify-between text-xs">
            <div class="flex items-center gap-2 text-primary font-medium">
                <span class="material-symbols-outlined text-[16px] animate-pulse">timer</span>
                <span>Reserva de Piezas Únicas:</span>
            </div>
            <span id="reservation-timer" class="font-tabular-data font-bold text-primary bg-white px-2 py-0.5 rounded shadow-2xs">
                14:48 min
            </span>
        </div>
    </div>

    <!-- Lista Desplazable de Artículos Reservados -->
    <div class="flex-1 overflow-y-auto p-space-lg space-y-4 no-scrollbar">
        
        <!-- Ítem 1: Vinilo Histórico -->
        <div class="p-3 rounded-xl bg-surface-container-low border border-purple-200/60 flex gap-3 relative group">
            <div class="w-16 h-16 rounded-lg bg-surface-container flex items-center justify-center shrink-0 border border-purple-200/50">
                <span class="material-symbols-outlined text-secondary text-[28px]">album</span>
            </div>
            <div class="flex-1 min-w-0">
                <span class="font-label-sm text-primary font-bold">VINILO LP · 1959</span>
                <h3 class="font-bold text-on-surface text-sm truncate font-quicksand">Kind of Blue (Mono 1st Press)</h3>
                <p class="font-title-editorial text-xs text-on-surface-variant italic">Miles Davis · Columbia CL 1355</p>
                <div class="flex items-center justify-between mt-2">
                    <span class="font-label-code text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">MINT (M)</span>
                    <span class="font-tabular-data font-bold text-on-surface text-sm">$18,500 MXN</span>
                </div>
            </div>
            <button class="absolute top-2 right-2 text-outline hover:text-error transition" title="Liberar de reserva">
                <span class="material-symbols-outlined text-[16px]">delete</span>
            </button>
        </div>

        <!-- Ítem 2: Libro Raro / Primera Edición -->
        <div class="p-3 rounded-xl bg-surface-container-low border border-purple-200/60 flex gap-3 relative group">
            <div class="w-16 h-16 rounded-lg bg-surface-container flex items-center justify-center shrink-0 border border-purple-200/50">
                <span class="material-symbols-outlined text-secondary text-[28px]">menu_book</span>
            </div>
            <div class="flex-1 min-w-0">
                <span class="font-label-sm text-primary font-bold">PRIMERA EDICIÓN · 1967</span>
                <h3 class="font-bold text-on-surface text-sm truncate font-quicksand">Cien Años de Soledad</h3>
                <p class="font-title-editorial text-xs text-on-surface-variant italic">G. García Márquez · Editorial Sudamericana</p>
                <div class="flex items-center justify-between mt-2">
                    <span class="font-label-code text-primary bg-primary/10 px-2 py-0.5 rounded border border-primary/20">VERY FINE (VF)</span>
                    <span class="font-tabular-data font-bold text-on-surface text-sm">$32,000 MXN</span>
                </div>
            </div>
            <button class="absolute top-2 right-2 text-outline hover:text-error transition" title="Liberar de reserva">
                <span class="material-symbols-outlined text-[16px]">delete</span>
            </button>
        </div>

        <!-- Ítem 3: Memorabilia / Documento Autografiado -->
        <div class="p-3 rounded-xl bg-surface-container-low border border-purple-200/60 flex gap-3 relative group">
            <div class="w-16 h-16 rounded-lg bg-surface-container flex items-center justify-center shrink-0 border border-purple-200/50">
                <span class="material-symbols-outlined text-secondary text-[28px]">history_edu</span>
            </div>
            <div class="flex-1 min-w-0">
                <span class="font-label-sm text-tertiary font-bold">MEMORABILIA · 1928</span>
                <h3 class="font-bold text-on-surface text-sm truncate font-quicksand">Manuscrito con Firma Notariada</h3>
                <p class="font-title-editorial text-xs text-on-surface-variant italic">Carta Colección Diplomática Bellas Artes</p>
                <div class="flex items-center justify-between mt-2">
                    <span class="font-label-code text-purple-700 bg-purple-100 px-2 py-0.5 rounded border border-purple-200">COA SELLADO</span>
                    <span class="font-tabular-data font-bold text-on-surface text-sm">$9,400 MXN</span>
                </div>
            </div>
            <button class="absolute top-2 right-2 text-outline hover:text-error transition" title="Liberar de reserva">
                <span class="material-symbols-outlined text-[16px]">delete</span>
            </button>
        </div>

    </div>

    <!-- Pie del Cajón: Subtotales y Botón de Checkout -->
    <div class="p-space-lg border-t border-purple-200/70 bg-surface-container-lowest space-y-3">
        <div class="space-y-1.5 text-xs">
            <div class="flex justify-between text-on-surface-variant">
                <span>Subtotal Lote Certificado:</span>
                <span class="font-tabular-data font-medium text-on-surface">$59,900 MXN</span>
            </div>
            <div class="flex justify-between text-on-surface-variant">
                <span>Embalaje Antiácido &amp; Seguro:</span>
                <span class="font-tabular-data text-emerald-700 font-semibold">Cortesía de Bóveda</span>
            </div>
            <div class="flex justify-between font-bold text-sm text-on-surface pt-2 border-t border-purple-200/50">
                <span class="font-quicksand">Total Estimado de Depósito:</span>
                <span class="font-tabular-data text-primary font-bold text-base">$59,900 MXN</span>
            </div>
        </div>

        <a href="#checkout" class="w-full bg-primary-container hover:bg-primary text-white font-label-md py-3 rounded-xl flex items-center justify-center gap-2 shadow-sm transition">
            <span class="material-symbols-outlined text-[18px]">lock</span>
            <span>Proceder a Pasarela Notariada</span>
        </a>

        <p class="text-[10px] text-center text-outline font-label-code">
            Adquisición protegida bajo Ley Federal de Protección al Patrimonio &amp; Comercio Seguro.
        </p>
    </div>

</aside>
