@props([
    'producto',
])

@php
    /** @var \App\Models\Producto $producto */
    $variante = $producto->variantesSku->first();
    $tipo = $producto->catalogo?->tipo ?? 'coleccionable';
    $sku = $variante?->sku ?? 'ARC-SKU-'.$producto->id;
    $graduacionRaw = $variante?->descripcion ?? 'MINT (M)';
    $partesGraduacion = array_map('trim', explode('·', $graduacionRaw));
    $graduacionCorta = $partesGraduacion[0] ?? 'MINT (M)';
    $etiquetaSuperior = $partesGraduacion[1] ?? match ($tipo) {
        'libro' => 'PRIMERA EDICIÓN',
        'disco' => 'VINILO LP 180G',
        default => 'MEMORABILIA COA',
    };

    $badgeType = str_contains($graduacionCorta, 'COA')
        ? 'coa'
        : (str_contains($graduacionCorta, 'VERY FINE') ? 'rare' : 'mint');

    $iconoCategoria = match ($tipo) {
        'libro' => 'auto_stories',
        'disco' => 'album',
        default => 'history_edu',
    };

    $subtituloCuratorial = match ($tipo) {
        'libro' => ($producto->libro?->autor ?? 'Autor Histórico').' · '.($producto->libro?->editorial ?? 'Edición de Época').($producto->libro?->anio_publicacion ? ' ('.$producto->libro->anio_publicacion.')' : ''),
        'disco' => ($producto->disco?->artista ?? 'Intérprete').' · '.($producto->disco?->sello ?? 'Prensado Original').($producto->disco?->anio_lanzamiento ? ' ('.$producto->disco->anio_lanzamiento.')' : ''),
        default => ($producto->coleccionable?->marca ?? 'Archivo Histórico').' · '.($producto->coleccionable?->material ?? 'Pieza Autenticada'),
    };

    $piezaUnica = $variante
        ? \App\Http\Controllers\CheckoutSimuladoController::obtenerPiezaUnicaDeVariante((int) $variante->id)
        : null;
    $esEdicionLimitada = (bool) ($producto->coleccionable?->edicion_limitada ?? false);
    $esPiezaUnica = $piezaUnica !== null || $esEdicionLimitada;
    $sedeCustodia = $piezaUnica?->existencia?->sucursal?->nombre
        ?? $variante?->existencias?->first()?->sucursal?->nombre;

    $stockTotal = $variante ? (int) $variante->existencias->sum('cantidad') : 0;
    $estaReservadaTemporalmente = $piezaUnica !== null && $piezaUnica->estado === 'reservada';
    $estaAgotado = $stockTotal <= 0 || ($piezaUnica !== null && in_array($piezaUnica->estado, ['reservada', 'vendida'], true));
    $esPreventa = \App\Http\Controllers\CheckoutSimuladoController::esPiezaPreventa($producto, $variante);
    $promoProducto = \App\Http\Controllers\CheckoutSimuladoController::obtenerPromocionDeProducto($producto);
    $fasePromoProducto = $promoProducto?->faseActual();
    $maxUnidadesCompra = $piezaUnica !== null ? 1 : max(1, min(10, $stockTotal));

    $clasesTarjeta = $esPiezaUnica
        ? 'bg-white border-2 border-purple-600 ring-2 ring-purple-600/20 shadow-md shadow-purple-600/15 hover:border-purple-700 hover:shadow-xl hover:shadow-purple-600/25'
        : 'bg-white border border-purple-200/80 shadow-xs hover:border-primary hover:shadow-lg';

    $clasesMiniatura = $esPiezaUnica
        ? 'bg-purple-50/60 border-2 border-purple-600/80 ring-1 ring-purple-500/30 group-hover:bg-purple-100/60'
        : 'bg-surface-container-low border border-purple-200/60 group-hover:bg-purple-50';
@endphp

<article
    data-unique-piece="{{ $esPiezaUnica ? 'true' : 'false' }}"
    class="{{ $clasesTarjeta }} rounded-2xl p-5 flex flex-col justify-between transition-all duration-200 group relative"
