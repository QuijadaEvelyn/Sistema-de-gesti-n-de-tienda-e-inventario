<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sucursal;
use App\Services\ReporteInventarioService;
use App\Services\SucursalContextoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteController extends Controller
{
    public function __construct(
        private readonly ReporteInventarioService $reporteInventarioService,
        private readonly SucursalContextoService $sucursalContextoService
    ) {}

    /**
     * Muestra el centro de reportes calculados del acervo, inventario en bóveda,
     * lotes certificados y transacciones recientes con opciones de exportación CSV y PDF.
     */
    public function index(Request $request): View
    {
        $sucursalActiva = $this->sucursalContextoService->resolverSucursalAdmin($request, $request->user());
        $sucursalId = $sucursalActiva?->id;
        $tipoCatalogo = $request->filled('tipo_catalogo') ? (string) $request->input('tipo_catalogo') : null;

        $sucursales = Sucursal::query()->where('activa', true)->orderBy('nombre')->get();
        $resumen = $this->reporteInventarioService->resumenEstadoAcervo($sucursalId, $tipoCatalogo);
        $valoracionSucursales = $this->reporteInventarioService->valoracionPorSucursal($sucursalId);
        $rotacionPiezas = $this->reporteInventarioService->rotacionPiezasUnicas($sucursalId);
        $alertasStockBajo = $this->reporteInventarioService->stockBajoMinimo($sucursalId);
        $filasInventario = $this->reporteInventarioService->obtenerFilasExportacion($sucursalId, $tipoCatalogo);
        $transaccionesRecientes = $this->reporteInventarioService->transaccionesRecientes(10);

        return view('admin.reportes.index', [
            'sucursales' => $sucursales,
            'sucursalActiva' => $sucursalActiva,
            'sucursalSeleccionada' => $sucursalId,
            'categoriaSeleccionada' => $tipoCatalogo,
            'resumen' => $resumen,
            'valoracionSucursales' => $valoracionSucursales,
            'rotacionPiezas' => $rotacionPiezas,
            'alertasStockBajo' => $alertasStockBajo,
            'filasInventario' => $filasInventario,
            'transaccionesRecientes' => $transaccionesRecientes,
        ]);
    }

    /**
     * Exporta los datos del catálogo e inventario actual en formato .csv descargable.
     */
    public function exportarCsv(Request $request): StreamedResponse
    {
        $sucursalId = $request->filled('sucursal_id') ? (int) $request->input('sucursal_id') : null;
        $tipoCatalogo = $request->filled('tipo_catalogo') ? (string) $request->input('tipo_catalogo') : null;

        $contenidoCsv = $this->reporteInventarioService->generarCsvInventario($sucursalId, $tipoCatalogo);
        $nombreArchivo = 'archivum-inventario-boveda-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($contenidoCsv): void {
            echo $contenidoCsv;
        }, $nombreArchivo, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    /**
     * Emite el reporte profesional en formato .pdf oficial con logotipo de ARCHIVUM,
     * valoración del acervo y desglose de inventario / lotes certificados.
     */
    public function exportarPdf(Request $request): Response|View
    {
        $sucursalId = $request->filled('sucursal_id') ? (int) $request->input('sucursal_id') : null;
        $tipoCatalogo = $request->filled('tipo_catalogo') ? (string) $request->input('tipo_catalogo') : null;

        $sucursalModelo = $sucursalId !== null ? Sucursal::find($sucursalId) : null;
        $mapaCategorias = [
            'libro' => 'Libros Raros & Incunables',
            'disco' => 'Discos & Vinilos Históricos',
            'coleccionable' => 'Memorabilia & Archivo',
        ];

        $datosReporte = [
            'folio_reporte' => 'ARC-CERT-'.now()->format('Ymd-His'),
            'fecha_emision' => now()->format('d/m/Y H:i:s'),
            'filtro_sucursal' => $sucursalModelo?->nombre ?? 'Todas las Bóvedas y Sedes Activas',
            'filtro_categoria' => ($tipoCatalogo !== null && isset($mapaCategorias[$tipoCatalogo]))
                ? $mapaCategorias[$tipoCatalogo]
                : 'Acervo Curatorial Completo',
            'resumen' => $this->reporteInventarioService->resumenEstadoAcervo($sucursalId, $tipoCatalogo),
            'valoracion_sucursales' => $this->reporteInventarioService->valoracionPorSucursal($sucursalId),
            'rotacion_piezas' => $this->reporteInventarioService->rotacionPiezasUnicas($sucursalId),
            'filas' => $this->reporteInventarioService->obtenerFilasExportacion($sucursalId, $tipoCatalogo),
            'transacciones' => $this->reporteInventarioService->transaccionesRecientes(8),
        ];

        if ($request->boolean('vista_previa')) {
            return view('admin.reportes.pdf', $datosReporte);
        }

        $htmlRenderizado = view('admin.reportes.pdf', $datosReporte)->render();
        $pdfBinario = $this->reporteInventarioService->generarPdfOficial($datosReporte, $htmlRenderizado);
        $nombreArchivo = 'archivum-reporte-oficial-'.now()->format('Ymd-His').'.pdf';

        return response($pdfBinario, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$nombreArchivo.'"',
            'Content-Length' => (string) strlen($pdfBinario),
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
}
