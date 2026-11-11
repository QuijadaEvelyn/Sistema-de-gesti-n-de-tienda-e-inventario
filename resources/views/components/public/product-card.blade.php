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

    $imagenRuta = $producto->imagen
        ?? $producto->disco?->imagen
        ?? $producto->libro?->imagen
        ?? $producto->coleccionable?->imagen;

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
    $maxUnidadesCompra = $piezaUnica !== null ? 1 : max(1, min(10, $stockTotal));

    $clasesTarjeta = $esPiezaUnica
        ? 'bg-white border-2 border-purple-600 ring-2 ring-purple-600/20 shadow-md shadow-purple-600/15 hover:border-purple-700 hover:shadow-xl hover:shadow-purple-600/25'
        : 'bg-white border border-purple-200/80 shadow-xs hover:border-primary hover:shadow-lg';

    $clasesMiniatura = $esPiezaUnica
        ? 'bg-purple-50/60 border-2 border-purple-600/80 ring-1 ring-purple-500/30 group-hover:bg-purple-100/60'
        : 'bg-surface-container-low border border-purple-200/60 group-hover:bg-purple-50';

    $datosModal = [
        'id' => $producto->id,
        'nombre' => $producto->nombre,
        'subtitulo' => $subtituloCuratorial,
        'descripcion' => $producto->descripcion,
        'precio' => number_format((float) $producto->precio, 0),
        'tipo' => $tipo,
        'icono' => $iconoCategoria,
        'sku' => $sku,
        'graduacion' => $graduacionCorta,
        'badgeType' => $badgeType,
        'stock' => $stockTotal,
        'sede' => $sedeCustodia ? str_replace('Sucursal ', '', $sedeCustodia) : null,
        'esPiezaUnica' => $esPiezaUnica,
        'numeroSerie' => $piezaUnica?->numero_serie,
        'imagen' => !empty($imagenRuta) ? asset('/' . $imagenRuta) : null,
        'estaAgotado' => $estaAgotado,
        'estaReservada' => $estaReservadaTemporalmente,
        'varianteSkuId' => $variante?->id,
        'actionUrl' => route('carrito.agregar'),
        'especificaciones' => match ($tipo) {
            'libro' => [
                'Autor' => $producto->libro?->autor ?? 'Autor Histórico',
                'Editorial' => $producto->libro?->editorial ?? 'Edición Histórica',
                'Año de Publicación' => (string) ($producto->libro?->anio_publicacion ?? 'N/D'),
                'Género Literario' => ucfirst($producto->libro?->genero ?? 'General'),
                'Páginas' => $producto->libro?->paginas ? $producto->libro->paginas . ' págs.' : 'Edición íntegra peritada',
                'Encuadernación' => $producto->libro?->tapa ?? 'Tapa dura artesanal',
                'ISBN' => $producto->libro?->isbn ?? 'Custodia Patrimonial',
            ],
            'disco' => [
                'Artista / Intérprete' => $producto->disco?->artista ?? 'Intérprete Histórico',
                'Sello Discográfico' => $producto->disco?->sello ?? 'Prensado Original',
                'Año de Lanzamiento' => (string) ($producto->disco?->anio_lanzamiento ?? 'N/D'),
                'Formato' => $producto->disco?->formato === 'cd' ? 'Disco Compacto (CD Audiofilia)' : 'Vinilo LP 180g (Prensado Analógico)',
                'Velocidad' => $producto->disco?->rpm ? $producto->disco->rpm . ' RPM' : '33 ⅓ RPM',
                'Pistas' => $producto->disco?->numero_pistas ? $producto->disco->numero_pistas . ' pistas' : 'Álbum Completo',
                'Código Matriz' => $producto->disco?->codigo_matriz ?? 'Autenticación notarial',
            ],
            default => [
                'Marca / Fabricante' => $producto->coleccionable?->marca ?? 'Archivo Histórico',
                'Material de Confección' => $producto->coleccionable?->material ?? 'Material Auténtico',
                'Categoría' => ucfirst($producto->coleccionable?->categoria ?? 'Memorabilia'),
                'Año / Datación' => (string) ($producto->coleccionable?->anio_fabricacion ?? 'Época Clásica'),
                'Certificación COA' => $producto->coleccionable?->certificado_autenticidad ?? 'Certificado de Autenticidad Notariado',
                'Edición Limitada' => $producto->coleccionable?->edicion_limitada ? 'Sí · Serie Numerada' : 'Colección Especial',
            ],
        },
    ];
@endphp

<article
    data-unique-piece="{{ $esPiezaUnica ? 'true' : 'false' }}"
    data-product-details="{{ json_encode($datosModal, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}"
    class="{{ $clasesTarjeta }} rounded-2xl p-5 flex flex-col justify-between transition-all duration-200 group relative"
