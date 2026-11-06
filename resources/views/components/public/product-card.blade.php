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

    $stockTotal = $variante ? (int) $variante->existencias->sum('cantidad') : 1;
@endphp

<article class="bg-white border border-purple-200/80 rounded-2xl p-5 flex flex-col justify-between hover:border-primary hover:shadow-lg transition-all duration-200 group shadow-xs">
    <div>
        <div class="w-full aspect-square rounded-xl bg-surface-container-low border border-purple-200/60 flex items-center justify-center relative overflow-hidden mb-4 group-hover:bg-purple-50 transition-colors">
            <span class="absolute top-2.5 left-2.5 bg-primary-container text-white text-[10px] font-label-sm font-semibold uppercase px-2.5 py-0.5 rounded shadow-2xs">
                {{ $etiquetaSuperior }}
            </span>
            <span class="absolute bottom-2.5 right-2.5 bg-white/90 text-emerald-700 border border-emerald-200 text-[10px] font-label-code font-semibold px-2 py-0.5 rounded-full">
                {{ $stockTotal }} en bóveda
            </span>
            <span class="material-symbols-outlined text-purple-300 group-hover:text-primary transition-colors text-[64px]">{{ $iconoCategoria }}</span>
        </div>

        <div class="space-y-1.5">
            <div class="flex items-center justify-between gap-2">
                <span class="font-label-code text-[11px] text-outline truncate">SKU: #{{ $sku }}</span>
                <x-ui.badge-grading :type="$badgeType" :text="$graduacionCorta" />
            </div>
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

    <div class="pt-4 mt-4 border-t border-purple-100 flex items-center justify-between gap-3">
        <div>
            <span class="text-[10px] uppercase font-label-code text-outline block">Precio Tasado:</span>
            <span class="font-tabular-data font-bold text-on-surface text-base">
                ${{ number_format((float) $producto->precio, 0) }}
                <span class="text-xs font-normal text-on-surface-variant">MXN</span>
            </span>
        </div>

        @if ($variante)
            <form method="POST" action="{{ route('carrito.agregar') }}">
                @csrf
                <input type="hidden" name="variante_sku_id" value="{{ $variante->id }}" />
                <input type="hidden" name="cantidad" value="1" />
                <button
                    type="submit"
                    class="px-3.5 py-2.5 rounded-xl bg-primary-container hover:bg-primary text-white text-xs font-label-md font-bold uppercase tracking-wider flex items-center gap-1.5 transition shadow-2xs"
                    title="Reservar en Bóveda de Adquisición"
                >
                    <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                    <span>Reservar</span>
                </button>
            </form>
        @endif
    </div>
</article>

