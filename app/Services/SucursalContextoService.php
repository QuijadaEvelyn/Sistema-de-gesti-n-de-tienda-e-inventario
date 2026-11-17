<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Sucursal;
use App\Models\Usuario;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class SucursalContextoService
{
    public const SESSION_SUCURSAL_KEY = 'archivum_sucursal_id';

    public const SESSION_PERIODO_KEY = 'archivum_periodo_activo';

    /**
     * Obtiene todas las sucursales activas ordenadas de forma consistente.
     *
     * @return Collection<int, Sucursal>
     */
    public function obtenerSucursalesActivas(): Collection
    {
        if (! Schema::hasTable('sucursales')) {
            return collect();
        }

        return Sucursal::query()
            ->where('activa', true)
            ->orderBy('id')
            ->get();
    }

    /**
     * Resuelve una sucursal a partir de un ID numérico, código corto ('central', 'coyoacan', 'almacen')
     * o fragmento de nombre.
     */
    public function resolverSucursal(mixed $identificador): ?Sucursal
    {
        if ($identificador === null || $identificador === '' || ! Schema::hasTable('sucursales')) {
            return null;
        }

        $valor = mb_strtolower(trim((string) $identificador));

        if (in_array($valor, ['all', 'todas', 'global', '0'], true)) {
            return null;
        }

        if (is_numeric($valor)) {
            return Sucursal::query()->where('activa', true)->find((int) $valor);
        }

        $mapaAlias = [
            'central' => 'Bellas Artes',
            'centro' => 'Bellas Artes',
            'bellas_artes' => 'Bellas Artes',
            'bellas-artes' => 'Bellas Artes',
            'coyoacan' => 'Coyoacán',
            'coyoacán' => 'Coyoacán',
            'almacen' => 'Almacén',
            'almacén' => 'Almacén',
        ];

        $busqueda = $mapaAlias[$valor] ?? (string) $identificador;

        return Sucursal::query()
            ->where('activa', true)
            ->where(function ($q) use ($busqueda, $identificador): void {
                $q->where('nombre', 'like', '%'.$busqueda.'%')
                    ->orWhere('nombre', 'like', '%'.trim((string) $identificador).'%');
            })
            ->first();
    }

    /**
     * Determina si el usuario autenticado tiene permisos de Administrador para consultar
     * la vista global consolidada ("Todas las Sedes") en el panel administrativo.
     */
    public function puedeVerVistaGlobal(?Usuario $usuario): bool
    {
        if (! $usuario) {
            return false;
        }

        return $usuario->esAdmin();
    }

    /**
     * Sincroniza y devuelve la sucursal activa para el panel administrativo (/admin).
     * Regla de negocio:
     * - Si el usuario selecciona una sucursal específica (ej. "Sucursal Coyoacán"), se filtra estrictamente por esa sede.
     * - Solo el rol administrador puede seleccionar "Todas las Sedes" (null) para ver datos globales.
     * - Si el usuario NO es administrador, siempre opera sobre una sede específica (la seleccionada, la asignada a su cuenta o la sede principal por defecto).
     */
    public function resolverSucursalAdmin(Request $request, ?Usuario $usuario = null): ?Sucursal
    {
        $usuario ??= $request->user();
        $esAdmin = $this->puedeVerVistaGlobal($usuario);

        $parametroExplicito = $request->input('sucursal_id', $request->input('sucursal'));

        if ($parametroExplicito !== null && $parametroExplicito !== '') {
            $valorLimpio = mb_strtolower(trim((string) $parametroExplicito));

            if (in_array($valorLimpio, ['all', 'todas', 'global', '0'], true)) {
                if ($esAdmin) {
                    $request->session()->put(self::SESSION_SUCURSAL_KEY, 'all');

                    return null;
                }
            } else {
                $sucursalSeleccionada = $this->resolverSucursal($parametroExplicito);
                if ($sucursalSeleccionada) {
                    $request->session()->put(self::SESSION_SUCURSAL_KEY, $sucursalSeleccionada->id);

                    return $sucursalSeleccionada;
                }
            }
        }

        $valorEnSesion = $request->session()->get(self::SESSION_SUCURSAL_KEY);

        if ($valorEnSesion === 'all' && $esAdmin) {
            return null;
        }

        if ($valorEnSesion !== null && $valorEnSesion !== 'all') {
            $sucursalSesion = $this->resolverSucursal($valorEnSesion);
            if ($sucursalSesion) {
                return $sucursalSesion;
            }
        }

        // Si el usuario es administrador y no ha elegido una sede específica, inicia en vista global
        if ($esAdmin) {
            return null;
        }

        // Para roles no administradores (ventas, almacén, compras, encargado), evitar datos globales:
        // usar su sucursal asignada o la primera sucursal activa disponible.
        if ($usuario && ! empty($usuario->sucursal_id)) {
            $asignada = $this->resolverSucursal($usuario->sucursal_id);
            if ($asignada) {
                $request->session()->put(self::SESSION_SUCURSAL_KEY, $asignada->id);

                return $asignada;
            }
        }

        $primeraActiva = $this->obtenerSucursalesActivas()->first();
        if ($primeraActiva) {
            $request->session()->put(self::SESSION_SUCURSAL_KEY, $primeraActiva->id);
        }

        return $primeraActiva;
    }

    /**
     * Sincroniza y devuelve la sucursal activa en el portal público (null = Todas las Sedes).
     */
    public function resolverSucursalPublica(Request $request): ?Sucursal
    {
        $parametro = $request->input('sucursal_id', $request->input('sucursal'));

        if ($parametro !== null && $parametro !== '') {
            $valor = mb_strtolower(trim((string) $parametro));
            if (in_array($valor, ['all', 'todas', 'global', '0'], true)) {
                $request->session()->forget(self::SESSION_SUCURSAL_KEY);

                return null;
            }

            $sucursal = $this->resolverSucursal($parametro);
            if ($sucursal) {
                $request->session()->put(self::SESSION_SUCURSAL_KEY, $sucursal->id);

                return $sucursal;
            }
        }

        $enSesion = $request->session()->get(self::SESSION_SUCURSAL_KEY);
        if ($enSesion === null || $enSesion === 'all') {
            return null;
        }

        return $this->resolverSucursal($enSesion);
    }

    /**
     * Resuelve y sincroniza el filtro de temporalidad ('hoy', '7d', '30d', '2026').
     *
     * @return array{
     *     codigo: string,
     *     etiqueta: string,
     *     inicio: CarbonImmutable,
     *     fin: CarbonImmutable
     * }
     */
    public function resolverPeriodo(Request $request): array
    {
        $periodosValidos = ['hoy', '7d', '30d', '2026'];
        $solicitado = mb_strtolower(trim((string) $request->input('periodo', '')));

        if (in_array($solicitado, $periodosValidos, true)) {
            $codigo = $solicitado;
            $request->session()->put(self::SESSION_PERIODO_KEY, $codigo);
        } else {
            $guardado = (string) $request->session()->get(self::SESSION_PERIODO_KEY, '30d');
            $codigo = in_array($guardado, $periodosValidos, true) ? $guardado : '30d';
        }

        $ahora = CarbonImmutable::now();

        return match ($codigo) {
            'hoy' => [
                'codigo' => 'hoy',
                'etiqueta' => 'HOY (24H)',
                'inicio' => $ahora->startOfDay(),
                'fin' => $ahora->endOfDay(),
            ],
            '7d' => [
                'codigo' => '7d',
                'etiqueta' => 'ÚLTIMOS 7 DÍAS',
                'inicio' => $ahora->subDays(7)->startOfDay(),
                'fin' => $ahora->endOfDay(),
            ],
            '2026' => [
                'codigo' => '2026',
                'etiqueta' => 'AÑO 2026',
                'inicio' => CarbonImmutable::create(2026, 1, 1, 0, 0, 0),
                'fin' => CarbonImmutable::create(2026, 12, 31, 23, 59, 59),
            ],
            default => [
                'codigo' => '30d',
                'etiqueta' => 'ÚLTIMOS 30 DÍAS',
                'inicio' => $ahora->subDays(30)->startOfDay(),
                'fin' => $ahora->endOfDay(),
            ],
        };
    }
}
