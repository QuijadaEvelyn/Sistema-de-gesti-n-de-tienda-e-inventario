<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Catalogo;
use App\Models\Producto;
use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Estructura base e indispensable del sistema ARCHIVUM:
     * - 5 Roles del sistema (administrador, compras, ventas, almacen, cliente) y usuarios iniciales (`updateOrCreate` / `firstOrCreate`).
     * - Cuentas base del personal interno con `activo = true` y contraseña encriptada una sola vez con `Hash::make('12345678')`.
     * - Sucursales base y los 3 catálogos maestros (`libro`, `disco`, `coleccionable`) usando `firstOrCreate`.
     * No inserta productos ficticios ni sobreescribe el catálogo real en MariaDB.
     */
    public function run(): void
    {
        $this->call([
            UsuarioSeeder::class,
        ]);

        // Asegurar y sincronizar explícitamente las 4 cuentas base del personal interno con contraseña '12345678'
        if (Schema::hasTable('usuarios') && Schema::hasTable('roles')) {
            $passwordHash = Hash::make('12345678');

            $cuentasPersonal = [
                [
                    'email' => 'admin@archivum.com',
                    'nombre' => 'Carlos Mendoza',
                    'rol' => 'administrador',
                    'rol_descripcion' => 'Acceso total y configuración del sistema',
                ],
                [
                    'email' => 'compras1@archivum.com',
                    'nombre' => 'Fernando Morales',
                    'rol' => 'compras',
                    'rol_descripcion' => 'Gestión de compras, proveedores y recepciones',
                ],
                [
                    'email' => 'ventas1@archivum.com',
                    'nombre' => 'Sofía Valenzuela',
                    'rol' => 'ventas',
                    'rol_descripcion' => 'Gestión de pedidos, ventas, pagos y promociones',
                ],
                [
                    'email' => 'almacen1@archivum.com',
                    'nombre' => 'Miguel Ángel Torres',
                    'rol' => 'almacen',
                    'rol_descripcion' => 'Gestión de existencias, envíos y transferencias',
                ],
            ];

            foreach ($cuentasPersonal as $cuenta) {
                $rol = Rol::firstOrCreate(
                    ['nombre' => $cuenta['rol']],
                    ['descripcion' => $cuenta['rol_descripcion']]
                );

                /** @var Usuario $usuario */
                $usuario = Usuario::withTrashed()->updateOrCreate(
                    ['email' => $cuenta['email']],
                    [
                        'nombre' => $cuenta['nombre'],
                        'password' => $passwordHash,
                        'activo' => true,
                        'email_verificado_en' => now(),
                        'deleted_at' => null,
                    ]
                );

                $usuario->roles()->syncWithoutDetaching([$rol->id]);
            }
        }

        if (Schema::hasTable('sucursales')) {
            Sucursal::firstOrCreate(
                ['nombre' => 'Sucursal Central Bellas Artes'],
                [
                    'direccion' => 'Av. Juárez 18, Centro Histórico, Cuauhtémoc, CDMX',
                    'telefono' => '5555128900',
                    'activa' => true,
                ]
            );

            Sucursal::firstOrCreate(
                ['nombre' => 'Sucursal Coyoacán'],
                [
                    'direccion' => 'Jardín Centenario 14, Villa Coyoacán, CDMX',
                    'telefono' => '5556584420',
                    'activa' => true,
                ]
            );

            Sucursal::firstOrCreate(
                ['nombre' => 'Almacén General'],
                [
                    'direccion' => 'Calz. Vallejo 1020, Industrial Vallejo, Azcapotzalco, CDMX',
                    'telefono' => '5553689100',
                    'activa' => true,
                ]
            );
        }

        if (Schema::hasTable('catalogos')) {
            Catalogo::firstOrCreate(
                ['tipo' => 'libro'],
                [
                    'nombre' => 'Libros',
                    'descripcion' => 'Catálogo general de libros, novelas, lanzamientos actuales y primeras ediciones.',
                    'activo' => true,
                ]
            );

            Catalogo::firstOrCreate(
                ['tipo' => 'disco'],
                [
                    'nombre' => 'Discos',
                    'descripcion' => 'Catálogo fonográfico de discos de vinilo LP y discos compactos (CD).',
                    'activo' => true,
                ]
            );

            Catalogo::firstOrCreate(
                ['tipo' => 'coleccionable'],
                [
                    'nombre' => 'Coleccionables',
                    'descripcion' => 'Figuras de cultura pop y anime, juguetes, monedas, videojuegos y piezas exclusivas.',
                    'activo' => true,
                ]
            );
        }

        // Si la tabla productos está vacía (y no se está ejecutando una prueba unitaria aislada),
        // restaurar el catálogo versionado de database/seeders/assets/catalogo_curado.json usando updateOrCreate
        if (! app()->runningUnitTests() && Schema::hasTable('productos') && Producto::count() === 0) {
            $this->call([
                CatalogoCuradoSeeder::class,
            ]);
        }
    }
}
