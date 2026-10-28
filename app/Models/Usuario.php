<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'usuarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'email',
        'password',
        'activo',
        'email_verificado_en',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verificado_en' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    /**
     * Roles asignados al usuario.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class, 'usuario_rol', 'usuario_id', 'rol_id')
            ->using(UsuarioRol::class)
            ->withTimestamps();
    }

    /**
     * Determina si el usuario posee al menos uno de los roles especificados.
     *
     * @param  string|array<int, string>  ...$roles
     */
    public function tieneRol(string|array ...$roles): bool
    {
        $rolesSolicitados = collect($roles)
            ->flatten()
            ->flatMap(function (mixed $r): array {
                if ($r instanceof Rol) {
                    return [$r->nombre];
                }

                return is_string($r) ? (preg_split('/[,|]/', $r) ?: [$r]) : [];
            })
            ->map(fn (string $r): string => mb_strtolower(trim($r)))
            ->filter()
            ->values()
            ->all();

        if (empty($rolesSolicitados)) {
            return false;
        }

        $aliasMap = [
            'admin' => ['admin', 'administrador'],
            'administrador' => ['admin', 'administrador'],
            'encargado' => ['encargado', 'supervisor', 'almacenista', 'gerente'],
            'almacenista' => ['almacenista', 'encargado'],
            'cliente' => ['cliente', 'comprador', 'consumidor'],
        ];

        $rolesPermitidos = collect($rolesSolicitados)
            ->flatMap(fn (string $r): array => $aliasMap[$r] ?? [$r])
            ->unique()
            ->all();

        $rolesActuales = ($this->relationLoaded('roles') ? $this->roles : $this->roles()->get())
            ->pluck('nombre')
            ->map(fn (mixed $r): string => mb_strtolower(trim((string) $r)))
            ->all();

        return ! empty(array_intersect($rolesPermitidos, $rolesActuales));
    }

    /**
     * Alias de tieneRol en inglés.
     *
     * @param  string|array<int, string>  ...$roles
     */
    public function hasRole(string|array ...$roles): bool
    {
        return $this->tieneRol(...$roles);
    }

    /**
     * Determina si el usuario tiene todos los roles solicitados.
     *
     * @param  string|array<int, string>  ...$roles
     */
    public function tieneTodosLosRoles(string|array ...$roles): bool
    {
        $rolesSolicitados = collect($roles)
            ->flatten()
            ->map(fn (mixed $r): string => mb_strtolower(trim((string) $r)))
            ->filter()
            ->values()
            ->all();

        if (empty($rolesSolicitados)) {
            return false;
        }

        $rolesActuales = ($this->relationLoaded('roles') ? $this->roles : $this->roles()->get())
            ->pluck('nombre')
            ->map(fn (mixed $r): string => mb_strtolower(trim((string) $r)))
            ->all();

        return collect($rolesSolicitados)->diff($rolesActuales)->isEmpty();
    }

    /**
     * Comprueba si el usuario tiene rol de Administrador.
     */
    public function esAdmin(): bool
    {
        return $this->tieneRol('admin', 'administrador');
    }

    /**
     * Comprueba si el usuario tiene rol de Encargado o Almacén.
     */
    public function esEncargado(): bool
    {
        return $this->tieneRol('encargado', 'supervisor', 'almacenista');
    }

    /**
     * Comprueba si el usuario tiene rol de Cliente.
     */
    public function esCliente(): bool
    {
        return $this->tieneRol('cliente');
    }

    /**
     * Asigna un rol al usuario por nombre de rol o modelo Rol.
     */
    public function asignarRol(string|Rol $rol): self
    {
        $rolModel = is_string($rol)
            ? Rol::firstOrCreate(['nombre' => $rol])
            : $rol;

        $this->roles()->syncWithoutDetaching([$rolModel->id]);

        return $this;
    }

    /**
     * Solicitudes de recuperación de contraseña generadas para el usuario.
     */
    public function recuperacionesContrasena(): HasMany
    {
        return $this->hasMany(RecuperacionContrasena::class, 'usuario_id');
    }

    /**
     * Registros de bitácora generados por las acciones del usuario.
     */
    public function bitacoras(): HasMany
    {
        return $this->hasMany(Bitacora::class, 'usuario_id');
    }

    /**
     * Perfil de cliente vinculado a esta cuenta de usuario (si aplica).
     */
    public function cliente(): HasOne
    {
        return $this->hasOne(Cliente::class, 'usuario_id');
    }

    /**
     * Movimientos de inventario registrados por el usuario.
     */
    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'usuario_id');
    }
}
