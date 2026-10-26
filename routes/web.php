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
Route::prefix('admin')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/inventario', function () {
        return view('admin.dashboard'); // Reutiliza la vista demostrativa por ahora
    })->name('admin.inventario');
});
