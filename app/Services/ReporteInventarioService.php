<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Existencia;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\PiezaUnica;
use App\Models\ReservaStock;
use App\Models\Sucursal;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Illuminate\Support\Collection;

class ReporteInventarioService
{
    /**
     * Consulta la valoracion economica del inventario agrupada por sucursal.
     * Calcula el valor de cada existencia considerando el precio de la variante o, en su defecto,
     * el precio base del producto.
     *
     * @return array<int, array{sucursal_id: int, sucursal_nombre: string, total_skus: int, total_unidades: int, valor_total_inventario: float}>
     */
    public function valoracionPorSucursal(?int $sucursalId = null): array
    {
        $query = Sucursal::query()->where('activa', true);

        if ($sucursalId !== null) {
            $query->where('id', $sucursalId);
        }

        $sucursales = $query->with(['existencias.varianteSku.producto'])->get();

        $reporte = [];

        foreach ($sucursales as $sucursal) {
            $totalSkus = 0;
            $totalUnidades = 0;
            $valorTotal = 0.0;

            foreach ($sucursal->existencias as $existencia) {
                $variante = $existencia->varianteSku;
                if (! $variante) {
                    continue;
                }

                $precioUnitario = (float) ($variante->precio ?? $variante->producto?->precio ?? 0.0);
                $unidades = (int) $existencia->cantidad;

                $totalSkus++;
                $totalUnidades += $unidades;
                $valorTotal += ($unidades * $precioUnitario);
            }

            $reporte[] = [
                'sucursal_id' => $sucursal->id,
                'sucursal_nombre' => $sucursal->nombre,
                'total_skus' => $totalSkus,
                'total_unidades' => $totalUnidades,
                'valor_total_inventario' => round($valorTotal, 2),
            ];
        }

        return $reporte;
    }

    /**
     * Genera un reporte de rotacion y estado de piezas unicas (ejemplares numerados / certificados).
     *
     * @return array{total_piezas: int, total_disponibles: int, total_reservadas: int, total_vendidas: int, tasa_rotacion_porcentaje: float, detalle_por_existencia: array<int, array{existencia_id: int, sku: string, producto: string, sucursal: string, disponibles: int, reservadas: int, vendidas: int}>}
     */
    public function rotacionPiezasUnicas(?int $sucursalId = null): array
    {
        $query = PiezaUnica::query()
            ->with(['existencia.varianteSku.producto', 'existencia.sucursal']);

        if ($sucursalId !== null) {
            $query->whereHas('existencia', function ($q) use ($sucursalId): void {
                $q->where('sucursal_id', $sucursalId);
            });
        }

        $piezas = $query->get();

        $totalDisponibles = 0;
        $totalReservadas = 0;
        $totalVendidas = 0;
        $porExistencia = [];

        foreach ($piezas as $pieza) {
            $estado = $pieza->estado;
            if ($estado === 'disponible') {
                $totalDisponibles++;
            } elseif ($estado === 'reservada') {
                $totalReservadas++;
            } elseif ($estado === 'vendida') {
                $totalVendidas++;
            }

            $existenciaId = $pieza->existencia_id;
            if (! isset($porExistencia[$existenciaId])) {
                $porExistencia[$existenciaId] = [
                    'existencia_id' => $existenciaId,
                    'sku' => $pieza->existencia?->varianteSku?->sku ?? 'N/A',
                    'producto' => $pieza->existencia?->varianteSku?->producto?->nombre ?? 'N/A',
                    'sucursal' => $pieza->existencia?->sucursal?->nombre ?? 'N/A',
                    'disponibles' => 0,
                    'reservadas' => 0,
                    'vendidas' => 0,
                ];
            }

            if ($estado === 'disponible') {
                $porExistencia[$existenciaId]['disponibles']++;
            } elseif ($estado === 'reservada') {
                $porExistencia[$existenciaId]['reservadas']++;
            } elseif ($estado === 'vendida') {
                $porExistencia[$existenciaId]['vendidas']++;
            }
        }

        $totalPiezas = $totalDisponibles + $totalReservadas + $totalVendidas;
        $tasaRotacion = $totalPiezas > 0
            ? round(($totalVendidas / $totalPiezas) * 100, 2)
            : 0.0;

        return [
            'total_piezas' => $totalPiezas,
            'total_disponibles' => $totalDisponibles,
            'total_reservadas' => $totalReservadas,
            'total_vendidas' => $totalVendidas,
            'tasa_rotacion_porcentaje' => $tasaRotacion,
            'detalle_por_existencia' => array_values($porExistencia),
        ];
    }

