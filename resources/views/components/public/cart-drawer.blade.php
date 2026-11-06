@php
    $carritoDrawer = \App\Http\Controllers\CheckoutSimuladoController::obtenerEstadoCarrito();
    $tieneItemsActivos = $carritoDrawer['cantidad_total'] > 0;
@endphp

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
                    <span class="font-label-code text-secondary text-[11px] uppercase tracking-wider">
                        {{ $carritoDrawer['cantidad_total'] }} {{ $carritoDrawer['cantidad_total'] === 1 ? 'pieza en custodia' : 'piezas en custodia temporal' }}
                    </span>
                </div>
            </div>
            <button type="button" id="close-cart-btn" class="p-1.5 rounded-lg text-outline hover:text-on-surface hover:bg-surface-container transition" aria-label="Cerrar Bóveda">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Temporizador de Expiración de Reserva (Countdown Estricto de 5 Minutos) -->
        <div
            id="reservation-timer-box"
            class="mt-3 {{ $tieneItemsActivos ? 'bg-primary-container/10 border-primary/20' : 'bg-surface-container border-purple-200/60 opacity-75' }} border rounded-lg p-2.5 flex items-center justify-between text-xs transition-colors"
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
                class="font-tabular-data font-bold text-primary bg-white px-2.5 py-0.5 rounded shadow-2xs border border-purple-200/60"
            >
                {{ $tieneItemsActivos ? $carritoDrawer['tiempo_formateado'] : '05:00 min' }}
            </span>
        </div>
    </div>

    <!-- Lista Desplazable de Artículos Reservados -->
    <div class="flex-1 overflow-y-auto p-space-lg space-y-4 no-scrollbar">
        @forelse ($carritoDrawer['items'] as $item)
            @php
                $iconoItem = match ($item['tipo']) {
                    'libro' => 'menu_book',
                    'disco' => 'album',
                    default => 'history_edu',
                };
            @endphp
            <div class="p-3 rounded-xl bg-surface-container-low border border-purple-200/60 flex gap-3 relative group">
                <div class="w-16 h-16 rounded-lg bg-surface-container flex items-center justify-center shrink-0 border border-purple-200/50">
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
                    <p class="font-title-editorial text-xs text-on-surface-variant italic">{{ $item['subtitulo'] }} &middot; Cant: {{ $item['cantidad'] }}</p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="font-label-code text-[10px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 truncate max-w-[140px]">{{ $item['graduacion'] }}</span>
                        <span class="font-tabular-data font-bold text-on-surface text-sm">${{ number_format($item['subtotal'], 0) }} MXN</span>
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
            <div class="py-12 text-center space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-surface-container mx-auto flex items-center justify-center text-outline">
                    <span class="material-symbols-outlined text-[30px]">shopping_bag</span>
                </div>
                <p class="text-sm font-bold text-on-surface font-quicksand">Tu Bóveda de Adquisición está vacía</p>
                <p class="text-xs text-on-surface-variant max-w-xs mx-auto">
                    Explora nuestras secciones de Libros Raros, Vinilos Históricos o Memorabilia y pulsa «Reservar» para apartar temporalmente una pieza por 5 minutos.
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pie del Cajón: Subtotales y Botón de Checkout -->
    <div class="p-space-lg border-t border-purple-200/70 bg-surface-container-lowest space-y-3">
        <div class="space-y-1.5 text-xs">
            <div class="flex justify-between text-on-surface-variant">
                <span>Subtotal Lote Certificado:</span>
                <span class="font-tabular-data font-medium text-on-surface">${{ number_format($carritoDrawer['subtotal'], 2) }} MXN</span>
            </div>
            <div class="flex justify-between text-on-surface-variant">
                <span>Embalaje Antiácido &amp; Seguro:</span>
                <span class="font-tabular-data text-emerald-700 font-semibold">Cortesía de Bóveda</span>
            </div>
            <div class="flex justify-between font-bold text-sm text-on-surface pt-2 border-t border-purple-200/50">
                <span class="font-quicksand">Total Estimado de Depósito:</span>
                <span class="font-tabular-data text-primary font-bold text-base">${{ number_format($carritoDrawer['total'], 2) }} MXN</span>
            </div>
        </div>

        <a href="{{ route('checkout.index') }}" class="w-full bg-primary-container hover:bg-primary text-white font-label-md py-3 rounded-xl flex items-center justify-center gap-2 shadow-sm transition">
            <span class="material-symbols-outlined text-[18px]">lock</span>
            <span>Proceder a Pasarela Notariada</span>
        </a>

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
