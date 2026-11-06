<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Producto;
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
            'titulo' => 'Libros Raros & Incunables',
            'subtitulo' => 'Fondo Bibliográfico Antiguo y Primeras Ediciones',
            'badge' => 'CATÁLOGO BIBLIOGRÁFICO · LIBROS',
            'icono' => 'auto_stories',
            'descripcion' => 'Primeras ediciones hispanoamericanas, ejemplares dedicados por el autor y encuadernaciones históricas con dictamen pericial.',
        ],
        'vinilos' => [
            'titulo' => 'Discos & Vinilos Históricos',
            'subtitulo' => 'Prensados Originales 180g, Matrices Mono y Sellos de Época',
            'badge' => 'CATÁLOGO FONOGRÁFICO · VINILOS',
            'icono' => 'album',
            'descripcion' => 'Ediciones príncipe de jazz, rock clásico y vanguardia latinoamericana graduadas bajo estándar internacional Goldmine.',
        ],
        'memorabilia' => [
            'titulo' => 'Memorabilia & Archivo',
            'subtitulo' => 'Manuscritos, Correspondencia Diplomática y Obra Gráfica',
            'badge' => 'ARCHIVO DOCUMENTAL · MEMORABILIA',
            'icono' => 'history_edu',
            'descripcion' => 'Documentos autógrafos, partituras manuscritas y fotografías de época en plata sobre gelatina con Certificado de Autenticidad (COA) notariado.',
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
     * Página principal de la tienda pública (Catálogo General y Galería).
     */
    public function home(Request $request): View
    {
        $productos = $this->consultarProductos($request);

        return view('public.index', [
            'productos' => $productos,
            'categoriaActiva' => 'inicio',
        ]);
    }

    /**
     * Sección: Libros Raros & Incunables (filtra exclusivamente libros).
     */
    public function libros(Request $request): View
    {
        $productos = $this->consultarProductos($request, 'libros');

        return view('public.categoria', [
            'categoriaSlug' => 'libros',
            'meta' => self::CATEGORIAS['libros'],
            'productos' => $productos,
            'categoriaActiva' => 'libros',
        ]);
    }

    /**
     * Sección: Discos & Vinilos Históricos (filtra exclusivamente discos/vinilos y renderiza el 3D Disc Showcase).
     */
    public function vinilos(Request $request): View
    {
        $productos = $this->consultarProductos($request, 'vinilos');

        return view('public.vinilos', [
            'categoriaSlug' => 'vinilos',
            'meta' => self::CATEGORIAS['vinilos'],
            'productos' => $productos,
            'categoriaActiva' => 'vinilos',
        ]);
    }

    /**
     * Sección: Memorabilia & Archivo (filtra exclusivamente coleccionables/memorabilia).
     */
    public function memorabilia(Request $request): View
    {
        $productos = $this->consultarProductos($request, 'memorabilia');

        return view('public.categoria', [
            'categoriaSlug' => 'memorabilia',
            'meta' => self::CATEGORIAS['memorabilia'],
            'productos' => $productos,
            'categoriaActiva' => 'memorabilia',
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
        }

        $query = Producto::query()
            ->with(['catalogo', 'libro', 'disco', 'coleccionable', 'variantesSku.existencias'])
            ->where('activo', true);

        if ($categoria === 'libros') {
            $query->whereHas('catalogo', fn (Builder $q) => $q->where('tipo', 'libro'));
        } elseif ($categoria === 'vinilos') {
            $query->whereHas('catalogo', fn (Builder $q) => $q->where('tipo', 'disco'));
        } elseif ($categoria === 'memorabilia') {
            $query->whereHas('catalogo', fn (Builder $q) => $q->where('tipo', 'coleccionable'));
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
}
