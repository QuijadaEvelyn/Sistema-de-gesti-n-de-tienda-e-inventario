<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CheckRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Rutas de prueba para aislar el middleware de vistas o assets externos
        Route::middleware('rol:administrador,encargado')->get('/test-gestion-inventario', function () {
            return response()->json(['message' => 'Acceso concedido a inventario']);
        });

        Route::middleware('rol:administrador')->get('/test-gestion-bitacoras', function () {
            return response()->json(['message' => 'Acceso concedido a bitácoras']);
        });

        Route::middleware('rol:cliente')->get('/test-tienda-perfil', function () {
            return response()->json(['message' => 'Acceso concedido a perfil de cliente']);
        });
    }

    public function test_guest_is_unauthorized_when_accessing_protected_route(): void
    {
        $response = $this->getJson('/test-gestion-inventario');

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'No autenticado. Por favor inicia sesión.',
            ]);
    }

    public function test_inactive_user_is_forbidden_even_if_they_have_role(): void
    {
        $admin = Usuario::create([
            'nombre' => 'Admin Inactivo',
            'email' => 'admin.inactivo@example.com',
            'password' => 'secret123',
            'activo' => false,
        ]);
        $admin->asignarRol('administrador');

        $response = $this->actingAs($admin)->getJson('/test-gestion-inventario');

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Tu cuenta de usuario se encuentra inactiva.',
            ]);
    }

    public function test_user_without_required_role_receives_forbidden(): void
    {
        $cliente = Usuario::create([
            'nombre' => 'Juan Cliente',
            'email' => 'juan@example.com',
            'password' => 'secret123',
            'activo' => true,
        ]);
        $cliente->asignarRol('cliente');

        $response = $this->actingAs($cliente)->getJson('/test-gestion-inventario');

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Acceso denegado: no cuentas con los permisos requeridos.',
                'roles_requeridos' => ['administrador', 'encargado'],
            ]);
    }

    public function test_admin_and_encargado_can_access_inventory(): void
    {
        $admin = Usuario::create([
            'nombre' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => 'secret123',
            'activo' => true,
        ]);
        $admin->asignarRol('administrador');

        $encargado = Usuario::create([
            'nombre' => 'Carlos Encargado',
            'email' => 'encargado@example.com',
            'password' => 'secret123',
            'activo' => true,
        ]);
        $encargado->asignarRol('encargado');

        $this->actingAs($admin)
            ->getJson('/test-gestion-inventario')
            ->assertOk()
            ->assertJson(['message' => 'Acceso concedido a inventario']);

        $this->actingAs($encargado)
            ->getJson('/test-gestion-inventario')
            ->assertOk()
            ->assertJson(['message' => 'Acceso concedido a inventario']);
    }

    public function test_only_admin_can_access_audit_logs(): void
    {
        $admin = Usuario::create([
            'nombre' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => 'secret123',
            'activo' => true,
        ]);
        $admin->asignarRol('administrador');

        $encargado = Usuario::create([
            'nombre' => 'Carlos Encargado',
            'email' => 'encargado@example.com',
            'password' => 'secret123',
            'activo' => true,
        ]);
        $encargado->asignarRol('encargado');

        // Admin puede acceder
        $this->actingAs($admin)
            ->getJson('/test-gestion-bitacoras')
            ->assertOk();

        // Encargado no puede acceder a bitácoras
        $this->actingAs($encargado)
            ->getJson('/test-gestion-bitacoras')
            ->assertStatus(403);
    }

    public function test_usuario_model_helper_methods(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Usuario Multifuncional',
            'email' => 'multi@example.com',
            'password' => 'secret123',
            'activo' => true,
        ]);

        $this->assertFalse($usuario->esAdmin());
        $this->assertFalse($usuario->esEncargado());
        $this->assertFalse($usuario->esCliente());

        $usuario->asignarRol('administrador');

        $this->assertTrue($usuario->esAdmin());
        $this->assertTrue($usuario->tieneRol('admin')); // Alias funciona
        $this->assertTrue($usuario->hasRole('administrador'));

        $usuario->asignarRol('encargado');
        $this->assertTrue($usuario->esEncargado());
        $this->assertTrue($usuario->tieneTodosLosRoles('administrador', 'encargado'));
    }
}
