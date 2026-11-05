<?php

namespace Tests\Feature;

use App\Models\Carrito;
use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\RecuperacionContrasena;
use App\Models\Rol;
use App\Models\Usuario;
use Carbon\Carbon;
use Database\Seeders\UsuarioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthControllersTest extends TestCase
{
    use RefreshDatabase;

    protected Rol $rolAdmin;

    protected Rol $rolVentas;

    protected Rol $rolAlmacen;

    protected Rol $rolCompras;

    protected Rol $rolCliente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rolAdmin = Rol::firstOrCreate(['nombre' => 'administrador']);
        $this->rolVentas = Rol::firstOrCreate(['nombre' => 'ventas']);
        $this->rolAlmacen = Rol::firstOrCreate(['nombre' => 'almacen']);
        $this->rolCompras = Rol::firstOrCreate(['nombre' => 'compras']);
        $this->rolCliente = Rol::firstOrCreate(['nombre' => 'cliente']);
    }

    public function test_muestra_formulario_de_login(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_inicio_de_sesion_exitoso_para_cliente(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Cliente Uno',
            'email' => 'cliente@test.com',
            'password' => Hash::make('password123'),
            'activo' => true,
        ]);
        $usuario->asignarRol('cliente');

        $response = $this->post('/login', [
            'email' => 'cliente@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('public.home'));
        $this->assertAuthenticatedAs($usuario);
    }

    public function test_inicio_de_sesion_exitoso_para_administrador_redirige_a_admin(): void
    {
        $admin = Usuario::create([
            'nombre' => 'Admin Boss',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'activo' => true,
        ]);
        $admin->asignarRol('administrador');

        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_inicio_de_sesion_falla_con_credenciales_invalidas(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Cliente Test',
            'email' => 'test@test.com',
            'password' => Hash::make('password123'),
            'activo' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'test@test.com',
            'password' => 'clave_incorrecta',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_inicio_de_sesion_bloqueado_si_cuenta_esta_inactiva(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Usuario Inactivo',
            'email' => 'inactivo@test.com',
            'password' => Hash::make('password123'),
            'activo' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'inactivo@test.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_rate_limiting_bloquea_tras_cinco_intentos_fallidos(): void
    {
        RateLimiter::clear('test-throttle@test.com|127.0.0.1');

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'test-throttle@test.com',
                'password' => 'incorrecta',
            ]);
        }

        // El sexto intento debe ser bloqueado por throttle
        $response = $this->post('/login', [
            'email' => 'test-throttle@test.com',
            'password' => 'incorrecta',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseHas('bitacoras', [
            'accion' => 'LOGIN_BLOQUEADO_POR_THROTTLE',
        ]);
    }

    public function test_cierre_de_sesion_exitoso(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Test User',
            'email' => 'logout@test.com',
            'password' => Hash::make('password123'),
            'activo' => true,
        ]);

        $this->actingAs($usuario);

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_registro_publico_de_cliente_crea_usuario_cliente_y_carrito(): void
    {
        $response = $this->post('/register', [
            'nombre' => 'Ana',
            'apellidos' => 'Gomez',
            'email' => 'ana.gomez@test.com',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
            'telefono' => '5512345678',
        ]);

        $response->assertRedirect(route('public.home'));

        $this->assertDatabaseHas('usuarios', [
            'email' => 'ana.gomez@test.com',
            'nombre' => 'Ana',
            'activo' => 1,
        ]);

        $usuario = Usuario::where('email', 'ana.gomez@test.com')->first();
        $this->assertNotNull($usuario);
        $this->assertTrue($usuario->esCliente());

        $this->assertDatabaseHas('clientes', [
            'usuario_id' => $usuario->id,
            'nombre' => 'Ana',
            'apellidos' => 'Gomez',
            'email' => 'ana.gomez@test.com',
        ]);

        $cliente = Cliente::where('usuario_id', $usuario->id)->first();
        $this->assertNotNull($cliente);

        $this->assertDatabaseHas('carritos', [
            'cliente_id' => $cliente->id,
        ]);

        $this->assertAuthenticatedAs($usuario);
    }

    public function test_registro_falla_con_contrasena_menor_a_ocho_caracteres(): void
    {
        $response = $this->post('/register', [
            'nombre' => 'Ana',
            'apellidos' => 'Gomez',
            'email' => 'ana.corta@test.com',
            'password' => '12345',
            'password_confirmation' => '12345',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_solicitud_de_recuperacion_genera_token_temporal(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Usuario Reset',
            'email' => 'reset@test.com',
            'password' => Hash::make('password123'),
            'activo' => true,
        ]);

        $response = $this->post('/forgot-password', [
            'email' => 'reset@test.com',
        ]);

        $response->assertSessionHas('status');

        $recuperacion = RecuperacionContrasena::where('usuario_id', $usuario->id)->first();
        $this->assertNotNull($recuperacion);
        $this->assertNotNull($recuperacion->token_hash);
        $this->assertNull($recuperacion->usado_en);
        $this->assertTrue(Carbon::parse($recuperacion->expira_en)->isFuture());
    }

    public function test_restablecimiento_de_contrasena_con_token_valido(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Usuario Cambio',
            'email' => 'cambio@test.com',
            'password' => Hash::make('claveVieja123'),
            'activo' => true,
        ]);

        $plainToken = 'token_seguro_de_prueba_1234567890abcdefghijklmnopqrstuvwxyz';
        RecuperacionContrasena::create([
            'usuario_id' => $usuario->id,
            'token_hash' => hash('sha256', $plainToken),
            'expira_en' => Carbon::now()->addMinutes(60),
            'usado_en' => null,
        ]);

        $response = $this->post('/reset-password', [
            'token' => $plainToken,
            'email' => 'cambio@test.com',
            'password' => 'claveNueva456',
            'password_confirmation' => 'claveNueva456',
        ]);

        $response->assertRedirect(route('login'));

        $usuarioFresh = $usuario->fresh();
        $this->assertTrue(Hash::check('claveNueva456', $usuarioFresh->password));

        $recuperacion = RecuperacionContrasena::where('usuario_id', $usuario->id)->first();
        $this->assertNotNull($recuperacion->usado_en);
    }

    public function test_restablecimiento_falla_si_token_ha_expirado(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Usuario Expirado',
            'email' => 'expirado@test.com',
            'password' => Hash::make('claveVieja123'),
            'activo' => true,
        ]);

        $plainToken = 'token_expirado_prueba';
        RecuperacionContrasena::create([
            'usuario_id' => $usuario->id,
            'token_hash' => hash('sha256', $plainToken),
            'expira_en' => Carbon::now()->subHour(),
            'usado_en' => null,
        ]);

        $response = $this->post('/reset-password', [
            'token' => $plainToken,
            'email' => 'expirado@test.com',
            'password' => 'claveNueva456',
            'password_confirmation' => 'claveNueva456',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_actualizacion_de_perfil_y_cliente(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Perfil Inicial',
            'email' => 'perfil@test.com',
            'password' => Hash::make('password123'),
            'activo' => true,
        ]);

        $cliente = Cliente::create([
            'usuario_id' => $usuario->id,
            'nombre' => 'Perfil Inicial',
            'apellidos' => 'Apellido Inicial',
            'email' => 'perfil@test.com',
            'telefono' => '1111111111',
        ]);

        $this->actingAs($usuario);

        $response = $this->put('/profile', [
            'nombre' => 'Perfil Actualizado',
            'apellidos' => 'Apellido Nuevo',
            'email' => 'perfil.nuevo@test.com',
            'telefono' => '9999999999',
        ]);

        $response->assertSessionHas('status');

        $this->assertDatabaseHas('usuarios', [
            'id' => $usuario->id,
            'nombre' => 'Perfil Actualizado',
            'email' => 'perfil.nuevo@test.com',
        ]);

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'nombre' => 'Perfil Actualizado',
            'apellidos' => 'Apellido Nuevo',
            'email' => 'perfil.nuevo@test.com',
            'telefono' => '9999999999',
        ]);
    }

    public function test_cambio_de_contrasena_en_perfil_requiere_contrasena_actual_correcta(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Pass Test',
            'email' => 'pass@test.com',
            'password' => Hash::make('claveActual123'),
            'activo' => true,
        ]);

        $this->actingAs($usuario);

        // Intento con contraseña actual incorrecta
        $failResponse = $this->put('/profile/password', [
            'current_password' => 'claveErronea',
            'password' => 'claveNueva999',
            'password_confirmation' => 'claveNueva999',
        ]);
        $failResponse->assertSessionHasErrors('current_password');

        // Intento con contraseña correcta
        $successResponse = $this->put('/profile/password', [
            'current_password' => 'claveActual123',
            'password' => 'claveNueva999',
            'password_confirmation' => 'claveNueva999',
        ]);
        $successResponse->assertSessionHas('status');

        $this->assertTrue(Hash::check('claveNueva999', $usuario->fresh()->password));
    }

    public function test_control_de_roles_protege_rutas_respectivas(): void
    {
        // 1. Cliente no puede ingresar a /admin ni rutas de staff; es redirigido a la tienda con aviso
        $cliente = Usuario::create([
            'nombre' => 'Cliente Solo',
            'email' => 'solo_cliente@test.com',
            'password' => Hash::make('pass12345'),
            'activo' => true,
        ]);
        $cliente->asignarRol('cliente');

        $this->actingAs($cliente);
        $this->get('/admin')->assertRedirect(route('public.home'))->assertSessionHas('warning');
        $this->getJson('/admin')->assertStatus(403);
        $this->get('/ventas')->assertRedirect(route('public.home'))->assertSessionHas('warning');
        $this->get('/almacen')->assertRedirect(route('public.home'))->assertSessionHas('warning');
        $this->get('/compras')->assertRedirect(route('public.home'))->assertSessionHas('warning');
        $this->get('/mi-cuenta')->assertStatus(200);

        // 2. Ventas puede acceder a /admin y /ventas pero no a /compras ni /almacen
        $ventas = Usuario::create([
            'nombre' => 'Ventas User',
            'email' => 'ventas@test.com',
            'password' => Hash::make('pass12345'),
            'activo' => true,
        ]);
        $ventas->asignarRol('ventas');

        $this->actingAs($ventas);
        $this->get('/admin')->assertStatus(200);
        $this->get('/ventas')->assertStatus(200);
        $this->get('/compras')->assertStatus(403);

        // 3. Compras puede acceder a /admin y /compras pero no a /ventas
        $compras = Usuario::create([
            'nombre' => 'Compras User',
            'email' => 'compras@test.com',
            'password' => Hash::make('pass12345'),
            'activo' => true,
        ]);
        $compras->asignarRol('compras');

        $this->actingAs($compras);
        $this->get('/admin')->assertStatus(200);
        $this->get('/compras')->assertStatus(200);
        $this->get('/ventas')->assertStatus(403);

        // 4. Administrador tiene acceso total
        $admin = Usuario::create([
            'nombre' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => Hash::make('pass12345'),
            'activo' => true,
        ]);
        $admin->asignarRol('administrador');

        $this->actingAs($admin);
        $this->get('/admin')->assertStatus(200);
        $this->get('/admin/bitacoras')->assertStatus(200);
        $this->get('/ventas')->assertStatus(200);
        $this->get('/compras')->assertStatus(200);
    }

    public function test_gates_validan_aislamiento_de_datos_de_clientes(): void
    {
        $clienteUser1 = Usuario::create([
            'nombre' => 'Cliente Uno',
            'email' => 'c1@test.com',
            'password' => Hash::make('pass12345'),
            'activo' => true,
        ]);
        $c1 = Cliente::create([
            'usuario_id' => $clienteUser1->id,
            'nombre' => 'Cliente',
            'apellidos' => 'Uno',
            'email' => 'c1@test.com',
        ]);
        $carrito1 = Carrito::create(['cliente_id' => $c1->id]);
        $pedido1 = Pedido::create([
            'folio' => 'PED-C1-001',
            'cliente_id' => $c1->id,
            'subtotal' => 100,
            'total' => 100,
        ]);

        $clienteUser2 = Usuario::create([
            'nombre' => 'Cliente Dos',
            'email' => 'c2@test.com',
            'password' => Hash::make('pass12345'),
            'activo' => true,
        ]);
        $c2 = Cliente::create([
            'usuario_id' => $clienteUser2->id,
            'nombre' => 'Cliente',
            'apellidos' => 'Dos',
            'email' => 'c2@test.com',
        ]);

        // Cliente 1 puede ver su pedido y carrito
        $this->assertTrue(Gate::forUser($clienteUser1)->allows('ver-pedido-propio', $pedido1));
        $this->assertTrue(Gate::forUser($clienteUser1)->allows('gestionar-carrito-propio', $carrito1));

        // Cliente 2 NO puede ver el pedido ni carrito de Cliente 1
        $this->assertFalse(Gate::forUser($clienteUser2)->allows('ver-pedido-propio', $pedido1));
        $this->assertFalse(Gate::forUser($clienteUser2)->allows('gestionar-carrito-propio', $carrito1));

        // Administrador puede acceder a los pedidos de cualquier cliente
        $admin = Usuario::create([
            'nombre' => 'Admin All',
            'email' => 'adminall@test.com',
            'password' => Hash::make('pass12345'),
            'activo' => true,
        ]);
        $admin->asignarRol('administrador');

        $this->assertTrue(Gate::forUser($admin)->allows('ver-pedido-propio', $pedido1));
        $this->assertTrue(Gate::forUser($admin)->allows('gestionar-carrito-propio', $carrito1));
    }

    public function test_registro_cliente_no_requiere_autorizacion_y_activa_cuenta_de_inmediato(): void
    {
        // El cliente se registra de forma pública sin requerir intervención o aprobación de admin
        $response = $this->post('/register', [
            'nombre' => 'Cliente Inmediato',
            'apellidos' => 'Directo',
            'email' => 'inmediato@test.com',
            'password' => 'claveSegura123',
            'password_confirmation' => 'claveSegura123',
        ]);

        $response->assertRedirect(route('public.home'));

        /** @var Usuario $nuevoCliente */
        $nuevoCliente = Usuario::where('email', 'inmediato@test.com')->first();
        $this->assertNotNull($nuevoCliente);
        $this->assertTrue($nuevoCliente->activo, 'La cuenta de cliente debe crearse activa inmediatamente');
        $this->assertTrue($nuevoCliente->esCliente(), 'Debe asignarse exclusivamente el rol cliente');
        $this->assertFalse($nuevoCliente->esAdmin());
        $this->assertFalse($nuevoCliente->esVentas());
        $this->assertAuthenticatedAs($nuevoCliente);
    }

    public function test_administrador_puede_crear_cuentas_de_empleados(): void
    {
        $admin = Usuario::create([
            'nombre' => 'Admin Central',
            'email' => 'admin_emp@test.com',
            'password' => Hash::make('pass12345'),
            'activo' => true,
        ]);
        $admin->asignarRol('administrador');

        $this->actingAs($admin);

        // Crear empleado con rol de 'ventas'
        $responseVentas = $this->post('/admin/empleados', [
            'nombre' => 'Empleado Ventas',
            'email' => 'ventas1@archivum.com',
            'password' => 'claveEmpleado123',
            'rol' => 'ventas',
            'activo' => 1,
        ]);

        $responseVentas->assertRedirect(route('admin.empleados.index'));

        $empleadoVentas = Usuario::where('email', 'ventas1@archivum.com')->first();
        $this->assertNotNull($empleadoVentas);
        $this->assertTrue($empleadoVentas->esVentas());
        $this->assertFalse($empleadoVentas->esCliente());

        // Crear empleado con rol de 'almacen'
        $responseAlmacen = $this->post('/admin/empleados', [
            'nombre' => 'Empleado Almacen',
            'email' => 'almacen1@archivum.com',
            'password' => 'claveEmpleado123',
            'rol' => 'almacen',
            'activo' => 1,
        ]);

        $responseAlmacen->assertRedirect(route('admin.empleados.index'));

        $empleadoAlmacen = Usuario::where('email', 'almacen1@archivum.com')->first();
        $this->assertNotNull($empleadoAlmacen);
        $this->assertTrue($empleadoAlmacen->esAlmacen());
    }

    public function test_usuario_no_administrador_no_puede_crear_empleados(): void
    {
        $cliente = Usuario::create([
            'nombre' => 'Cliente Bloqueado',
            'email' => 'bloqueado@test.com',
            'password' => Hash::make('pass12345'),
            'activo' => true,
        ]);
        $cliente->asignarRol('cliente');

        $this->actingAs($cliente);

        // Cliente vía web es redirigido amigablemente a la tienda pública con advertencia
        $response = $this->post('/admin/empleados', [
            'nombre' => 'Hacker Empleado',
            'email' => 'hacker@archivum.com',
            'password' => 'clave123456',
            'rol' => 'administrador',
        ]);

        $response->assertRedirect(route('public.home'));
        $response->assertSessionHas('warning');
        $this->assertDatabaseMissing('usuarios', ['email' => 'hacker@archivum.com']);

        // Petición JSON de cliente es rechazada con 403
        $this->postJson('/admin/empleados', [
            'nombre' => 'Hacker JSON',
            'email' => 'hacker_json@archivum.com',
            'password' => 'clave123456',
            'rol' => 'administrador',
        ])->assertStatus(403);

        // Empleado sin rol de administrador (ej. ventas) recibe 403
        $ventas = Usuario::create([
            'nombre' => 'Ventas Sin Permiso',
            'email' => 'ventas_sin_permiso@test.com',
            'password' => Hash::make('pass12345'),
            'activo' => true,
        ]);
        $ventas->asignarRol('ventas');

        $this->actingAs($ventas);
        $this->post('/admin/empleados', [
            'nombre' => 'Ventas Intentando Crear Admin',
            'email' => 'ventas_intento@archivum.com',
            'password' => 'clave123456',
            'rol' => 'administrador',
        ])->assertStatus(403);
    }

    public function test_administrador_puede_actualizar_y_desactivar_empleado(): void
    {
        $admin = Usuario::create([
            'nombre' => 'Admin Gestor',
            'email' => 'gestor@test.com',
            'password' => Hash::make('pass12345'),
            'activo' => true,
        ]);
        $admin->asignarRol('administrador');

        $empleado = Usuario::create([
            'nombre' => 'Empleado Base',
            'email' => 'base@archivum.com',
            'password' => Hash::make('pass12345'),
            'activo' => true,
        ]);
        $empleado->asignarRol('compras');

        $this->actingAs($admin);

        // Actualizar datos y cambiar rol a 'ventas'
        $updateResponse = $this->put("/admin/empleados/{$empleado->id}", [
            'nombre' => 'Empleado Promovido',
            'email' => 'base@archivum.com',
            'rol' => 'ventas',
            'activo' => 1,
        ]);

        $updateResponse->assertRedirect(route('admin.empleados.index'));

        $empleadoFresh = $empleado->fresh();
        $this->assertEquals('Empleado Promovido', $empleadoFresh->nombre);
        $this->assertTrue($empleadoFresh->esVentas());
        $this->assertFalse($empleadoFresh->esCompras());

        // Desactivar empleado
        $deleteResponse = $this->delete("/admin/empleados/{$empleado->id}");
        $deleteResponse->assertRedirect(route('admin.empleados.index'));

        $this->assertFalse($empleado->fresh()->activo);

        // Intento de auto-desactivación del admin debe ser denegado
        $selfDelete = $this->delete("/admin/empleados/{$admin->id}");
        $selfDelete->assertSessionHasErrors('error');
        $this->assertTrue($admin->fresh()->activo);
    }

    public function test_usuario_seeder_crea_diez_usuarios_con_sus_roles_y_clientes(): void
    {
        $this->seed(UsuarioSeeder::class);

        // Validar total de 10 usuarios creados por el seeder
        $this->assertEquals(10, Usuario::count());

        // 1 Administrador
        $admin = Usuario::where('email', 'admin@archivum.com')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->esAdmin());

        // 5 Empleados
        $ventas1 = Usuario::where('email', 'ventas1@archivum.com')->first();
        $this->assertNotNull($ventas1);
        $this->assertTrue($ventas1->esVentas());

        $ventas2 = Usuario::where('email', 'ventas2@archivum.com')->first();
        $this->assertNotNull($ventas2);
        $this->assertTrue($ventas2->esVentas());

        $almacen1 = Usuario::where('email', 'almacen1@archivum.com')->first();
        $this->assertNotNull($almacen1);
        $this->assertTrue($almacen1->esAlmacen());

        $almacen2 = Usuario::where('email', 'almacen2@archivum.com')->first();
        $this->assertNotNull($almacen2);
        $this->assertTrue($almacen2->esAlmacen());

        $compras1 = Usuario::where('email', 'compras1@archivum.com')->first();
        $this->assertNotNull($compras1);
        $this->assertTrue($compras1->esCompras());

        // 4 Clientes con sus perfiles y carritos
        $clientes = [
            'elena.ramirez@gmail.com',
            'javier.morales@hotmail.com',
            'mariana.rivas@outlook.com',
            'daniel.castro@gmail.com',
        ];

        foreach ($clientes as $email) {
            $clienteUser = Usuario::where('email', $email)->first();
            $this->assertNotNull($clienteUser);
            $this->assertTrue($clienteUser->esCliente());

            $this->assertDatabaseHas('clientes', [
                'usuario_id' => $clienteUser->id,
                'email' => $email,
            ]);

            $clienteModel = Cliente::where('usuario_id', $clienteUser->id)->first();
            $this->assertNotNull($clienteModel);

            $this->assertDatabaseHas('carritos', [
                'cliente_id' => $clienteModel->id,
            ]);
        }
    }

    public function test_login_cliente_con_intended_hacia_admin_redirige_a_tienda_publica(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Cliente Protegido',
            'email' => 'cliente.protegido@test.com',
            'password' => Hash::make('password123'),
            'activo' => true,
        ]);
        $usuario->asignarRol('cliente');

        // Simular que el usuario intentó acceder a /admin previamente como invitado
        $response = $this->withSession(['url.intended' => 'http://127.0.0.1:8000/admin'])
            ->post('/login', [
                'email' => 'cliente.protegido@test.com',
                'password' => 'password123',
            ]);

        // Debe redirigir a la tienda pública / (public.home), NUNCA a /admin
        $response->assertRedirect(route('public.home'));
        $this->assertNull(session('url.intended'), 'La variable url.intended debe ser eliminada para clientes');
    }

    public function test_registro_cliente_con_intended_hacia_admin_redirige_a_tienda_publica(): void
    {
        // Simular que el visitante intentó acceder a /admin antes de registrarse
        $response = $this->withSession(['url.intended' => 'http://127.0.0.1:8000/admin'])
            ->post('/register', [
                'nombre' => 'Nuevo',
                'apellidos' => 'Cliente',
                'email' => 'nuevo.cliente@test.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertRedirect(route('public.home'));
        $response->assertSessionHas('status');
        $this->assertNull(session('url.intended'));
    }

    public function test_login_para_empleados_ventas_almacen_compras_redirige_a_admin(): void
    {
        $rolesStaff = ['ventas', 'almacen', 'compras'];

        foreach ($rolesStaff as $rol) {
            $empleado = Usuario::create([
                'nombre' => "Empleado {$rol}",
                'email' => "{$rol}@archivum.com",
                'password' => Hash::make('password123'),
                'activo' => true,
            ]);
            $empleado->asignarRol($rol);

            $response = $this->post('/login', [
                'email' => "{$rol}@archivum.com",
                'password' => 'password123',
            ]);

            $response->assertRedirect(route('admin.dashboard'));
            $this->post('/logout');
        }
    }

    public function test_cliente_con_sesion_activa_que_intenta_entrar_a_admin_es_redirigido_a_tienda_con_aviso(): void
    {
        $cliente = Usuario::create([
            'nombre' => 'Cliente Navegador',
            'email' => 'cliente.navegador@test.com',
            'password' => Hash::make('password123'),
            'activo' => true,
        ]);
        $cliente->asignarRol('cliente');

        $this->actingAs($cliente);

        $response = $this->get('/admin');

        // No debe mostrar pantalla blanca 403 directa, sino redirigir amigablemente con advertencia
        $response->assertRedirect(route('public.home'));
        $response->assertSessionHas('warning', 'Acceso denegado: Tu cuenta de cliente no tiene permisos para acceder al panel de administración.');
    }

    public function test_cliente_autenticado_visita_login_o_registro_redirige_a_tienda_publica(): void
    {
        $cliente = Usuario::create([
            'nombre' => 'Cliente Logueado',
            'email' => 'cliente.logueado@test.com',
            'password' => Hash::make('password123'),
            'activo' => true,
        ]);
        $cliente->asignarRol('cliente');

        $this->actingAs($cliente);

        $this->get('/login')->assertRedirect(route('public.home'));
        $this->get('/register')->assertRedirect(route('public.home'));
    }

    public function test_empleado_autenticado_visita_login_o_registro_redirige_a_panel_admin(): void
    {
        $admin = Usuario::create([
            'nombre' => 'Admin Logueado',
            'email' => 'admin.logueado@test.com',
            'password' => Hash::make('password123'),
            'activo' => true,
        ]);
        $admin->asignarRol('administrador');

        $this->actingAs($admin);

        $this->get('/login')->assertRedirect(route('admin.dashboard'));
        $this->get('/register')->assertRedirect(route('admin.dashboard'));
    }
}
