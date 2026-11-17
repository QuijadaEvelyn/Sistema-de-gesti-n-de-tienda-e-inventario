<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Bitacora;
use App\Models\MensajeContacto;
use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MensajesContactoTest extends TestCase
{
    use RefreshDatabase;

    private function crearUsuarioConRol(string $nombreRol): Usuario
    {
        $rol = Rol::firstOrCreate(
            ['nombre' => $nombreRol],
            ['descripcion' => "Rol {$nombreRol}"]
        );

        $usuario = Usuario::factory()->create([
            'activo' => true,
        ]);

        $usuario->roles()->syncWithoutDetaching([$rol->id]);

        return $usuario;
    }

    private function crearSucursal(): Sucursal
    {
        return Sucursal::create([
            'nombre' => 'Sucursal Central Bellas Artes',
            'direccion' => 'Av. Juárez 42, Centro Histórico, CDMX',
            'telefono' => '5555124200',
            'activa' => true,
        ]);
    }

    public function test_visitante_puede_enviar_formulario_de_contacto_con_exito_y_se_registra_en_bd_y_bitacora(): void
    {
        $sucursal = $this->crearSucursal();

        $payload = [
            'nombre' => 'Dra. Beatriz Espejo',
            'email' => 'beatriz.espejo@literatura.unam.mx',
            'telefono' => '+52 55 1234 5678',
            'sucursal_id' => $sucursal->id,
            'asunto' => 'Consulta sobre Primera Edición de Pedro Páramo 1955',
            'mensaje' => 'Deseo concertar una cita notariada para verificar el estado de conservación del ejemplar en custodia.',
        ];

        $response = $this->post(route('public.contacto.enviar'), $payload);

        $response->assertRedirect(route('public.contacto'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('mensajes_contacto', [
            'nombre' => 'Dra. Beatriz Espejo',
            'email' => 'beatriz.espejo@literatura.unam.mx',
            'telefono' => '+52 55 1234 5678',
            'sucursal_id' => $sucursal->id,
            'asunto' => 'Consulta sobre Primera Edición de Pedro Páramo 1955',
            'estatus' => 'pendiente',
        ]);

        $this->assertDatabaseHas('bitacoras', [
            'accion' => 'MENSAJE_CONTACTO_RECIBIDO',
            'tabla_afectada' => 'mensajes_contacto',
        ]);
    }

    public function test_validacion_rechaza_formulario_de_contacto_con_datos_incompletos_o_invalidos(): void
    {
        $response = $this->post(route('public.contacto.enviar'), [
            'nombre' => '',
            'email' => 'correo-invalido',
            'asunto' => '',
            'mensaje' => 'corto',
        ]);

        $response->assertSessionHasErrors(['nombre', 'email', 'asunto', 'mensaje']);
        $this->assertDatabaseCount('mensajes_contacto', 0);
    }

    public function test_acceso_a_bandeja_de_mensajes_requiere_autenticacion_y_rol_autorizado(): void
    {
        // Invitado es redirigido a login
        $this->get(route('admin.mensajes'))
            ->assertRedirect(route('login'));

        // Cliente no autorizado recibe 403
        $cliente = $this->crearUsuarioConRol('cliente');
        $this->actingAs($cliente)
            ->get(route('admin.mensajes'))
            ->assertForbidden();

        // Almacén no tiene acceso a esta bandeja
        $almacen = $this->crearUsuarioConRol('almacen');
        $this->actingAs($almacen)
            ->get(route('admin.mensajes'))
            ->assertForbidden();

        // Administrador tiene acceso completo
        $admin = $this->crearUsuarioConRol('administrador');
        $this->actingAs($admin)
            ->get(route('admin.mensajes'))
            ->assertOk()
            ->assertSee('Bandeja de Mensajes &amp; Consultas', false);

        // Ventas tiene acceso completo
        $ventas = $this->crearUsuarioConRol('ventas');
        $this->actingAs($ventas)
            ->get(route('admin.mensajes'))
            ->assertOk()
            ->assertSee('Bandeja de Mensajes &amp; Consultas', false);
    }

    public function test_administrador_puede_filtrar_mensajes_por_busqueda_estatus_y_sucursal(): void
    {
        $admin = $this->crearUsuarioConRol('administrador');
        $sucursal1 = $this->crearSucursal();
        $sucursal2 = Sucursal::create([
            'nombre' => 'Sucursal Coyoacán',
            'direccion' => 'Jardín Centenario 14, CDMX',
            'telefono' => '5555124201',
            'activa' => true,
        ]);

        MensajeContacto::create([
            'nombre' => 'Octavio Paz',
            'email' => 'octavio@poesia.mx',
            'sucursal_id' => $sucursal1->id,
            'asunto' => 'Consulta Manuscritos Vuelta',
            'mensaje' => 'Solicitud de tasación de manuscrito original.',
            'estatus' => 'pendiente',
        ]);

        MensajeContacto::create([
            'nombre' => 'Carlos Fuentes',
            'email' => 'fuentes@fondo.mx',
            'sucursal_id' => $sucursal2->id,
            'asunto' => 'Edición Príncipe La Región Más Transparente',
            'mensaje' => 'Solicitud de certificación pericial COA.',
            'estatus' => 'respondido',
        ]);

        $this->actingAs($admin);

        // Filtro por estatus pendiente
        $resPendientes = $this->get(route('admin.mensajes', ['estatus' => 'pendiente']));
        $resPendientes->assertOk();
        $resPendientes->assertSee('Octavio Paz');
        $resPendientes->assertDontSee('Carlos Fuentes');

        // Filtro por búsqueda textual
        $resBusqueda = $this->get(route('admin.mensajes', ['buscar' => 'Transparente']));
        $resBusqueda->assertOk();
        $resBusqueda->assertSee('Carlos Fuentes');
        $resBusqueda->assertDontSee('Octavio Paz');

        // Filtro por sucursal
        $resSede = $this->get(route('admin.mensajes', ['sucursal_id' => $sucursal1->id]));
        $resSede->assertOk();
        $resSede->assertSee('Octavio Paz');
        $resSede->assertDontSee('Carlos Fuentes');
    }

    public function test_administrador_o_ventas_puede_actualizar_estatus_de_mensaje_y_se_audita_en_bitacora(): void
    {
        $admin = $this->crearUsuarioConRol('administrador');

        $mensaje = MensajeContacto::create([
            'nombre' => 'Elena Garro',
            'email' => 'elena.garro@recuerdos.mx',
            'asunto' => 'Los Recuerdos del Porvenir',
            'mensaje' => 'Solicito información del lote certificado.',
            'estatus' => 'pendiente',
        ]);

        $this->actingAs($admin);

        // 1. Cambiar a leído
        $resLeido = $this->patch(route('admin.mensajes.estatus', $mensaje), [
            'estatus' => 'leido',
        ]);

        $resLeido->assertRedirect();
        $this->assertDatabaseHas('mensajes_contacto', [
            'id' => $mensaje->id,
            'estatus' => 'leido',
        ]);

        $this->assertDatabaseHas('bitacoras', [
            'accion' => 'MENSAJE_CONTACTO_ESTATUS_ACTUALIZADO',
            'tabla_afectada' => 'mensajes_contacto',
            'registro_id' => $mensaje->id,
        ]);

        // 2. Cambiar a respondido
        $resRespondido = $this->patch(route('admin.mensajes.estatus', $mensaje), [
            'estatus' => 'respondido',
        ]);

        $resRespondido->assertRedirect();
        $this->assertDatabaseHas('mensajes_contacto', [
            'id' => $mensaje->id,
            'estatus' => 'respondido',
        ]);
    }

    public function test_sidebar_muestra_contador_de_mensajes_pendientes(): void
    {
        $admin = $this->crearUsuarioConRol('administrador');

        MensajeContacto::create([
            'nombre' => 'Alfonso Reyes',
            'email' => 'alfonso@reyes.mx',
            'asunto' => 'Visión de Anáhuac',
            'mensaje' => 'Consulta de ejemplar autografiado.',
            'estatus' => 'pendiente',
        ]);

        MensajeContacto::create([
            'nombre' => 'Salvador Novo',
            'email' => 'novo@poesia.mx',
            'asunto' => 'Poemas Prohibidos',
            'mensaje' => 'Revisión en bóveda.',
            'estatus' => 'pendiente',
        ]);

        $res = $this->actingAs($admin)->get(route('admin.dashboard'));
        $res->assertOk();
        $res->assertSee('Mensajes &amp; Consultas', false);
        $res->assertSee('2');
    }
}
