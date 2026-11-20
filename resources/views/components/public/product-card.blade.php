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

    $imagenUrl = $producto->imagen_url;

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
        ? 'bg-white dark:bg-[#171024] border-2 border-purple-300 dark:border-purple-500 ring-1 ring-purple-300/30 dark:ring-2 dark:ring-purple-500/20 shadow-xs dark:shadow-md dark:shadow-purple-600/15 hover:border-purple-400 dark:hover:border-purple-400 hover:shadow-md dark:hover:shadow-xl'
        : 'bg-white dark:bg-[#171024] border border-purple-200/80 dark:border-purple-500/30 shadow-xs hover:border-primary hover:shadow-lg';

    $clasesMiniatura = $esPiezaUnica
        ? 'bg-purple-50/60 dark:bg-[#140d20] border border-purple-200 dark:border-purple-500/40 group-hover:bg-purple-100/60'
        : 'bg-surface-container-low dark:bg-[#140d20] border border-purple-200/60 dark:border-purple-500/30 group-hover:bg-purple-50 dark:group-hover:bg-purple-900/20';

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
        'imagen' => $imagenUrl,
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
    draggable="false"
    data-unique-piece="{{ $esPiezaUnica ? 'true' : 'false' }}"
    data-product-details="{{ json_encode($datosModal, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }}"
    class="{{ $clasesTarjeta }} rounded-2xl p-2.5 sm:p-5 flex flex-col justify-between transition-all duration-200 group relative h-full select-none"
