<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\PiezaUnica;
use App\Models\Producto;
use App\Models\Promocion;
use App\Models\Sucursal;
use App\Services\SucursalContextoService;
use Database\Seeders\CatalogoProductoSeeder;
use Illuminate\Database\Eloquent\Builder;
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
            'subtitulo' => 'Figuras Pop/Anime, Juguetes, Numismática, Videojuegos & Archivo Histórico',
            'badge' => 'ARCHIVO PATRIMONIAL · COLECCIONABLES',
            'icono' => 'history_edu',
            'descripcion' => 'Figuras de culto, juguetes manufacturados, monedas y billetes, memorabilia gamer y piezas históricas notariadas.',
        ],
        'adquisiciones' => [
            'titulo' => 'Adquisiciones Selectas',
            'subtitulo' => 'Piezas Cúmbre de Alta Curaduría y Ejemplares Únicos',
            'badge' => 'BÓVEDA DE ALTA CURADURÍA · LOTES SELECTOS',
            'icono' => 'auto_awesome',
            'descripcion' => 'Selección transversal de las piezas de mayor rareza patrimonial disponibles para reserva inmediata con custodia preferente.',
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

        return view('public.index', [
            'productos' => $productos,
            'categoriaActiva' => 'inicio',
            'promocionesDestacadas' => $promocionesDestacadas,
            'clienteActual' => $clienteActual,
            'esClienteFrecuente' => $clienteActual?->esClienteFrecuente() ?? false,
            'totalComprasCliente' => $clienteActual?->totalComprasRealizadas() ?? 0,
            'comprasRestantesFrecuente' => $clienteActual ? $clienteActual->comprasRestantesParaFrecuente() : Cliente::COMPRAS_MINIMAS_FRECUENTE,
        ]);
    }

    /**
     * Sección: Libros (clasificación por Géneros Literarios: novela, ensayo, poesía, historia, ciencia, etc.).
     */
    public function libros(Request $request): View
    {
        $productos = $this->consultarProductos($request, 'libros');

        $generosDisponibles = [
            'todos' => 'Todos los Géneros',
            'novela' => 'Novela',
            'ensayo' => 'Ensayo',
            'poesia' => 'Poesía',
            'historia' => 'Historia',
            'ciencia' => 'Ciencia',
        ];

        return view('public.categoria', [
            'categoriaSlug' => 'libros',
            'meta' => self::CATEGORIAS['libros'],
            'productos' => $productos,
            'categoriaActiva' => 'libros',
            'filtroTipo' => 'genero',
            'filtros' => $generosDisponibles,
            'filtroActivo' => $request->string('genero')->lower()->toString() ?: 'todos',
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

        // Si estamos en entorno web interactivo y aún no se han sembrado los productos, poblar automáticamente
        if (! app()->runningUnitTests() && Producto::count() === 0) {
            app(CatalogoProductoSeeder::class)->run();
        } elseif (Schema::hasTable('discos') && Schema::hasColumn('discos', 'imagen')) {
            // Asegurar que los vinilos existentes tengan su imagen asignada en la base de datos
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
            $query->whereHas('catalogo', fn (Builder $q) => $q->where('tipo', 'libro'));

            if ($request->filled('genero') && $request->string('genero')->lower()->toString() !== 'todos') {
                $generoFiltro = $request->string('genero')->lower()->toString();
                $query->whereHas('libro', function (Builder $l) use ($generoFiltro): void {
                    $l->where('genero', $generoFiltro);
                });
            }
        } elseif ($categoria === 'vinilos') {
            $query->whereHas('catalogo', fn (Builder $q) => $q->where('tipo', 'disco'));

            if ($request->filled('formato') && $request->string('formato')->lower()->toString() !== 'todos') {
                $formatoFiltro = $request->string('formato')->lower()->toString() === 'cd' ? 'CD' : 'vinilo';
                $query->whereHas('disco', function (Builder $d) use ($formatoFiltro): void {
                    $d->where('formato', $formatoFiltro);
                });
            }
        } elseif ($categoria === 'memorabilia') {
            $query->whereHas('catalogo', fn (Builder $q) => $q->where('tipo', 'coleccionable'));

            if ($request->filled('categoria') && $request->string('categoria')->lower()->toString() !== 'todos') {
                $catFiltro = $request->string('categoria')->lower()->toString();
                $query->whereHas('coleccionable', function (Builder $c) use ($catFiltro): void {
                    $c->where('categoria', $catFiltro);
                });
            }
        } elseif ($categoria === 'adquisiciones') {
            $query->where(function (Builder $q): void {
                $q->where('precio', '>=', 18000)
                    ->orWhereHas('variantesSku', fn (Builder $v) => $v->where('descripcion', 'like', '%SELECTA%'))
                    ->orWhereHas('coleccionable', fn (Builder $c) => $c->where('edicion_limitada', true));
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

        if (! app()->runningUnitTests() && Promocion::count() === 0 && Producto::count() > 0) {
            app(CatalogoProductoSeeder::class)->run();
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
}