>
    <div>
        <!-- Imagen / Miniatura con Indicador de Disponibilidad Limpio -->
        <div
            onclick="abrirModalDetalleProducto(this)"
            class="w-full aspect-square rounded-xl {{ $clasesMiniatura }} flex items-center justify-center relative overflow-hidden mb-4 transition-colors cursor-pointer group-hover:shadow-inner"
            title="Haz clic para ver la ficha curatorial de {{ $producto->nombre }}"
        >
            @if ($esPiezaUnica)
                <span class="absolute top-2.5 left-2.5 z-10 bg-purple-600 text-white border border-purple-400/60 text-[10px] font-label-sm font-bold uppercase px-2.5 py-0.5 rounded-md shadow-sm inline-flex items-center gap-1">
                    <span class="material-symbols-outlined text-[12px]">diamond</span>
                    <span>PIEZA ÚNICA 1/1</span>
                    <span class="sr-only">Pieza Única · Edición Coleccionable {{ $piezaUnica?->numero_serie }}</span>
                </span>
            @endif

            @if ($estaReservadaTemporalmente)
                <span class="absolute bottom-2.5 right-2.5 z-10 bg-amber-600 text-white border border-amber-700 text-[10px] font-label-code font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full shadow-xs">
                    RESERVADA EN BÓVEDA (5 MIN)
                </span>
            @elseif ($estaAgotado)
                <span class="absolute bottom-2.5 right-2.5 z-10 bg-red-600 text-white border border-red-700 text-[10px] font-label-code font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full shadow-xs">
                    AGOTADO
                </span>
            @else
                <span class="absolute bottom-2.5 right-2.5 z-10 bg-white/95 dark:bg-[#1b132a]/95 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30 text-[10px] font-label-code font-semibold px-2 py-0.5 rounded-full shadow-2xs">
                    {{ $stockTotal }} en bóveda
                </span>
            @endif

            @if (!empty($imagenRuta))
                <img src="{{ asset('/' . $imagenRuta) }}" alt="{{ $producto->nombre }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
            @else
                <span class="material-symbols-outlined {{ $esPiezaUnica ? 'text-purple-600 group-hover:scale-105' : 'text-purple-300 group-hover:text-primary' }} transition-all text-[64px]">{{ $iconoCategoria }}</span>
            @endif
        </div>

        <!-- Información Limpia: Nombre, Subtítulo y Enlace Sutil a Ficha Curatorial -->
        <div class="space-y-1">
            <h3
                onclick="abrirModalDetalleProducto(this)"
                class="font-bold text-on-surface text-base group-hover:text-primary transition font-quicksand leading-snug cursor-pointer line-clamp-2"
                title="Haz clic para ver detalles de {{ $producto->nombre }}"
            >
                {{ $producto->nombre }}
            </h3>
            <p class="font-title-editorial text-xs text-on-surface-variant italic truncate">
                {{ $subtituloCuratorial }}
            </p>

            <button
                type="button"
                onclick="abrirModalDetalleProducto(this)"
                class="pt-1.5 inline-flex items-center gap-1 text-[11px] font-label-code text-primary dark:text-purple-300 hover:underline font-semibold cursor-pointer group-hover:translate-x-0.5 transition-transform"
                title="Abrir ficha curatorial y especificaciones técnicas"
            >
                <span class="material-symbols-outlined text-[14px]">info</span>
                <span>Ver detalles &rarr;</span>
            </button>
        </div>
    </div>

    <!-- Pie: Precio Tasado y Botón/Contador de Reserva -->
    <div class="pt-4 mt-4 border-t border-purple-100 dark:border-purple-500/20 flex items-center justify-between gap-2">
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
                        class="px-2 py-2 rounded-xl border border-purple-200 dark:border-purple-500/40 bg-surface-container-lowest dark:bg-[#140d20] text-xs font-tabular-data font-bold text-on-surface focus:outline-none focus:border-primary"
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
                class="px-3.5 py-2.5 rounded-xl bg-slate-200 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-300 dark:border-slate-700 text-xs font-label-md font-bold uppercase tracking-wider flex items-center gap-1.5 opacity-50 cursor-not-allowed pointer-events-none select-none"
                title="{{ $estaReservadaTemporalmente ? 'Pieza única reservada temporalmente por otro coleccionista (5 min)' : 'Pieza agotada en bóveda' }}"
            >
                <span class="material-symbols-outlined text-[18px]">{{ $estaReservadaTemporalmente ? 'lock_clock' : 'block' }}</span>
                <span>{{ $estaReservadaTemporalmente ? 'En Reserva' : 'Reservar' }}</span>
            </button>
        @endif
    </div>
</article>
