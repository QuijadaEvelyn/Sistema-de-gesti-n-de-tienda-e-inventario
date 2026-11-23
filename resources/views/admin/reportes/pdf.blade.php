<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Oficial de Catálogo e Inventario — ARCHIVUM ({{ $folio_reporte }})</title>
    <style>
        @page {
            size: letter landscape;
            margin: 14mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Georgia, "Times New Roman", serif;
            color: #1c1917;
            background: #faf7f2;
            margin: 0;
            padding: 20px;
            font-size: 11px;
        }
        .no-print {
            margin-bottom: 16px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .btn-action {
            display: inline-block;
            padding: 8px 16px;
            font-family: monospace;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            text-decoration: none;
            border: 1px solid #9a7b3e;
            background: #1c1917;
            color: #f5efe4;
            cursor: pointer;
        }
        .header-banner {
            background: #191715;
            color: #f5efe4;
            border-bottom: 3px solid #c4a059;
            padding: 18px 22px;
            display: table;
            width: 100%;
            margin-bottom: 16px;
        }
        .header-left {
            display: table-cell;
            vertical-align: middle;
            width: 68%;
        }
        .header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            font-family: "Courier New", monospace;
            font-size: 10px;
            color: #d6cfc2;
        }
        .logo-box {
            display: inline-block;
            width: 44px;
            height: 44px;
            border: 2px solid #c4a059;
            text-align: center;
            line-height: 40px;
            font-size: 24px;
            font-weight: bold;
            color: #c4a059;
            margin-right: 14px;
            vertical-align: middle;
        }
        .brand-block {
            display: inline-block;
            vertical-align: middle;
        }
        .brand-title {
            font-size: 22px;
            letter-spacing: 0.18em;
            margin: 0;
            color: #f5efe4;
        }
        .brand-subtitle {
            font-family: "Courier New", monospace;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            color: #c4a059;
            margin: 3px 0 0 0;
        }
        .kpi-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
            margin-bottom: 16px;
        }
        .kpi-card {
            display: table-cell;
            background: #f1ece1;
            border: 1px solid #d5cab8;
            padding: 10px 14px;
        }
        .kpi-label {
            font-family: "Courier New", monospace;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #786544;
        }
        .kpi-value {
            font-size: 16px;
            font-weight: bold;
            margin: 4px 0;
            color: #191715;
        }
        .kpi-meta {
            font-family: "Courier New", monospace;
            font-size: 9px;
            color: #57534e;
        }
        .section-title {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            border-bottom: 1px solid #c4a059;
            padding-bottom: 4px;
            margin: 14px 0 8px 0;
            color: #191715;
        }
        table.sedes-grid {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-bottom: 14px;
            background: #e8e1d0;
            border: 1px solid #c4a059;
        }
        table.sedes-grid td {
            padding: 7px 10px;
            font-family: "Courier New", monospace;
            font-size: 8.5pt;
            color: #201c16;
            border-right: 1px solid #d5cab8;
            white-space: normal;
            word-wrap: break-word;
            overflow-wrap: break-word;
            vertical-align: middle;
        }
        table.sedes-grid td:last-child {
            border-right: none;
        }
        table.report-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        table.report-table th {
            background: #26231f;
            color: #f5efe4;
            font-family: "Courier New", monospace;
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 7px 6px;
            text-align: left;
            border: 1px solid #26231f;
            white-space: normal;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        table.report-table td {
            padding: 6px 6px;
            border: 1px solid #dcd3c2;
            font-size: 8pt;
            vertical-align: top;
            white-space: normal;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        table.report-table tr:nth-child(even) td {
            background: #f4efe6;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        .mono {
            font-family: "Courier New", monospace;
        }
        .stock-number {
            display: block;
            font-size: 8.5pt;
            font-weight: bold;
            line-height: 1.2;
        }
        .stock-badge {
            display: block;
            font-size: 6.8pt;
            margin-top: 2px;
            line-height: 1.15;
        }
        .badge-agotado {
            color: #991b1b;
            font-weight: bold;
        }
        .badge-bajo {
            color: #92400e;
            font-weight: bold;
        }
        .badge-ok {
            color: #166534;
            font-weight: bold;
        }
        .footer-seal {
            margin-top: 18px;
            padding-top: 10px;
            border-top: 1px solid #c4a059;
            font-family: "Courier New", monospace;
            font-size: 9px;
            color: #57534e;
            display: table;
            width: 100%;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button type="button" onclick="window.print()" class="btn-action">Imprimir / Guardar PDF</button>
        <a href="{{ route('admin.reportes.pdf') }}" class="btn-action">Descargar Binario PDF (.pdf)</a>
    </div>

    {{-- Encabezado Institucional con Emblema de ARCHIVUM --}}
    <div class="header-banner">
        <div class="header-left">
            <div class="logo-box">A</div>
            <div class="brand-block">
                <h1 class="brand-title">ARCHIVUM</h1>
                <p class="brand-subtitle">Sistema de Gestión de Tienda e Inventario</p>
            </div>
        </div>
        <div class="header-right">
            <div><strong>FOLIO OFICIAL: {{ $folio_reporte }}</strong></div>
            <div>Emisión: {{ $fecha_emision }}</div>
            <div>Sucursal: {{ $filtro_sucursal }}</div>
            <div>Categoría: {{ $filtro_categoria }}</div>
        </div>
    </div>

    {{-- Resumen Ejecutivo Calculado --}}
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-label">SKUs Registrados</div>
            <div class="kpi-value">{{ $resumen['total_skus'] }} referencias</div>
            <div class="kpi-meta">Agotados: {{ $resumen['skus_agotados'] }} | Bajo mín: {{ $resumen['alertas_stock_bajo'] }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Productos Disponibles</div>
            <div class="kpi-value">{{ $resumen['total_unidades_boveda'] }} unidades</div>
            <div class="kpi-meta">Reservas activas: {{ $resumen['reservas_activas'] }} uds.</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Valor Total del Catálogo</div>
            <div class="kpi-value">${{ number_format($resumen['valor_total_acervo'], 2) }} MXN</div>
            <div class="kpi-meta">Inventario valorizado activo</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Certificados de Autenticidad</div>
            <div class="kpi-value">{{ $resumen['total_piezas_certificadas'] }} certificados</div>
            <div class="kpi-meta">Ingresos: ${{ number_format($resumen['ingresos_acumulados'], 2) }} MXN</div>
        </div>
    </div>

    {{-- Barra de Valoración por Sede en Cuadrícula Proporcional Sin Cortes --}}
    @if(! empty($valoracion_sucursales))
        <table class="sedes-grid">
            <tr>
                @foreach($valoracion_sucursales as $sedeVal)
                    <td>
                        <strong>{{ $sedeVal['sucursal_nombre'] }}:</strong>
                        {{ $sedeVal['total_unidades'] }} uds
                        (${{ number_format((float) $sedeVal['valor_total_inventario'], 2) }} MXN)
                    </td>
                @endforeach
            </tr>
        </table>
    @endif

    {{-- Desglose Detallado de Inventario y Certificados (100% Exacto: 11% + 24% + 9% + 13% + 12% + 11% + 10% + 10%) --}}
    <h2 class="section-title">Desglose Oficial de Inventario Disponible y Certificados de Autenticidad</h2>
    <table class="report-table">
        <colgroup>
            <col style="width: 11%;">
            <col style="width: 24%;">
            <col style="width: 9%;">
            <col style="width: 13%;">
            <col style="width: 12%;">
            <col style="width: 11%;">
            <col style="width: 10%;">
            <col style="width: 10%;">
        </colgroup>
        <thead>
            <tr>
                <th style="width: 11%;">SKU</th>
                <th style="width: 24%;">PIEZA / TITULO CURATORIAL</th>
                <th style="width: 9%;">CATEGORIA</th>
                <th style="width: 13%;">BOVEDA / SEDE</th>
                <th style="width: 12%;">COA / LOTE</th>
                <th class="text-center" style="width: 11%;">STOCK</th>
                <th class="text-right" style="width: 10%;">PRECIO U.</th>
                <th class="text-right" style="width: 10%;">VALOR TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @forelse($filas as $fila)
                <tr>
                    <td class="mono"><strong>{{ $fila['sku'] }}</strong></td>
                    <td>
                        <strong>{{ $fila['producto'] }}</strong><br>
                        <span class="mono" style="font-size: 7pt; color: #57534e;">{{ $fila['condicion'] }}</span>
                    </td>
                    <td>{{ $fila['categoria_etiqueta'] }}</td>
                    <td>{{ $fila['sucursal'] }}</td>
                    <td class="mono" style="font-size: 7.2pt;">{{ $fila['certificados_coa'] }}</td>
                    <td class="text-center mono">
                        <span class="stock-number">{{ $fila['stock_boveda'] }}</span>
                        @if($fila['estado_stock'] === 'AGOTADO')
                            <span class="stock-badge badge-agotado">[AGOTADO]</span>
                        @elseif($fila['estado_stock'] === 'BAJO MINIMO')
                            <span class="stock-badge badge-bajo">[BAJO MIN]</span>
                        @else
                            <span class="stock-badge badge-ok">[EN BOVEDA]</span>
                        @endif
                    </td>
                    <td class="text-right mono">${{ number_format($fila['precio_unitario'], 2) }}</td>
                    <td class="text-right mono"><strong>${{ number_format($fila['valor_total'], 2) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center mono">Sin registros disponibles para este criterio.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-seal">
        <div style="display: table-cell; width: 70%;">
            <strong>ARCHIVUM — REPORTE OFICIAL DE TIENDA E INVENTARIO</strong><br>
            Documento emitido por el Sistema de Gestión de Tienda e Inventario ARCHIVUM para control de inventario y ventas.
        </div>
        <div style="display: table-cell; width: 30%; text-align: right;">
            Sello Digital: {{ strtoupper(substr(md5($folio_reporte), 0, 12)) }}
        </div>
    </div>

</body>
</html>