    /**
     * Consulta existencias de inventario que estan por debajo del stock minimo configurado.
     *
     * @return Collection<int, Existencia>
     */
    public function stockBajoMinimo(?int $sucursalId = null): Collection
    {
        $query = Existencia::query()
            ->whereColumn('cantidad', '<', 'stock_minimo')
            ->where('stock_minimo', '>', 0)
            ->with(['varianteSku.producto', 'sucursal']);

        if ($sucursalId !== null) {
            $query->where('sucursal_id', $sucursalId);
        }

        return $query->get();
    }

    /**
     * Calcula el resumen ejecutivo consolidado del estado del acervo, inventario actual y bóveda.
     *
     * @return array{
     *     total_skus: int,
     *     total_unidades_boveda: int,
     *     skus_agotados: int,
     *     alertas_stock_bajo: int,
     *     reservas_activas: int,
     *     valor_total_acervo: float,
     *     total_piezas_certificadas: int,
     *     total_pedidos_confirmados: int,
     *     ingresos_acumulados: float,
     *     desglose_categorias: array<string, array{etiqueta: string, skus: int, unidades: int, valor_tasado: float}>
     * }
     */
    public function resumenEstadoAcervo(?int $sucursalId = null, ?string $tipoCatalogo = null): array
    {
        $filas = $this->obtenerFilasExportacion($sucursalId, $tipoCatalogo);

        $totalSkus = $filas->count();
        $totalUnidadesBoveda = (int) $filas->sum('stock_boveda');
        $skusAgotados = $filas->where('stock_boveda', '<=', 0)->count();
        $alertasStockBajo = $filas->where('es_stock_bajo', true)->count();
        $valorTotalAcervo = round((float) $filas->sum('valor_total'), 2);

        $reservasQuery = ReservaStock::query()->where('estado', 'activa');
        if ($sucursalId !== null) {
            $reservasQuery->whereHas('existencia', function ($q) use ($sucursalId): void {
                $q->where('sucursal_id', $sucursalId);
            });
        }
        $reservasActivas = (int) $reservasQuery->sum('cantidad');

        $piezasQuery = PiezaUnica::query();
        if ($sucursalId !== null) {
            $piezasQuery->whereHas('existencia', function ($q) use ($sucursalId): void {
                $q->where('sucursal_id', $sucursalId);
            });
        }
        $totalPiezasCertificadas = $piezasQuery->count();

        $totalPedidosConfirmados = Pedido::query()
            ->whereIn('estatus', ['pagado', 'confirmado', 'preparando', 'enviado', 'entregado'])
            ->count();

        $ingresosAcumulados = round((float) Pago::query()
            ->whereIn('estatus', ['aprobado', 'completado', 'pagado'])
            ->sum('monto'), 2);

        if ($ingresosAcumulados <= 0.0) {
            $ingresosAcumulados = round((float) Pedido::query()
                ->whereIn('estatus', ['pagado', 'confirmado', 'preparando', 'enviado', 'entregado'])
                ->sum('total'), 2);
        }

        $mapaEtiquetas = [
            'libro' => 'Libros Raros & Incunables',
            'disco' => 'Discos & Vinilos Históricos',
            'coleccionable' => 'Memorabilia & Archivo',
        ];

        $desgloseCategorias = [];
        foreach ($mapaEtiquetas as $clave => $etiqueta) {
            $grupo = $filas->where('tipo_catalogo', $clave);
            $desgloseCategorias[$clave] = [
                'etiqueta' => $etiqueta,
                'skus' => $grupo->count(),
                'unidades' => (int) $grupo->sum('stock_boveda'),
                'valor_tasado' => round((float) $grupo->sum('valor_total'), 2),
            ];
        }

        return [
            'total_skus' => $totalSkus,
            'total_unidades_boveda' => $totalUnidadesBoveda,
            'skus_agotados' => $skusAgotados,
            'alertas_stock_bajo' => $alertasStockBajo,
            'reservas_activas' => $reservasActivas,
            'valor_total_acervo' => $valorTotalAcervo,
            'total_piezas_certificadas' => $totalPiezasCertificadas,
            'total_pedidos_confirmados' => $totalPedidosConfirmados,
            'ingresos_acumulados' => $ingresosAcumulados,
            'desglose_categorias' => $desgloseCategorias,
        ];
    }

    /**
     * Obtiene las transacciones y pedidos más recientes del sistema.
     *
     * @return Collection<int, Pedido>
     */
    public function transaccionesRecientes(int $limite = 12): Collection
    {
        return Pedido::query()
            ->with(['cliente.usuario', 'detalles.varianteSku.producto', 'pagos', 'envios'])
            ->latest('id')
            ->limit($limite)
            ->get();
    }