>
    <div>
        <div class="w-full aspect-square rounded-xl {{ $clasesMiniatura }} flex items-center justify-center relative overflow-hidden mb-4 transition-colors">
            @if ($esPiezaUnica)
                <span class="absolute top-2.5 left-2.5 bg-purple-600 text-white border border-purple-400/60 text-[10px] font-label-sm font-bold uppercase px-2.5 py-0.5 rounded-md shadow-sm shadow-purple-900/30 inline-flex items-center gap-1">
                    <span class="material-symbols-outlined text-[12px]">diamond</span>
                    <span>{{ $esPreventa ? 'PREVENTA · PIEZA ÚNICA' : ($piezaUnica ? 'PIEZA ÚNICA 1/1' : 'EDICIÓN COLECCIONABLE') }}</span>
                </span>
            @else
                <span class="absolute top-2.5 left-2.5 bg-primary-container text-white text-[10px] font-label-sm font-semibold uppercase px-2.5 py-0.5 rounded shadow-2xs">
                    {{ $esPreventa ? 'PREVENTA · '.$etiquetaSuperior : 'STOCK MASIVO · '.$etiquetaSuperior }}
                </span>
            @endif

            @if ($sedeCustodia)
                <span class="absolute top-2.5 right-2.5 bg-purple-950/85 text-purple-100 text-[9px] font-label-code uppercase px-2 py-0.5 rounded">
                    {{ str_replace('Sucursal ', '', $sedeCustodia) }}
                </span>
            @endif

            @if ($estaReservadaTemporalmente)
                <span class="absolute bottom-2.5 right-2.5 bg-amber-600 text-white border border-amber-700 text-[10px] font-label-code font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full shadow-xs">
                    RESERVADA EN BÓVEDA (5 MIN)
                </span>
            @elseif ($estaAgotado)
                <span class="absolute bottom-2.5 right-2.5 bg-red-600 text-white border border-red-700 text-[10px] font-label-code font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full shadow-xs">
                    AGOTADO
                </span>
            @else
                <span class="absolute bottom-2.5 right-2.5 bg-white/90 text-emerald-700 border border-emerald-200 text-[10px] font-label-code font-semibold px-2 py-0.5 rounded-full">
                    {{ $stockTotal }} en bóveda
                </span>
            @endif
            <span class="material-symbols-outlined {{ $esPiezaUnica ? 'text-purple-600 group-hover:scale-105' : 'text-purple-300 group-hover:text-primary' }} transition-all text-[64px]">{{ $iconoCategoria }}</span>
        </div>

        <div class="space-y-1.5">
            <div class="flex items-center justify-between gap-2">
                <span class="font-label-code text-[11px] text-outline truncate">SKU: #{{ $sku }}</span>
                <x-ui.badge-grading :type="$badgeType" :text="$graduacionCorta" />
            </div>

            @if ($esPiezaUnica)
                <div class="flex flex-wrap items-center justify-between gap-1.5 text-[10px] font-label-code text-white bg-purple-600 border border-purple-700 px-2.5 py-1 rounded-lg shadow-2xs">
                    <span class="inline-flex items-center gap-1 font-bold uppercase tracking-wider">
                        <span class="material-symbols-outlined text-[13px]">verified</span>
                        <span>{{ $piezaUnica ? 'Pieza Única · Edición Coleccionable' : 'Edición Coleccionable Limitada' }}</span>
                    </span>
                    @if ($piezaUnica)
                        <span class="font-bold bg-purple-800/90 text-purple-100 px-1.5 py-0.5 rounded text-[9px]">
                            SERIE: {{ $piezaUnica->numero_serie }}
                        </span>
                    @endif
                </div>
            @else
                <div class="flex flex-wrap items-center gap-1.5 text-[10px] font-label-code text-emerald-800 bg-emerald-50/80 border border-emerald-200/70 px-2 py-0.5 rounded-md">
                    <span class="font-bold">STOCK MASIVO</span>
                    <span class="text-emerald-700">&middot; Compra múltiple habilitada</span>
                </div>
            @endif

            @if ($promoProducto)
                <div class="promo-shimmer-badge flex items-center justify-between gap-1.5 text-[10px] font-label-code px-2.5 py-1 rounded-lg {{ $fasePromoProducto === 'preventa_exclusiva' ? 'bg-amber-400 text-slate-950 font-extrabold' : 'bg-purple-700 text-white font-bold' }}">
                    <span class="inline-flex items-center gap-1 uppercase">
                        <span class="material-symbols-outlined text-[13px]">workspace_premium</span>
                        <span>{{ $fasePromoProducto === 'preventa_exclusiva' ? 'Preventa Activa VIP (5+ Compras)' : 'Edición Especial · Apertura Pública' }}</span>
                    </span>
                    <span class="font-tabular-data font-extrabold">
                        {{ $promoProducto->tipo_descuento === 'porcentaje' ? '-' . number_format((float) $promoProducto->valor, 0) . '%' : '-$' . number_format((float) $promoProducto->valor, 0) }}
                    </span>
                </div>
            @endif

            <h3 class="font-bold text-on-surface text-base group-hover:text-primary transition font-quicksand leading-snug">
                {{ $producto->nombre }}
            </h3>
            <p class="font-title-editorial text-xs text-on-surface-variant italic">
                {{ $subtituloCuratorial }}
            </p>
            <p class="text-xs text-secondary line-clamp-3 pt-1 font-normal leading-relaxed">
                {{ $producto->descripcion }}
            </p>
        </div>
    </div>

    <div class="pt-4 mt-4 border-t border-purple-100 flex items-center justify-between gap-2">
        <div>
            <span class="text-[10px] uppercase font-label-code text-outline block">Precio Tasado:</span>
            <span class="font-tabular-data font-bold text-on-surface text-base">
                ${{ number_format((float) $producto->precio, 0) }}
                <span class="text-xs font-normal text-on-surface-variant">MXN</span>
            </span>
        </div>

        @if ($variante && ! $estaAgotado)
            <form method="POST" action="{{ route('carrito.agregar') }}" class="flex items-center gap-1.5">
                @csrf
                <input type="hidden" name="variante_sku_id" value="{{ $variante->id }}" />
                @if (! $esPiezaUnica && $maxUnidadesCompra > 1)
                    <select
                        name="cantidad"
                        aria-label="Cantidad de unidades a adquirir"
                        class="px-2 py-2 rounded-xl border border-purple-200 bg-surface-container-lowest text-xs font-tabular-data font-bold text-on-surface focus:outline-none focus:border-primary"
                    >
                        @for ($u = 1; $u <= $maxUnidadesCompra; $u++)
                            <option value="{{ $u }}">{{ $u }} u.</option>
                        @endfor
                    </select>
                @else
                    <input type="hidden" name="cantidad" value="1" />
                @endif
                <button
                    type="submit"
                    class="px-3.5 py-2.5 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-label-md font-bold uppercase tracking-wider flex items-center gap-1.5 transition shadow-2xs cursor-pointer"
                    title="Reservar en Bóveda de Adquisición"
                >
                    <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                    <span>Reservar</span>
                </button>
            </form>
        @else
            <button
                type="button"
                disabled
                aria-disabled="true"
                class="px-3.5 py-2.5 rounded-xl bg-slate-200 text-slate-500 border border-slate-300 text-xs font-label-md font-bold uppercase tracking-wider flex items-center gap-1.5 opacity-50 cursor-not-allowed pointer-events-none select-none"
                title="{{ $estaReservadaTemporalmente ? 'Pieza única reservada temporalmente por otro coleccionista (5 min)' : 'Pieza agotada en bóveda' }}"
            >
                <span class="material-symbols-outlined text-[18px]">{{ $estaReservadaTemporalmente ? 'lock_clock' : 'block' }}</span>
                <span>{{ $estaReservadaTemporalmente ? 'En Reserva' : 'Reservar' }}</span>
            </button>
        @endif
    </div>
</article>
