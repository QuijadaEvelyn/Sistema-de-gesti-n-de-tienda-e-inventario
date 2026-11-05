<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\EmpleadoController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - ARCHIVUM Sistema de Tienda & Inventario de Coleccionables
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. Portal Público de Navegación (Catálogo General y Galería)
// =========================================================================
Route::get('/', function () {
    return view('public.index');
})->name('public.home');

// =========================================================================
// 2. Rutas de Invitados (Autenticación y Recuperación de Contraseña)
// =========================================================================
Route::middleware('guest')->group(function (): void {
    // -------------------------------------------------------
    // Portal público de clientes: /login y /register
    // -------------------------------------------------------
    // Inicio de sesión (clientes y personal — redirige por rol tras autenticar)
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Registro público de clientes (rol 'cliente' por defecto)
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // -------------------------------------------------------
    // Portal exclusivo para personal interno: /admin/login
    // Muestra el mismo formulario pero con contexto visual de staff
    // -------------------------------------------------------
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
});

// =========================================================================
// 4. Módulos y Portales Protegidos por Rol (5 Roles de Negocio)
// =========================================================================

// Panel General Backoffice /admin: Acceso para personal (administrador, encargado, ventas, almacen, compras)
Route::prefix('admin')
    ->middleware(['auth', 'rol:administrador,encargado,ventas,almacen,compras'])
    ->group(function (): void {
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        Route::get('/inventario', function () {
            return view('admin.dashboard');
        })->name('admin.inventario');

        // Áreas exclusivas de Administrador: Bitácoras, Sucursales y Gestión de Empleados
        Route::middleware('rol:administrador')->group(function (): void {
            Route::get('/bitacoras', function () {
                return view('admin.dashboard');
            })->name('admin.bitacoras');

            Route::get('/sucursales', function () {
                return view('admin.dashboard');
            })->name('admin.sucursales');

            // Gestión de Cuentas de Empleados (creación, edición y desactivación de personal)
            Route::prefix('empleados')->group(function (): void {
                Route::get('/', [EmpleadoController::class, 'index'])->name('admin.empleados.index');
                Route::get('/crear', [EmpleadoController::class, 'create'])->name('admin.empleados.create');
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
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('ventas.dashboard');

        Route::get('/pedidos', function () {
            return view('admin.dashboard');
        })->name('ventas.pedidos');

        Route::get('/pagos', function () {
            return view('admin.dashboard');
        })->name('ventas.pagos');

        Route::get('/devoluciones', function () {
            return view('admin.dashboard');
        })->name('ventas.devoluciones');

        Route::get('/promociones', function () {
            return view('admin.dashboard');
        })->name('ventas.promociones');
    });

// Rol 3: ALMACÉN (Preparación y entrega de pedidos, ajustes de inventario, transferencias y recepciones)
Route::prefix('almacen')
    ->middleware(['auth', 'rol:almacen,administrador', 'sucursal.acceso'])
    ->group(function (): void {
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('almacen.dashboard');

        Route::get('/envios', function () {
            return view('admin.dashboard');
        })->name('almacen.envios');

        Route::get('/transferencias', function () {
            return view('admin.dashboard');
        })->name('almacen.transferencias');

        Route::get('/recepciones', function () {
            return view('admin.dashboard');
        })->name('almacen.recepciones');

        Route::get('/ajustes', function () {
            return view('admin.dashboard');
        })->name('almacen.ajustes');
    });

// Rol 4: COMPRAS (Gestión de proveedores, órdenes de compra, recepciones y altas de productos)
Route::prefix('compras')
    ->middleware(['auth', 'rol:compras,administrador'])
    ->group(function (): void {
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('compras.dashboard');

        Route::get('/proveedores', function () {
            return view('admin.dashboard');
        })->name('compras.proveedores');

        Route::get('/ordenes', function () {
            return view('admin.dashboard');
        })->name('compras.ordenes');

        Route::get('/productos', function () {
            return view('admin.dashboard');
        })->name('compras.productos');
    });

// Rol 5: CLIENTE (Gestión de carrito personal, historial de pedidos propios y apartados con aislamiento estricto)
Route::prefix('mi-cuenta')
    ->middleware(['auth', 'rol:cliente'])
    ->group(function (): void {
        Route::get('/', function () {
            return view('public.index');
        })->name('cliente.dashboard');

        Route::get('/pedidos', function () {
            return view('public.index');
        })->name('cliente.pedidos');

        // Inspección de pedido propio con validación estricta de propiedad
        Route::get('/pedidos/{pedido}', function () {
            return view('public.index');
        })->name('cliente.pedidos.detalle')->middleware('cliente.propietario:pedido');

        // Carrito personal con validación estricta de propiedad
        Route::get('/carrito/{carrito}', function () {
            return view('public.index');
        })->name('cliente.carrito')->middleware('cliente.propietario:carrito');
    });
