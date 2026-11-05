<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Carrito;
use App\Models\Cliente;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Ejecuta el seeder para poblar la base de datos con 10 usuarios de ejemplo:
     * - 1 Administrador
     * - 5 Empleados (Ventas, Almacén, Compras)
     * - 4 Clientes (con perfiles y carritos aprovisionados)
     */
    public function run(): void
    {
        // Contraseña unificada para pruebas de acceso en entorno local/desarrollo
        $passwordCifrada = Hash::make('Password123!');

        // 1. Asegurar existencia de los 5 roles del sistema
        $rolesDefinidos = [
            'administrador' => 'Acceso total y configuración del sistema',
            'ventas' => 'Gestión de pedidos, ventas, pagos y promociones',
            'almacen' => 'Gestión de existencias, envíos y transferencias',
            'compras' => 'Gestión de compras, proveedores y recepciones',
            'cliente' => 'Cliente de la tienda y catálogo público',
        ];

        $rolesModelos = [];
        foreach ($rolesDefinidos as $nombre => $descripcion) {
            $rolesModelos[$nombre] = Rol::firstOrCreate(
                ['nombre' => $nombre],
                ['descripcion' => $descripcion]
            );
        }

        // 2. Definición estructurada de los 10 usuarios de ejemplo
        $usuariosEjemplo = [
            // =================================================================
            // A. Administrador del Sistema (1 cuenta)
            // =================================================================
            [
                'tipo' => 'administrador',
                'nombre' => 'Carlos Mendoza',
                'email' => 'admin@archivum.com',
                'rol' => 'administrador',
            ],

            // =================================================================
            // B. Personal / Empleados (5 cuentas de personal operativo)
            // =================================================================
            [
                'tipo' => 'empleado',
                'nombre' => 'Sofía Valenzuela',
                'email' => 'ventas1@archivum.com',
                'rol' => 'ventas',
            ],
            [
                'tipo' => 'empleado',
                'nombre' => 'Roberto Domínguez',
                'email' => 'ventas2@archivum.com',
                'rol' => 'ventas',
            ],
            [
                'tipo' => 'empleado',
                'nombre' => 'Miguel Ángel Torres',
                'email' => 'almacen1@archivum.com',
                'rol' => 'almacen',
            ],
            [
                'tipo' => 'empleado',
                'nombre' => 'Laura Esquivel',
                'email' => 'almacen2@archivum.com',
                'rol' => 'almacen',
            ],
            [
                'tipo' => 'empleado',
                'nombre' => 'Fernando Morales',
                'email' => 'compras1@archivum.com',
                'rol' => 'compras',
            ],

            // =================================================================
            // C. Clientes de la Tienda (4 cuentas con perfil de cliente y carrito)
            // =================================================================
            [
                'tipo' => 'cliente',
                'nombre' => 'Elena',
                'apellidos' => 'Ramírez Castillo',
                'email' => 'elena.ramirez@gmail.com',
                'telefono' => '5511223344',
                'rol' => 'cliente',
            ],
            [
                'tipo' => 'cliente',
                'nombre' => 'Javier',
                'apellidos' => 'Morales Pineda',
                'email' => 'javier.morales@hotmail.com',
                'telefono' => '5522334455',
                'rol' => 'cliente',
            ],
            [
                'tipo' => 'cliente',
                'nombre' => 'Mariana',
                'apellidos' => 'Rivas Soto',
                'email' => 'mariana.rivas@outlook.com',
                'telefono' => '5533445566',
                'rol' => 'cliente',
            ],
            [
                'tipo' => 'cliente',
                'nombre' => 'Daniel',
                'apellidos' => 'Castro Herrera',
                'email' => 'daniel.castro@gmail.com',
                'telefono' => '5544556677',
                'rol' => 'cliente',
            ],
        ];

        // 3. Inserción atómica y vinculación de roles y perfiles
        DB::transaction(function () use ($usuariosEjemplo, $rolesModelos, $passwordCifrada): void {
            foreach ($usuariosEjemplo as $data) {
                $nombreCompleto = isset($data['apellidos'])
                    ? "{$data['nombre']} {$data['apellidos']}"
                    : $data['nombre'];

                // Crear o recuperar usuario base
                /** @var Usuario $usuario */
                $usuario = Usuario::firstOrCreate(
                    ['email' => $data['email']],
                    [
                        'nombre' => $nombreCompleto,
                        'password' => $passwordCifrada,
                        'activo' => true,
                        'email_verificado_en' => now(),
                    ]
                );

                // Asignar el rol al usuario
                $rolId = $rolesModelos[$data['rol']]->id;
                $usuario->roles()->syncWithoutDetaching([$rolId]);

                // Si es cliente, asegurar la creación de su perfil en 'clientes' y su 'carrito'
                if ($data['tipo'] === 'cliente') {
                    $cliente = Cliente::firstOrCreate(
                        ['usuario_id' => $usuario->id],
                        [
                            'nombre' => $data['nombre'],
                            'apellidos' => $data['apellidos'],
                            'email' => $data['email'],
                            'telefono' => $data['telefono'] ?? null,
                        ]
                    );

                    Carrito::firstOrCreate([
                        'cliente_id' => $cliente->id,
                    ]);
                }
            }
        });
    }
}

