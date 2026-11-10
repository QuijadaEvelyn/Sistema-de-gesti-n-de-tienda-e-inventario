@php
    $carritoDrawer = \App\Http\Controllers\CheckoutSimuladoController::obtenerEstadoCarrito();
    $tieneItemsActivos = $carritoDrawer['cantidad_total'] > 0;
@endphp

<!-- Overlay Translúcido de Bóveda Nocturna -->
<div id="drawer-overlay" class="fixed inset-0 bg-[#07040d]/85 backdrop-blur-sm z-50 transition-opacity duration-300 opacity-0 pointer-events-none" aria-hidden="true"></div>

<!-- Cajón Desplegable del Carrito (Cart Drawer) -->
<aside id="cart-drawer" class="fixed top-0 right-0 h-full w-full max-w-md bg-surface-container-lowest dark:bg-[#171024] border-l border-purple-200/80 dark:border-purple-500/30 z-50 shadow-2xl flex flex-col justify-between transform translate-x-full transition-transform duration-300 ease-out" role="dialog" aria-modal="true" aria-label="Bóveda de Adquisiciones">

    <!-- Encabezado del Cajón -->
    <div class="p-space-lg border-b border-purple-200/70 dark:border-purple-500/30 bg-surface-container-low/50 dark:bg-[#1b132a]">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[24px]">lock</span>
                <div>
                    <h2 class="font-headline-sm text-base text-on-surface font-bold">Bóveda de Adquisición</h2>
                    <span id="cart-drawer-pieces-count" class="font-label-code text-secondary text-[11px] uppercase tracking-wider">
                        {{ $carritoDrawer['cantidad_total'] }} {{ $carritoDrawer['cantidad_total'] === 1 ? 'pieza en custodia' : 'piezas en custodia temporal' }}
                    </span>
                </div>
            </div>
            <button type="button" id="close-cart-btn" class="p-1.5 rounded-lg text-outline hover:text-on-surface hover:bg-surface-container dark:hover:bg-[#221834] transition" aria-label="Cerrar Bóveda">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Temporizador de Expiración de Reserva (Countdown Estricto de 5 Minutos) -->
        <div
            id="reservation-timer-box"
            class="mt-3 {{ $tieneItemsActivos ? 'bg-primary-container/10 border-primary/20' : 'bg-surface-container dark:bg-[#140d20] border-purple-200/60 dark:border-purple-500/30 opacity-75' }} border rounded-lg p-2.5 flex items-center justify-between text-xs transition-colors"
        >
            <div class="flex items-center gap-2 text-primary font-medium">
                <span class="material-symbols-outlined text-[16px] {{ $tieneItemsActivos ? 'animate-pulse' : '' }}">timer</span>
                <span>Reserva Temporal (5 min):</span>
            </div>
            <span
                id="reservation-timer"
                data-active="{{ $tieneItemsActivos ? '1' : '0' }}"
                data-seconds="{{ $carritoDrawer['segundos_restantes'] }}"
                data-expires-ms="{{ $carritoDrawer['expira_en_ms'] }}"
                data-expire-url="{{ route('carrito.expirar') }}"
                class="font-tabular-data font-bold text-primary bg-white dark:bg-[#140d20] px-2.5 py-0.5 rounded shadow-2xs border border-purple-200/60 dark:border-purple-500/40"
            >
                {{ $tieneItemsActivos ? $carritoDrawer['tiempo_formateado'] : '05:00 min' }}
            </span>
        </div>
    </div>

    <!-- Lista Desplazable de Artículos Reservados con Centrado Vertical en Estado Vacío -->
    <div class="flex-1 overflow-y-auto p-space-lg {{ ! $tieneItemsActivos ? 'flex flex-col justify-center items-center my-auto min-h-[320px]' : 'space-y-4' }} no-scrollbar">
        @forelse ($carritoDrawer['items'] as $item)
            @php
                $iconoItem = match ($item['tipo']) {
                    'libro' => 'menu_book',
                    'disco' => 'album',
                    default => 'history_edu',
                };
            @endphp
            <div class="p-3 rounded-xl bg-surface-container-low dark:bg-[#1b132a] border border-purple-200/60 dark:border-purple-500/30 flex gap-3 relative group">
                <div class="w-16 h-16 rounded-lg bg-surface-container dark:bg-[#140d20] flex items-center justify-center shrink-0 border border-purple-200/50 dark:border-purple-500/30">
                    <span class="material-symbols-outlined text-secondary text-[28px]">{{ $iconoItem }}</span>
                </div>
                <div class="flex-1 min-w-0 pr-6">
                    <div class="flex items-center gap-1.5">
                        <span class="font-label-sm text-primary font-bold uppercase">SKU: #{{ $item['sku'] }}</span>
                        @if (! empty($item['es_preventa']))
                            <span class="bg-amber-100 text-amber-800 border border-amber-300 text-[9px] font-label-code font-bold uppercase px-1.5 py-0.5 rounded">Preventa</span>
                        @endif
                    </div>
                    <h3 class="font-bold text-on-surface text-sm truncate font-quicksand">{{ $item['nombre'] }}</h3>
                    <p class="font-title-editorial text-xs text-on-surface-variant italic truncate">{{ $item['subtitulo'] }}</p>

                    <!-- Selector Interactivo de Cantidad y Disponibilidad -->
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        @if (! empty($item['es_pieza_unica']))
                            <div class="inline-flex items-center gap-1.5 text-xs text-on-surface font-medium bg-surface-container/60 dark:bg-[#140d20] px-2 py-0.5 rounded-lg border border-purple-200/60 dark:border-purple-500/30">
                                <span class="text-on-surface-variant text-[11px]">Cantidad:</span>
                                <span class="font-bold text-on-surface text-xs">1 unidad</span>
                                <span class="text-[10px] text-amber-600 dark:text-amber-400 font-label-code font-semibold">(Pieza Única 1/1)</span>
                            </div>
                        @else
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
                        @endif
                    </div>

                    <div class="flex items-center justify-between mt-2 pt-1 border-t border-purple-200/40 dark:border-purple-500/20">
                        <span class="font-label-code text-[10px] text-emerald-700 bg-emerald-50 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/40 px-2 py-0.5 rounded border border-emerald-200 truncate max-w-[140px]">{{ $item['graduacion'] }}</span>
                        <span id="cart-item-subtotal-{{ $item['variante_sku_id'] }}" class="font-tabular-data font-bold text-on-surface text-sm">${{ number_format($item['subtotal'], 0) }} MXN</span>
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
        @empty
            <div class="w-full my-auto py-8 text-center space-y-4 flex flex-col items-center justify-center">
                <div class="w-16 h-16 rounded-2xl bg-surface-container dark:bg-[#1b132a] mx-auto flex items-center justify-center text-outline shadow-2xs border border-transparent dark:border-purple-500/20">
                    <span class="material-symbols-outlined text-[32px] text-primary">shopping_bag</span>
                </div>
                <div class="space-y-1.5 max-w-xs mx-auto">
                    <p class="text-sm font-bold text-on-surface font-quicksand">Tu Bóveda de Adquisición está vacía</p>
                    <p class="text-xs text-on-surface-variant leading-relaxed">
                        Explora nuestras secciones de Libros Raros, Vinilos Históricos o Memorabilia y pulsa el botón de carrito para reservar una pieza.
                    </p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pie del Cajón: Subtotales y Botón de Checkout con Recálculo Inmediato -->
    <div class="p-space-lg border-t border-purple-200/70 dark:border-purple-500/30 bg-surface-container-lowest dark:bg-[#140d20] space-y-3">
        <div class="space-y-1.5 text-xs">
            <div class="flex justify-between text-on-surface-variant">
                <span>Subtotal Lote Certificado:</span>
                <span id="cart-drawer-subtotal" class="font-tabular-data font-medium text-on-surface">${{ number_format($carritoDrawer['subtotal'], 2) }} MXN</span>
            </div>
            <div class="flex justify-between text-on-surface-variant">
                <span>Embalaje Antiácido &amp; Seguro:</span>
                <span class="font-tabular-data text-emerald-700 font-semibold">Cortesía de Bóveda</span>
            </div>
            <div class="flex justify-between font-bold text-sm text-on-surface pt-2 border-t border-purple-200/50 dark:border-purple-500/30">
                <span class="font-quicksand">Total Estimado de Depósito:</span>
                <span id="cart-drawer-total" class="font-tabular-data text-primary font-bold text-base">${{ number_format($carritoDrawer['total'], 2) }} MXN</span>
            </div>
        </div>

        @auth
            <a href="{{ route('checkout.index') }}" class="w-full bg-primary-container hover:bg-primary text-white font-label-md py-3 rounded-xl flex items-center justify-center gap-2 shadow-sm transition">
                <span class="material-symbols-outlined text-[18px]">lock</span>
                <span>Proceder a Pasarela Notariada</span>
            </a>
        @else
            <button
                type="button"
                id="btn-proceder-pasarela-guest"
                class="w-full bg-primary-container hover:bg-primary text-white font-label-md py-3 rounded-xl flex items-center justify-center gap-2 shadow-sm transition cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">lock</span>
                <span>Proceder a Pasarela Notariada</span>
            </button>
        @endauth

        <p class="text-[10px] text-center text-outline font-label-code">
            Si no completas tu adquisición en 5 minutos, las piezas se liberan automáticamente a la bóveda.
        </p>
    </div>

