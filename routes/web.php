<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\BitacoraController;
use App\Http\Controllers\Admin\ComprasController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmpleadoController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\RolController;
use App\Http\Controllers\Admin\SucursalController;
use App\Http\Controllers\Admin\VentasController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CatalogoPublicoController;
use App\Http\Controllers\CheckoutSimuladoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - ARCHIVUM Sistema de Tienda & Inventario de Coleccionables
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. Portal Público de Navegación por Categorías, Carrito y Checkout Simulado
// =========================================================================
Route::middleware('reservas.liberar')->group(function (): void {
    Route::get('/', [CatalogoPublicoController::class, 'home'])->name('public.home');
    Route::get('/catalogo/libros', [CatalogoPublicoController::class, 'libros'])->name('public.libros');
    Route::get('/catalogo/vinilos', [CatalogoPublicoController::class, 'vinilos'])->name('public.vinilos');
    Route::get('/catalogo/memorabilia', [CatalogoPublicoController::class, 'memorabilia'])->name('public.memorabilia');
    Route::get('/catalogo/adquisiciones-selectas', [CatalogoPublicoController::class, 'adquisiciones'])->name('public.adquisiciones');

    // Gestión de Carrito, Expiración de Reserva (5 min), Pasarela de Pago Simulada y Seguimiento de Paquete
    Route::post('/carrito/agregar', [CheckoutSimuladoController::class, 'agregar'])->name('carrito.agregar');
    Route::delete('/carrito/remover/{varianteSkuId}', [CheckoutSimuladoController::class, 'remover'])->name('carrito.remover');
    Route::post('/carrito/expirar', [CheckoutSimuladoController::class, 'expirarReservas'])->name('carrito.expirar');
    Route::get('/checkout', [CheckoutSimuladoController::class, 'checkout'])->name('checkout.index');
    Route::post('/checkout/pagar', [CheckoutSimuladoController::class, 'procesarPago'])->name('checkout.pagar');
    Route::get('/checkout/confirmacion/{pedido}', [CheckoutSimuladoController::class, 'confirmacion'])->name('checkout.confirmacion');
    Route::post('/checkout/pedidos/{pedido}/simular-envio', [CheckoutSimuladoController::class, 'simularAvanceEnvio'])->name('checkout.simular-envio');
});

