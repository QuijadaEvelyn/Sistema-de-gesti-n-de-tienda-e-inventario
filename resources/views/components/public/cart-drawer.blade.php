@php
    $carritoDrawer = \App\Http\Controllers\CheckoutSimuladoController::obtenerEstadoCarrito();
    $tieneItemsActivos = $carritoDrawer['cantidad_total'] > 0;
    $tienePiezasUnicas = (bool) ($carritoDrawer['tiene_piezas_unicas'] ?? false);
    $tieneProductosRegulares = (bool) ($carritoDrawer['tiene_productos_regulares'] ?? false);
    $tieneAmbosTipos = (bool) ($carritoDrawer['tiene_ambos_tipos'] ?? false);
    $itemsPiezasUnicas = $carritoDrawer['items_piezas_unicas'] ?? [];
    $itemsRegulares = $carritoDrawer['items_regulares'] ?? [];
@endphp

<!-- Overlay Translúcido de Bóveda Nocturna -->
<div id="drawer-overlay" class="fixed inset-0 bg-[#07040d]/85 backdrop-blur-sm z-50 transition-opacity duration-300 opacity-0 pointer-events-none" aria-hidden="true"></div>

<!-- Cajón Desplegable del Carrito (Cart Drawer) -->
<aside id="cart-drawer" class="fixed top-0 right-0 h-full w-full max-w-md bg-surface-container-lowest dark:bg-[#171024] border-l border-purple-200/80 dark:border-purple-500/30 z-50 shadow-2xl flex flex-col justify-between transform translate-x-full transition-transform duration-300 ease-out" role="dialog" aria-modal="true" aria-label="Carrito de Compras">

    <!-- Encabezado del Cajón -->
    <div class="p-space-lg border-b border-purple-200/70 dark:border-purple-500/30 bg-surface-container-low/50 dark:bg-[#1b132a]">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[24px]">shopping_bag</span>
                <div>
                    <h2 class="font-headline-sm text-base text-on-surface font-bold">Carrito de Compras</h2>
                    <span id="cart-drawer-pieces-count" class="font-label-code text-secondary text-[11px] uppercase tracking-wider">
                        {{ $carritoDrawer['cantidad_total'] }} {{ $carritoDrawer['cantidad_total'] === 1 ? 'producto en carrito' : 'productos reservados' }}
                    </span>
                </div>
            </div>
            <button type="button" id="close-cart-btn" class="p-1.5 rounded-lg text-outline hover:text-on-surface hover:bg-surface-container dark:hover:bg-[#221834] transition" aria-label="Cerrar Carrito">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Indicador Superior de Vigencia según Composición del Carrito -->
        @if ($tienePiezasUnicas)
            <!-- Temporizador de Expiración de Reserva (Countdown Estricto de 5 Minutos exclusivo para Piezas Únicas) -->
            <div
                id="reservation-timer-box"
                class="mt-3 bg-primary-container/10 border-primary/30 border rounded-lg p-2.5 flex items-center justify-between text-xs transition-colors"
            >
                <div class="flex items-center gap-2 text-primary font-medium">
                    <span class="material-symbols-outlined text-[16px] animate-pulse">timer</span>
                    <span>Reserva Temporal (5 min) · Piezas Únicas:</span>
                </div>
                <span
                    id="reservation-timer"
                    data-active="1"
                    data-seconds="{{ $carritoDrawer['segundos_restantes'] }}"
                    data-expires-ms="{{ $carritoDrawer['expira_en_ms'] }}"
                    data-expire-url="{{ route('carrito.expirar') }}"
                    class="font-tabular-data font-bold text-primary bg-white dark:bg-[#140d20] px-2.5 py-0.5 rounded shadow-2xs border border-purple-200/60 dark:border-purple-500/40"
                >
                    {{ $carritoDrawer['tiempo_formateado'] }}
                </span>
            </div>
        @elseif ($tieneProductosRegulares)
            <!-- Si solo hay productos comunes, ocultar el temporizador de 5 minutos y mostrar únicamente la vigencia de 7 días -->
            <div
                id="regular-validity-box"
                class="mt-3 bg-emerald-500/10 border border-emerald-500/30 rounded-lg p-2.5 flex items-center justify-between text-xs"
            >
                <div class="flex items-center gap-2 text-emerald-700 dark:text-emerald-300 font-semibold">
                    <span class="material-symbols-outlined text-[16px]">event_available</span>
                    <span>Productos Regulares (Vigencia de 7 días):</span>
                </div>
                <span class="font-label-code font-bold text-emerald-800 dark:text-emerald-200 bg-white dark:bg-[#140d20] px-2.5 py-0.5 rounded border border-emerald-300 dark:border-emerald-500/40">
                    Guardado por 7 días
                </span>
            </div>
        @endif
    </div>

    <!-- Lista Desplazable de Artículos Reservados con División Visual Clara entre Piezas Únicas y Productos Comunes -->
    <div class="flex-1 overflow-y-auto p-space-lg {{ ! $tieneItemsActivos ? 'flex flex-col justify-center items-center my-auto min-h-[320px]' : 'space-y-6' }} no-scrollbar">
        @if (! $tieneItemsActivos)
            <div class="w-full my-auto py-8 text-center space-y-4 flex flex-col items-center justify-center">
                <div class="w-16 h-16 rounded-2xl bg-surface-container dark:bg-[#1b132a] mx-auto flex items-center justify-center text-outline shadow-2xs border border-transparent dark:border-purple-500/20">
                    <span class="material-symbols-outlined text-[32px] text-primary">shopping_bag</span>
                </div>
                <div class="space-y-1.5 max-w-xs mx-auto">
                    <p class="text-sm font-bold text-on-surface font-quicksand">Tu Carrito de Compras está vacío</p>
                    <p class="text-xs text-on-surface-variant leading-relaxed">
                        Explora nuestras secciones de Libros, Discos o Coleccionables y pulsa el botón de agregar para añadir productos a tu carrito.
                    </p>
                </div>
            </div>
        @else
            {{-- =========================================================================
                 SECCIÓN 1: PIEZAS ÚNICAS (RESERVA TEMPORAL DE 5 MINUTOS)
                 ========================================================================= --}}
            @if ($tienePiezasUnicas)
                <section id="cart-section-unique-pieces" class="space-y-3" aria-label="Piezas Únicas (Reserva de 5 min)">
                    <div class="p-3 rounded-xl bg-purple-100/70 dark:bg-purple-950/50 border border-purple-300 dark:border-purple-500/40 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="material-symbols-outlined text-primary text-[18px] shrink-0">diamond</span>
                            <div class="min-w-0">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-on-surface font-label-code truncate">
                                    Piezas Únicas (Reserva de 5 min)
                                </h3>
                                <p class="text-[10px] text-on-surface-variant">
                                    Exclusividad 1/1 &middot; Si expira, solo estas piezas se liberan
                                </p>
                            </div>
                        </div>
                        <span
                            data-unique-section-timer
                            class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white dark:bg-[#140d20] border border-purple-300 dark:border-purple-400/50 font-tabular-data font-bold text-xs text-primary shadow-2xs"
                        >
                            <span class="material-symbols-outlined text-[13px] animate-pulse">timer</span>
                            <span class="unique-timer-readout">{{ $carritoDrawer['tiempo_formateado'] }}</span>
                        </span>
                    </div>

                    <div class="space-y-3">
                        @foreach ($itemsPiezasUnicas as $item)
                            @php
                                $iconoItem = match ($item['tipo']) {
                                    'libro' => 'menu_book',
                                    'disco' => 'album',
                                    default => 'history_edu',
                                };
                            @endphp
                            <div class="p-3 rounded-xl bg-surface-container-low dark:bg-[#1b132a] border-2 border-purple-300 dark:border-purple-500/60 flex gap-3 relative group shadow-2xs">
                                <div class="w-16 h-16 rounded-lg bg-surface-container dark:bg-[#140d20] flex items-center justify-center shrink-0 border border-purple-200/50 dark:border-purple-500/30 overflow-hidden">
                                    @if (! empty($item['imagen']))
                                        <img src="{{ asset('/' . $item['imagen']) }}" alt="{{ $item['nombre'] }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="material-symbols-outlined text-secondary text-[28px]">{{ $iconoItem }}</span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0 pr-6">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="font-label-sm text-primary font-bold uppercase">SKU: #{{ $item['sku'] }}</span>
                                        <span class="bg-purple-100 dark:bg-purple-600 text-purple-800 dark:text-white border border-purple-300 dark:border-purple-400/60 text-[9px] font-label-code font-bold uppercase px-1.5 py-0.5 rounded">
                                            Pieza Única 1/1
                                        </span>
                                        @if (! empty($item['es_preventa']))
                                            <span class="bg-amber-100 text-amber-800 border border-amber-300 text-[9px] font-label-code font-bold uppercase px-1.5 py-0.5 rounded">Preventa</span>
                                        @endif
                                    </div>
                                    <h4 class="font-bold text-on-surface text-sm truncate font-quicksand mt-0.5">{{ $item['nombre'] }}</h4>
                                    <p class="font-title-editorial text-xs text-on-surface-variant italic truncate">{{ $item['subtitulo'] }}</p>

                                    <div class="mt-2 flex flex-wrap items-center gap-2">
                                        <div class="inline-flex items-center gap-1.5 text-xs text-on-surface font-medium bg-surface-container/60 dark:bg-[#140d20] px-2 py-0.5 rounded-lg border border-purple-200/60 dark:border-purple-500/30">
                                            <span class="text-on-surface-variant text-[11px]">Cantidad:</span>
                                            <span class="font-bold text-on-surface text-xs">1 unidad</span>
                                            <span class="text-[10px] text-amber-600 dark:text-amber-400 font-label-code font-semibold">(Reserva 5 min)</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between mt-2 pt-1 border-t border-purple-200/40 dark:border-purple-500/20">
                                        <span class="font-label-code text-[10px] text-emerald-700 bg-emerald-50 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/40 px-2 py-0.5 rounded border border-emerald-200 truncate max-w-[140px]">{{ $item['graduacion'] }}</span>
                                        <div class="text-right">
                                            <span id="cart-item-subtotal-{{ $item['variante_sku_id'] }}" class="font-tabular-data font-bold text-on-surface text-sm block">${{ number_format($item['subtotal'], 0) }} MXN</span>
                                            <span class="text-[10px] font-label-code text-on-surface-variant block">
                                                Precio actual del catálogo
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('carrito.remover', $item['variante_sku_id']) }}" class="absolute top-2 right-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-outline hover:text-error transition" title="Quitar del carrito y devolver al catálogo">
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Divisor visual cuando conviven ambos tipos de productos en el carrito --}}
            @if ($tieneAmbosTipos)
                <div class="relative py-1 flex items-center" aria-hidden="true">
                    <div class="flex-grow border-t-2 border-dashed border-purple-200 dark:border-purple-500/30"></div>
                    <span class="shrink-0 mx-3 px-2.5 py-0.5 rounded-full bg-surface-container dark:bg-[#221834] text-[10px] font-label-code uppercase tracking-wider text-secondary font-bold border border-purple-200/80 dark:border-purple-500/30">
                        Catálogo Regular &middot; Sin Expiración de 5 Min
                    </span>
                    <div class="flex-grow border-t-2 border-dashed border-purple-200 dark:border-purple-500/30"></div>
                </div>
            @endif

            {{-- =========================================================================
                 SECCIÓN 2: PRODUCTOS REGULARES / COMUNES (VIGENCIA DE 7 DÍAS)
                 ========================================================================= --}}
            @if ($tieneProductosRegulares)
                <section id="cart-section-regular-products" class="space-y-3" aria-label="Productos Regulares (Vigencia de 7 días)">
                    <div class="p-3 rounded-xl bg-emerald-50/80 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="material-symbols-outlined text-emerald-700 dark:text-emerald-300 text-[18px] shrink-0">inventory_2</span>
                            <div class="min-w-0">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-on-surface font-label-code truncate">
                                    Productos Regulares (Vigencia de 7 días)
                                </h3>
                                <p class="text-[10px] text-on-surface-variant">
                                    Catálogo general &middot; Permanecen activos por 1 semana
                                </p>
                            </div>
                        </div>
                        <span class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white dark:bg-[#140d20] border border-emerald-300 dark:border-emerald-500/40 font-label-code font-bold text-[10px] text-emerald-800 dark:text-emerald-300 uppercase shadow-2xs">
                            <span class="material-symbols-outlined text-[13px]">event_available</span>
                            <span>Guardado por 7 días</span>
                        </span>
                    </div>

                    <div class="space-y-3">
                        @foreach ($itemsRegulares as $item)
                            @php
                                $iconoItem = match ($item['tipo']) {
                                    'libro' => 'menu_book',
                                    'disco' => 'album',
                                    default => 'history_edu',
                                };
                            @endphp
                            <div class="p-3 rounded-xl bg-surface-container-low dark:bg-[#1b132a] border border-purple-200/60 dark:border-purple-500/30 flex gap-3 relative group">
                                <div class="w-16 h-16 rounded-lg bg-surface-container dark:bg-[#140d20] flex items-center justify-center shrink-0 border border-purple-200/50 dark:border-purple-500/30 overflow-hidden">
                                    @if (! empty($item['imagen']))
                                        <img src="{{ asset('/' . $item['imagen']) }}" alt="{{ $item['nombre'] }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="material-symbols-outlined text-secondary text-[28px]">{{ $iconoItem }}</span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0 pr-6">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="font-label-sm text-primary font-bold uppercase">SKU: #{{ $item['sku'] }}</span>
                                        <span class="bg-emerald-50 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/40 text-[9px] font-label-code font-bold uppercase px-1.5 py-0.5 rounded">
                                            Guardado por 7 días
                                        </span>
                                        @if (! empty($item['es_preventa']))
                                            <span class="bg-amber-100 text-amber-800 border border-amber-300 text-[9px] font-label-code font-bold uppercase px-1.5 py-0.5 rounded">Preventa</span>
                                        @endif
                                    </div>
                                    <h4 class="font-bold text-on-surface text-sm truncate font-quicksand mt-0.5">{{ $item['nombre'] }}</h4>
                                    <p class="font-title-editorial text-xs text-on-surface-variant italic truncate">{{ $item['subtitulo'] }}</p>

                                    <!-- Selector Interactivo de Cantidad y Disponibilidad -->
                                    <div class="mt-2 flex flex-wrap items-center gap-2">
                                        <div class="inline-flex items-center gap-1.5 text-xs text-on-surface font-medium">
                                            <span class="text-on-surface-variant text-[11px]">Cantidad:</span>
                                            <div class="relative inline-flex items-center">
                                                <select
                                                    class="cart-qty-select appearance-none bg-white dark:bg-[#140d20] text-on-surface dark:text-stone-100 border border-purple-200 dark:border-purple-500/40 rounded-lg pl-2 pr-6 py-0.5 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-primary cursor-pointer transition shadow-2xs"
                                                    data-variante-id="{{ $item['variante_sku_id'] }}"
                                                    data-update-url="{{ route('carrito.actualizar', $item['variante_sku_id']) }}"
                                                    data-current-val="{{ $item['cantidad'] }}"
                                                    aria-label="Cantidad para {{ $item['nombre'] }}"
                                                >
                                                    @for ($i = 1; $i <= ($item['max_disponible'] ?? 10); $i++)
                                                        <option value="{{ $i }}" {{ $item['cantidad'] === $i ? 'selected' : '' }} class="bg-white dark:bg-[#171024] text-on-surface dark:text-stone-100">
                                                            {{ $i }} {{ $i === 1 ? 'unidad' : 'unidades' }}
                                                        </option>
                                                    @endfor
                                                </select>
                                                <span class="material-symbols-outlined text-[14px] text-primary absolute right-1 pointer-events-none">expand_more</span>
                                            </div>
                                            <span id="cart-item-disponibles-{{ $item['variante_sku_id'] }}" class="text-[11px] text-on-surface-variant/80 dark:text-purple-200/70 font-label-code">
                                                @if (($item['disponibles_adicionales'] ?? 0) > 0)
                                                    (+{{ $item['disponibles_adicionales'] }} disponibles)
                                                @endif
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between mt-2 pt-1 border-t border-purple-200/40 dark:border-purple-500/20">
                                        <span class="font-label-code text-[10px] text-emerald-700 bg-emerald-50 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/40 px-2 py-0.5 rounded border border-emerald-200 truncate max-w-[140px]">{{ $item['graduacion'] }}</span>
                                        <div class="text-right">
                                            <span id="cart-item-subtotal-{{ $item['variante_sku_id'] }}" class="font-tabular-data font-bold text-on-surface text-sm block">${{ number_format($item['subtotal'], 0) }} MXN</span>
                                            <span class="text-[10px] font-label-code text-on-surface-variant block">
                                                ${{ number_format($item['precio'], 0) }} MXN c/u &middot; Precio congelado
                                                @if (! empty($item['cambio_precio_catalogo']))
                                                    <span class="line-through text-outline ml-0.5">${{ number_format($item['precio_catalogo_actual'], 0) }}</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('carrito.remover', $item['variante_sku_id']) }}" class="absolute top-2 right-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-outline hover:text-error transition" title="Liberar de reserva y devolver a bóveda">
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        @endif
    </div>

    <!-- Pie del Cajón: Subtotales y Total General Unificado -->
    <div class="p-space-lg border-t border-purple-200/70 dark:border-purple-500/30 bg-surface-container-lowest dark:bg-[#140d20] space-y-3">
        <div class="space-y-1.5 text-xs">
            @if ($tieneAmbosTipos)
                <div class="flex justify-between text-on-surface-variant">
                    <span>Subtotal Piezas Únicas (5 min):</span>
                    <span id="cart-drawer-subtotal-unicas" class="font-tabular-data font-medium text-on-surface">${{ number_format($carritoDrawer['subtotal_piezas_unicas'], 2) }} MXN</span>
                </div>
                <div class="flex justify-between text-on-surface-variant">
                    <span>Subtotal Productos Regulares (7 días):</span>
                    <span id="cart-drawer-subtotal-regulares" class="font-tabular-data font-medium text-on-surface">${{ number_format($carritoDrawer['subtotal_regulares'], 2) }} MXN</span>
                </div>
            @endif
            <div class="flex justify-between text-on-surface-variant">
                <span>Subtotal de Productos:</span>
                <span id="cart-drawer-subtotal" class="font-tabular-data font-medium text-on-surface">${{ number_format($carritoDrawer['subtotal'], 2) }} MXN</span>
            </div>
            <div class="flex justify-between text-on-surface-variant">
                <span>Empaque Protegido &amp; Seguro:</span>
                <span class="font-tabular-data text-emerald-700 font-semibold">Envío Gratis</span>
            </div>
            <div class="flex justify-between font-bold text-sm text-on-surface pt-2 border-t border-purple-200/50 dark:border-purple-500/30">
                <span class="font-quicksand">Total a Pagar:</span>
                <span id="cart-drawer-total" class="font-tabular-data text-primary font-bold text-base">${{ number_format($carritoDrawer['total'], 2) }} MXN</span>
            </div>
        </div>

        @auth
            <a href="{{ route('checkout.index') }}" class="btn-glass-purple w-full font-label-md py-3.5 rounded-xl flex items-center justify-center gap-2 shadow-md">
                <span class="material-symbols-outlined text-[18px]">lock</span>
                <span>Proceder al Pago Seguro</span>
            </a>
        @else
            <button
                type="button"
                id="btn-proceder-pasarela-guest"
                class="btn-glass-purple w-full font-label-md py-3.5 rounded-xl flex items-center justify-center gap-2 shadow-md cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">lock</span>
                <span>Proceder al Pago Seguro</span>
            </button>
        @endauth

        <p class="text-[10px] text-center text-outline font-label-code">
            @if ($tieneAmbosTipos)
                Las Piezas Únicas se reservan por 5 minutos; los Productos Regulares se guardan por 7 días.
            @elseif ($tienePiezasUnicas)
                Si no completas tu compra en 5 minutos, las piezas únicas regresan automáticamente al catálogo.
            @else
                Tus productos regulares se conservan guardados en tu carrito durante 7 días.
            @endif
        </p>
    </div>

