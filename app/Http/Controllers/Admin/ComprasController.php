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
use App\Models\Proveedor;
use App\Models\Sucursal;
use App\Models\VarianteSku;
use App\Services\OrdenCompraProveedorService;
use App\Services\SucursalContextoService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ComprasController extends Controller
{
    /**
     * Catálogo de productos y variantes (altas de productos por Compras).
     */
    public function productos(Request $request): View
    {
        $productos = Producto::query()
            ->with(['catalogo', 'variantesSku'])
            ->withCount('variantesSku')
            ->when($request->filled('buscar'), fn (Builder $q) => $q->where('nombre', 'like', '%'.$request->string('buscar')->trim().'%'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $sucursales = Schema::hasTable('sucursales')
            ? Sucursal::where('activa', true)->orderBy('id')->get()
            : collect();

        $sucursalActivaId = $request->session()->get(SucursalContextoService::SESSION_SUCURSAL_KEY)
            ?: $sucursales->first()?->id;

        $vista = view()->exists('compras.productos.index')
            ? 'compras.productos.index'
            : 'admin.compras.productos';

        return view($vista, [
            'productos' => $productos,
            'catalogos' => Catalogo::where('activo', true)->orderBy('nombre')->get(),
            'sucursales' => $sucursales,
            'sucursalActivaId' => $sucursalActivaId,
        ]);
    }

    /**
     * Alta de un producto con su variante SKU inicial, soporte para subida de imagen y existencia inicial.
     */
    public function storeProducto(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'catalogo_id' => ['required', 'integer', 'exists:catalogos,id'],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'precio' => ['required', 'numeric', 'min:0'],
            'sku' => ['required', 'string', 'max:100', 'unique:variantes_sku,sku'],
            'imagen' => ['nullable', 'image', 'max:4096'],
            'sucursal_id' => ['nullable', 'integer', 'exists:sucursales,id'],
            'cantidad' => ['nullable', 'integer', 'min:1'],
        ], [
            'imagen.image' => 'El archivo adjunto debe ser una imagen válida.',
            'imagen.max' => 'La imagen del producto no debe superar los 4MB.',
        ]);

        $imagenRuta = null;
        if ($request->hasFile('imagen')) {
            $path = Storage::disk('public')->putFile('productos', $request->file('imagen'));
            $imagenRuta = 'storage/'.$path;
        }

        DB::transaction(function () use ($validated, $request, $imagenRuta): void {
            $catalogo = Catalogo::find($validated['catalogo_id']);

            $producto = Producto::create([
                'catalogo_id' => $validated['catalogo_id'],
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'] ?? $validated['nombre'],
                'precio' => $validated['precio'],
                'activo' => true,
                'imagen' => $imagenRuta,
            ]);

            $variante = VarianteSku::create([
                'producto_id' => $producto->id,
                'sku' => $validated['sku'],
                'descripcion' => $validated['nombre'],
                'precio' => $validated['precio'],
                'activa' => true,
                'imagen' => $imagenRuta,
            ]);

            // Sincronizar ficha especializada según el tipo de catálogo
            if ($catalogo) {
                if ($catalogo->tipo === 'disco' && Schema::hasTable('discos')) {
                    Disco::firstOrCreate(
                        ['producto_id' => $producto->id],
                        [
                            'artista' => $producto->nombre,
                            'sello' => 'Curaduría ARCHIVUM',
                            'genero' => 'Fonoteca Histórica',
                            'formato' => 'vinilo',
                            'anio_lanzamiento' => (int) date('Y'),
                            'imagen' => $imagenRuta,
                        ]
                    );
                } elseif ($catalogo->tipo === 'libro' && Schema::hasTable('libros')) {
                    Libro::firstOrCreate(
                        ['producto_id' => $producto->id],
                        [
                            'autor' => 'Curaduría ARCHIVUM',
                            'editorial' => 'Ediciones ARCHIVUM',
                            'genero' => 'novela',
                            'anio_publicacion' => (int) date('Y'),
                            'paginas' => 100,
                            'imagen' => $imagenRuta,
                        ]
                    );
                } elseif ($catalogo->tipo === 'coleccionable' && Schema::hasTable('coleccionables')) {
                    Coleccionable::firstOrCreate(
                        ['producto_id' => $producto->id],
                        [
                            'marca' => 'ARCHIVUM Histórico',
                            'serie' => 'Colección Exclusiva',
                            'categoria' => 'figuras',
                            'material' => 'Edición Autenticada',
                            'edicion_limitada' => true,
                            'imagen' => $imagenRuta,
                        ]
                    );
                }
            }

            // Crear existencia inicial con al menos 1 unidad para que aparezca de inmediato en el catálogo del Sitio Público
            $sucursales = Schema::hasTable('sucursales')
                ? Sucursal::where('activa', true)->get()
                : collect();

            if ($sucursales->isEmpty() && Schema::hasTable('sucursales')) {
                $sucursalDefault = Sucursal::firstOrCreate(
                    ['nombre' => 'Sucursal Central Bellas Artes'],
                    [
                        'direccion' => 'Av. Juárez 42, Centro Histórico, CDMX',
                        'telefono' => '5555124200',
                        'activa' => true,
                    ]
                );
                $sucursales = collect([$sucursalDefault]);
            }

            if ($sucursales->isNotEmpty()) {
                $sucursalSeleccionadaId = ! empty($validated['sucursal_id'])
                    ? (int) $validated['sucursal_id']
                    : ($request->session()->get(SucursalContextoService::SESSION_SUCURSAL_KEY)
                        ?: $sucursales->first()->id);

                $cantidadInicial = ! empty($validated['cantidad']) ? (int) $validated['cantidad'] : 5;

                foreach ($sucursales as $suc) {
                    $cant = ($suc->id == $sucursalSeleccionadaId) ? $cantidadInicial : 2;

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

                    if (Schema::hasTable('movimientos_inventario')) {
                        MovimientoInventario::create([
                            'existencia_id' => $existencia->id,
                            'usuario_id' => $request->user()?->id,
                            'tipo' => 'entrada',
                            'cantidad' => $cant,
                            'motivo' => "Alta inicial de catálogo [{$validated['sku']}]",
                            'created_at' => now(),
                        ]);
                    }
                }
            }

            Bitacora::create([
                'usuario_id' => $request->user()->id,
                'accion' => $imagenRuta ? 'PRODUCTO_CREADO_CON_IMAGEN' : 'PRODUCTO_CREADO',
                'tabla_afectada' => 'productos',
                'registro_id' => $producto->id,
                'detalle' => [
                    'nombre' => $producto->nombre,
                    'sku' => $validated['sku'],
                    'imagen' => $imagenRuta,
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);
        });

        return redirect()->route('compras.productos')->with('status', "Producto '{$validated['nombre']}' dado de alta correctamente.");
    }

    /**
     * Actualiza la imagen de portada de un producto existente y sus variantes SKU.
     */
    public function actualizarImagen(Request $request, Producto $producto): RedirectResponse
    {
        $request->validate([
            'imagen' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'imagen.required' => 'Debe seleccionar una imagen para actualizar el producto.',
            'imagen.image' => 'El archivo seleccionado debe ser una imagen válida.',
            'imagen.mimes' => 'La imagen debe tener formato JPG, JPEG, PNG o WEBP.',
            'imagen.max' => 'La imagen no debe superar los 4MB.',
        ]);

        $path = Storage::disk('public')->putFile('productos', $request->file('imagen'));
        $nuevaRuta = 'storage/'.$path;

        if ($producto->imagen && str_starts_with($producto->imagen, 'storage/productos/')) {
            $viejoPath = str_replace('storage/', '', $producto->imagen);
            Storage::disk('public')->delete($viejoPath);
        }

        DB::transaction(function () use ($producto, $nuevaRuta, $request): void {
            $producto->update(['imagen' => $nuevaRuta]);

            $producto->variantesSku()->update(['imagen' => $nuevaRuta]);

            Bitacora::create([
                'usuario_id' => $request->user()->id,
                'accion' => 'PRODUCTO_IMAGEN_ACTUALIZADA',
                'tabla_afectada' => 'productos',
                'registro_id' => $producto->id,
                'detalle' => [
                    'nombre' => $producto->nombre,
                    'imagen' => $nuevaRuta,
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);
        });

        return redirect()->route('compras.productos')->with('status', "Imagen de '{$producto->nombre}' actualizada correctamente.");
    }

    /**
     * Descarga de plantilla CSV oficial para importación masiva de catálogo e inventario.
     */
    public function descargarPlantillaCsv(): StreamedResponse
    {
        $cabeceras = "sku,nombre,catalogo,precio,sucursal,cantidad\n";
        $ejemplos = "LIB-2026-001,Rayuela (Primera Edición Sudamericana),Libros,45000.00,Sucursal Central Bellas Artes,2\n"
                  ."DIS-2026-002,Kind of Blue 180g Original Pressing,Discos,18500.00,Gabinete Coyoacán,4\n"
                  ."COL-2026-003,Figura Evangelion EVA-01 Coleccionista,Coleccionables,12500.00,Sucursal Central Bellas Artes,1\n";

        $contenido = "\xEF\xBB\xBF".$cabeceras.$ejemplos; // UTF-8 BOM para compatibilidad con Excel

        return response()->streamDownload(function () use ($contenido): void {
            echo $contenido;
        }, 'plantilla_importacion_archivum.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    /**
     * Procesa la importación masiva de productos e inventario desde un archivo CSV.
     */
    public function importarCsv(Request $request): RedirectResponse
    {
        $request->validate([
            'archivo_csv' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ], [
            'archivo_csv.required' => 'Debe adjuntar un archivo CSV para proceder con la importación.',
            'archivo_csv.file' => 'El archivo proporcionado no es un archivo válido.',
            'archivo_csv.mimes' => 'El archivo debe tener formato .csv o .txt delimitado por comas.',
            'archivo_csv.max' => 'El archivo no puede superar los 2 MB de tamaño.',
        ]);

        $archivo = $request->file('archivo_csv');
        if (! $archivo || ! $archivo->isValid()) {
            return back()->withErrors(['archivo_csv' => 'No fue posible leer el archivo subido.']);
        }

        $rutaReal = $archivo->getRealPath();
        $handle = fopen($rutaReal, 'r');
        if ($handle === false) {
            return back()->withErrors(['archivo_csv' => 'Error al abrir el archivo CSV en el servidor.']);
        }

        // Leer primera fila (encabezados)
        $primeraFila = fgetcsv($handle, 2000, ',');
        if (! $primeraFila) {
            fclose($handle);

            return back()->withErrors(['archivo_csv' => 'El archivo CSV está vacío.']);
        }

        // Limpiar BOM UTF-8 y normalizar nombres de columna en minúsculas
        $primeraFila[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $primeraFila[0]);
        $mapaColumnas = [];
        foreach ($primeraFila as $indice => $nombreColumna) {
            $nombreLimpio = strtolower(trim((string) $nombreColumna));
            $mapaColumnas[$nombreLimpio] = (int) $indice;
        }

        $columnasRequeridas = ['sku', 'nombre', 'catalogo', 'precio', 'sucursal', 'cantidad'];
        foreach ($columnasRequeridas as $req) {
            if (! array_key_exists($req, $mapaColumnas)) {
                fclose($handle);

                return back()->withErrors([
                    'archivo_csv' => "La columna requerida '{$req}' no está presente en el encabezado del archivo CSV.",
                ]);
            }
        }

        // Catálogos y sucursales en memoria para validación rápida
        $catalogos = Catalogo::all();
        $sucursales = Sucursal::where('activa', true)->get();

        $filasValidadas = [];
        $erroresFilas = [];
        $numeroFila = 1; // 1 fue el encabezado

        while (($datosFila = fgetcsv($handle, 2000, ',')) !== false) {
            $numeroFila++;

            // Omitir filas en blanco
            if (empty(array_filter($datosFila, fn ($v) => trim((string) $v) !== ''))) {
                continue;
            }

            $sku = trim((string) ($datosFila[$mapaColumnas['sku']] ?? ''));
            $nombre = trim((string) ($datosFila[$mapaColumnas['nombre']] ?? ''));
            $catalogoRaw = trim((string) ($datosFila[$mapaColumnas['catalogo']] ?? ''));
            $precioRaw = trim((string) ($datosFila[$mapaColumnas['precio']] ?? ''));
            $sucursalRaw = trim((string) ($datosFila[$mapaColumnas['sucursal']] ?? ''));
            $cantidadRaw = trim((string) ($datosFila[$mapaColumnas['cantidad']] ?? ''));

            // Validaciones por fila
            if ($sku === '') {
                $erroresFilas[] = "Fila {$numeroFila}: El campo SKU no puede estar vacío.";
            }

            if ($nombre === '') {
                $erroresFilas[] = "Fila {$numeroFila}: El nombre del producto es obligatorio.";
            }

            $catalogoEncontrado = $catalogos->first(function ($c) use ($catalogoRaw) {
                return (string) $c->id === $catalogoRaw
                    || strcasecmp($c->nombre, $catalogoRaw) === 0
                    || strcasecmp($c->tipo ?? '', $catalogoRaw) === 0;
            });
            if (! $catalogoEncontrado) {
                $erroresFilas[] = "Fila {$numeroFila}: El catálogo '{$catalogoRaw}' no existe en el sistema.";
            }

            if (! is_numeric($precioRaw) || (float) $precioRaw < 0) {
                $erroresFilas[] = "Fila {$numeroFila}: El precio '{$precioRaw}' debe ser un valor numérico mayor o igual a 0.";
            }

            $sucursalEncontrada = $sucursales->first(function ($s) use ($sucursalRaw) {
                return (string) $s->id === $sucursalRaw
                    || strcasecmp($s->nombre, $sucursalRaw) === 0;
            });
            if (! $sucursalEncontrada) {
                $erroresFilas[] = "Fila {$numeroFila}: La sucursal '{$sucursalRaw}' no fue localizada entre las sedes activas.";
            }

            if (! filter_var($cantidadRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) && $cantidadRaw !== '0') {
                $erroresFilas[] = "Fila {$numeroFila}: La cantidad '{$cantidadRaw}' debe ser un número entero mayor o igual a 0.";
            }

            if (empty($erroresFilas)) {
                $filasValidadas[] = [
                    'sku' => $sku,
                    'nombre' => $nombre,
                    'catalogo_id' => $catalogoEncontrado?->id,
                    'precio' => (float) $precioRaw,
                    'sucursal_id' => $sucursalEncontrada?->id,
                    'cantidad' => (int) $cantidadRaw,
                ];
            }
        }

        fclose($handle);

        if (! empty($erroresFilas)) {
            return back()->withInput()->withErrors([
                'importacion' => $erroresFilas,
            ]);
        }

        if (empty($filasValidadas)) {
            return back()->withErrors(['archivo_csv' => 'El archivo no contiene filas con datos para procesar.']);
        }

        $totalProcesados = 0;
        $totalUnidades = 0;
        $usuarioActual = $request->user();

        DB::transaction(function () use ($filasValidadas, $usuarioActual, $request, $archivo, &$totalProcesados, &$totalUnidades): void {
            foreach ($filasValidadas as $fila) {
                $variante = VarianteSku::where('sku', $fila['sku'])->first();

                if ($variante) {
                    $producto = $variante->producto;
                    $producto->update([
                        'precio' => $fila['precio'],
                    ]);
                    $variante->update([
                        'precio' => $fila['precio'],
                        'activa' => true,
                    ]);
                } else {
                    $producto = Producto::create([
                        'catalogo_id' => $fila['catalogo_id'],
                        'nombre' => $fila['nombre'],
                        'descripcion' => $fila['nombre'],
                        'precio' => $fila['precio'],
                        'activo' => true,
                    ]);

                    $variante = VarianteSku::create([
                        'producto_id' => $producto->id,
                        'sku' => $fila['sku'],
                        'descripcion' => $fila['nombre'],
                        'precio' => $fila['precio'],
                        'activa' => true,
                    ]);
                }

                $existencia = Existencia::firstOrCreate(
                    [
                        'variante_sku_id' => $variante->id,
                        'sucursal_id' => $fila['sucursal_id'],
                    ],
                    [
                        'cantidad' => 0,
                        'stock_minimo' => 2,
                    ]
                );

                if ($fila['cantidad'] > 0) {
                    $existencia->increment('cantidad', $fila['cantidad']);

                    MovimientoInventario::create([
                        'existencia_id' => $existencia->id,
                        'usuario_id' => $usuarioActual?->id,
                        'tipo' => 'entrada',
                        'cantidad' => $fila['cantidad'],
                        'motivo' => "Importación masiva CSV [{$fila['sku']}]",
                        'created_at' => now(),
                    ]);

                    $totalUnidades += $fila['cantidad'];
                }

                $totalProcesados++;
            }

            Bitacora::create([
                'usuario_id' => $usuarioActual?->id,
                'accion' => 'IMPORTACION_CSV',
                'tabla_afectada' => 'productos',
                'registro_id' => null,
                'detalle' => [
                    'filas_procesadas' => $totalProcesados,
                    'total_unidades' => $totalUnidades,
                    'archivo' => $archivo->getClientOriginalName(),
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);
        });

        return redirect()->route('compras.productos')->with(
            'status',
            "Importación CSV completada con éxito: {$totalProcesados} registros procesados y {$totalUnidades} unidades ingresadas a bóveda."
        );
    }

    /**
     * Directorio y catálogo CRUD de proveedores y casas editoriales.
     */
    public function proveedores(Request $request): View
    {
        $proveedores = Proveedor::query()
            ->when($request->filled('buscar'), function (Builder $q) use ($request): void {
                $termino = '%'.$request->string('buscar')->trim().'%';
                $q->where(function (Builder $sub) use ($termino): void {
                    $sub->where('nombre', 'like', $termino)
                        ->orWhere('razon_social', 'like', $termino)
                        ->orWhere('rfc', 'like', $termino)
                        ->orWhere('contacto_nombre', 'like', $termino)
                        ->orWhere('email', 'like', $termino);
                });
            })
            ->when($request->filled('estado'), function (Builder $q) use ($request): void {
                $estado = $request->string('estado')->toString();
                if ($estado === 'activos') {
                    $q->where('activo', true);
                } elseif ($estado === 'inactivos') {
                    $q->where('activo', false);
                }
            })
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('admin.compras.proveedores', [
            'proveedores' => $proveedores,
        ]);
    }

    /**
     * Da de alta un nuevo proveedor.
     */
    public function storeProveedor(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'razon_social' => ['nullable', 'string', 'max:255'],
            'rfc' => ['nullable', 'string', 'max:20', 'unique:proveedores,rfc'],
            'contacto_nombre' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'direccion' => ['nullable', 'string', 'max:500'],
        ]);

        $proveedor = DB::transaction(function () use ($validated, $request): Proveedor {
            $nuevo = Proveedor::create([
                ...$validated,
                'activo' => true,
            ]);

            Bitacora::create([
                'usuario_id' => $request->user()->id,
                'accion' => 'PROVEEDOR_CREADO',
                'tabla_afectada' => 'proveedores',
                'registro_id' => $nuevo->id,
                'detalle' => [
                    'nombre' => $nuevo->nombre,
                    'rfc' => $nuevo->rfc,
                    'contacto' => $nuevo->contacto_nombre,
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);

            return $nuevo;
        });

        return redirect()->route('compras.proveedores')->with('status', "Proveedor '{$proveedor->nombre}' dado de alta exitosamente.");
    }

    /**
     * Actualiza la información de un proveedor existente.
     */
    public function updateProveedor(Request $request, Proveedor $proveedor): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'razon_social' => ['nullable', 'string', 'max:255'],
            'rfc' => ['nullable', 'string', 'max:20', Rule::unique('proveedores', 'rfc')->ignore($proveedor->id)],
            'contacto_nombre' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'direccion' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($proveedor, $validated, $request): void {
            $proveedor->update($validated);

            Bitacora::create([
                'usuario_id' => $request->user()->id,
                'accion' => 'PROVEEDOR_ACTUALIZADO',
                'tabla_afectada' => 'proveedores',
                'registro_id' => $proveedor->id,
                'detalle' => [
                    'nombre' => $proveedor->nombre,
                    'rfc' => $proveedor->rfc,
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);
        });

        return redirect()->route('compras.proveedores')->with('status', "Proveedor '{$proveedor->nombre}' actualizado exitosamente.");
    }

    /**
     * Activa o desactiva lógicamente a un proveedor.
     */
    public function toggleProveedor(Request $request, Proveedor $proveedor): RedirectResponse
    {
        DB::transaction(function () use ($proveedor, $request): void {
            $proveedor->activo = ! $proveedor->activo;
            $proveedor->save();

            Bitacora::create([
                'usuario_id' => $request->user()->id,
                'accion' => 'PROVEEDOR_ESTADO_CAMBIADO',
                'tabla_afectada' => 'proveedores',
                'registro_id' => $proveedor->id,
                'detalle' => [
                    'nombre' => $proveedor->nombre,
                    'activo' => $proveedor->activo,
                ],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);
        });

        $estadoTexto = $proveedor->activo ? 'activo' : 'inactivo';

        return redirect()->route('compras.proveedores')->with('status', "El proveedor '{$proveedor->nombre}' ahora está {$estadoTexto}.");
    }

    /**
     * Órdenes de compra / recepciones de mercancía de proveedor.
     */
    public function ordenes(): View
    {
        $recepciones = MovimientoInventario::query()
            ->with(['existencia.varianteSku', 'existencia.sucursal', 'usuario', 'proveedor'])
            ->where('tipo', 'entrada')
            ->where('motivo', 'like', 'Recepci%')
            ->latest('created_at')
            ->limit(50)
            ->get();

        return view('admin.compras.ordenes', [
            'recepciones' => $recepciones,
            'sucursales' => Sucursal::where('activa', true)->orderBy('nombre')->get(),
            'variantes' => VarianteSku::where('activa', true)->orderBy('sku')->get(),
            'proveedores' => Proveedor::where('activo', true)->orderBy('nombre')->get(),
        ]);
    }

    /**
     * Recepciona mercancía de un proveedor y surte preventas pendientes.
     */
    public function recepcionar(Request $request, OrdenCompraProveedorService $ordenes): RedirectResponse
    {
        $validated = $request->validate([
            'proveedor_id' => ['nullable', 'integer', 'exists:proveedores,id'],
            'proveedor' => ['nullable', 'string', 'max:150'],
            'sucursal_id' => ['required', 'integer', 'exists:sucursales,id'],
            'variante_sku_id' => ['required', 'integer', 'exists:variantes_sku,id'],
            'cantidad' => ['required', 'integer', 'min:1'],
        ]);

        $proveedorModelo = ! empty($validated['proveedor_id'])
            ? Proveedor::find((int) $validated['proveedor_id'])
            : null;

        $nombreProveedor = $proveedorModelo?->nombre ?? trim((string) ($validated['proveedor'] ?? ''));

        if ($nombreProveedor === '') {
            return back()->withInput()->withErrors(['proveedor' => 'Debe seleccionar un proveedor registrado o especificar su razón social.']);
        }

        try {
            $ordenes->recepcionarStock(
                [['variante_sku_id' => (int) $validated['variante_sku_id'], 'cantidad' => (int) $validated['cantidad']]],
                (int) $validated['sucursal_id'],
                "Recepcion de mercancia de proveedor: {$nombreProveedor}",
                $request->user(),
                $proveedorModelo?->id
            );
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['cantidad' => $exception->getMessage()]);
        }

        return redirect()->route('compras.ordenes')->with('status', "Recepción de mercancía registrada en inventario para '{$nombreProveedor}'.");
    }
}
