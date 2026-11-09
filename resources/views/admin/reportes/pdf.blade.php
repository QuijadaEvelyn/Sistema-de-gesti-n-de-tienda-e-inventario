<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Oficial de Acervo e Inventario — ARCHIVUM ({{ $folio_reporte }})</title>
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
        table.report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        table.report-table th {
            background: #26231f;
            color: #f5efe4;
            font-family: "Courier New", monospace;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 7px 8px;
            text-align: left;
            border: 1px solid #26231f;
        }
        table.report-table td {
            padding: 6px 8px;
            border: 1px solid #dcd3c2;
            font-size: 10px;
            vertical-align: top;
        }
        table.report-table tr:nth-child(even) td {
            background: #f4efe6;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .mono {
            font-family: "Courier New", monospace;
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
                <p class="brand-subtitle">Bóveda Curatorial &amp; Sistema de Gestión de Inventario</p>
            </div>
        </div>
        <div class="header-right">
            <div><strong>FOLIO OFICIAL: {{ $folio_reporte }}</strong></div>
            <div>Emisión: {{ $fecha_emision }}</div>
            <div>Sede: {{ $filtro_sucursal }}</div>
            <div>Colección: {{ $filtro_categoria }}</div>
        </div>
    </div>

    {{-- Resumen Ejecutivo Calculado --}}
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-label">SKUs Catalogados</div>
            <div class="kpi-value">{{ $resumen['total_skus'] }} referencias</div>
            <div class="kpi-meta">Agotados: {{ $resumen['skus_agotados'] }} | Bajo mín: {{ $resumen['alertas_stock_bajo'] }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Piezas en Bóveda</div>
            <div class="kpi-value">{{ $resumen['total_unidades_boveda'] }} ejemplares</div>
            <div class="kpi-meta">Reservas activas: {{ $resumen['reservas_activas'] }} uds.</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Valoración del Acervo</div>
            <div class="kpi-value">${{ number_format($resumen['valor_total_acervo'], 2) }} MXN</div>
            <div class="kpi-meta">Tasación curatorial asegurada</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Lotes Certificados COA</div>
            <div class="kpi-value">{{ $resumen['total_piezas_certificadas'] }} certificados</div>
            <div class="kpi-meta">Ingresos: ${{ number_format($resumen['ingresos_acumulados'], 2) }} MXN</div>
        </div>
    </div>

    {{-- Desglose Detallado de Inventario y Lotes Certificados --}}
    <h2 class="section-title">Desglose Oficial de Inventario en Bóveda y Lotes Certificados (COA)</h2>
    <table class="report-table">
        <thead>
            <tr>
                <th>SKU</th>
                <th>Pieza / Título Curatorial</th>
                <th>Categoría</th>
                <th>Bóveda / Sede</th>
                <th>Certificado COA / Serie</th>
                <th class="text-center">Stock</th>
                <th class="text-right">Precio Unitario</th>
                <th class="text-right">Valor Tasado</th>
                <th class="text-center">Estatus</th>
            </tr>
        </thead>
        <tbody>
            @forelse($filas as $fila)
                <tr>
                    <td class="mono"><strong>{{ $fila['sku'] }}</strong></td>
                    <td>
                        <strong>{{ $fila['producto'] }}</strong><br>
                        <span class="mono" style="font-size: 9px; color: #57534e;">{{ $fila['condicion'] }}</span>
                    </td>
                    <td>{{ $fila['categoria_etiqueta'] }}</td>
                    <td>{{ $fila['sucursal'] }}</td>
                    <td class="mono" style="font-size: 9px;">{{ $fila['certificados_coa'] }}</td>
                    <td class="text-center mono">{{ $fila['stock_boveda'] }} (mín {{ $fila['stock_minimo'] }})</td>
                    <td class="text-right mono">${{ number_format($fila['precio_unitario'], 2) }}</td>
                    <td class="text-right mono"><strong>${{ number_format($fila['valor_total'], 2) }}</strong></td>
                    <td class="text-center mono">
                        @if($fila['estado_stock'] === 'AGOTADO')
                            <span class="badge-agotado">AGOTADO</span>
                        @elseif($fila['estado_stock'] === 'BAJO MINIMO')
                            <span class="badge-bajo">BAJO MÍNIMO</span>
                        @else
                            <span class="badge-ok">EN BÓVEDA</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center mono">Sin registros disponibles para este criterio.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-seal">
        <div style="display: table-cell; width: 70%;">
            <strong>ARCHIVUM CURATORIAL VAULT — CERTIFICADO DE AUDITORÍA PATRIMONIAL</strong><br>
            Documento emitido por el Sistema de Gestión de Tienda e Inventario ARCHIVUM para respaldo contable, seguro curatorial y control de bóveda.
        </div>
        <div style="display: table-cell; width: 30%; text-align: right;">
            Sello Digital: {{ strtoupper(substr(md5($folio_reporte), 0, 12)) }}
        </div>
    </div>

</body>
</html>