    /**
     * Obtiene todas las filas normalizadas de existencias y catálogo para vistas de reporte y exportación CSV/PDF.
     *
     * @return Collection<int, array{
     *     existencia_id: int,
     *     sku: string,
     *     producto: string,
     *     tipo_catalogo: string,
     *     categoria_etiqueta: string,
     *     condicion: string,
     *     sucursal: string,
     *     certificados_coa: string,
     *     stock_boveda: int,
     *     stock_minimo: int,
     *     precio_unitario: float,
     *     valor_total: float,
     *     es_stock_bajo: bool,
     *     estado_stock: string
     * }>
     */
    public function obtenerFilasExportacion(?int $sucursalId = null, ?string $tipoCatalogo = null): Collection
    {
        $query = Existencia::query()
            ->with([
                'varianteSku.producto.catalogo',
                'sucursal',
                'piezasUnicas',
            ]);

        if ($sucursalId !== null) {
            $query->where('sucursal_id', $sucursalId);
        }

        if ($tipoCatalogo !== null && $tipoCatalogo !== '') {
            $query->whereHas('varianteSku.producto.catalogo', function ($q) use ($tipoCatalogo): void {
                $q->where('tipo', $tipoCatalogo);
            });
        }

        $existencias = $query->orderBy('sucursal_id')->orderBy('id')->get();

        $mapaTipos = [
            'libro' => 'Libros Raros & Incunables',
            'disco' => 'Discos & Vinilos Históricos',
            'coleccionable' => 'Memorabilia & Archivo',
        ];

        return $existencias->map(function (Existencia $existencia) use ($mapaTipos): array {
            $variante = $existencia->varianteSku;
            $producto = $variante?->producto;
            $catalogo = $producto?->catalogo;
            $tipo = (string) ($catalogo?->tipo ?? 'coleccionable');
            $categoriaEtiqueta = $catalogo?->nombre
                ?? ($mapaTipos[$tipo] ?? ucfirst($tipo));

            $precioUnitario = round((float) ($variante?->precio ?? $producto?->precio ?? 0.0), 2);
            $cantidad = (int) $existencia->cantidad;
            $stockMinimo = (int) $existencia->stock_minimo;
            $valorTotal = round($cantidad * $precioUnitario, 2);

            $esStockBajo = $stockMinimo > 0 && $cantidad < $stockMinimo;
            if ($cantidad <= 0) {
                $estadoStock = 'AGOTADO';
            } elseif ($esStockBajo) {
                $estadoStock = 'BAJO MINIMO';
            } else {
                $estadoStock = 'EN BOVEDA';
            }

            $coas = $existencia->piezasUnicas
                ->map(fn (PiezaUnica $p): string => trim(($p->certificado ?: $p->numero_serie).' ('.$p->estado.')'))
                ->filter()
                ->implode(' | ');

            return [
                'existencia_id' => $existencia->id,
                'sku' => (string) ($variante?->sku ?? 'SIN-SKU'),
                'producto' => (string) ($producto?->nombre ?? 'Pieza sin título'),
                'tipo_catalogo' => $tipo,
                'categoria_etiqueta' => (string) $categoriaEtiqueta,
                'condicion' => (string) ($variante?->descripcion ?? 'Edición de Archivo'),
                'sucursal' => (string) ($existencia->sucursal?->nombre ?? 'Bóveda Central'),
                'certificados_coa' => $coas !== '' ? $coas : 'Lote Estándar Curatorial',
                'stock_boveda' => $cantidad,
                'stock_minimo' => $stockMinimo,
                'precio_unitario' => $precioUnitario,
                'valor_total' => $valorTotal,
                'es_stock_bajo' => $esStockBajo,
                'estado_stock' => $estadoStock,
            ];
        });
    }

    /**
     * Genera el contenido CSV completo (con BOM UTF-8 para compatibilidad con Excel) del inventario y acervo.
     */
    public function generarCsvInventario(?int $sucursalId = null, ?string $tipoCatalogo = null): string
    {
        $filas = $this->obtenerFilasExportacion($sucursalId, $tipoCatalogo);
        $resumen = $this->resumenEstadoAcervo($sucursalId, $tipoCatalogo);

        $handle = fopen('php://temp', 'r+');
        if ($handle === false) {
            return '';
        }

        // BOM UTF-8 para apertura directa con acentos en Microsoft Excel / LibreOffice
        fwrite($handle, "\xEF\xBB\xBF");

        fputcsv($handle, [
            'SKU',
            'Pieza / Título Curatorial',
            'Categoría del Acervo',
            'Graduación / Condición',
            'Sucursal / Bóveda',
            'Certificados COA / Series',
            'Stock en Bóveda',
            'Stock Mínimo',
            'Precio Unitario (MXN)',
            'Valor Total Tasado (MXN)',
            'Estatus de Disponibilidad',
        ]);

        foreach ($filas as $fila) {
            fputcsv($handle, [
                $fila['sku'],
                $fila['producto'],
                $fila['categoria_etiqueta'],
                $fila['condicion'],
                $fila['sucursal'],
                $fila['certificados_coa'],
                $fila['stock_boveda'],
                $fila['stock_minimo'],
                number_format($fila['precio_unitario'], 2, '.', ''),
                number_format($fila['valor_total'], 2, '.', ''),
                $fila['estado_stock'],
            ]);
        }

        // Fila en blanco y resumen institucional al pie del archivo CSV
        fputcsv($handle, []);
        fputcsv($handle, [
            'RESUMEN OFICIAL ARCHIVUM',
            'Total SKUs: '.$resumen['total_skus'],
            'Unidades en Bóveda: '.$resumen['total_unidades_boveda'],
            'Agotados: '.$resumen['skus_agotados'],
            'Alertas Stock Bajo: '.$resumen['alertas_stock_bajo'],
            'Lotes Certificados COA: '.$resumen['total_piezas_certificadas'],
            $resumen['total_unidades_boveda'],
            '',
            'VALOR TOTAL ACERVO (MXN):',
            number_format($resumen['valor_total_acervo'], 2, '.', ''),
            'CERTIFICADO',
        ]);

        rewind($handle);
        $contenido = (string) stream_get_contents($handle);
        fclose($handle);

        return $contenido;
    }

