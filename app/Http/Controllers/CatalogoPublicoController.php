<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Cliente;
use App\Models\Libro;
use App\Models\MensajeContacto;
use App\Models\PiezaUnica;
use App\Models\Producto;
use App\Models\Promocion;
use App\Models\Sucursal;
use App\Services\SucursalContextoService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CatalogoPublicoController extends Controller
{
    /**
     * Metadatos curatoriales de cada categoría pública del menú superior.
     *
     * @var array<string, array{titulo: string, subtitulo: string, badge: string, icono: string, descripcion: string}>
     */
    public const CATEGORIAS = [
        'libros' => [
            'titulo' => 'Libros',
            'subtitulo' => 'Fondo Bibliográfico Clasificado por Géneros Literarios',
            'badge' => 'CATÁLOGO BIBLIOGRÁFICO · LIBROS',
            'icono' => 'auto_stories',
            'descripcion' => 'Primeras ediciones, narrativa, ensayo, poesía, historia y tratados científicos peritados para coleccionistas.',
        ],
        'vinilos' => [
            'titulo' => 'Discos',
            'subtitulo' => 'Fonoteca Histórica · Vinilos LP 180g & Discos Compactos (CD)',
            'badge' => 'CATÁLOGO FONOGRÁFICO · DISCOS',
            'icono' => 'album',
            'descripcion' => 'Prensados analógicos originales en vinilo de 180g y ediciones maestras en CD clasificadas con alta fidelidad.',
        ],
        'memorabilia' => [
            'titulo' => 'Coleccionables',
            'subtitulo' => 'Figuras Pop/Anime, Juguetes, Numismática, Videojuegos & Artículos de Colección',
            'badge' => 'CATÁLOGO ESPECIAL · COLECCIONABLES',
            'icono' => 'history_edu',
            'descripcion' => 'Figuras de colección, juguetes clásicos, monedas y billetes, memorabilia gamer y ediciones con certificado de autenticidad.',
        ],
        'adquisiciones' => [
            'titulo' => 'Ediciones Especiales',
            'subtitulo' => 'Colección Destacada y Ejemplares Únicos',
            'badge' => 'COLECCIÓN DESTACADA · EDICIONES ESPECIALES',
            'icono' => 'auto_awesome',
            'descripcion' => 'Selección destacada de ediciones especiales y piezas únicas disponibles para compra inmediata.',
        ],
    ];

    /**
     * Catálogo exhaustivo de géneros literarios clasificados en ARCHIVUM.
     *
     * @var array<string, string>
     */
    public const GENEROS_LIBROS = [
        'todos' => 'Todos los Géneros',
        'novela' => 'Novela',
        'ensayo' => 'Ensayo',
        'poesia' => 'Poesía',
        'historia' => 'Historia',
        'ciencia' => 'Ciencia',
        'terror' => 'Terror',
        'ciencia_ficcion' => 'Ciencia Ficción',
        'fantasia' => 'Fantasía',
        'misterio_thriller' => 'Misterio & Thriller',
        'filosofia' => 'Filosofía',
        'clasicos' => 'Clásicos Universales',
        'biografia_cronica' => 'Biografía & Crónica',
    ];

    /**
     * Iconos de Google Material Symbols asignados a cada género literario.
     *
     * @var array<string, string>
     */
    public const ICONOS_GENEROS = [
        'todos' => 'auto_stories',
        'novela' => 'menu_book',
        'ensayo' => 'history_edu',
        'poesia' => 'draw',
        'historia' => 'account_balance',
        'ciencia' => 'psychology',
        'terror' => 'skull',
        'ciencia_ficcion' => 'rocket_launch',
        'fantasia' => 'magic_button',
        'misterio_thriller' => 'fingerprint',
        'filosofia' => 'lightbulb',
        'clasicos' => 'hotel_class',
        'biografia_cronica' => 'person_book',
    ];

    /**
     * Paletas visuales editoriales de encuadernación en piel y tela de archivo (para el Estante 3D).
     *
     * @var array<int, array{nombre: string, bg: string, bg_hex: string, bg_top: string, bg_bottom: string, border: string, border_hex: string, accent: string, text_gold: string, band_hex: string, band_secondary_hex: string, tejuelo_bg: string, tejuelo_text: string, textura: string, relieve: string}>
     */
    public const PALETAS_EDITORIALES = [
        [
            'nombre' => 'Borgoña',
            'bg' => 'from-[#6b1423] via-[#57101c] to-[#3a0912]',
            'bg_hex' => '#57101c',
            'bg_top' => '#6b1423',
            'bg_bottom' => '#3a0912',
            'border' => 'border-amber-300/45',
            'border_hex' => 'rgba(212, 175, 55, 0.45)',
            'accent' => '#e11d48',
            'text_gold' => 'text-amber-200',
            'band_hex' => '#d4af37',
            'band_secondary_hex' => 'rgba(212, 175, 55, 0.45)',
            'tejuelo_bg' => 'rgba(25, 8, 12, 0.55)',
            'tejuelo_text' => '#f5e1a4',
            'textura' => 'Guaflex Borgoña Imperial',
            'relieve' => 'Estampación notarial en pan de oro',
        ],
        [
            'nombre' => 'Verde Bosque',
            'bg' => 'from-[#174530] via-[#123626] to-[#0a2116]',
            'bg_hex' => '#123626',
            'bg_top' => '#174530',
            'bg_bottom' => '#0a2116',
            'border' => 'border-amber-300/45',
            'border_hex' => 'rgba(212, 175, 55, 0.45)',
            'accent' => '#10b981',
            'text_gold' => 'text-amber-200',
            'band_hex' => '#d4af37',
            'band_secondary_hex' => 'rgba(212, 175, 55, 0.45)',
            'tejuelo_bg' => 'rgba(8, 24, 16, 0.58)',
            'tejuelo_text' => '#f5e1a4',
            'textura' => 'Tela Editorial Verde Bosque',
            'relieve' => 'Nervios dorados estampados al calor',
        ],
        [
            'nombre' => 'Azul Marino',
            'bg' => 'from-[#162d4c] via-[#11233b] to-[#091424]',
            'bg_hex' => '#11233b',
            'bg_top' => '#162d4c',
            'bg_bottom' => '#091424',
            'border' => 'border-slate-300/45',
            'border_hex' => 'rgba(203, 213, 225, 0.45)',
            'accent' => '#38bdf8',
            'text_gold' => 'text-slate-200',
            'band_hex' => '#cbd5e1',
            'band_secondary_hex' => 'rgba(203, 213, 225, 0.45)',
            'tejuelo_bg' => 'rgba(9, 18, 32, 0.6)',
            'tejuelo_text' => '#e2e8f0',
            'textura' => 'Tela Editorial Azul Marino',
            'relieve' => 'Fileteado con rueda gofrada plateada',
        ],
        [
            'nombre' => 'Ocre Mostaza',
            'bg' => 'from-[#8a5a14] via-[#70480e] to-[#472c07]',
            'bg_hex' => '#70480e',
            'bg_top' => '#8a5a14',
            'bg_bottom' => '#472c07',
            'border' => 'border-amber-200/50',
            'border_hex' => 'rgba(253, 230, 138, 0.5)',
            'accent' => '#f59e0b',
            'text_gold' => 'text-amber-100',
            'band_hex' => '#fde68a',
            'band_secondary_hex' => 'rgba(253, 230, 138, 0.45)',
            'tejuelo_bg' => 'rgba(42, 25, 4, 0.55)',
            'tejuelo_text' => '#fef3c7',
            'textura' => 'Piel Marroquinería Ocre Dorado',
            'relieve' => 'Tejuelo grabado a fuego',
        ],
        [
            'nombre' => 'Gris Pizarra',
            'bg' => 'from-[#394454] via-[#2d3643] to-[#1c222b]',
            'bg_hex' => '#2d3643',
            'bg_top' => '#394454',
            'bg_bottom' => '#1c222b',
            'border' => 'border-slate-300/45',
            'border_hex' => 'rgba(203, 213, 225, 0.45)',
            'accent' => '#94a3b8',
            'text_gold' => 'text-slate-200',
            'band_hex' => '#cbd5e1',
            'band_secondary_hex' => 'rgba(203, 213, 225, 0.45)',
            'tejuelo_bg' => 'rgba(20, 25, 33, 0.6)',
            'tejuelo_text' => '#f1f5f9',
            'textura' => 'Bucarán Gris Pizarra',
            'relieve' => 'Orla ornamental plateada estampada',
        ],
        [
            'nombre' => 'Terracota',
            'bg' => 'from-[#8c3723] via-[#722b1b] to-[#47190f]',
            'bg_hex' => '#722b1b',
            'bg_top' => '#8c3723',
            'bg_bottom' => '#47190f',
            'border' => 'border-amber-200/45',
            'border_hex' => 'rgba(245, 208, 138, 0.45)',
            'accent' => '#f97316',
            'text_gold' => 'text-amber-100',
            'band_hex' => '#f5d08a',
            'band_secondary_hex' => 'rgba(245, 208, 138, 0.45)',
            'tejuelo_bg' => 'rgba(46, 16, 10, 0.55)',
            'tejuelo_text' => '#fde68a',
            'textura' => 'Pergamino & Cuero Terracota',
            'relieve' => 'Letras capitulares gofradas en relieve',
        ],
        [
            'nombre' => 'Negro Carbón',
            'bg' => 'from-[#23252a] via-[#1a1c20] to-[#0e0f12]',
            'bg_hex' => '#1a1c20',
            'bg_top' => '#23252a',
            'bg_bottom' => '#0e0f12',
            'border' => 'border-amber-300/45',
            'border_hex' => 'rgba(212, 175, 55, 0.45)',
            'accent' => '#a1a1aa',
            'text_gold' => 'text-amber-200',
            'band_hex' => '#d4af37',
            'band_secondary_hex' => 'rgba(212, 175, 55, 0.45)',
            'tejuelo_bg' => 'rgba(10, 10, 12, 0.72)',
            'tejuelo_text' => '#f5e1a4',
            'textura' => 'Tafilete Negro Carbón',
            'relieve' => 'Costillas marcadas y cantoneras doradas',
        ],
        [
            'nombre' => 'Cuero Coñac',
            'bg' => 'from-[#54301a] via-[#422514] to-[#29160b]',
            'bg_hex' => '#422514',
            'bg_top' => '#54301a',
            'bg_bottom' => '#29160b',
            'border' => 'border-amber-300/45',
            'border_hex' => 'rgba(229, 193, 88, 0.45)',
            'accent' => '#d97706',
            'text_gold' => 'text-amber-200',
            'band_hex' => '#e5c158',
            'band_secondary_hex' => 'rgba(229, 193, 88, 0.45)',
            'tejuelo_bg' => 'rgba(28, 15, 7, 0.6)',
            'tejuelo_text' => '#fef3c7',
            'textura' => 'Badana Clásica Coñac Envejecido',
            'relieve' => 'Motivo arabesco gofrado bruñido',
        ],
    ];

    /**
     * Página principal de la tienda pública (Catálogo General, Galería y Promociones Exclusivas).
     */
    public function home(Request $request): View
    {
        $productos = $this->consultarProductos($request);
        $promocionesDestacadas = $this->consultarPromocionesDestacadas();

        $clienteActual = auth()->check() && Schema::hasTable('clientes')
            ? Cliente::with(['pedidos', 'clienteFrecuente'])->where('usuario_id', auth()->id())->first()
            : null;

        // Selección curatorial de exactamente 6 piezas balanceadas para el carrusel 3D (2 libros, 2 discos, 2 coleccionables)
        $productosCarrusel = $this->obtenerPiezasCarruselDestacadas($productos);

        return view('public.index', [
            'productos' => $productos,
            'productosCarrusel' => $productosCarrusel,
            'categoriaActiva' => 'inicio',
            'promocionesDestacadas' => $promocionesDestacadas,
            'clienteActual' => $clienteActual,
            'esClienteFrecuente' => $clienteActual?->esClienteFrecuente() ?? false,
            'totalComprasCliente' => $clienteActual?->totalComprasRealizadas() ?? 0,
            'comprasRestantesFrecuente' => $clienteActual ? $clienteActual->comprasRestantesParaFrecuente() : Cliente::COMPRAS_MINIMAS_FRECUENTE,
        ]);
    }

    /**
     * Sección: Libros (Fondo Bibliográfico con Estante 3D Interactivo y Filtro de 12 Géneros).
     */
    public function libros(Request $request): View
    {
        $productos = $this->consultarProductos($request, 'libros');

        $filtroActivoRaw = $request->string('genero')->lower()->toString();
        $filtroActivoSlug = $filtroActivoRaw ? str_replace(['-', ' '], '_', $filtroActivoRaw) : 'todos';
        $filtroActivo = array_key_exists($filtroActivoSlug, self::GENEROS_LIBROS) ? $filtroActivoSlug : 'todos';

        $sucursalActiva = app(SucursalContextoService::class)->resolverSucursalPublica($request);
        $sucursalId = $sucursalActiva?->id;

        // Obtener todos los libros activos para el contador de géneros y el Estante 3D
        $todosLibros = Producto::query()
            ->with([
                'catalogo',
                'libro',
                'variantesSku.existencias.piezasUnicas',
                'variantesSku.existencias.sucursal',
            ])
            ->where('activo', true)
            ->when($sucursalId !== null, function (Builder $q) use ($sucursalId): void {
                $q->whereHas('variantesSku.existencias', function (Builder $eq) use ($sucursalId): void {
                    $eq->where('sucursal_id', $sucursalId);
                });
            })
            ->where(function (Builder $q): void {
                $q->whereHas('catalogo', function (Builder $c): void {
                    $c->whereIn('tipo', ['libro', 'libros'])
                        ->orWhere('nombre', 'like', '%libro%');
                })->orWhereHas('libro');
            })
            ->get();

        $conteoGeneros = ['todos' => $todosLibros->count()];
        foreach (self::GENEROS_LIBROS as $slug => $nombre) {
            if ($slug === 'todos') {
                continue;
            }
            $conteoGeneros[$slug] = $todosLibros->filter(function (Producto $p) use ($slug): bool {
                $gen = str_replace(['-', ' '], '_', strtolower((string) ($p->libro?->genero ?? '')));

                return $gen === $slug;
            })->count();
        }

        // Selección curatorial balanceada para el Estante 3D de Libros (hasta 8 libros destacados de géneros variados)
        $librosCandidatos = $todosLibros->sortByDesc(function (Producto $p): float {
            $variante = $p->variantesSku->first();
            $esUnica = $variante && $variante->existencias->flatMap->piezasUnicas->isNotEmpty();

            return ($esUnica ? 500000.0 : 0.0) + (float) $p->precio;
        })->values();

        $seleccionados = collect();
        $generosIncluidos = [];
        foreach ($librosCandidatos as $cand) {
            $g = str_replace(['-', ' '], '_', strtolower((string) ($cand->libro?->genero ?? 'novela')));
            if (! in_array($g, $generosIncluidos, true) || $seleccionados->count() < 4) {
                $seleccionados->push($cand);
                $generosIncluidos[] = $g;
                if ($seleccionados->count() >= 8) {
                    break;
                }
            }
        }

        if ($seleccionados->count() < 6) {
            $seleccionados = $librosCandidatos->take(8);
        }

        $librosEstante = [];
        foreach ($seleccionados->values() as $idx => $prod) {
            /** @var Producto $prod */
            $variante = $prod->variantesSku->first();
            $sku = $variante?->sku ?? 'LIB-ARC-'.$prod->id;
            $graduacionRaw = $variante?->descripcion ?? 'MINT (M) · PRIMERA EDICIÓN';
            $partesGrad = array_map('trim', explode('·', $graduacionRaw));
            $graduacionCorta = $partesGrad[0] ?? 'MINT (M)';
            $stockTotal = $variante ? (int) $variante->existencias->sum('cantidad') : 0;
            $piezaUnica = $variante
                ? CheckoutSimuladoController::obtenerPiezaUnicaDeVariante((int) $variante->id)
                : null;
            $esPiezaUnica = $piezaUnica !== null;
            $sedeCustodia = $piezaUnica?->existencia?->sucursal?->nombre
                ?? $variante?->existencias?->first()?->sucursal?->nombre
                ?? 'Bóveda Central Bellas Artes';

            $generoSlug = str_replace(['-', ' '], '_', strtolower((string) ($prod->libro?->genero ?? 'novela')));
            $paleta = self::PALETAS_EDITORIALES[$idx % count(self::PALETAS_EDITORIALES)];

            $badgeType = str_contains($graduacionCorta, 'COA')
                ? 'coa'
                : (str_contains($graduacionCorta, 'VERY FINE') ? 'rare' : 'mint');

            $subtituloCuratorial = ($prod->libro?->autor ?? 'Autor Histórico').' · '.($prod->libro?->editorial ?? 'Edición Histórica').($prod->libro?->anio_publicacion ? ' ('.$prod->libro->anio_publicacion.')' : '');

            $datosModal = [
                'id' => $prod->id,
                'nombre' => $prod->nombre,
                'subtitulo' => $subtituloCuratorial,
                'descripcion' => $prod->descripcion,
                'precio' => number_format((float) $prod->precio, 0),
                'tipo' => 'libro',
                'icono' => self::ICONOS_GENEROS[$generoSlug] ?? 'menu_book',
                'sku' => $sku,
                'graduacion' => $graduacionCorta,
                'badgeType' => $badgeType,
                'stock' => $stockTotal,
                'sede' => str_replace('Sucursal ', '', $sedeCustodia),
                'esPiezaUnica' => $esPiezaUnica,
                'numeroSerie' => $piezaUnica?->numero_serie,
                'imagen' => $prod->imagen_url,
                'estaAgotado' => $stockTotal <= 0 || ($esPiezaUnica && in_array($piezaUnica->estado, ['reservada', 'vendida'], true)),
                'estaReservada' => $piezaUnica !== null && $piezaUnica->estado === 'reservada',
                'varianteSkuId' => $variante?->id,
                'actionUrl' => route('carrito.agregar'),
                'especificaciones' => [
                    'Autor' => $prod->libro?->autor ?? 'Autor Histórico',
                    'Editorial' => $prod->libro?->editorial ?? 'Edición Histórica',
                    'Año de Publicación' => (string) ($prod->libro?->anio_publicacion ?? 'N/D'),
                    'Género Literario' => self::GENEROS_LIBROS[$generoSlug] ?? ucfirst($generoSlug),
                    'Páginas' => $prod->libro?->paginas ? $prod->libro->paginas.' págs.' : 'Edición íntegra peritada',
                    'Encuadernación' => $paleta['textura'].' · '.$paleta['relieve'],
                    'ISBN' => $prod->libro?->isbn ?? '978-ARCHIVUM-00',
                    'Graduación' => $graduacionRaw,
                    'Certificado' => 'COA-BIB-'.substr(md5($sku), 0, 8),
                ],
            ];

            $librosEstante[] = [
                'index' => $idx,
                'producto_id' => $prod->id,
                'variante_sku_id' => $variante?->id,
                'sku' => $sku,
                'nombre' => $prod->nombre,
                'autor' => $prod->libro?->autor ?? 'Autor Histórico',
                'editorial' => $prod->libro?->editorial ?? 'Edición Histórica',
                'anio' => $prod->libro?->anio_publicacion ?? 'Época Clásica',
                'paginas' => $prod->libro?->paginas ?? 320,
                'isbn' => $prod->libro?->isbn ?? '978-ARCHIVUM-00',
                'genero' => $generoSlug,
                'genero_nombre' => self::GENEROS_LIBROS[$generoSlug] ?? 'Novela',
                'genero_icono' => self::ICONOS_GENEROS[$generoSlug] ?? 'menu_book',
                'graduacion' => $graduacionCorta,
                'graduacion_completa' => $graduacionRaw,
                'certificado' => 'COA-BIB-'.substr(md5($sku), 0, 8),
                'descripcion' => $prod->descripcion,
                'precio' => (float) $prod->precio,
                'precio_formateado' => '$'.number_format((float) $prod->precio, 0),
                'stock' => $stockTotal,
                'agotado' => $stockTotal <= 0 || ($esPiezaUnica && in_array($piezaUnica->estado, ['reservada', 'vendida'], true)),
                'es_pieza_unica' => $esPiezaUnica,
                'numero_serie' => $piezaUnica?->numero_serie,
                'sede' => $sedeCustodia,
                'paleta' => $paleta,
                'imagen' => $prod->imagen_url,
                'datos_modal' => $datosModal,
            ];
        }

        return view('public.libros', [
            'categoriaSlug' => 'libros',
            'meta' => self::CATEGORIAS['libros'],
            'productos' => $productos,
            'categoriaActiva' => 'libros',
            'filtroTipo' => 'genero',
            'filtros' => self::GENEROS_LIBROS,
            'filtroActivo' => $filtroActivo,
            'conteoGeneros' => $conteoGeneros,
            'iconosGeneros' => self::ICONOS_GENEROS,
            'librosEstante' => $librosEstante,
        ]);
    }

    /**
     * Sección: Discos (filtro interactivo para separar entre Vinilos y CDs, con Disc Showcase 3D).
     */
    public function vinilos(Request $request): View
    {
        $productos = $this->consultarProductos($request, 'vinilos');

        $formatosDisponibles = [
            'todos' => 'Todos los Discos',
            'vinilo' => 'Vinilos LP 180g',
            'cd' => 'Discos Compactos (CD)',
        ];

        return view('public.vinilos', [
            'categoriaSlug' => 'vinilos',
            'meta' => self::CATEGORIAS['vinilos'],
            'productos' => $productos,
            'categoriaActiva' => 'vinilos',
            'filtroTipo' => 'formato',
            'filtros' => $formatosDisponibles,
            'filtroActivo' => $request->string('formato')->lower()->toString() ?: 'todos',
        ]);
    }

    /**
     * Sección: Coleccionables (filtro por categorías: figuras pop/anime, juguetes, numismática, históricos, videojuegos).
     */
    public function memorabilia(Request $request): View
    {
        $productos = $this->consultarProductos($request, 'memorabilia');

        // Seleccionar los coleccionables destacados / piezas únicas para el Carrusel Horizontal con Desenfoque Lateral
        $coleccionablesDestacados = Schema::hasTable('productos') && Schema::hasTable('catalogos')
            ? Producto::query()
                ->with([
                    'catalogo',
                    'coleccionable',
                    'variantesSku.existencias.piezasUnicas',
                    'variantesSku.existencias.sucursal',
                ])
                ->where('activo', true)
                ->where(function (Builder $q): void {
                    $q->whereHas('catalogo', function (Builder $c): void {
                        $c->whereIn('tipo', ['coleccionable', 'coleccionables', 'memorabilia'])
                            ->orWhere('nombre', 'like', '%coleccion%')
                            ->orWhere('nombre', 'like', '%memorabilia%');
                    })->orWhereHas('coleccionable');
                })
                ->orderByDesc('precio')
                ->limit(10)
                ->get()
            : $productos->take(10);

        if ($coleccionablesDestacados->isEmpty()) {
            $coleccionablesDestacados = $productos->take(10);
        }

        $subcategoriasDisponibles = [
            'todos' => 'Todos los Coleccionables',
            'figuras' => 'Figuras coleccionables (cultura pop y anime)',
            'juguetes' => 'Juguetes y objetos manufacturados',
            'numismatica' => 'Monedas y billetes',
            'historicos' => 'Objetos culturales e históricos',
            'videojuegos' => 'Coleccionables de videojuegos',
        ];

        return view('public.categoria', [
            'categoriaSlug' => 'memorabilia',
            'meta' => self::CATEGORIAS['memorabilia'],
            'productos' => $productos,
            'coleccionablesDestacados' => $coleccionablesDestacados,
            'categoriaActiva' => 'memorabilia',
            'filtroTipo' => 'categoria',
            'filtros' => $subcategoriasDisponibles,
            'filtroActivo' => $request->string('categoria')->lower()->toString() ?: 'todos',
        ]);
    }

    /**
     * Sección: Adquisiciones Selectas (filtra piezas de alta curaduría / edición selecta).
     */
    public function adquisiciones(Request $request): View
    {
        $productos = $this->consultarProductos($request, 'adquisiciones');

        return view('public.categoria', [
            'categoriaSlug' => 'adquisiciones',
            'meta' => self::CATEGORIAS['adquisiciones'],
            'productos' => $productos,
            'categoriaActiva' => 'adquisiciones',
        ]);
    }

    /**
     * Vista institucional dedicada: "Conoce más de nosotros" con resumen de la plataforma
     * y animación de lluvia de elementos temáticos (libros, vinilos y coleccionables).
     */
    public function nosotros(): View
    {
        $sucursales = Schema::hasTable('sucursales')
            ? Sucursal::query()->where('activa', true)->orderBy('id')->get()
            : collect();

        $metricasAcervo = [
            'total_productos' => Schema::hasTable('productos') ? Producto::where('activo', true)->count() : 20,
            'total_piezas_unicas' => Schema::hasTable('piezas_unicas') ? PiezaUnica::count() : 10,
            'total_sedes' => $sucursales->count() ?: 3,
        ];

        return view('public.nosotros', [
            'categoriaActiva' => 'nosotros',
            'sucursales' => $sucursales,
            'metricasAcervo' => $metricasAcervo,
        ]);
    }

    /**
     * Vista pública de Contacto y Horarios de atención de las sucursales y bóvedas activas.
     */
    public function contacto(Request $request): View
    {
        $sucursales = Schema::hasTable('sucursales')
            ? Sucursal::query()->where('activa', true)->orderBy('id')->get()
            : collect();

        return view('public.contacto', [
            'categoriaActiva' => 'contacto',
            'sucursales' => $sucursales,
        ]);
    }

    /**
     * Procesa el formulario de contacto público y lo registra en bitácora de auditoría.
     */
    public function enviarMensajeContacto(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'sucursal_id' => ['nullable', 'integer', 'exists:sucursales,id'],
            'asunto' => ['required', 'string', 'max:200'],
            'mensaje' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'nombre.required' => 'Por favor ingrese su nombre o seudónimo de coleccionista.',
            'email.required' => 'El correo electrónico es indispensable para remitir nuestra respuesta.',
            'email.email' => 'Ingrese una dirección de correo electrónico válida.',
            'asunto.required' => 'El asunto de la comunicación es obligatorio.',
            'mensaje.required' => 'El cuerpo del mensaje no puede estar vacío.',
            'mensaje.min' => 'El mensaje debe contener al menos 10 caracteres explicativos.',
        ]);

        $mensajeContacto = MensajeContacto::create([
            'nombre' => $validated['nombre'],
            'email' => $validated['email'],
            'telefono' => $validated['telefono'] ?? null,
            'sucursal_id' => ! empty($validated['sucursal_id']) ? (int) $validated['sucursal_id'] : null,
            'asunto' => $validated['asunto'],
            'mensaje' => $validated['mensaje'],
            'estatus' => 'pendiente',
            'ip' => $request->ip() ?? '127.0.0.1',
        ]);

        if (Schema::hasTable('bitacoras')) {
            Bitacora::create([
                'usuario_id' => auth()->id(),
                'accion' => 'MENSAJE_CONTACTO_RECIBIDO',
                'tabla_afectada' => ! empty($validated['sucursal_id']) ? 'sucursales' : 'mensajes_contacto',
                'registro_id' => ! empty($validated['sucursal_id']) ? (int) $validated['sucursal_id'] : $mensajeContacto->id,
                'detalle' => [
                    'mensaje_id' => $mensajeContacto->id,
                    'nombre' => $validated['nombre'],
                    'email' => $validated['email'],
                    'telefono' => $validated['telefono'] ?? null,
                    'asunto' => $validated['asunto'],
                    'mensaje' => $validated['mensaje'],
                    'sucursal_id' => $mensajeContacto->sucursal_id,
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);
        }

        return redirect()->route('public.contacto')->with(
            'status',
            'Tu mensaje ha sido enviado al equipo de atención de ARCHIVUM con éxito. Te responderemos a la brevedad.'
        );
    }

    /**
     * Aviso de Privacidad integral de ARCHIVUM (Protección de datos personales y adquisiciones).
     */
    public function privacidad(): View
    {
        return view('public.privacidad', [
            'categoriaActiva' => 'privacidad',
        ]);
    }

    /**
     * Consulta los productos activos aplicando filtro estricto de categoría,
     * búsqueda por término y ordenamiento.
     *
     * @return Collection<int, Producto>
     */
    protected function consultarProductos(Request $request, ?string $categoria = null): Collection
    {
        if (! Schema::hasTable('productos') || ! Schema::hasTable('catalogos')) {
            return collect();
        }

        // Liberar automáticamente cualquier reserva de carrito cuyo temporizador de 5 minutos haya expirado
        CheckoutSimuladoController::liberarReservasExpiradas();

        // Sincronizar imágenes conocidas para discos existentes que aún no tengan imagen asignada
        if (Schema::hasTable('discos') && Schema::hasColumn('discos', 'imagen')) {
            static $imagenesSincronizadas = false;
            if (! $imagenesSincronizadas) {
                $imagenesSincronizadas = true;
                foreach (Producto::IMAGENES_CONOCIDAS as $nombreProd => $rutaImg) {
                    $prodExistente = Producto::where('nombre', $nombreProd)->first();
                    if ($prodExistente && $prodExistente->disco && empty($prodExistente->disco->getRawOriginal('imagen'))) {
                        $prodExistente->disco->update(['imagen' => $rutaImg]);
                    }
                }
            }
        }

        $sucursalActiva = app(SucursalContextoService::class)->resolverSucursalPublica($request);
        $sucursalId = $sucursalActiva?->id;

        $relaciones = [
            'catalogo',
            'libro',
            'disco',
            'coleccionable',
            'variantesSku.existencias' => function ($q) use ($sucursalId): void {
                $q->with(['piezasUnicas', 'sucursal']);
                if ($sucursalId !== null) {
                    $q->where('sucursal_id', $sucursalId);
                }
            },
        ];

        if (Schema::hasTable('promociones') && Schema::hasColumn('promociones', 'producto_id')) {
            $relaciones['promociones'] = fn ($q) => $q->where('activa', true);
        }

        $query = Producto::query()
            ->with($relaciones)
            ->where('activo', true);

        if ($sucursalId !== null) {
            $query->whereHas('variantesSku.existencias', function (Builder $q) use ($sucursalId): void {
                $q->where('sucursal_id', $sucursalId);
            });
        }

        if ($categoria === 'libros') {
            $query->where(function (Builder $q): void {
                $q->whereHas('catalogo', function (Builder $c): void {
                    $c->whereIn('tipo', ['libro', 'libros'])
                        ->orWhere('nombre', 'like', '%libro%');
                })->orWhereHas('libro');
            });

            if ($request->filled('genero') && $request->string('genero')->lower()->toString() !== 'todos') {
                $generoRaw = $request->string('genero')->lower()->toString();
                $generoSlug = str_replace(['-', ' '], '_', $generoRaw);
                $query->whereHas('libro', function (Builder $l) use ($generoSlug, $generoRaw): void {
                    $l->where('genero', $generoSlug)
                        ->orWhere('genero', $generoRaw)
                        ->orWhere('genero', 'like', '%'.$generoRaw.'%');
                });
            }
        } elseif ($categoria === 'vinilos') {
            $query->where(function (Builder $q): void {
                $q->whereHas('catalogo', function (Builder $c): void {
                    $c->whereIn('tipo', ['disco', 'discos', 'vinilo', 'vinilos'])
                        ->orWhere('nombre', 'like', '%disco%')
                        ->orWhere('nombre', 'like', '%vinil%');
                })->orWhereHas('disco');
            });

            if ($request->filled('formato') && $request->string('formato')->lower()->toString() !== 'todos') {
                $formatoFiltro = $request->string('formato')->lower()->toString() === 'cd' ? 'CD' : 'vinilo';
                $query->whereHas('disco', function (Builder $d) use ($formatoFiltro): void {
                    $d->where('formato', $formatoFiltro)
                        ->orWhere('formato', strtolower($formatoFiltro));
                });
            }
        } elseif ($categoria === 'memorabilia') {
            $query->where(function (Builder $q): void {
                $q->whereHas('catalogo', function (Builder $c): void {
                    $c->whereIn('tipo', ['coleccionable', 'coleccionables', 'memorabilia'])
                        ->orWhere('nombre', 'like', '%coleccion%')
                        ->orWhere('nombre', 'like', '%memorabilia%');
                })->orWhereHas('coleccionable');
            });

            if ($request->filled('categoria') && $request->string('categoria')->lower()->toString() !== 'todos') {
                $catFiltro = $request->string('categoria')->lower()->toString();
                $query->whereHas('coleccionable', function (Builder $c) use ($catFiltro): void {
                    $c->where('categoria', $catFiltro)
                        ->orWhere('categoria', 'like', '%'.$catFiltro.'%');
                });
            }
        } elseif ($categoria === 'adquisiciones') {
            $query->where(function (Builder $q): void {
                $q->where('precio', '>=', 18000)
                    ->orWhereHas('variantesSku', fn (Builder $v) => $v->where('descripcion', 'like', '%SELECTA%')->orWhere('descripcion', 'like', '%PIEZA ÚNICA%')->orWhere('descripcion', 'like', '%EDICIÓN ESPECIAL%'))
                    ->orWhereHas('variantesSku.existencias.piezasUnicas')
                    ->orWhereHas('coleccionable', fn (Builder $c) => $c->where('edicion_limitada', true));

                if (Schema::hasColumn('productos', 'es_preventa')) {
                    $q->orWhere('es_preventa', true);
                }
            });
        }

        if ($request->filled('q')) {
            $termino = '%'.$request->string('q')->trim().'%';
            $query->where(function (Builder $q) use ($termino): void {
                $q->where('nombre', 'like', $termino)
                    ->orWhere('descripcion', 'like', $termino)
                    ->orWhereHas('variantesSku', fn (Builder $v) => $v->where('sku', 'like', $termino))
                    ->orWhereHas('libro', fn (Builder $l) => $l->where('autor', 'like', $termino)->orWhere('isbn', 'like', $termino))
                    ->orWhereHas('disco', fn (Builder $d) => $d->where('artista', 'like', $termino)->orWhere('sello', 'like', $termino));
            });
        }

        $orden = $request->string('orden')->toString();
        if ($orden === 'precio_desc') {
            $query->orderByDesc('precio');
        } elseif ($orden === 'precio_asc') {
            $query->orderBy('precio');
        } else {
            $query->orderByDesc('precio');
        }

        return $query->get();
    }

    /**
     * Obtiene las promociones activas y preventas programadas para la vitrina principal.
     *
     * @return Collection<int, Promocion>
     */
    protected function consultarPromocionesDestacadas(): Collection
    {
        if (! Schema::hasTable('promociones')) {
            return collect();
        }

        $query = Promocion::query()->where('activa', true);

        if (Schema::hasColumn('promociones', 'producto_id')) {
            $query->with([
                'producto.catalogo',
                'producto.libro',
                'producto.disco',
                'producto.coleccionable',
                'producto.variantesSku.existencias',
            ]);
        }

        return $query->orderByDesc('es_preventa_exclusiva')
            ->orderBy('inicia_en')
            ->get()
            ->filter(fn (Promocion $p): bool => in_array($p->faseActual(), ['preventa_exclusiva', 'abierta_publico', 'programada'], true))
            ->values();
    }

    /**
     * Obtiene exactamente 6 piezas activas balanceadas para el carrusel 3D Coverflow:
     * 2 Libros, 2 Discos y 2 Coleccionables, intercalados armoniosamente.
     *
     * @param  Collection<int, Producto>|null  $productosBase
     * @return Collection<int, Producto>
     */
    protected function obtenerPiezasCarruselDestacadas(?Collection $productosBase = null): Collection
    {
        if (! Schema::hasTable('productos') || ! Schema::hasTable('catalogos')) {
            return collect();
        }

        $esLibro = fn (Producto $p): bool => $p->catalogo?->tipo === 'libro'
            || str_starts_with((string) $p->catalogo?->tipo, 'libro')
            || ($p->relationLoaded('libro') && $p->libro !== null);

        $esDisco = fn (Producto $p): bool => in_array($p->catalogo?->tipo, ['disco', 'vinilo', 'vinilos'], true)
            || ($p->relationLoaded('disco') && $p->disco !== null);

        $esColeccionable = fn (Producto $p): bool => in_array($p->catalogo?->tipo, ['coleccionable', 'memorabilia', 'coleccionables'], true)
            || ($p->relationLoaded('coleccionable') && $p->coleccionable !== null);

        $libros = $productosBase ? $productosBase->filter($esLibro)->take(2)->values() : collect();
        $discos = $productosBase ? $productosBase->filter($esDisco)->take(2)->values() : collect();
        $coleccionables = $productosBase ? $productosBase->filter($esColeccionable)->take(2)->values() : collect();

        // Si la colección base no tiene los 2 de cada categoría (ej. por filtros de búsqueda),
        // consultamos directamente los productos activos para garantizar la presencia de las 6 piezas.
        if ($libros->count() < 2 || $discos->count() < 2 || $coleccionables->count() < 2) {
            $consultarCategoria = function (array $tipos): Collection {
                return Producto::query()
                    ->with([
                        'catalogo',
                        'libro',
                        'disco',
                        'coleccionable',
                        'variantesSku.existencias.piezasUnicas',
                        'variantesSku.existencias.sucursal',
                    ])
                    ->where('activo', true)
                    ->whereHas('catalogo', fn (Builder $q) => $q->whereIn('tipo', $tipos))
                    ->orderByDesc('precio')
                    ->limit(2)
                    ->get();
            };

            if ($libros->count() < 2) {
                $libros = $consultarCategoria(['libro']);
            }
            if ($discos->count() < 2) {
                $discos = $consultarCategoria(['disco', 'vinilo']);
            }
            if ($coleccionables->count() < 2) {
                $coleccionables = $consultarCategoria(['coleccionable', 'memorabilia']);
            }
        }

        // Intercalar armoniosamente: Libro 1, Disco 1, Coleccionable 1, Libro 2, Disco 2, Coleccionable 2
        $intercalados = collect();
        $totalMax = max($libros->count(), $discos->count(), $coleccionables->count());

        for ($i = 0; $i < $totalMax; $i++) {
            if ($libros->has($i)) {
                $intercalados->push($libros->get($i));
            }
            if ($discos->has($i)) {
                $intercalados->push($discos->get($i));
            }
            if ($coleccionables->has($i)) {
                $intercalados->push($coleccionables->get($i));
            }
        }

        return $intercalados->take(6)->values();
    }
}
