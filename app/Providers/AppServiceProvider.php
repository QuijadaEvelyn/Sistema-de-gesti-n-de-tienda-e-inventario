<?php

namespace App\Providers;

use App\Events\PedidoPagado;
use App\Listeners\CrearEnvioInicial;
use App\Listeners\EnviarCorreoConfirmacion;
use App\Listeners\RegistrarSalidaDefinitivaInventario;
use App\Models\Carrito;
use App\Models\Pedido;
use App\Models\Usuario;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
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

        // ---------------------------------------------------------------------
        // Definición de Gates para los 5 Roles del Sistema y Reglas de Negocio
        // ---------------------------------------------------------------------

        // El Administrador tiene bypass implícito sobre cualquier autorización
        Gate::before(function (Usuario $user, string $ability): ?bool {
            if ($user->esAdmin()) {
                return true;
            }

            return null;
        });

        // Gates de Rol Base
        Gate::define('administrador', fn (Usuario $user): bool => $user->esAdmin());
        Gate::define('ventas', fn (Usuario $user): bool => $user->esVentas());
        Gate::define('almacen', fn (Usuario $user): bool => $user->esAlmacen());
        Gate::define('compras', fn (Usuario $user): bool => $user->esCompras());
        Gate::define('cliente', fn (Usuario $user): bool => $user->esCliente());

        // Gates de Dominios Operativos
        Gate::define('gestionar-usuarios', fn (Usuario $user): bool => $user->esAdmin());
        Gate::define('gestionar-sucursales', fn (Usuario $user): bool => $user->esAdmin());
        Gate::define('gestionar-ventas', fn (Usuario $user): bool => $user->esVentas());
        Gate::define('gestionar-almacen', fn (Usuario $user): bool => $user->esAlmacen());
        Gate::define('gestionar-compras', fn (Usuario $user): bool => $user->esCompras());

        // Aislamiento de Datos de Cliente (Seguridad y Privacidad Estricta)
        Gate::define('ver-pedido-propio', function (Usuario $user, Pedido $pedido): bool {
            return $user->cliente !== null && (int) $pedido->cliente_id === (int) $user->cliente->id;
        });

        Gate::define('gestionar-carrito-propio', function (Usuario $user, Carrito $carrito): bool {
            return $user->cliente !== null && (int) $carrito->cliente_id === (int) $user->cliente->id;
        });
    }
}
