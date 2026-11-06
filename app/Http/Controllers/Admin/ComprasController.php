<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Catalogo;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\VarianteSku;
use App\Services\OrdenCompraProveedorService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

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

        return view('admin.compras.productos', [
            'productos' => $productos,
            'catalogos' => Catalogo::where('activo', true)->orderBy('nombre')->get(),
        ]);
    }

    /**
     * Alta de un producto con su variante SKU inicial.
     */
    public function storeProducto(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'catalogo_id' => ['required', 'integer', 'exists:catalogos,id'],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'precio' => ['required', 'numeric', 'min:0'],
            'sku' => ['required', 'string', 'max:100', 'unique:variantes_sku,sku'],
        ]);

        DB::transaction(function () use ($validated, $request): void {
            $producto = Producto::create([
                'catalogo_id' => $validated['catalogo_id'],
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'] ?? null,
                'precio' => $validated['precio'],
                'activo' => true,
            ]);

            VarianteSku::create([
                'producto_id' => $producto->id,
                'sku' => $validated['sku'],
                'descripcion' => $validated['nombre'],
                'precio' => $validated['precio'],
                'activa' => true,
            ]);

            Bitacora::create([
                'usuario_id' => $request->user()->id,
                'accion' => 'PRODUCTO_CREADO',
                'tabla_afectada' => 'productos',
                'registro_id' => $producto->id,
                'detalle' => ['nombre' => $producto->nombre, 'sku' => $validated['sku']],
                'ip' => $request->ip() ?? '127.0.0.1',
                'created_at' => now(),
            ]);
        });

        return redirect()->route('compras.productos')->with('status', "Producto '{$validated['nombre']}' dado de alta correctamente.");
    }

    /**
     * Directorio de proveedores.
     *
     * El esquema actual no incluye la tabla `proveedores`; la vista informa
     * esta dependencia hasta que se apruebe su migración.
     */
    public function proveedores(): View
    {
        return view('admin.compras.proveedores');
    }

    /**
     * Órdenes de compra / recepciones de mercancía de proveedor.
     */
    public function ordenes(): View
    {
        $recepciones = MovimientoInventario::query()
            ->with(['existencia.varianteSku', 'existencia.sucursal', 'usuario'])
            ->where('tipo', 'entrada')
            ->where('motivo', 'like', 'Recepci%')
            ->latest('created_at')
            ->limit(50)
            ->get();

        return view('admin.compras.ordenes', [
            'recepciones' => $recepciones,
            'sucursales' => Sucursal::where('activa', true)->orderBy('nombre')->get(),
            'variantes' => VarianteSku::where('activa', true)->orderBy('sku')->get(),
        ]);
    }

    /**
     * Recepciona mercancía de un proveedor y surte preventas pendientes.
     */
    public function recepcionar(Request $request, OrdenCompraProveedorService $ordenes): RedirectResponse
    {
        $validated = $request->validate([
            'proveedor' => ['required', 'string', 'max:150'],
            'sucursal_id' => ['required', 'integer', 'exists:sucursales,id'],
            'variante_sku_id' => ['required', 'integer', 'exists:variantes_sku,id'],
            'cantidad' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $ordenes->recepcionarStock(
                [['variante_sku_id' => (int) $validated['variante_sku_id'], 'cantidad' => (int) $validated['cantidad']]],
                (int) $validated['sucursal_id'],
                "Recepcion de mercancia de proveedor: {$validated['proveedor']}",
                $request->user(),
            );
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['cantidad' => $exception->getMessage()]);
        }

        return redirect()->route('compras.ordenes')->with('status', 'Recepción de mercancía registrada en inventario.');
    }
}