</aside>

<script>
    document.addEventListener('DOMContentLoaded', function () {
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

                // 1. Cerrar el cajón de la bóveda
                const closeCartBtn = document.getElementById('close-cart-btn');
                if (closeCartBtn) {
                    closeCartBtn.click();
                }

                // 2. Abrir la Bóveda de Autenticación de inmediato configurando el destino de checkout
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
                        // 1. Recalcular Subtotal de la Línea y Disponibilidad
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

                        // 2. Recalcular Subtotal Lote Certificado y Total Estimado de Depósito
                        const subtotalEl = document.getElementById('cart-drawer-subtotal');
                        if (subtotalEl) {
                            subtotalEl.textContent = `$${Number(carrito.subtotal).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} MXN`;
                        }
                        const totalEl = document.getElementById('cart-drawer-total');
                        if (totalEl) {
                            totalEl.textContent = `$${Number(carrito.total).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} MXN`;
                        }

                        // 3. Actualizar Contador de Piezas en Custodia en Encabezado de Bóveda
                        const piecesCountEl = document.getElementById('cart-drawer-pieces-count');
                        if (piecesCountEl) {
                            piecesCountEl.textContent = `${carrito.cantidad_total} ${carrito.cantidad_total === 1 ? 'pieza en custodia' : 'piezas en custodia temporal'}`;
                        }

                        // 4. Actualizar Contador Global del Carrito en Cabecera
                        const cartCounterBadge = document.getElementById('cart-counter-badge');
                        if (cartCounterBadge) {
                            cartCounterBadge.textContent = carrito.cantidad_total;
                        }

                        // 5. Actualizar totales en vista de Checkout si está presente
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
