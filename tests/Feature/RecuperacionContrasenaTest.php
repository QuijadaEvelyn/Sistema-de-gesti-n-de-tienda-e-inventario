<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Mail\RecuperacionContrasenaMail;
use App\Models\RecuperacionContrasena;
use App\Models\Rol;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RecuperacionContrasenaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(now());
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * Comprueba que la vista de solicitud de enlace se renderiza correctamente (HTTP 200).
     */
    public function test_vista_solicitud_recuperacion_contrasena_renderiza_correctamente(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertOk()
            ->assertSee('Recuperar Contraseña')
            ->assertSee('Correo Electrónico');
    }

    /**
     * 1. Que se envía el correo RecuperacionContrasenaMail cuando el correo existe
     * y no se envía cuando no existe o está inactivo.
     */
    public function test_envio_correo_recuperacion_cuando_usuario_existe_y_esta_activo(): void
    {
        Mail::fake();

        $usuario = Usuario::create([
            'nombre' => 'María Moliner',
            'email' => 'maria.moliner@archivum.mx',
            'password' => Hash::make('ClaveAntigua123'),
            'activo' => true,
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'maria.moliner@archivum.mx',
        ]);

        $response->assertRedirect(route('password.reset', ['email' => 'maria.moliner@archivum.mx']))
            ->assertSessionHas('status');

        // Verificar que el correo fue despachado
        Mail::assertSent(RecuperacionContrasenaMail::class, function (RecuperacionContrasenaMail $mail) use ($usuario): bool {
            return $mail->hasTo('maria.moliner@archivum.mx')
                && $mail->usuario->id === $usuario->id
                && strlen($mail->token) === 64
                && str_contains($mail->resetUrl, $mail->token);
        });

        // Verificar registro en base de datos
        $this->assertDatabaseHas('recuperacion_contrasenas', [
            'usuario_id' => $usuario->id,
            'usado_en' => null,
        ]);

        // Verificar auditoría en Bitácora
        $this->assertDatabaseHas('bitacoras', [
            'usuario_id' => $usuario->id,
            'accion' => 'SOLICITUD_RECUPERACION_CONTRASENA',
            'tabla_afectada' => 'recuperacion_contrasenas',
        ]);
    }

    public function test_no_se_despacha_correo_cuando_cuenta_no_existe(): void
    {
        Mail::fake();

        $response = $this->post(route('password.email'), [
            'email' => 'inexistente@archivum.mx',
        ]);

        $response->assertRedirect(route('password.reset', ['email' => 'inexistente@archivum.mx']))
            ->assertSessionHas('status');

        Mail::assertNothingSent();
        $this->assertDatabaseCount('recuperacion_contrasenas', 0);
    }

    public function test_no_se_despacha_correo_cuando_usuario_esta_inactivo(): void
    {
        Mail::fake();

        Usuario::create([
            'nombre' => 'Usuario Desactivado',
            'email' => 'inactivo@archivum.mx',
            'password' => Hash::make('ClaveSecreta123'),
            'activo' => false,
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'inactivo@archivum.mx',
        ]);

        $response->assertRedirect(route('password.reset', ['email' => 'inactivo@archivum.mx']))
            ->assertSessionHas('status');

        Mail::assertNothingSent();
        $this->assertDatabaseCount('recuperacion_contrasenas', 0);
    }

    /**
     * Comprueba que la vista de restablecimiento de contraseña carga con los campos necesarios.
     */
    public function test_vista_restablecer_contrasena_muestra_formulario_con_token_prellenado(): void
    {
        $response = $this->get(route('password.reset', [
            'token' => 'token-de-prueba-simulado-12345',
            'email' => 'lector@archivum.mx',
        ]));

        $response->assertOk()
            ->assertSee('Restablecer Contraseña')
            ->assertSee('Token de Verificación')
            ->assertSee('token-de-prueba-simulado-12345')
            ->assertSee('lector@archivum.mx');
    }

    /**
     * 2. Que un token inválido o expirado es rechazado.
     */
    public function test_restablecimiento_falla_con_token_invalido(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Elena Garro',
            'email' => 'elena.garro@archivum.mx',
            'password' => Hash::make('ClaveInicial123'),
            'activo' => true,
        ]);

        RecuperacionContrasena::create([
            'usuario_id' => $usuario->id,
            'token_hash' => hash('sha256', 'token_legitimo_1234567890'),
            'expira_en' => Carbon::now()->addMinutes(60),
            'usado_en' => null,
        ]);

        $response = $this->from(route('password.reset', ['email' => 'elena.garro@archivum.mx']))
            ->post(route('password.update'), [
                'token' => 'token_totalmente_falso_999999',
                'email' => 'elena.garro@archivum.mx',
                'password' => 'NuevaClaveSegura2026',
                'password_confirmation' => 'NuevaClaveSegura2026',
            ]);

        $response->assertSessionHasErrors(['token', 'email']);
        $this->assertGuest();

        // La contraseña original no se modificó
        $this->assertTrue(Hash::check('ClaveInicial123', $usuario->fresh()->password));
    }

    public function test_restablecimiento_falla_cuando_token_ha_expirado(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Octavio Paz',
            'email' => 'octavio.paz@archivum.mx',
            'password' => Hash::make('Laberinto123'),
            'activo' => true,
        ]);

        $plainToken = 'token_que_ha_caducado_hace_diez_minutos';
        RecuperacionContrasena::create([
            'usuario_id' => $usuario->id,
            'token_hash' => hash('sha256', $plainToken),
            'expira_en' => Carbon::now()->subMinutes(10), // Expirado
            'usado_en' => null,
        ]);

        $response = $this->post(route('password.update'), [
            'token' => $plainToken,
            'email' => 'octavio.paz@archivum.mx',
            'password' => 'NuevaClaveSegura2026',
            'password_confirmation' => 'NuevaClaveSegura2026',
        ]);

        $response->assertSessionHasErrors(['token', 'email']);
        $this->assertGuest();
        $this->assertTrue(Hash::check('Laberinto123', $usuario->fresh()->password));
    }

    /**
     * 3. Que al ingresar un token válido, se actualiza la contraseña, se marca usado_en
     * (impidiendo usarlo dos veces), se inicia sesión automáticamente (assertAuthenticatedAs($usuario))
     * y permite acceder a rutas protegidas.
     */
    public function test_restablecimiento_exitoso_actualiza_clave_invalida_reuso_e_inicia_sesion_automatica_rol_cliente(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Sor Juana Inés',
            'email' => 'sor.juana@archivum.mx',
            'password' => Hash::make('ClaveAnterior123'),
            'activo' => true,
        ]);

        $rolCliente = Rol::firstOrCreate(['nombre' => 'cliente']);
        $usuario->roles()->attach($rolCliente->id);

        $plainToken = 'token_valido_notarial_seguro_de_64_caracteres_1234567890abcdefghijkl';
        $recuperacion = RecuperacionContrasena::create([
            'usuario_id' => $usuario->id,
            'token_hash' => hash('sha256', $plainToken),
            'expira_en' => Carbon::now()->addMinutes(60),
            'usado_en' => null,
        ]);

        $response = $this->post(route('password.update'), [
            'token' => $plainToken,
            'email' => 'sor.juana@archivum.mx',
            'password' => 'NuevaClaveFuerte2026!',
            'password_confirmation' => 'NuevaClaveFuerte2026!',
        ]);

        // Redirige al inicio para cliente
        $response->assertRedirect(route('public.home'))
            ->assertSessionHas('status');

        // 1. Contraseña actualizada correctamente
        $this->assertTrue(Hash::check('NuevaClaveFuerte2026!', $usuario->fresh()->password));

        // 2. Token marcado como consumido (usado_en != null)
        $this->assertNotNull($recuperacion->fresh()->usado_en);

        // 3. Autenticación automática activa (sin pedir credenciales nuevamente)
        $this->assertAuthenticatedAs($usuario);

        // 4. Auditoría en Bitácora
        $this->assertDatabaseHas('bitacoras', [
            'usuario_id' => $usuario->id,
            'accion' => 'CONTRASENA_RESTABLECIDA',
        ]);
        $this->assertDatabaseHas('bitacoras', [
            'usuario_id' => $usuario->id,
            'accion' => 'ACCESO_AUTOMATICO_POST_RECUPERACION',
        ]);

        // 5. INTENTO DE REUSO INMEDIATO: El token no puede utilizarse una segunda vez
        $this->app['auth']->guard()->logout();
        $this->assertGuest();

        $intentoReuso = $this->post(route('password.update'), [
            'token' => $plainToken,
            'email' => 'sor.juana@archivum.mx',
            'password' => 'OtraClaveCualquiera123',
            'password_confirmation' => 'OtraClaveCualquiera123',
        ]);

        $intentoReuso->assertSessionHasErrors(['token', 'email']);
        $this->assertTrue(Hash::check('NuevaClaveFuerte2026!', $usuario->fresh()->password));
    }

    public function test_restablecimiento_exitoso_para_administrador_redirige_directo_a_panel_admin(): void
    {
        $admin = Usuario::create([
            'nombre' => 'Conservador Mayor',
            'email' => 'admin.boveda@archivum.mx',
            'password' => Hash::make('ClaveAntiguaAdmin123'),
            'activo' => true,
        ]);

        $rolAdmin = Rol::firstOrCreate(['nombre' => 'administrador']);
        $admin->roles()->attach($rolAdmin->id);

        $plainToken = 'token_admin_64_caracteres_seguro_notarial_archivum_1234567890abcdefg';
        RecuperacionContrasena::create([
            'usuario_id' => $admin->id,
            'token_hash' => hash('sha256', $plainToken),
            'expira_en' => Carbon::now()->addMinutes(60),
            'usado_en' => null,
        ]);

        $response = $this->post(route('password.update'), [
            'token' => $plainToken,
            'email' => 'admin.boveda@archivum.mx',
            'password' => 'SuperPasswordAdmin2026!',
            'password_confirmation' => 'SuperPasswordAdmin2026!',
        ]);

        // Redirige al Dashboard General del Administrador
        $response->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('status');

        $this->assertAuthenticatedAs($admin);

        // Permite acceder inmediatamente a rutas protegidas de administrador
        $this->get(route('admin.dashboard'))->assertOk();
    }
}