>
    <div>
        <!-- Imagen / Miniatura con Indicador de Disponibilidad Limpio -->
        <div
            onclick="abrirModalDetalleProducto(this)"
            class="w-full aspect-square rounded-xl {{ $clasesMiniatura }} flex items-center justify-center relative overflow-hidden mb-2.5 sm:mb-4 transition-colors cursor-pointer group-hover:shadow-inner"
            title="Haz clic para ver la ficha curatorial de {{ $producto->nombre }}"
        >
            <!-- Capa Glossy Curatorial Sutil -->
            <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/10 to-white/20 dark:via-white/5 dark:to-white/10 pointer-events-none rounded-xl z-20"></div>

            <!-- Contenedor Flex Superior para Insignias (Izquierda: Pieza Única, Derecha: Stock) sin Encimarse Jamás -->
            <div class="absolute top-2.5 inset-x-2.5 flex items-center justify-between gap-1.5 sm:gap-2 z-10 pointer-events-none">
                <div>
                    @if ($esPiezaUnica)
                        <span class="inline-flex items-center gap-1 bg-purple-100 dark:bg-purple-600 text-purple-800 dark:text-white border border-purple-300 dark:border-purple-400/60 text-[9px] sm:text-[10px] font-label-sm font-bold uppercase px-1.5 sm:px-2 py-0.5 rounded-md shadow-2xs dark:shadow-sm">
                            <span class="material-symbols-outlined text-[11px] sm:text-[12px]">diamond</span>
                            <span class="hidden sm:inline">PIEZA ÚNICA</span>
                            <span>1/1</span>
                            <span class="sr-only">Pieza Única · Edición Coleccionable {{ $piezaUnica?->numero_serie }}</span>
                        </span>
                    @endif
                </div>

                <div class="shrink-0">
                    @if ($estaReservadaTemporalmente)
                        <span class="inline-flex items-center bg-amber-600 text-white border border-amber-700 text-[9px] sm:text-[10px] font-label-code font-bold uppercase tracking-wider px-1.5 sm:px-2.5 py-0.5 rounded-full shadow-xs whitespace-nowrap">
                            RESERVADA (5 MIN)
                        </span>
                    @elseif ($estaAgotado)
                        <span class="inline-flex items-center bg-red-600 text-white border border-red-700 text-[9px] sm:text-[10px] font-label-code font-bold uppercase tracking-wider px-1.5 sm:px-2.5 py-0.5 rounded-full shadow-xs whitespace-nowrap">
                            AGOTADO
                        </span>
                    @else
                        <span class="inline-flex items-center bg-white/95 dark:bg-[#1b132a]/95 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30 text-[9px] sm:text-[10px] font-label-code font-semibold px-1.5 sm:px-2 py-0.5 rounded-full shadow-2xs whitespace-nowrap">
                            {{ $stockTotal }} en bóveda
                        </span>
                    @endif
                </div>
            </div>

            @if (!empty($imagenUrl))
                <img draggable="false" src="{{ $imagenUrl }}" alt="{{ $producto->nombre }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105 pointer-events-none select-none">
            @else
                <span class="material-symbols-outlined {{ $esPiezaUnica ? 'text-purple-600 group-hover:scale-105' : 'text-purple-300 group-hover:text-primary' }} transition-all text-[40px] sm:text-[64px]">{{ $iconoCategoria }}</span>
            @endif
        </div>

        <!-- Información Limpia: Nombre, Subtítulo y Enlace Sutil a Ficha Curatorial -->
        <div class="space-y-1">
            <h3
                onclick="abrirModalDetalleProducto(this)"
                class="font-bold text-on-surface dark:text-white text-xs sm:text-base group-hover:text-primary dark:group-hover:text-purple-300 transition font-quicksand leading-snug cursor-pointer line-clamp-2"
                title="Haz clic para ver detalles de {{ $producto->nombre }}"
            >
                {{ $producto->nombre }}
            </h3>
            <p class="font-title-editorial text-[10px] sm:text-xs text-on-surface-variant dark:text-purple-200/70 italic truncate">
                {{ $subtituloCuratorial }}
            </p>

            <button
                type="button"
                onclick="abrirModalDetalleProducto(this)"
                class="pt-1 sm:pt-1.5 inline-flex items-center gap-1 text-[10px] sm:text-[11px] font-label-code text-primary dark:text-purple-300 hover:underline font-semibold cursor-pointer group-hover:translate-x-0.5 transition-transform"
                title="Abrir ficha curatorial y especificaciones técnicas"
            >
                <span class="material-symbols-outlined text-[13px] sm:text-[14px]">info</span>
                <span>Ver detalles &rarr;</span>
            </button>
        </div>
    </div>

    <!-- Pie: Precio Tasado y Botón/Contador de Reserva -->
    <div class="pt-2.5 sm:pt-4 mt-2.5 sm:mt-4 border-t border-purple-100 dark:border-purple-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div>
            <span class="text-[9px] sm:text-[10px] uppercase font-label-code text-outline dark:text-purple-300/60 block">Precio Tasado:</span>
            <span class="font-tabular-data font-bold text-on-surface dark:text-white text-sm sm:text-base">
                ${{ number_format((float) $producto->precio, 0) }}
                <span class="text-[10px] sm:text-xs font-normal text-on-surface-variant dark:text-purple-200/60">MXN</span>
            </span>
        </div>

        @if ($variante && ! $estaAgotado)
            <form method="POST" action="{{ route('carrito.agregar') }}" class="w-full sm:w-auto flex items-center gap-1.5">
                @csrf
                <input type="hidden" name="variante_sku_id" value="{{ $variante->id }}" />
                @if (! $esPiezaUnica && $maxUnidadesCompra > 1)
                    <select
                        name="cantidad"
                        aria-label="Cantidad de unidades a adquirir"
                        class="px-1.5 py-1.5 sm:px-2 sm:py-2 rounded-lg sm:rounded-xl border border-purple-200 dark:border-purple-500/40 bg-surface-container-lowest dark:bg-[#140d20] text-[10px] sm:text-xs font-tabular-data font-bold text-on-surface dark:text-white focus:outline-none focus:border-primary"
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
                    class="w-full sm:w-auto justify-center btn-glass-purple px-2.5 sm:px-3.5 py-1.5 sm:py-2.5 rounded-lg sm:rounded-xl text-[11px] sm:text-xs font-label-md font-bold uppercase tracking-wider flex items-center gap-1.5 shadow-xs cursor-pointer"
                    title="Reservar en Bóveda de Adquisición"
                >
                    <span class="material-symbols-outlined text-[15px] sm:text-[18px]">add_shopping_cart</span>
                    <span>Reservar</span>
                </button>
            </form>
        @else
            <button
                type="button"
                disabled
                aria-disabled="true"
                class="w-full sm:w-auto justify-center px-2.5 sm:px-3.5 py-1.5 sm:py-2.5 rounded-lg sm:rounded-xl bg-slate-200 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-300 dark:border-slate-700 text-[11px] sm:text-xs font-label-md font-bold uppercase tracking-wider flex items-center gap-1.5 opacity-50 cursor-not-allowed pointer-events-none select-none"
                title="{{ $estaReservadaTemporalmente ? 'Pieza única reservada temporalmente por otro coleccionista (5 min)' : 'Pieza agotada en bóveda' }}"
            >
                <span class="material-symbols-outlined text-[15px] sm:text-[18px]">{{ $estaReservadaTemporalmente ? 'lock_clock' : 'block' }}</span>
                <span>{{ $estaReservadaTemporalmente ? 'En Reserva' : 'Reservar' }}</span>
            </button>
        @endif
    </div>
</article>
