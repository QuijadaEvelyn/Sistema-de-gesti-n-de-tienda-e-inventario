<?php

namespace Tests\Unit;

use App\Models\Bitacora;
use App\Models\Carrito;
use App\Models\CarritoItem;
use App\Models\Catalogo;
use App\Models\Cliente;
use App\Models\ClienteFrecuente;
use App\Models\ClientePromocion;
use App\Models\Coleccionable;
use App\Models\DetalleEnvio;
use App\Models\DetallePago;
use App\Models\DetallePedido;
use App\Models\Devolucion;
use App\Models\Direccion;
use App\Models\Disco;
use App\Models\Envio;
use App\Models\Existencia;
use App\Models\Libro;
use App\Models\MovimientoInventario;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\PiezaUnica;
use App\Models\Producto;
use App\Models\Promocion;
use App\Models\RecuperacionContrasena;
use App\Models\ReservaStock;
use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\Usuario;
use App\Models\UsuarioRol;
use App\Models\VarianteSku;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    /**
     * Verifica que los 30 modelos tengan sus tablas configuradas correctamente.
     */
    public function test_all_models_have_correct_table_names(): void
    {
        $this->assertSame('roles', (new Rol)->getTable());
        $this->assertSame('usuarios', (new Usuario)->getTable());
        $this->assertSame('usuario_rol', (new UsuarioRol)->getTable());
        $this->assertSame('recuperacion_contrasenas', (new RecuperacionContrasena)->getTable());
        $this->assertSame('bitacoras', (new Bitacora)->getTable());
        $this->assertSame('clientes', (new Cliente)->getTable());
        $this->assertSame('direcciones', (new Direccion)->getTable());
        $this->assertSame('clientes_frecuentes', (new ClienteFrecuente)->getTable());
        $this->assertSame('promociones', (new Promocion)->getTable());
        $this->assertSame('cliente_promocion', (new ClientePromocion)->getTable());
        $this->assertSame('catalogos', (new Catalogo)->getTable());
        $this->assertSame('productos', (new Producto)->getTable());
        $this->assertSame('libros', (new Libro)->getTable());
        $this->assertSame('discos', (new Disco)->getTable());
        $this->assertSame('coleccionables', (new Coleccionable)->getTable());
        $this->assertSame('variantes_sku', (new VarianteSku)->getTable());
        $this->assertSame('sucursales', (new Sucursal)->getTable());
        $this->assertSame('existencias', (new Existencia)->getTable());
        $this->assertSame('piezas_unicas', (new PiezaUnica)->getTable());
        $this->assertSame('movimientos_inventario', (new MovimientoInventario)->getTable());
        $this->assertSame('carritos', (new Carrito)->getTable());
        $this->assertSame('carrito_items', (new CarritoItem)->getTable());
        $this->assertSame('pedidos', (new Pedido)->getTable());
        $this->assertSame('detalle_pedidos', (new DetallePedido)->getTable());
        $this->assertSame('reservas_stock', (new ReservaStock)->getTable());
        $this->assertSame('pagos', (new Pago)->getTable());
        $this->assertSame('detalle_pagos', (new DetallePago)->getTable());
        $this->assertSame('devoluciones', (new Devolucion)->getTable());
        $this->assertSame('envios', (new Envio)->getTable());
        $this->assertSame('detalle_envios', (new DetalleEnvio)->getTable());
    }

    /**
     * Verifica modelos que no usan timestamps estándar.
     */
    public function test_models_without_timestamps_or_custom_timestamps(): void
    {
        $this->assertFalse((new Libro)->timestamps);
        $this->assertFalse((new Disco)->timestamps);
        $this->assertFalse((new Coleccionable)->timestamps);
        $this->assertFalse((new ClientePromocion)->timestamps);

        $this->assertNull(Bitacora::UPDATED_AT);
        $this->assertNull(MovimientoInventario::UPDATED_AT);

        $this->assertSame('cliente_id', (new ClienteFrecuente)->getKeyName());
        $this->assertSame('producto_id', (new Libro)->getKeyName());
        $this->assertSame('producto_id', (new Disco)->getKeyName());
        $this->assertSame('producto_id', (new Coleccionable)->getKeyName());
    }

    /**
     * Verifica que las relaciones devuelvan las instancias de Eloquent Relation correspondientes.
     */
    public function test_relationships_are_properly_configured(): void
    {
        // Rol & Usuario
        $this->assertInstanceOf(BelongsToMany::class, (new Rol)->usuarios());
        $this->assertInstanceOf(BelongsToMany::class, (new Usuario)->roles());
        $this->assertInstanceOf(HasMany::class, (new Usuario)->recuperacionesContrasena());
        $this->assertInstanceOf(HasMany::class, (new Usuario)->bitacoras());
        $this->assertInstanceOf(HasOne::class, (new Usuario)->cliente());
        $this->assertInstanceOf(HasMany::class, (new Usuario)->movimientosInventario());

        // Pivot UsuarioRol
        $this->assertInstanceOf(BelongsTo::class, (new UsuarioRol)->usuario());
        $this->assertInstanceOf(BelongsTo::class, (new UsuarioRol)->rol());

        // Cliente & Direccion & ClienteFrecuente & Promocion
        $this->assertInstanceOf(BelongsTo::class, (new Cliente)->usuario());
        $this->assertInstanceOf(HasMany::class, (new Cliente)->direcciones());
        $this->assertInstanceOf(HasOne::class, (new Cliente)->direccionPredeterminada());
        $this->assertInstanceOf(HasOne::class, (new Cliente)->clienteFrecuente());
        $this->assertInstanceOf(HasOne::class, (new Cliente)->carrito());
        $this->assertInstanceOf(HasMany::class, (new Cliente)->pedidos());
        $this->assertInstanceOf(HasMany::class, (new Cliente)->devoluciones());

        $this->assertInstanceOf(BelongsTo::class, (new Direccion)->cliente());
        $this->assertInstanceOf(HasMany::class, (new Direccion)->pedidos());

        $this->assertInstanceOf(BelongsTo::class, (new ClienteFrecuente)->cliente());
        $this->assertInstanceOf(BelongsToMany::class, (new ClienteFrecuente)->promociones());
        $this->assertInstanceOf(BelongsToMany::class, (new Promocion)->clientesFrecuentes());
        $this->assertInstanceOf(BelongsTo::class, (new ClientePromocion)->clienteFrecuente());
        $this->assertInstanceOf(BelongsTo::class, (new ClientePromocion)->promocion());

        // Catalogo & Producto & Subtipos
        $this->assertInstanceOf(HasMany::class, (new Catalogo)->productos());
        $this->assertInstanceOf(BelongsTo::class, (new Producto)->catalogo());
        $this->assertInstanceOf(HasOne::class, (new Producto)->libro());
        $this->assertInstanceOf(HasOne::class, (new Producto)->disco());
        $this->assertInstanceOf(HasOne::class, (new Producto)->coleccionable());
        $this->assertInstanceOf(HasMany::class, (new Producto)->variantesSku());

        $this->assertInstanceOf(BelongsTo::class, (new Libro)->producto());
        $this->assertInstanceOf(BelongsTo::class, (new Disco)->producto());
        $this->assertInstanceOf(BelongsTo::class, (new Coleccionable)->producto());

        // Variantes & Inventario
        $this->assertInstanceOf(BelongsTo::class, (new VarianteSku)->producto());
        $this->assertInstanceOf(HasMany::class, (new VarianteSku)->existencias());
        $this->assertInstanceOf(HasMany::class, (new VarianteSku)->carritoItems());
        $this->assertInstanceOf(HasMany::class, (new VarianteSku)->detallePedidos());

        $this->assertInstanceOf(BelongsTo::class, (new Existencia)->varianteSku());
        $this->assertInstanceOf(BelongsTo::class, (new Existencia)->sucursal());
        $this->assertInstanceOf(HasMany::class, (new Existencia)->piezasUnicas());
        $this->assertInstanceOf(HasMany::class, (new Existencia)->movimientosInventario());
        $this->assertInstanceOf(HasMany::class, (new Existencia)->reservasStock());

        $this->assertInstanceOf(BelongsTo::class, (new PiezaUnica)->existencia());
        $this->assertInstanceOf(BelongsTo::class, (new MovimientoInventario)->existencia());
        $this->assertInstanceOf(BelongsTo::class, (new MovimientoInventario)->usuario());
        $this->assertInstanceOf(MorphTo::class, (new MovimientoInventario)->referencia());

        // Carrito
        $this->assertInstanceOf(BelongsTo::class, (new Carrito)->cliente());
        $this->assertInstanceOf(HasMany::class, (new Carrito)->items());
        $this->assertInstanceOf(HasMany::class, (new Carrito)->reservasStock());
        $this->assertInstanceOf(BelongsTo::class, (new CarritoItem)->carrito());
        $this->assertInstanceOf(BelongsTo::class, (new CarritoItem)->varianteSku());

        // Pedidos & Detalles & Pagos & Envíos
        $this->assertInstanceOf(BelongsTo::class, (new Pedido)->cliente());
        $this->assertInstanceOf(BelongsTo::class, (new Pedido)->direccionEnvio());
        $this->assertInstanceOf(HasMany::class, (new Pedido)->detalles());
        $this->assertInstanceOf(HasMany::class, (new Pedido)->reservasStock());
        $this->assertInstanceOf(HasMany::class, (new Pedido)->pagos());
        $this->assertInstanceOf(HasMany::class, (new Pedido)->devoluciones());
        $this->assertInstanceOf(HasMany::class, (new Pedido)->envios());

        $this->assertInstanceOf(BelongsTo::class, (new DetallePedido)->pedido());
        $this->assertInstanceOf(BelongsTo::class, (new DetallePedido)->varianteSku());
        $this->assertInstanceOf(HasMany::class, (new DetallePedido)->devoluciones());
        $this->assertInstanceOf(HasMany::class, (new DetallePedido)->detalleEnvios());

        $this->assertInstanceOf(BelongsTo::class, (new Pago)->pedido());
        $this->assertInstanceOf(HasMany::class, (new Pago)->detalles());
        $this->assertInstanceOf(BelongsTo::class, (new DetallePago)->pago());

        $this->assertInstanceOf(BelongsTo::class, (new Devolucion)->pedido());
        $this->assertInstanceOf(BelongsTo::class, (new Devolucion)->cliente());
        $this->assertInstanceOf(BelongsTo::class, (new Devolucion)->detallePedido());

        $this->assertInstanceOf(BelongsTo::class, (new Envio)->pedido());
        $this->assertInstanceOf(BelongsTo::class, (new Envio)->sucursal());
        $this->assertInstanceOf(HasMany::class, (new Envio)->detalles());

        $this->assertInstanceOf(BelongsTo::class, (new DetalleEnvio)->envio());
        $this->assertInstanceOf(BelongsTo::class, (new DetalleEnvio)->detallePedido());
    }
}