    /**
     * Genera un documento PDF oficial de ARCHIVUM con logotipo vectorial, encabezado institucional,
     * métricas ejecutivas y el desglose de inventario y lotes certificados.
     * Si Dompdf está instalado en el entorno, lo utiliza; de lo contrario, construye un binario
     * PDF 1.4 nativo de alta precisión sin dependencias externas.
     *
     * @param  array{
     *     folio_reporte: string,
     *     fecha_emision: string,
     *     filtro_sucursal: string,
     *     filtro_categoria: string,
     *     resumen: array<string, mixed>,
     *     valoracion_sucursales: array<int, array<string, mixed>>,
     *     rotacion_piezas: array<string, mixed>,
     *     filas: Collection<int, array<string, mixed>>
     * }  $datosReporte
     */
    public function generarPdfOficial(array $datosReporte, ?string $htmlRenderizado = null): string
    {
        if ($htmlRenderizado !== null) {
            if (class_exists(Pdf::class)) {
                /** @phpstan-ignore-next-line */
                return Pdf::loadHTML($htmlRenderizado)
                    ->setPaper('letter', 'landscape')
                    ->output();
            }

            if (class_exists(Dompdf::class)) {
                /** @phpstan-ignore-next-line */
                $dompdf = new Dompdf;
                $dompdf->loadHtml($htmlRenderizado);
                $dompdf->setPaper('letter', 'landscape');
                $dompdf->render();

                return (string) $dompdf->output();
            }
        }

        return $this->construirPdfBinarioArchivum($datosReporte);
    }