</aside>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Función global para actualizar inmediatamente el badge contador del carrito en la barra superior
        window.updateArchivumCartBadge = function (count) {
            const parsedCount = Math.max(0, parseInt(count || 0, 10) || 0);
            const cartCounterBadge = document.getElementById('cart-counter-badge');
            if (cartCounterBadge) {
                cartCounterBadge.textContent = parsedCount;
                cartCounterBadge.setAttribute('data-cart-count', String(parsedCount));
                if (parsedCount > 0) {
                    cartCounterBadge.classList.remove('hidden');
                } else {
                    cartCounterBadge.classList.add('hidden');
                }
            }
            const piecesCountEl = document.getElementById('cart-drawer-pieces-count');
            if (piecesCountEl) {
                piecesCountEl.textContent = `${parsedCount} ${parsedCount === 1 ? 'producto en carrito' : 'productos reservados'}`;
            }
        };

        window.addEventListener('archivum:cart-updated', function (ev) {
            if (ev && ev.detail && ev.detail.cantidad_total !== undefined) {
                window.updateArchivumCartBadge(ev.detail.cantidad_total);
            }
        });

        @if (session('abrir_carrito'))
            const openBtn = document.getElementById('open-cart-btn');
            if (openBtn) {
                setTimeout(() => openBtn.click(), 120);
            }
        @endif

        // -------------------------------------------------------------
        // Control de Acceso: Proceder a Pasarela Notariada para Invitados
        // -------------------------------------------------------------
        const btnGuest = document.getElementById('btn-proceder-pasarela-guest');
        if (btnGuest) {
            btnGuest.addEventListener('click', function (e) {
                e.preventDefault();

                const closeCartBtn = document.getElementById('close-cart-btn');
                if (closeCartBtn) {
                    closeCartBtn.click();
                }

                if (typeof window.openLoginDrawer === 'function') {
                    setTimeout(function () {
                        window.openLoginDrawer('login', '{{ route('checkout.index') }}');
                    }, 150);
                } else {
                    window.location.href = '{{ route('login', ['intended' => 'checkout']) }}';
                }
            });
        }

        // -------------------------------------------------------------
        // Selector Interactivo de Cantidad con Recálculo Inmediato
        // -------------------------------------------------------------
        document.querySelectorAll('.cart-qty-select').forEach(function (selectEl) {
            selectEl.addEventListener('change', async function () {
                const varianteId = this.dataset.varianteId;
                const updateUrl = this.dataset.updateUrl;
                const newQty = parseInt(this.value, 10);
                const originalValue = this.getAttribute('data-current-val') || this.value;
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                this.disabled = true;
                this.classList.add('opacity-50');

                try {
                    const response = await fetch(updateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-HTTP-Method-Override': 'PATCH',
                        },
                        body: JSON.stringify({
                            _method: 'PATCH',
                            cantidad: newQty,
                        }),
                    });

                    const data = await response.json();

                    if (!response.ok || data.status !== 'success') {
                        alert(data.message || 'No fue posible actualizar la cantidad en la bóveda.');
                        this.value = originalValue;
                        return;
                    }

                    this.setAttribute('data-current-val', newQty);

                    const carrito = data.carrito;
                    if (carrito) {
                        const itemActualizado = carrito.items.find(i => String(i.variante_sku_id) === String(varianteId));
                        if (itemActualizado) {
                            const subtotalLineaEl = document.getElementById(`cart-item-subtotal-${varianteId}`);
                            if (subtotalLineaEl) {
                                subtotalLineaEl.textContent = `$${Number(itemActualizado.subtotal).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 })} MXN`;
                            }
                            const disponiblesEl = document.getElementById(`cart-item-disponibles-${varianteId}`);
                            if (disponiblesEl) {
                                disponiblesEl.textContent = (itemActualizado.disponibles_adicionales > 0)
                                    ? `(+${itemActualizado.disponibles_adicionales} disponibles)`
                                    : '';
                            }
                        }

                        const subtotalRegularesEl = document.getElementById('cart-drawer-subtotal-regulares');
                        if (subtotalRegularesEl && carrito.subtotal_regulares !== undefined) {
                            subtotalRegularesEl.textContent = `$${Number(carrito.subtotal_regulares).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} MXN`;
                        }

                        const subtotalUnicasEl = document.getElementById('cart-drawer-subtotal-unicas');
                        if (subtotalUnicasEl && carrito.subtotal_piezas_unicas !== undefined) {
                            subtotalUnicasEl.textContent = `$${Number(carrito.subtotal_piezas_unicas).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} MXN`;
                        }

                        const subtotalEl = document.getElementById('cart-drawer-subtotal');
                        if (subtotalEl) {
                            subtotalEl.textContent = `$${Number(carrito.subtotal).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} MXN`;
                        }
                        const totalEl = document.getElementById('cart-drawer-total');
                        if (totalEl) {
                            totalEl.textContent = `$${Number(carrito.total).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} MXN`;
                        }

                        if (typeof window.updateArchivumCartBadge === 'function') {
                            window.updateArchivumCartBadge(carrito.cantidad_total || 0);
                        } else {
                            const piecesCountEl = document.getElementById('cart-drawer-pieces-count');
                            if (piecesCountEl) {
                                piecesCountEl.textContent = `${carrito.cantidad_total} ${carrito.cantidad_total === 1 ? 'producto en carrito' : 'productos reservados'}`;
                            }

                            const cartCounterBadge = document.getElementById('cart-counter-badge');
                            if (cartCounterBadge) {
                                const cantidadNum = parseInt(carrito.cantidad_total || 0, 10);
                                cartCounterBadge.textContent = cantidadNum;
                                if (cantidadNum > 0) {
                                    cartCounterBadge.classList.remove('hidden');
                                } else {
                                    cartCounterBadge.classList.add('hidden');
                                }
                            }
                        }

                        const checkoutSubtotalEl = document.getElementById('checkout-subtotal');
                        if (checkoutSubtotalEl) {
                            checkoutSubtotalEl.textContent = `$${Number(carrito.subtotal).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} MXN`;
                        }
                        const checkoutTotalEl = document.getElementById('checkout-total');
                        if (checkoutTotalEl) {
                            checkoutTotalEl.textContent = `$${Number(carrito.total).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} MXN`;
                        }
                    }
                } catch (err) {
                    console.error('Error al actualizar la cantidad en bóveda:', err);
                    this.value = originalValue;
                } finally {
                    this.disabled = false;
                    this.classList.remove('opacity-50');
                }
            });
        });

        const timerEl = document.getElementById('reservation-timer');
        if (!timerEl || timerEl.dataset.active !== '1') {
            return;
        }

        const expireUrl = timerEl.dataset.expireUrl;
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
        let remainingSeconds = parseInt(timerEl.dataset.seconds || '300', 10);
        let expiringInProgress = false;

        function formatCountdown(sec) {
            const safeSec = Math.max(0, sec);
            const mins = Math.floor(safeSec / 60);
            const secs = safeSec % 60;
            return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0') + ' min';
        }

        function updateTimerDisplay() {
            const text = formatCountdown(remainingSeconds);
            timerEl.textContent = text;

            document.querySelectorAll('.unique-timer-readout').forEach(function (el) {
                el.textContent = text;
            });

            const checkoutTimerEl = document.getElementById('checkout-reservation-timer');
            if (checkoutTimerEl) {
                checkoutTimerEl.textContent = text;
            }

            if (remainingSeconds <= 60) {
                timerEl.classList.remove('text-primary');
                timerEl.classList.add('text-red-600', 'border-red-300', 'bg-red-50');
            }
        }

        async function triggerExpirationRelease() {
            if (expiringInProgress) {
                return;
            }
            expiringInProgress = true;
            timerEl.textContent = '00:00 min';

            try {
                await fetch(expireUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ reason: 'timeout_5_min' }),
                });
            } catch (e) {
                // La liberación también se ejecuta automáticamente en el servidor al recargar
            }

            window.location.reload();
        }

        updateTimerDisplay();

        const intervalId = setInterval(function () {
            remainingSeconds -= 1;
            updateTimerDisplay();

            if (remainingSeconds <= 0) {
                clearInterval(intervalId);
                triggerExpirationRelease();
            }
        }, 1000);
    });
</script>