// =========================================================================
// 2. Rutas de Invitados (Autenticación y Recuperación de Contraseña)
// =========================================================================
Route::middleware('guest')->group(function (): void {
    // Portal público de clientes: /login y /register
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Registro público de clientes (rol 'cliente' por defecto)
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Portal exclusivo para personal interno: /admin/login
    Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');

    // Recuperación de contraseña vía correo electrónico con token temporal
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    // Restablecimiento de contraseña mediante token
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// =========================================================================
// 3. Rutas Autenticadas Comunes (Cierre de Sesión y Perfil de Usuario)
// =========================================================================
Route::middleware('auth')->group(function (): void {
    // Cierre de sesión seguro
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Administración de cuenta y perfil
    Route::prefix('profile')->group(function (): void {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });

    // Alias en español /perfil para acceso directo desde el header de cliente
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('cliente.perfil');
});

// =========================================================================
// 4. Módulos y Portales Protegidos por Rol (5 Roles de Negocio)
// =========================================================================

// Panel General Backoffice /admin: Acceso para personal (administrador, encargado, ventas, almacen, compras)
Route::prefix('admin')
    ->middleware(['auth', 'rol:administrador,encargado,ventas,almacen,compras'])
    ->group(function (): void {
        // Dashboard General y Estado de Terminal Operativa
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/terminal', [DashboardController::class, 'terminal'])->name('admin.terminal');

        // Módulo de Reportes Calculados y Exportación Oficial de Datos (CSV y PDF)
        Route::get('/reportes', [ReporteController::class, 'index'])->name('admin.reportes');
        Route::get('/reportes/exportar/csv', [ReporteController::class, 'exportarCsv'])->name('admin.reportes.csv');
        Route::get('/reportes/exportar/pdf', [ReporteController::class, 'exportarPdf'])->name('admin.reportes.pdf');

        // Inventario Unificado (accesible para Administrador, Encargado, Almacén y Compras)
        Route::middleware('rol:administrador,encargado,almacen,compras')->group(function (): void {
            Route::get('/inventario', [InventarioController::class, 'index'])->name('admin.inventario');
        });

        // Administración de Ventas desde el prefijo /admin/ventas (Administrador y Ventas)
        Route::middleware('rol:administrador,ventas')->group(function (): void {
            Route::get('/ventas', [VentasController::class, 'pedidos'])->name('admin.ventas');
        });

        // Proveedores y Órdenes de Compra desde el prefijo /admin (Administrador y Compras)
        Route::middleware('rol:administrador,compras')->group(function (): void {
            Route::get('/proveedores', [ComprasController::class, 'proveedores'])->name('admin.proveedores');
            Route::get('/ordenes-compra', [ComprasController::class, 'ordenes'])->name('admin.ordenes');
        });

        // Áreas exclusivas de Administrador: Empleados, Sucursales, Bitácoras y Roles
        Route::middleware('rol:administrador')->group(function (): void {
            // Bitácora del Sistema (soporta /admin/bitacora y /admin/bitacoras)
            Route::get('/bitacoras', [BitacoraController::class, 'index'])->name('admin.bitacoras');
            Route::get('/bitacora', [BitacoraController::class, 'index'])->name('admin.bitacora');

            // Gestión de Sucursales y Sedes
            Route::get('/sucursales', [SucursalController::class, 'index'])->name('admin.sucursales');
            Route::post('/sucursales', [SucursalController::class, 'store'])->name('admin.sucursales.store');
            Route::patch('/sucursales/{sucursal}/toggle', [SucursalController::class, 'toggle'])->name('admin.sucursales.toggle');

            // Roles y Permisos del Sistema
            Route::get('/roles', [RolController::class, 'index'])->name('admin.roles');

            // Gestión de Cuentas de Empleados (Alta, Baja, Edición y Desactivación de Personal)
            Route::prefix('empleados')->group(function (): void {
                Route::get('/', [EmpleadoController::class, 'index'])->name('admin.empleados.index');
                Route::get('/crear', [EmpleadoController::class, 'create'])->name('admin.empleados.create');
                Route::get('/alta', [EmpleadoController::class, 'create'])->name('admin.empleados.alta');
                Route::get('/baja', [EmpleadoController::class, 'index'])->name('admin.empleados.baja');
                Route::post('/', [EmpleadoController::class, 'store'])->name('admin.empleados.store');
                Route::get('/{empleado}/editar', [EmpleadoController::class, 'edit'])->name('admin.empleados.edit');
                Route::put('/{empleado}', [EmpleadoController::class, 'update'])->name('admin.empleados.update');
                Route::delete('/{empleado}', [EmpleadoController::class, 'destroy'])->name('admin.empleados.destroy');
            });

            // Alias para compatibilidad de rutas de usuarios administrativos
            Route::get('/usuarios', [EmpleadoController::class, 'index'])->name('admin.usuarios');
        });
    });

// Rol 2: VENTAS (Confirmar/cancelar pedidos, registrar pagos, devoluciones, precios y promociones)
Route::prefix('ventas')
    ->middleware(['auth', 'rol:ventas,administrador'])
    ->group(function (): void {
        Route::get('/', [VentasController::class, 'pedidos'])->name('ventas.dashboard');
        Route::get('/pedidos', [VentasController::class, 'pedidos'])->name('ventas.pedidos');
        Route::get('/pedidos/{pedido}', [VentasController::class, 'show'])->name('ventas.pedidos.show');
        Route::patch('/pedidos/{pedido}/estatus', [VentasController::class, 'actualizarEstatus'])->name('ventas.pedidos.estatus');
        Route::get('/pagos', [VentasController::class, 'pagos'])->name('ventas.pagos');
        Route::get('/devoluciones', [VentasController::class, 'devoluciones'])->name('ventas.devoluciones');
        Route::get('/promociones', [VentasController::class, 'promociones'])->name('ventas.promociones');
    });

// Rol 3: ALMACÉN (Preparación y entrega de pedidos, ajustes de inventario, transferencias y recepciones)
Route::prefix('almacen')
    ->middleware(['auth', 'rol:almacen,administrador', 'sucursal.acceso'])
    ->group(function (): void {
        Route::get('/', [InventarioController::class, 'index'])->name('almacen.dashboard');
        Route::get('/envios', [InventarioController::class, 'envios'])->name('almacen.envios');
        Route::get('/transferencias', [InventarioController::class, 'transferencias'])->name('almacen.transferencias');
        Route::post('/transferencias', [InventarioController::class, 'registrarTransferencia'])->name('almacen.transferencias.store');
        Route::post('/transferencias/{movimiento}/confirmar', [InventarioController::class, 'confirmarTransferencia'])->name('almacen.transferencias.confirmar');
        Route::get('/recepciones', [InventarioController::class, 'movimientos'])->name('almacen.recepciones');
        Route::get('/ajustes', [InventarioController::class, 'movimientos'])->name('almacen.ajustes');
        Route::post('/ajustes', [InventarioController::class, 'registrarAjuste'])->name('almacen.ajustes.store');
    });

// Rol 4: COMPRAS (Gestión de proveedores, órdenes de compra, recepciones y altas de productos)
Route::prefix('compras')
    ->middleware(['auth', 'rol:compras,administrador'])
    ->group(function (): void {
        Route::get('/', [ComprasController::class, 'ordenes'])->name('compras.dashboard');
        Route::get('/proveedores', [ComprasController::class, 'proveedores'])->name('compras.proveedores');
        Route::get('/ordenes', [ComprasController::class, 'ordenes'])->name('compras.ordenes');
        Route::post('/ordenes/recepcionar', [ComprasController::class, 'recepcionar'])->name('compras.ordenes.recepcionar');
        Route::get('/productos', [ComprasController::class, 'productos'])->name('compras.productos');
        Route::post('/productos', [ComprasController::class, 'storeProducto'])->name('compras.productos.store');
    });

// Rol 5: CLIENTE (Gestión de carrito personal, historial de pedidos propios y apartados con aislamiento estricto)
Route::prefix('mi-cuenta')
    ->middleware(['auth', 'rol:cliente'])
    ->group(function (): void {
        Route::get('/', [CheckoutSimuladoController::class, 'misPedidos'])->name('cliente.dashboard');
        Route::get('/pedidos', [CheckoutSimuladoController::class, 'misPedidos'])->name('cliente.pedidos');

        // Inspección de pedido propio con validación estricta de propiedad
        Route::get('/pedidos/{pedido}', [CheckoutSimuladoController::class, 'confirmacion'])
            ->name('cliente.pedidos.detalle')
            ->middleware('cliente.propietario:pedido');

        // Carrito personal con validación estricta de propiedad
        Route::get('/carrito/{carrito}', [CheckoutSimuladoController::class, 'checkout'])
            ->name('cliente.carrito')
            ->middleware('cliente.propietario:carrito');
    });

// Alias /mis-pedidos para clientes autenticados
Route::get('/mis-pedidos', [CheckoutSimuladoController::class, 'misPedidos'])
    ->middleware(['auth', 'rol:cliente'])
    ->name('cliente.mis-pedidos');
