<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Catalogo;
use App\Models\Coleccionable;
use App\Models\Disco;
use App\Models\Existencia;
use App\Models\Libro;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\VarianteSku;
use App\Services\SucursalContextoService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductoController extends Controller
{
    /**
     * Listado maestro de productos con filtros rápidos por estado (Activos / Dados de Baja / Todos),
     * búsqueda por SKU/Nombre y catálogo, accesible para Administradores y Empleados.
     */
    public function index(Request $request): View|JsonResponse
    {
        $estadoFiltro = $request->string('estado')->lower()->trim()->toString() ?: 'activos';

        $baseQuery = Producto::query()
            ->with([
                'catalogo',
                'variantesSku.existencias.sucursal',
                'libro',
                'disco',
                'coleccionable',
            ])
            ->withCount('variantesSku');

        // Métricas de estado para las pestañas de filtro rápido
        $totalProductos = (int) Producto::count();
        $totalActivos = (int) Producto::where('activo', true)->count();
        $totalBaja = (int) Producto::where('activo', false)->count();

        // Aplicación del filtro por estado
        if ($estadoFiltro === 'activos') {
            $baseQuery->where('activo', true);
        } elseif ($estadoFiltro === 'baja' || $estadoFiltro === 'inactivos') {
            $baseQuery->where('activo', false);
        }

        // Filtro por término de búsqueda (nombre, SKU o autor/artista)
        if ($buscar = $request->string('buscar')->trim()->toString()) {
            $baseQuery->where(function (Builder $q) use ($buscar): void {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%")
                    ->orWhereHas('variantesSku', fn (Builder $sku) => $sku->where('sku', 'like', "%{$buscar}%"))
                    ->orWhereHas('libro', fn (Builder $l) => $l->where('autor', 'like', "%{$buscar}%")->orWhere('editorial', 'like', "%{$buscar}%"))
                    ->orWhereHas('disco', fn (Builder $d) => $d->where('artista', 'like', "%{$buscar}%")->orWhere('sello', 'like', "%{$buscar}%"))
                    ->orWhereHas('coleccionable', fn (Builder $c) => $c->where('marca', 'like', "%{$buscar}%"));
            });
        }

        // Filtro por Catálogo específico
        if ($catalogoId = $request->input('catalogo_id')) {
            $baseQuery->where('catalogo_id', $catalogoId);
        }

        $productos = $baseQuery
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $catalogos = Schema::hasTable('catalogos')
            ? Catalogo::where('activo', true)->orderBy('nombre')->get()
            : collect();

        $sucursales = Schema::hasTable('sucursales')
            ? Sucursal::where('activa', true)->orderBy('id')->get()
            : collect();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => $productos,
                'metricas' => [
                    'total' => $totalProductos,
                    'activos' => $totalActivos,
                    'baja' => $totalBaja,
                ],
            ]);
        }

        return view('admin.productos.index', [
            'productos' => $productos,
            'catalogos' => $catalogos,
            'sucursales' => $sucursales,
            'estadoFiltro' => $estadoFiltro,
            'totalProductos' => $totalProductos,
            'totalActivos' => $totalActivos,
            'totalBaja' => $totalBaja,
        ]);
    }

    /**
     * Muestra el formulario para dar de alta un nuevo producto.
     */
    public function create(Request $request): View
    {
        $catalogos = Schema::hasTable('catalogos')
            ? Catalogo::where('activo', true)->orderBy('nombre')->get()
            : collect();

        $sucursales = Schema::hasTable('sucursales')
            ? Sucursal::where('activa', true)->orderBy('id')->get()
            : collect();

        $sucursalActivaId = $request->session()->get(SucursalContextoService::SESSION_SUCURSAL_KEY)
            ?: $sucursales->first()?->id;

        return view('admin.productos.create', [
            'catalogos' => $catalogos,
            'sucursales' => $sucursales,
            'sucursalActivaId' => $sucursalActivaId,
        ]);
    }

    /**
     * Procesa y registra el alta de un nuevo producto, su variante SKU,
     * ficha especializada, existencia física inicial y auditoría en bitácora.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->has('isbn') && (! is_string($request->input('isbn')) || trim((string) $request->input('isbn')) === '')) {
            $request->merge(['isbn' => null]);
        }

        $validated = $request->validate([
            'catalogo_id' => ['required', 'integer', 'exists:catalogos,id'],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:5000'],
            'precio' => ['required', 'numeric', 'min:0'],
            'sku' => ['required', 'string', 'max:100', 'unique:variantes_sku,sku'],
            'estado_conservacion' => ['nullable', 'string', 'max:100'],
            'imagen' => ['nullable', 'image', 'max:4096'],
            'sucursal_id' => ['nullable', 'integer', 'exists:sucursales,id'],
            'stock_inicial' => ['nullable', 'integer', 'min:1', 'max:10000'],

            // Metadatos de Libros
            'autor' => ['nullable', 'string', 'max:255'],
            'editorial' => ['nullable', 'string', 'max:255'],
            'genero' => ['nullable', 'string', 'max:100'],
            'anio_publicacion' => ['nullable', 'integer', 'between:1800,2030'],
            'paginas' => ['nullable', 'integer', 'min:1', 'max:20000'],
            'isbn' => ['nullable', 'string', 'max:32', Rule::unique('libros', 'isbn')],
            'tapa' => ['nullable', 'string', 'max:100'],

            // Metadatos de Discos
            'artista' => ['nullable', 'string', 'max:255'],
            'sello_discografico' => ['nullable', 'string', 'max:255'],
            'disquera' => ['nullable', 'string', 'max:255'],
            'formato' => ['nullable', 'string', 'max:100'],
            'anio_lanzamiento' => ['nullable', 'integer', 'between:1900,2030'],
            'rpm' => ['nullable', 'string', 'max:50'],

            // Metadatos de Coleccionables
            'fabricante' => ['nullable', 'string', 'max:255'],
            'serie' => ['nullable', 'string', 'max:255'],
            'material' => ['nullable', 'string', 'max:255'],
            'categoria' => ['nullable', 'string', 'max:100'],
            'anio_edicion' => ['nullable', 'integer', 'between:1900,2030'],
        ], [
            'imagen.image' => 'El archivo adjunto debe ser una imagen válida (JPG, PNG, WEBP).',
            'imagen.max' => 'La fotografía del producto no debe exceder 4MB.',
            'sku.unique' => 'Este código SKU ya está registrado en el inventario.',
            'isbn.unique' => 'Este código ISBN ya pertenece a otro libro registrado.',
        ]);

        $imagenRuta = null;
        if ($request->hasFile('imagen')) {
            $archivo = $request->file('imagen');
            $nombreArchivo = Str::random(40).'.'.$archivo->guessExtension();
            $directorioDestino = public_path('images/productos');
            File::ensureDirectoryExists($directorioDestino);
            $archivo->move($directorioDestino, $nombreArchivo);
            $imagenRuta = 'images/productos/'.$nombreArchivo;
        }

        DB::transaction(function () use ($validated, $request, $imagenRuta): void {
            $catalogo = Catalogo::find($validated['catalogo_id']);
            $descripcion = $validated['descripcion'] ?? $validated['nombre'];
            $graduacion = $validated['estado_conservacion'] ?: 'MINT (M)';

            $datosProducto = [
                'catalogo_id' => $validated['catalogo_id'],
                'nombre' => $validated['nombre'],
                'descripcion' => $descripcion,
                'precio' => $validated['precio'],
                'activo' => true,
            ];
            if ($imagenRuta !== null && Schema::hasColumn('productos', 'imagen')) {
                $datosProducto['imagen'] = $imagenRuta;
            }

            $producto = Producto::create($datosProducto);

            // Variante SKU inicial
            $datosVariante = [
                'producto_id' => $producto->id,
                'sku' => strtoupper(trim($validated['sku'])),
                'descripcion' => "{$graduacion} · {$validated['nombre']}",
                'precio' => $validated['precio'],
                'activa' => true,
            ];
            if ($imagenRuta !== null && Schema::hasColumn('variantes_sku', 'imagen')) {
                $datosVariante['imagen'] = $imagenRuta;
            }

            $variante = VarianteSku::create($datosVariante);

            // Sincronización de ficha especializada según tipo de catálogo
            if ($catalogo) {
                $tipoCatalogo = strtolower((string) $catalogo->tipo);
                $nombreCatalogo = strtolower((string) $catalogo->nombre);
                $esVinilo = $tipoCatalogo === 'disco' || $tipoCatalogo === 'vinilo' || str_contains($nombreCatalogo, 'disco') || str_contains($nombreCatalogo, 'vinil');
                $esLibro = $tipoCatalogo === 'libro' || str_contains($nombreCatalogo, 'libro');
                $esColeccionable = $tipoCatalogo === 'coleccionable' || str_contains($nombreCatalogo, 'coleccion');

                if ($esVinilo && Schema::hasTable('discos')) {
                    $formatoNormalizado = 'vinilo';
                    $formatoRaw = strtolower((string) ($request->input('formato') ?: 'vinilo'));
                    if (str_contains($formatoRaw, 'cd')) {
                        $formatoNormalizado = 'CD';
                    } elseif (str_contains($formatoRaw, 'cassette')) {
                        $formatoNormalizado = 'cassette';
                    }

                    $sello = $request->input('sello_discografico') ?: ($request->input('disquera') ?: 'Prensado Curatorial');
                    $datosDisco = [
                        'artista' => $request->input('artista') ?: $producto->nombre,
                        'sello' => $sello,
                        'genero' => $request->input('genero') ?: 'Música Histórica',
                        'formato' => $formatoNormalizado,
                        'anio_lanzamiento' => $request->filled('anio_lanzamiento') ? (int) $request->input('anio_lanzamiento') : (int) date('Y'),
                        'rpm' => $request->input('rpm') ?: ($formatoNormalizado === 'CD' ? 'PCM' : '33 RPM'),
                    ];
                    if ($imagenRuta !== null && Schema::hasColumn('discos', 'imagen')) {
                        $datosDisco['imagen'] = $imagenRuta;
                    }
                    Disco::updateOrCreate(['producto_id' => $producto->id], $datosDisco);
                } elseif ($esLibro && Schema::hasTable('libros')) {
                    $isbn = filled($request->input('isbn')) ? trim((string) $request->input('isbn')) : null;
                    $datosLibro = [
                        'autor' => $request->input('autor') ?: 'Autor Histórico',
                        'editorial' => $request->input('editorial') ?: 'Edición Notariada',
                        'genero' => $request->input('genero') ?: 'novela',
                        'anio_publicacion' => $request->filled('anio_publicacion') ? (int) $request->input('anio_publicacion') : (int) date('Y'),
                        'paginas' => $request->filled('paginas') ? (int) $request->input('paginas') : 250,
                        'isbn' => $isbn,
                        'tapa' => $request->input('tapa') ?: 'Tapa dura artesanal',
                    ];
                    if ($imagenRuta !== null && Schema::hasColumn('libros', 'imagen')) {
                        $datosLibro['imagen'] = $imagenRuta;
                    }
                    Libro::updateOrCreate(['producto_id' => $producto->id], $datosLibro);
                } elseif ($esColeccionable && Schema::hasTable('coleccionables')) {
                    $datosColeccionable = [
                        'marca' => $request->input('fabricante') ?: ($request->input('autor') ?: 'Manufactura Histórica'),
                        'serie' => $request->input('serie') ?: 'Edición Limitada',
                        'categoria' => $request->input('categoria') ?: 'figuras',
                        'material' => $request->input('material') ?: 'Pieza Autenticada',
                        'edicion_limitada' => true,
                    ];
                    if ($imagenRuta !== null && Schema::hasColumn('coleccionables', 'imagen')) {
                        $datosColeccionable['imagen'] = $imagenRuta;
                    }
                    Coleccionable::updateOrCreate(['producto_id' => $producto->id], $datosColeccionable);
                }
            }

            // Asignación de existencia en sucursales
            $sucursales = Schema::hasTable('sucursales')
                ? Sucursal::where('activa', true)->get()
                : collect();

            if ($sucursales->isNotEmpty()) {
                $sucursalSeleccionadaId = ! empty($validated['sucursal_id'])
                    ? (int) $validated['sucursal_id']
                    : ($request->session()->get(SucursalContextoService::SESSION_SUCURSAL_KEY)
                        ?: $sucursales->first()->id);

                $cantidadInicial = ! empty($validated['stock_inicial']) ? (int) $validated['stock_inicial'] : 1;

                foreach ($sucursales as $suc) {
                    $cant = ($suc->id == $sucursalSeleccionadaId) ? $cantidadInicial : 0;

                    $existencia = Existencia::firstOrCreate(
                        [
                            'variante_sku_id' => $variante->id,
                            'sucursal_id' => $suc->id,
                        ],
                        [
                            'cantidad' => $cant,
                            'stock_minimo' => 1,
                        ]
                    );

                    if ($cant > 0 && Schema::hasTable('movimientos_inventario')) {
                        MovimientoInventario::create([
                            'existencia_id' => $existencia->id,
                            'usuario_id' => $request->user()?->id,
                            'tipo' => 'entrada',
                            'cantidad' => $cant,
                            'motivo' => "Alta de producto [{$validated['sku']}] por {$request->user()?->nombre}",
                            'created_at' => now(),
                        ]);
                    }
                }
            }

            // Registro de auditoría estricto en Bitácora
            Bitacora::create([
                'usuario_id' => $request->user()?->id,
                'accion' => 'ALTA_PRODUCTO',
                'tabla_afectada' => 'productos',
                'registro_id' => $producto->id,
                'detalle' => [
                    'nombre' => $producto->nombre,
                    'sku' => $validated['sku'],
                    'precio' => $producto->precio,
                    'usuario_nombre' => $request->user()?->nombre,
                    'usuario_rol' => $request->user()?->roles->pluck('nombre')->all(),
                    'imagen' => $imagenRuta,
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.productos.index')
            ->with('status', "Producto «{$validated['nombre']}» (SKU: {$validated['sku']}) dado de alta exitosamente.");
    }

    /**
     * Muestra el formulario para editar un producto existente.
     */
    public function edit(Producto $producto): View
    {
        $producto->loadMissing([
            'catalogo',
            'libro',
            'disco',
            'coleccionable',
            'variantesSku.existencias.sucursal',
        ]);

        $catalogos = Schema::hasTable('catalogos')
            ? Catalogo::where('activo', true)->orderBy('nombre')->get()
            : collect();

        $sucursales = Schema::hasTable('sucursales')
            ? Sucursal::where('activa', true)->orderBy('id')->get()
            : collect();

        return view('admin.productos.edit', [
            'producto' => $producto,
            'catalogos' => $catalogos,
            'sucursales' => $sucursales,
        ]);
    }

    /**
     * Actualiza la información de un producto, su variante SKU y ficha especializada.
     */
    public function update(Request $request, Producto $producto): RedirectResponse
    {
        $variante = $producto->variantesSku->first();

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:5000'],
            'precio' => ['required', 'numeric', 'min:0'],
            'sku' => ['required', 'string', 'max:100', Rule::unique('variantes_sku', 'sku')->ignore($variante?->id)],
            'imagen' => ['nullable', 'image', 'max:4096'],

            // Metadatos
            'autor' => ['nullable', 'string', 'max:255'],
            'editorial' => ['nullable', 'string', 'max:255'],
            'genero' => ['nullable', 'string', 'max:100'],
            'artista' => ['nullable', 'string', 'max:255'],
            'sello_discografico' => ['nullable', 'string', 'max:255'],
            'fabricante' => ['nullable', 'string', 'max:255'],
        ]);

        $imagenRuta = null;
        if ($request->hasFile('imagen')) {
            $archivo = $request->file('imagen');
            $nombreArchivo = Str::random(40).'.'.$archivo->guessExtension();
            $directorioDestino = public_path('images/productos');
            File::ensureDirectoryExists($directorioDestino);
            $archivo->move($directorioDestino, $nombreArchivo);
            $imagenRuta = 'images/productos/'.$nombreArchivo;
        }

        DB::transaction(function () use ($validated, $request, $producto, $variante, $imagenRuta): void {
            $datosProducto = [
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'] ?? $producto->descripcion,
                'precio' => $validated['precio'],
            ];
            if ($imagenRuta !== null && Schema::hasColumn('productos', 'imagen')) {
                $datosProducto['imagen'] = $imagenRuta;
            }

            $producto->update($datosProducto);

            if ($variante) {
                $datosVariante = [
                    'sku' => strtoupper(trim($validated['sku'])),
                    'precio' => $validated['precio'],
                ];
                if ($imagenRuta !== null && Schema::hasColumn('variantes_sku', 'imagen')) {
                    $datosVariante['imagen'] = $imagenRuta;
                }
                $variante->update($datosVariante);
            }

            // Actualizar metadatos según ficha
            if ($producto->libro) {
                $producto->libro->update(array_filter([
                    'autor' => $request->input('autor'),
                    'editorial' => $request->input('editorial'),
                    'genero' => $request->input('genero'),
                ]));
            } elseif ($producto->disco) {
                $producto->disco->update(array_filter([
                    'artista' => $request->input('artista'),
                    'sello' => $request->input('sello_discografico'),
                    'genero' => $request->input('genero'),
                ]));
            } elseif ($producto->coleccionable) {
                $producto->coleccionable->update(array_filter([
                    'marca' => $request->input('fabricante'),
                ]));
            }

            Bitacora::create([
                'usuario_id' => $request->user()?->id,
                'accion' => 'EDICION_PRODUCTO',
                'tabla_afectada' => 'productos',
                'registro_id' => $producto->id,
                'detalle' => [
                    'nombre' => $producto->nombre,
                    'sku' => $validated['sku'],
                    'precio' => $producto->precio,
                    'usuario_nombre' => $request->user()?->nombre,
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.productos.index')
            ->with('status', "Producto «{$validated['nombre']}» actualizado correctamente.");
    }

    /**
     * Alterna el estado activo/baja de un producto (Dar de Baja o Reactivar).
     * Oculta o habilita inmediatamente el producto en el catálogo público sin borrar historiales.
     */
    public function toggleEstado(Request $request, Producto $producto): RedirectResponse|JsonResponse
    {
        $estadoAnterior = (bool) $producto->activo;
        $nuevoEstado = $request->has('activo')
            ? (bool) $request->input('activo')
            : ! $estadoAnterior;

        $producto->activo = $nuevoEstado;
        $producto->save();

        // Cascada a sus variantes SKU para consistencia de tienda
        $producto->variantesSku()->update(['activa' => $nuevoEstado]);

        $accionAudit = $nuevoEstado ? 'REACTIVACION_PRODUCTO' : 'BAJA_PRODUCTO';
        $mensaje = $nuevoEstado
            ? "Producto «{$producto->nombre}» reactivado y habilitado exitosamente en el catálogo público."
            : "Producto «{$producto->nombre}» dado de baja y ocultado de la tienda pública sin borrar su historial.";

        Bitacora::create([
            'usuario_id' => $request->user()?->id,
            'accion' => $accionAudit,
            'tabla_afectada' => 'productos',
            'registro_id' => $producto->id,
            'detalle' => [
                'nombre' => $producto->nombre,
                'estado_anterior' => $estadoAnterior ? 'activo' : 'baja',
                'nuevo_estado' => $nuevoEstado ? 'activo' : 'baja',
                'usuario_nombre' => $request->user()?->nombre,
                'usuario_rol' => $request->user()?->roles->pluck('nombre')->all(),
            ],
            'ip' => $request->ip() ?? '127.0.0.1',
            'created_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'activo' => $nuevoEstado,
                'message' => $mensaje,
            ]);
        }

        return back()->with('status', $mensaje);
    }

    /**
     * Acción explícita para Dar de Baja un producto.
     */
    public function baja(Request $request, Producto $producto): RedirectResponse|JsonResponse
    {
        $request->merge(['activo' => false]);

        return $this->toggleEstado($request, $producto);
    }

    /**
     * Acción explícita para Reactivar / Dar de Alta un producto previamente dado de baja.
     */
    public function reactivar(Request $request, Producto $producto): RedirectResponse|JsonResponse
    {
        $request->merge(['activo' => true]);

        return $this->toggleEstado($request, $producto);
    }

    /**
     * Actualiza la fotografía de portada de un producto existente.
     */
    public function actualizarImagen(Request $request, Producto $producto): RedirectResponse
    {
        $request->validate([
            'imagen' => ['required', 'image', 'max:4096'],
        ], [
            'imagen.required' => 'Debe seleccionar un archivo de imagen.',
            'imagen.image' => 'El archivo debe ser una imagen válida (JPG, PNG, WEBP).',
            'imagen.max' => 'La imagen no debe superar los 4MB.',
        ]);

        $archivo = $request->file('imagen');
        $nombreArchivo = Str::random(40).'.'.$archivo->guessExtension();
        $directorioDestino = public_path('images/productos');
        File::ensureDirectoryExists($directorioDestino);
        $archivo->move($directorioDestino, $nombreArchivo);
        $imagenRuta = 'images/productos/'.$nombreArchivo;

        $producto->update(['imagen' => $imagenRuta]);
        $producto->variantesSku()->update(['imagen' => $imagenRuta]);

        if ($producto->disco && Schema::hasColumn('discos', 'imagen')) {
            $producto->disco->update(['imagen' => $imagenRuta]);
        } elseif ($producto->libro && Schema::hasColumn('libros', 'imagen')) {
            $producto->libro->update(['imagen' => $imagenRuta]);
        } elseif ($producto->coleccionable && Schema::hasColumn('coleccionables', 'imagen')) {
            $producto->coleccionable->update(['imagen' => $imagenRuta]);
        }

        Bitacora::create([
            'usuario_id' => $request->user()?->id,
            'accion' => 'ACTUALIZACION_IMAGEN_PRODUCTO',
            'tabla_afectada' => 'productos',
            'registro_id' => $producto->id,
            'detalle' => [
                'nombre' => $producto->nombre,
                'nueva_imagen' => $imagenRuta,
                'usuario_nombre' => $request->user()?->nombre,
            ],
            'ip' => $request->ip() ?? '127.0.0.1',
            'created_at' => now(),
        ]);

        return back()->with('status', "Fotografía de «{$producto->nombre}» actualizada exitosamente.");
    }

    /**
     * Oculta/desactiva el producto sin borrar el historial ni relaciones.
     */
    public function destroy(Request $request, Producto $producto): RedirectResponse
    {
        return $this->baja($request, $producto);
    }
}
