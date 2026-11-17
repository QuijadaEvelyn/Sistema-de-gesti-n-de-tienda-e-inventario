<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class MetricCardComponentTest extends TestCase
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

    public function test_componente_metric_card_renderiza_tokens_de_modo_oscuro_y_elevacion_fluida(): void
    {
        $html = Blade::render(
            '<x-ui.metric-card
                title="Acervo Certificado"
                value="1,420"
                unit="lotes"
                subtitle="Conservación pericial"
                badge="Bóveda Abierta"
                :badge-pulse="true"
                badge-color="emerald"
                icon="inventory_2"
                summary-left="Resguardo"
                summary-right="100% Protegido"
                action-url="/catalogo"
                action-text="Explorar Sala"
                action-icon="arrow_forward"
            />'
        );

        $this->assertStringContainsString('data-purpose="fluid-metric-card"', $html);
        // Superficie y bordes en modo oscuro requeridos
        $this->assertStringContainsString('dark:bg-[#171024]', $html);
        $this->assertStringContainsString('dark:border-[#5c4a78]/50', $html);
        // Tipografía de alto contraste
        $this->assertStringContainsString('dark:text-[#f4f0ea]', $html);
        $this->assertStringContainsString('dark:text-[#fdfbf7]', $html);
        // Elevación fluida y resplandor violeta en hover
        $this->assertStringContainsString('hover:-translate-y-1', $html);
        $this->assertStringContainsString('bg-gradient-to-r from-transparent via-primary to-transparent', $html);
        // Badge con pulso animado
        $this->assertStringContainsString('Bóveda Abierta', $html);
        $this->assertStringContainsString('animate-pulse', $html);
        // Microinteracción con active:scale-95
        $this->assertStringContainsString('active:scale-95', $html);
        $this->assertStringContainsString('Explorar Sala', $html);
    }

    public function test_componente_kpi_card_renderiza_estilo_fluid_dashboard(): void
    {
        $html = Blade::render(
            '<x-ui.kpi-card
                title="Ventas &amp; Ingresos"
                value="$45,000"
                currency="MXN"
                badge="Transacciones en Línea"
                :badge-pulse="true"
                badge-color="emerald"
                icon="payments"
                summary-left="Pedidos: 12"
                summary-right="Ticket: $3,750"
                :bento-delay="120"
            />'
        );

        $this->assertStringContainsString('data-purpose="fluid-kpi-card"', $html);
        $this->assertStringContainsString('bento-reveal', $html);
        $this->assertStringContainsString('--bento-delay: 120ms', $html);
        $this->assertStringContainsString('hover:-translate-y-1.5', $html);
        $this->assertStringContainsString('dark:bg-[#1a1626]/90', $html);
        $this->assertStringContainsString('backdrop-blur-md', $html);
        $this->assertStringContainsString('dark:text-[#f4f0ea]', $html);
        $this->assertStringContainsString('Transacciones en Línea', $html);
        $this->assertStringContainsString('animate-pulse', $html);
        // Soporte de Count-Up
        $this->assertStringContainsString('data-countup="45000"', $html);
        $this->assertStringContainsString('data-prefix="$"', $html);
    }

    public function test_componente_kpi_card_decodifica_entidades_html_evitando_amp_escapado(): void
    {
        $html = Blade::render(
            '<x-ui.kpi-card
                table-source="pedidos_table &amp; pagos_table"
                title="Logística &amp; Movimientos"
                value="100"
                currency="operaciones"
            />'
        );

        // El texto debe renderizarse limpio como texto plano
        $this->assertStringContainsString('pedidos_table & pagos_table', $html);
        $this->assertStringContainsString('Logística & Movimientos', $html);
        $this->assertStringNotContainsString('&amp;amp;', $html);
        $this->assertStringNotContainsString('&AMP;', $html);
    }

    public function test_layouts_incluyen_script_de_inicializacion_inmediata_de_tema_en_head(): void
    {
        $admin = $this->crearUsuarioConRol('admin');

        // Layout Administrativo
        $responseAdmin = $this->actingAs($admin)->get(route('admin.dashboard'));
        $responseAdmin->assertOk();
        $responseAdmin->assertSee('localStorage.getItem(\'theme\')', false);
        $responseAdmin->assertSee('document.documentElement.classList.add(\'dark\')', false);

        // Layout Público
        $responsePublic = $this->get(route('public.home'));
        $responsePublic->assertOk();
        $responsePublic->assertSee('localStorage.getItem(\'theme\')', false);
        $responsePublic->assertSee('document.documentElement.classList.add(\'dark\')', false);
    }

    public function test_vista_publica_nosotros_integra_las_tarjetas_de_metricas_curatorias(): void
    {
        $response = $this->get(route('public.nosotros'));

        $response->assertOk();
        $response->assertSee('Sedes Interconectadas');
        $response->assertSee('Acervo Certificado');
        $response->assertSee('Piezas Únicas (1/1)');
        $response->assertSee('data-purpose="fluid-metric-card"', false);
    }

    public function test_panel_administrativo_muestra_kpis_con_badges_de_estado_y_pulso(): void
    {
        $admin = $this->crearUsuarioConRol('admin');

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Ventas & Ingresos Estimados', false);
        $response->assertSee('Piezas en Custodia');
        $response->assertSee('Transacciones en Línea');
        $response->assertSee('Bóveda Abierta');
        $response->assertSee('data-purpose="fluid-kpi-card"', false);
        // Revelado escalonado estilo Bento
        $response->assertSee('bento-reveal', false);
        $response->assertSee('bento-delay-0', false);
        $response->assertSee('bento-delay-60', false);
        $response->assertSee('--bento-delay: 120ms', false);
        $response->assertSee('bento-delay-340', false);
        $response->assertSee('bento-delay-400', false);
        // Indicadores Count-Up
        $response->assertSee('data-countup=', false);
    }
}