    /**
     * Construye un archivo PDF 1.4 estándar, multipágina y auto-contenido con el emblema y formato
     * oficial de ARCHIVUM Curatorial Vault.
     *
     * @param  array<string, mixed>  $datos
     */
    private function construirPdfBinarioArchivum(array $datos): string
    {
        /** @var Collection<int, array<string, mixed>> $filas */
        $filas = $datos['filas'] ?? collect();
        /** @var array<string, mixed> $resumen */
        $resumen = $datos['resumen'] ?? [];
        /** @var array<int, array<string, mixed>> $valoracionSucursales */
        $valoracionSucursales = $datos['valoracion_sucursales'] ?? [];

        $chunks = $filas->values()->chunk(14);
        if ($chunks->isEmpty()) {
            $chunks = collect([collect()]);
        }

        $totalPaginas = $chunks->count();
        $streamsPaginas = [];

        foreach ($chunks as $indice => $loteFilas) {
            $numeroPagina = $indice + 1;
            $streamsPaginas[] = $this->generarContenidoPaginaPdf(
                $datos,
                $resumen,
                $valoracionSucursales,
                $loteFilas,
                $numeroPagina,
                $totalPaginas
            );
        }

        // Ensamblaje de objetos PDF 1.4 (Landscape Letter: 792 x 612 pt)
        $objetos = [];
        $objetos[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objetos[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objetos[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';
        $objetos[5] = '<< /Type /Font /Subtype /Type1 /BaseFont /Times-Bold /Encoding /WinAnsiEncoding >>';

        $kids = [];
        $siguienteId = 6;

        foreach ($streamsPaginas as $stream) {
            $pageId = $siguienteId++;
            $contentId = $siguienteId++;
            $kids[] = "{$pageId} 0 R";

            $longitud = strlen($stream);
            $objetos[$pageId] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 792 612] /Resources << /Font << /F1 3 0 R /F2 4 0 R /F3 5 0 R >> >> /Contents {$contentId} 0 R >>";
            $objetos[$contentId] = "<< /Length {$longitud} >>\nstream\n{$stream}\nendstream";
        }

        $listaKids = implode(' ', $kids);
        $objetos[2] = "<< /Type /Pages /Kids [ {$listaKids} ] /Count {$totalPaginas} >>";
        ksort($objetos);

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [];

        foreach ($objetos as $id => $cuerpo) {
            $offsets[$id] = strlen($pdf);
            $pdf .= "{$id} 0 obj\n{$cuerpo}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $totalObjetos = count($objetos) + 1;
        $pdf .= "xref\n0 {$totalObjetos}\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i < $totalObjetos; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i] ?? 0);
        }

        $pdf .= "trailer\n<< /Size {$totalObjetos} /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }

    /**
     * Genera las instrucciones gráficas PostScript/PDF para una página del reporte oficial ARCHIVUM.
     *
     * @param  array<string, mixed>  $datos
     * @param  array<string, mixed>  $resumen
     * @param  array<int, array<string, mixed>>  $valoracionSucursales
     * @param  Collection<int, array<string, mixed>>  $loteFilas
     */
    private function generarContenidoPaginaPdf(
        array $datos,
        array $resumen,
        array $valoracionSucursales,
        Collection $loteFilas,
        int $paginaActual,
        int $totalPaginas
    ): string {
        $ops = [];

        // Fondo crema editorial suave
        $ops[] = '0.98 0.97 0.94 rg 0 0 792 612 re f';

        // Banda superior institucional color carbón / bronce oscuro
        $ops[] = '0.10 0.09 0.08 rg 0 538 792 74 re f';
        // Línea dorada decorativa bajo el encabezado
        $ops[] = '0.77 0.63 0.35 RG 2 w 0 537 m 792 537 l S';

        // Logotipo Emblema Oficial de ARCHIVUM (Escudo Geométrico en Oro a la izquierda)
        $ops[] = '0.77 0.63 0.35 RG 1.5 w 36 552 44 44 re S';
        $ops[] = '0.77 0.63 0.35 RG 0.8 w 41 557 34 34 re S';
        $ops[] = $this->pdfTexto('F3', 20, 51, 567, 'A', '0.77 0.63 0.35');

        // Títulos del Encabezado Oficial
        $ops[] = $this->pdfTexto('F3', 18, 92, 582, 'ARCHIVUM', '0.96 0.93 0.86');
        $ops[] = $this->pdfTexto('F2', 8.5, 92, 568, 'BOVEDA CURATORIAL & SISTEMA INTEGRAL DE GESTION DE INVENTARIO', '0.77 0.63 0.35');
        $ops[] = $this->pdfTexto('F1', 7.5, 92, 555, 'REPORTE OFICIAL DE ESTADO DEL ACERVO, EXISTENCIAS EN BOVEDA Y LOTES CERTIFICADOS (COA)', '0.80 0.78 0.73');

        // Metadatos del reporte a la derecha del encabezado
        $folio = (string) ($datos['folio_reporte'] ?? 'ARC-REP-0001');
        $fecha = (string) ($datos['fecha_emision'] ?? now()->format('d/m/Y H:i'));
        $sucursalFiltro = (string) ($datos['filtro_sucursal'] ?? 'Todas las Bóvedas Activas');
        $categoriaFiltro = (string) ($datos['filtro_categoria'] ?? 'Acervo Completo');

        $ops[] = $this->pdfTexto('F2', 8.5, 545, 586, 'FOLIO OFICIAL: '.$folio, '0.77 0.63 0.35');
        $ops[] = $this->pdfTexto('F1', 7.5, 545, 574, 'Emision: '.$fecha, '0.92 0.90 0.86');
        $ops[] = $this->pdfTexto('F1', 7.5, 545, 563, 'Sede: '.$this->truncarTexto($sucursalFiltro, 36), '0.92 0.90 0.86');
        $ops[] = $this->pdfTexto('F1', 7.5, 545, 552, 'Coleccion: '.$this->truncarTexto($categoriaFiltro, 34), '0.92 0.90 0.86');

        // Tarjetas de Resumen Ejecutivo (KPIs)
        $kpis = [
            [
                'titulo' => 'SKUS CATALOGADOS',
                'valor' => (string) ($resumen['total_skus'] ?? 0).' referencias',
                'sub' => 'Agotados: '.(string) ($resumen['skus_agotados'] ?? 0).' | Stock bajo: '.(string) ($resumen['alertas_stock_bajo'] ?? 0),
            ],
            [
                'titulo' => 'UNIDADES EN BOVEDA',
                'valor' => (string) ($resumen['total_unidades_boveda'] ?? 0).' ejemplares',
                'sub' => 'Reservas activas: '.(string) ($resumen['reservas_activas'] ?? 0).' uds.',
            ],
            [
                'titulo' => 'VALORACION DEL ACERVO',
                'valor' => '$'.number_format((float) ($resumen['valor_total_acervo'] ?? 0.0), 2).' MXN',
                'sub' => 'Tasacion curatorial asegurada',
            ],
            [
                'titulo' => 'LOTES CERTIFICADOS COA',
                'valor' => (string) ($resumen['total_piezas_certificadas'] ?? 0).' certificados',
                'sub' => 'Ingresos: $'.number_format((float) ($resumen['ingresos_acumulados'] ?? 0.0), 2).' MXN',
            ],
        ];

        $kpiX = 36;
        foreach ($kpis as $kpi) {
            $ops[] = '0.95 0.93 0.88 rg '.$kpiX.' 478 174 48 re f';
            $ops[] = '0.78 0.72 0.60 RG 0.7 w '.$kpiX.' 478 174 48 re S';
            $ops[] = $this->pdfTexto('F2', 7, $kpiX + 8, 514, $kpi['titulo'], '0.45 0.36 0.20');
            $ops[] = $this->pdfTexto('F2', 11, $kpiX + 8, 498, $kpi['valor'], '0.12 0.11 0.10');
            $ops[] = $this->pdfTexto('F1', 6.8, $kpiX + 8, 485, $kpi['sub'], '0.35 0.33 0.30');
            $kpiX += 182;
        }

        // Resumen rápido por sucursal en cuadrícula de 3 columnas iguales (sin cortes en "Almacén General")
        if (! empty($valoracionSucursales)) {
            $ops[] = '0.91 0.88 0.81 rg 36 450 720 22 re f';
            $ops[] = '0.78 0.72 0.60 RG 0.6 w 36 450 720 22 re S';
            $ops[] = $this->pdfTexto('F2', 6.8, 42, 463, 'VALORACION POR SEDE:', '0.45 0.36 0.20');

            $numSedes = max(1, count($valoracionSucursales));
            $anchoColSede = (int) floor(708 / min(3, $numSedes));
            foreach (array_values($valoracionSucursales) as $idxSede => $sedeVal) {
                $colX = 42 + (($idxSede % 3) * $anchoColSede);
                $textoSede = $sedeVal['sucursal_nombre'].': '.$sedeVal['total_unidades'].' uds ($'.number_format((float) $sedeVal['valor_total_inventario'], 2).')';
                $ops[] = $this->pdfTexto('F2', 6.8, $colX, 454, $textoSede, '0.16 0.14 0.12');
            }
        }

        // Encabezado de Tabla de Inventario y Lotes Certificados
        // Proporciones sobre 720pt (x=36..756):
        // SKU 11% (79pt: 36..115), PIEZA/TITULO 24% (173pt: 115..288), CATEGORIA 9% (65pt: 288..353),
        // BOVEDA/SEDE 13% (93pt: 353..446), COA/LOTE 12% (87pt: 446..533), STOCK 11% (79pt: 533..612),
        // PRECIO U. 10% (72pt: 612..684), VALOR TOTAL 10% (72pt: 684..756)
        $tablaTop = 426;
        $ops[] = '0.16 0.14 0.12 rg 36 '.$tablaTop.' 720 18 re f';
        $ops[] = $this->pdfTexto('F2', 7.0, 40, $tablaTop + 6, 'SKU', '0.96 0.93 0.86');
        $ops[] = $this->pdfTexto('F2', 7.0, 118, $tablaTop + 6, 'PIEZA / TITULO CURATORIAL', '0.96 0.93 0.86');
        $ops[] = $this->pdfTexto('F2', 7.0, 291, $tablaTop + 6, 'CATEGORIA', '0.96 0.93 0.86');
        $ops[] = $this->pdfTexto('F2', 7.0, 356, $tablaTop + 6, 'BOVEDA / SEDE', '0.96 0.93 0.86');
        $ops[] = $this->pdfTexto('F2', 7.0, 449, $tablaTop + 6, 'COA / LOTE', '0.96 0.93 0.86');
        $ops[] = $this->pdfTextoCentrado('F2', 7.0, 533, 612, $tablaTop + 6, 'STOCK', '0.96 0.93 0.86');
        $ops[] = $this->pdfTextoDerecha('F2', 7.0, 680, $tablaTop + 6, 'PRECIO U.', '0.96 0.93 0.86');
        $ops[] = $this->pdfTextoDerecha('F2', 7.0, 752, $tablaTop + 6, 'VALOR TOTAL', '0.96 0.93 0.86');

        $alturaFila = 24;
        $filaY = $tablaTop - $alturaFila;
        $par = false;

        foreach ($loteFilas as $fila) {
            if ($par) {
                $ops[] = '0.95 0.93 0.89 rg 36 '.$filaY.' 720 '.$alturaFila.' re f';
            } else {
                $ops[] = '0.99 0.98 0.96 rg 36 '.$filaY.' 720 '.$alturaFila.' re f';
            }
            $ops[] = '0.86 0.83 0.77 RG 0.4 w 36 '.$filaY.' 720 '.$alturaFila.' re S';

            $colorStock = ((int) $fila['stock_boveda'] <= 0)
                ? '0.72 0.15 0.15'
                : ($fila['es_stock_bajo'] ? '0.75 0.45 0.08' : '0.12 0.38 0.22');

            // 1. SKU (11%: x=40..113) en hasta 2 líneas sin truncar
            $lineasSku = $this->dividirEnLineas((string) $fila['sku'], 16, 2);
            $ops[] = $this->pdfTexto('F2', 6.6, 40, $filaY + 14, $lineasSku[0] ?? '', '0.15 0.14 0.12');
            if (! empty($lineasSku[1])) {
                $ops[] = $this->pdfTexto('F2', 6.3, 40, $filaY + 6, $lineasSku[1], '0.15 0.14 0.12');
            }

            // 2. PIEZA / TITULO CURATORIAL (24%: x=118..286) en 2 líneas con salto natural de palabra
            $lineasTitulo = $this->dividirEnLineas((string) $fila['producto'], 36, 2);
            $ops[] = $this->pdfTexto('F1', 6.8, 118, $filaY + 14, $lineasTitulo[0] ?? '', '0.12 0.11 0.10');
            if (! empty($lineasTitulo[1])) {
                $ops[] = $this->pdfTexto('F1', 6.6, 118, $filaY + 6, $lineasTitulo[1], '0.25 0.23 0.20');
            }

            // 3. CATEGORIA (9%: x=291..351) en hasta 2 líneas
            $lineasCat = $this->dividirEnLineas((string) $fila['categoria_etiqueta'], 14, 2);
            $ops[] = $this->pdfTexto('F1', 6.5, 291, $filaY + 14, $lineasCat[0] ?? '', '0.30 0.28 0.24');
            if (! empty($lineasCat[1])) {
                $ops[] = $this->pdfTexto('F1', 6.3, 291, $filaY + 6, $lineasCat[1], '0.30 0.28 0.24');
            }

            // 4. BOVEDA / SEDE (13%: x=356..444) en hasta 2 líneas sin cortar "Sucursal Central Bellas Artes"
            $lineasSede = $this->dividirEnLineas((string) $fila['sucursal'], 20, 2);
            $ops[] = $this->pdfTexto('F1', 6.5, 356, $filaY + 14, $lineasSede[0] ?? '', '0.30 0.28 0.24');
            if (! empty($lineasSede[1])) {
                $ops[] = $this->pdfTexto('F1', 6.3, 356, $filaY + 6, $lineasSede[1], '0.30 0.28 0.24');
            }

            // 5. COA / LOTE (12%: x=449..531) en hasta 2 líneas completas
            $lineasCoa = $this->dividirEnLineas((string) $fila['certificados_coa'], 19, 2);
            $ops[] = $this->pdfTexto('F1', 6.3, 449, $filaY + 14, $lineasCoa[0] ?? '', '0.35 0.30 0.22');
            if (! empty($lineasCoa[1])) {
                $ops[] = $this->pdfTexto('F1', 6.1, 449, $filaY + 6, $lineasCoa[1], '0.35 0.30 0.22');
            }

            // 6. STOCK (11%: x=533..612) centrado: número en negrita arriba y [EN BOVEDA] debajo para que NUNCA toque PRECIO U.
            $ops[] = $this->pdfTextoCentrado('F2', 7.5, 533, 612, $filaY + 14, (string) $fila['stock_boveda'], $colorStock);
            $ops[] = $this->pdfTextoCentrado('F2', 5.8, 533, 612, $filaY + 6, '['.$fila['estado_stock'].']', $colorStock);

            // 7. PRECIO U. (10%: x=612..684) alineado a la derecha en x=680
            $ops[] = $this->pdfTextoDerecha('F1', 6.8, 680, $filaY + 10, '$'.number_format((float) $fila['precio_unitario'], 2), '0.15 0.14 0.12');

            // 8. VALOR TOTAL (10%: x=684..756) alineado a la derecha en x=752
            $ops[] = $this->pdfTextoDerecha('F2', 7.0, 752, $filaY + 10, '$'.number_format((float) $fila['valor_total'], 2), '0.12 0.11 0.10');

            $filaY -= $alturaFila;
            $par = ! $par;
        }

        // Pie de página oficial con sello de autenticidad curatorial
        $ops[] = '0.77 0.63 0.35 RG 1 w 36 48 m 756 48 l S';
        $ops[] = $this->pdfTexto(
            'F2',
            7.2,
            36,
            34,
            'ARCHIVUM CURATORIAL VAULT — DOCUMENTO OFICIAL DE AUDITORIA E INVENTARIO CERTIFICADO',
            '0.25 0.22 0.18'
        );
        $ops[] = $this->pdfTexto(
            'F1',
            6.8,
            36,
            23,
            'Validez interna y patrimonial para control de boveda, seguros de coleccion y trazabilidad COA.',
            '0.45 0.42 0.38'
        );
        $ops[] = $this->pdfTexto(
            'F2',
            7.5,
            675,
            30,
            "Pagina {$paginaActual} de {$totalPaginas}",
            '0.25 0.22 0.18'
        );

        return implode("\n", $ops);
    }

    /**
     * Genera una instrucción de texto PDF escapada en codificación WinAnsi (ISO-8859-1).
     */
    private function pdfTexto(string $fuente, float $tamano, float $x, float $y, string $texto, string $rgb = '0 0 0'): string
    {
        $limpio = $this->normalizarTextoPdf($texto);

        return sprintf(
            'BT /%s %.2F Tf %s rg 1 0 0 1 %.2F %.2F Tm (%s) Tj ET',
            $fuente,
            $tamano,
            $rgb,
            $x,
            $y,
            $limpio
        );
    }

    /**
     * Convierte cadenas UTF-8 a WinAnsi/ISO-8859-1 seguro y escapa paréntesis/barras para sintaxis PDF.
     */
    private function normalizarTextoPdf(string $texto): string
    {
        $reemplazos = [
            '—' => '-',
            '–' => '-',
            '“' => '"',
            '”' => '"',
            '‘' => "'",
            '’' => "'",
            '«' => '"',
            '»' => '"',
            '•' => '*',
            '…' => '...',
        ];

        $texto = strtr($texto, $reemplazos);
        $convertido = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $texto);
        if ($convertido === false) {
            $convertido = preg_replace('/[^\x20-\x7E]/', '', $texto) ?? '';
        }

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $convertido);
    }

    /**
     * Genera una instrucción de texto PDF centrada horizontalmente entre $xInicio y $xFin.
     */
    private function pdfTextoCentrado(string $fuente, float $tamano, float $xInicio, float $xFin, float $y, string $texto, string $rgb = '0 0 0'): string
    {
        $anchoEstimado = mb_strlen($texto) * ($tamano * 0.52);
        $anchoColumna = max(1.0, $xFin - $xInicio);
        $x = $xInicio + max(2.0, ($anchoColumna - $anchoEstimado) / 2.0);

        return $this->pdfTexto($fuente, $tamano, $x, $y, $texto, $rgb);
    }

    /**
     * Genera una instrucción de texto PDF alineada a la derecha en la coordenada $xDerecha.
     */
    private function pdfTextoDerecha(string $fuente, float $tamano, float $xDerecha, float $y, string $texto, string $rgb = '0 0 0'): string
    {
        $anchoEstimado = mb_strlen($texto) * ($tamano * 0.53);
        $x = max(36.0, $xDerecha - $anchoEstimado);

        return $this->pdfTexto($fuente, $tamano, $x, $y, $texto, $rgb);
    }

    /**
     * Divide un texto largo en múltiples líneas por palabra para permitir salto de línea limpio en lugar de cortar con '..'.
     *
     * @return list<string>
     */
    private function dividirEnLineas(string $texto, int $maxCaracteresPorLinea, int $maxLineas = 2): array
    {
        $texto = trim(preg_replace('/\s+/', ' ', $texto) ?? '');
        if ($texto === '') {
            return [''];
        }

        if (mb_strlen($texto) <= $maxCaracteresPorLinea) {
            return [$texto];
        }

        $envuelto = wordwrap($texto, $maxCaracteresPorLinea, "\n", true);
        $partes = array_values(array_filter(array_map('trim', explode("\n", $envuelto)), fn (string $l): bool => $l !== ''));

        if (count($partes) <= $maxLineas) {
            return $partes;
        }

        $resultado = array_slice($partes, 0, $maxLineas - 1);
        $ultimaLinea = implode(' ', array_slice($partes, $maxLineas - 1));
        if (mb_strlen($ultimaLinea) > $maxCaracteresPorLinea + 4) {
            $ultimaLinea = mb_substr($ultimaLinea, 0, $maxCaracteresPorLinea + 3);
        }
        $resultado[] = $ultimaLinea;

        return $resultado;
    }

    /**
     * Trunca un texto a una longitud máxima para evitar desbordamiento de columna en el PDF.
     */
    private function truncarTexto(string $texto, int $maximo): string
    {
        if (mb_strlen($texto) <= $maximo) {
            return $texto;
        }

        return mb_substr($texto, 0, max(1, $maximo - 2)).'..';
    }
}
