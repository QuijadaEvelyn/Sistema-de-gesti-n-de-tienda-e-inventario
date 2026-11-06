<x-layouts.public title="ARCHIVUM · Pasarela de Pago Notariada (Modo Simulación)">

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-purple-200/80 pb-5">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-label-code uppercase tracking-wider">
                    <span class="material-symbols-outlined text-[14px]">verified_user</span>
                    <span>Pasarela de Prueba / Simulación Activa</span>
                </span>
                @if (! empty($carrito['items']))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-300 text-amber-900 text-xs font-label-code font-bold uppercase tracking-wider">
                        <span class="material-symbols-outlined text-[14px] text-amber-700 animate-pulse">timer</span>
                        <span>Reserva Expira en:</span>
                        <span id="checkout-reservation-timer" class="font-tabular-data">{{ $carrito['tiempo_formateado'] ?? '05:00 min' }}</span>
                    </span>
                @endif
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-on-surface font-quicksand">Checkout &amp; Formalización de Adquisición</h1>
            <p class="text-xs text-on-surface-variant mt-1">
                En modo simulación puedes ingresar cualquier número de tarjeta de crédito para completar la compra y probar el rastreo de envío.
            </p>
        </div>
        <a href="{{ route('public.home') }}" class="text-xs font-label-md text-primary hover:underline flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            <span>Seguir Explorando el Catálogo</span>
        </a>
    </div>

    @if (empty($carrito['items']))
        <div class="bg-white rounded-2xl border border-purple-200/80 p-12 text-center space-y-4">
            <span class="material-symbols-outlined text-[48px] text-outline">shopping_bag</span>
            <h2 class="text-xl font-bold text-on-surface font-quicksand">Tu Bóveda de Adquisición está vacía</h2>
            <p class="text-sm text-on-surface-variant max-w-md mx-auto">
                Agrega al menos una pieza desde cualquiera de nuestras salas curatoriales para proceder a la pasarela de pago simulada.
            </p>
            <div class="flex flex-wrap justify-center gap-3 pt-2">
                <a href="{{ route('public.libros') }}" class="px-5 py-2.5 rounded-xl bg-primary-container text-white text-xs font-bold uppercase tracking-wider">Ver Libros Raros</a>
                <a href="{{ route('public.vinilos') }}" class="px-5 py-2.5 rounded-xl border border-purple-200 bg-white text-primary text-xs font-bold uppercase tracking-wider">Ver Vinilos Históricos</a>
                <a href="{{ route('public.memorabilia') }}" class="px-5 py-2.5 rounded-xl border border-purple-200 bg-white text-primary text-xs font-bold uppercase tracking-wider">Ver Memorabilia</a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Formulario de Entrega y Pasarela de Tarjeta Simulada -->
            <div class="lg:col-span-7">
                <form method="POST" action="{{ route('checkout.pagar') }}" class="bg-white rounded-2xl border border-purple-200/80 p-6 sm:p-8 shadow-xs space-y-6">
                    @csrf

                    @if ($errors->any())
                        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-900 text-xs space-y-1">
                            @foreach ($errors->all() as $error)
                                <p>&bull; {{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <!-- 1. Datos del Coleccionista y Destino de Custodia -->
                    <div class="space-y-4">
                        <h2 class="text-base font-bold text-on-surface font-quicksand flex items-center gap-2 border-b border-purple-100 pb-2">
                            <span class="material-symbols-outlined text-primary text-[20px]">local_shipping</span>
                            <span>1. Datos del Coleccionista &amp; Dirección de Entrega</span>
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-label-code uppercase text-on-surface mb-1">Nombre Completo</label>
                                <input type="text" name="nombre_completo" value="{{ old('nombre_completo', $usuario?->nombre ?? 'Elena Ramírez') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-label-code uppercase text-on-surface mb-1">Correo Electrónico</label>
                                <input type="email" name="email" value="{{ old('email', $usuario?->email ?? 'elena.ramirez@gmail.com') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-label-code uppercase text-on-surface mb-1">Calle y Número</label>
                                <input type="text" name="calle" value="{{ old('calle', 'Av. Álvaro Obregón 185, Col. Roma Norte') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-label-code uppercase text-on-surface mb-1">Teléfono</label>
                                <input type="text" name="telefono" value="{{ old('telefono', '5512345678') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-label-code uppercase text-on-surface mb-1">Ciudad / Alcaldía</label>
                                <input type="text" name="ciudad" value="{{ old('ciudad', 'Cuauhtémoc') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-label-code uppercase text-on-surface mb-1">Estado</label>
                                <input type="text" name="estado" value="{{ old('estado', 'Ciudad de México') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-label-code uppercase text-on-surface mb-1">Código Postal</label>
                                <input type="text" name="codigo_postal" value="{{ old('codigo_postal', '06700') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm" />
                            </div>
                        </div>
                    </div>

                    <!-- 2. Pasarela de Pago Simulada (Acepta cualquier tarjeta) -->
                    <div class="space-y-4 pt-2">
                        <div class="flex items-center justify-between border-b border-purple-100 pb-2">
                            <h2 class="text-base font-bold text-on-surface font-quicksand flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-[20px]">credit_card</span>
                                <span>2. Pasarela de Pago con Tarjeta (Modo Simulación)</span>
                            </h2>
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-label-code uppercase">
                                Acepta cualquier numeración
                            </span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-purple-50/70 border border-purple-200/70 text-xs text-on-surface-variant flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-primary text-[18px]">science</span>
                            <span>
                                <strong>Modo de Prueba Activo:</strong> Ingresa cualquier número de tarjeta de crédito o débito (por ejemplo <code class="font-label-code text-primary">4532 8899 1122 4242</code>) para autorizar la compra instantáneamente.
                            </span>
                        </div>

                        <div>
                            <label class="block text-xs font-label-code uppercase text-on-surface mb-1">Nombre del Titular en la Tarjeta</label>
                            <input type="text" name="nombre_titular" value="{{ old('nombre_titular', $usuario?->nombre ?? 'ELENA RAMIREZ') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm uppercase" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                            <div class="sm:col-span-6">
                                <label class="block text-xs font-label-code uppercase text-on-surface mb-1">Número de Tarjeta (Cualquier número)</label>
                                <input type="text" name="numero_tarjeta" value="{{ old('numero_tarjeta', '4532 8899 1122 4242') }}" required placeholder="XXXX XXXX XXXX XXXX" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm font-label-code" />
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-xs font-label-code uppercase text-on-surface mb-1">Vencimiento</label>
                                <input type="text" name="expiracion" value="{{ old('expiracion', '09/29') }}" required placeholder="MM/AA" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm font-label-code" />
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-xs font-label-code uppercase text-on-surface mb-1">CVV</label>
                                <input type="text" name="cvv" value="{{ old('cvv', '842') }}" required placeholder="123" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-200 text-sm font-label-code" />
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 px-6 rounded-xl bg-primary-container hover:bg-primary text-white font-bold uppercase tracking-wider text-xs transition shadow-md flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">verified</span>
                        <span>Confirmar y Procesar Pago Simulado (${{ number_format($carrito['total'], 2) }} MXN)</span>
                    </button>
                </form>
            </div>

            <!-- Resumen de Lotes en Custodia -->
            <div class="lg:col-span-5 space-y-4">
                <div class="bg-white rounded-2xl border border-purple-200/80 p-6 shadow-xs space-y-4">
                    <h2 class="text-base font-bold text-on-surface font-quicksand border-b border-purple-100 pb-3">
                        Resumen de Lotes a Adquirir ({{ $carrito['cantidad_total'] }})
                    </h2>

                    <div class="divide-y divide-purple-100">
                        @foreach ($carrito['items'] as $item)
                            <div class="py-3 flex items-start justify-between gap-3">
                                <div>
                                    <span class="text-[10px] font-label-code text-primary font-bold">SKU: #{{ $item['sku'] }}</span>
                                    <h3 class="text-sm font-bold text-on-surface font-quicksand">{{ $item['nombre'] }}</h3>
                                    <p class="text-xs text-on-surface-variant">{{ $item['subtitulo'] }} &middot; Cantidad: {{ $item['cantidad'] }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="font-tabular-data font-bold text-sm text-on-surface">${{ number_format($item['subtotal'], 2) }}</span>
                                    <form method="POST" action="{{ route('carrito.remover', $item['variante_sku_id']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[11px] text-error hover:underline">Quitar</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-3 border-t border-purple-200/70 space-y-2 text-xs">
                        <div class="flex justify-between text-on-surface-variant">
                            <span>Subtotal Tasado:</span>
                            <span class="font-tabular-data">${{ number_format($carrito['subtotal'], 2) }} MXN</span>
                        </div>
                        <div class="flex justify-between text-on-surface-variant">
                            <span>Custodia Climatizada &amp; Embalaje Antiácido:</span>
                            <span class="text-emerald-700 font-semibold">Cortesía de Bóveda</span>
                        </div>
                        <div class="flex justify-between text-base font-bold text-on-surface pt-2 border-t border-purple-100">
                            <span>Total a Pagar:</span>
                            <span class="font-tabular-data text-primary">${{ number_format($carrito['total'], 2) }} MXN</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endif

</x-layouts.public>

