<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - ARCHIVUM Sistema de Tienda & Inventario de Coleccionables
|--------------------------------------------------------------------------
*/

// Portal Público (Tienda, Catálogo y Bóveda de Adquisición)
Route::get('/', function () {
    return view('public.index');
})->name('public.home');

// Portal Privado (Administración, Inventario y Bitácora)
// Protegido por el middleware 'rol' para administradores y encargados
Route::prefix('admin')
    ->middleware('rol:administrador,encargado')
    ->group(function () {
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        Route::get('/inventario', function () {
            return view('admin.dashboard'); // Gestión de inventario para admin y encargados
        })->name('admin.inventario');

        // Ejemplo: Bitácora accesible exclusivamente para administradores
        Route::get('/bitacoras', function () {
            return view('admin.dashboard'); // Auditoría del sistema
        })->name('admin.bitacoras')->middleware('rol:administrador');
    });
