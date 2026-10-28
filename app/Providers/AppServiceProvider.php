<?php

namespace App\Providers;

use App\Events\PedidoPagado;
use App\Listeners\CrearEnvioInicial;
use App\Listeners\EnviarCorreoConfirmacion;
use App\Listeners\RegistrarSalidaDefinitivaInventario;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(PedidoPagado::class, RegistrarSalidaDefinitivaInventario::class);
        Event::listen(PedidoPagado::class, CrearEnvioInicial::class);
        Event::listen(PedidoPagado::class, EnviarCorreoConfirmacion::class);
    }
}
