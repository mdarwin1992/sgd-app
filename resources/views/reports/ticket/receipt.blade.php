<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamo Documental</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 20px;
            background: white;
            color: black;
        }

        .container {
            width: 350px;
            margin: 0 auto;
            background: white;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .company-name {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .company-info {
            font-size: 10px;
            line-height: 1.2;
        }

        .dotted-line {
            border-top: 1px dotted #000;
            margin: 8px 0;
        }

        .sistema-pos {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            margin: 10px 0;
        }

        .factura-line {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 10px 0;
        }

        .factura-box {
            border: 2px solid red;
            padding: 2px 8px;
            font-weight: bold;
            font-size: 11px;
            margin-right: 5px;
        }

        .factura-number {
            font-size: 11px;
        }

        .barcode {
            text-align: center;
            margin: 10px 0;
            font-family: "Libre Barcode 128", monospace;
            font-size: 24px;
            letter-spacing: 1px;
        }

        .info-section {
            margin: 8px 0;
        }

        .info-row {
            display: flex;
            margin: 3px 0;
        }

        .info-label {
            font-weight: bold;
            width: 110px;
            flex-shrink: 0;
        }

        .info-value {
            flex: 1;
        }

        .entity-section {
            margin: 10px 0;
            border: 1px solid #ccc;
            padding: 8px;
            background: #f9f9f9;
        }

        .entity-title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 5px;
            font-size: 11px;
            color: #2e7d32;
        }

        .office-section {
            margin: 10px 0;
            border: 1px solid #1976d2;
            padding: 8px;
            background: #e3f2fd;
        }

        .office-title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 5px;
            font-size: 11px;
            color: #1976d2;
        }

        .document-section {
            margin: 10px 0;
            border: 1px solid #f57c00;
            padding: 8px;
            background: #fff3e0;
        }

        .document-title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 5px;
            font-size: 11px;
            color: #f57c00;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .table th {
            background: #f0f0f0;
            border: 1px solid #000;
            padding: 3px;
            font-weight: bold;
            font-size: 10px;
            text-align: center;
        }

        .table td {
            border: 1px solid #000;
            padding: 3px;
            font-size: 10px;
        }

        .table .right {
            text-align: right;
        }

        .table .center {
            text-align: center;
        }

        .totals {
            margin: 10px 0;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }

        .total-label {
            font-weight: bold;
        }

        .items-count {
            text-align: center;
            font-size: 10px;
            margin: 5px 0;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
            line-height: 1.2;
        }

        .footer-bold {
            font-weight: bold;
        }

        .red-arrow {
            color: red;
            font-weight: bold;
            font-size: 14px;
            margin-right: 5px;
        }

        .status {
            text-align: center;
            font-weight: bold;
            margin: 10px 0;
            padding: 5px;
            background: #e8f5e8;
            border: 1px solid #4caf50;
            color: #2e7d32;
        }

        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 5px;
            margin: 10px 0;
            font-size: 10px;
            text-align: center;
        }

        .location-info {
            background: #f3e5f5;
            border: 1px solid #9c27b0;
            padding: 5px;
            margin: 10px 0;
            font-size: 10px;
        }

        .location-title {
            font-weight: bold;
            text-align: center;
            color: #9c27b0;
            margin-bottom: 3px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="company-name">{{ $documentLoan->company_name }}</div>
            <div class="company-info">
                NIT: {{ $documentLoan->nit }} - {{ $documentLoan->verification_digit }}<br>
                Calle {{ $documentLoan->address }}<br>
                Tel: {{ $documentLoan->phone }}<br>
                Email: {{ $documentLoan->email }}
            </div>
        </div>

        <div class="dotted-line"></div>

        <div class="sistema-pos">SISTEMA ARCHIVO CENTRAL</div>

        <div class="factura-line">
            <span class="red-arrow">→</span>
            <span class="factura-box">PRÉSTAMO DOCUMENTAL</span>
            <span class="factura-number">No. PD-{{ $documentLoan->order_number }}</span>
        </div>

        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Fecha Préstamo:</span>
                <span class="info-value">{{ $documentLoan->order_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha Devolución:</span>
                <span class="info-value">{{ $documentLoan->return_date }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Solicitante:</span>
                <span class="info-value">{{ $documentLoan->names }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Identificación:</span>
                <span class="info-value">{{ $documentLoan->identification }}</span>
            </div>
        </div>

        <div class="office-section">
            <div class="office-title">OFICINA SOLICITANTE</div>
            <div class="info-row">
                <span class="info-label">Oficina:</span>
                <span class="info-value">{{ $documentLoan->office_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Código:</span>
                <span class="info-value">{{ $documentLoan->code }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Departamento:</span>
                <span class="info-value">{{ $documentLoan->department_name }}</span>
            </div>
        </div>

        <div class="document-section">
            <div class="document-title">DOCUMENTO PRESTADO</div>
            <div class="info-row">
                <span class="info-label">Referencia:</span>
                <span class="info-value">{{ $documentLoan->document_reference }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Expediente:</span>
                <span class="info-value">{{ $documentLoan->filed }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Serie:</span>
                <span class="info-value">{{ $documentLoan->subseries_id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Subserie:</span>
                <span class="info-value">{{ $documentLoan->department_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Año Carpeta:</span>
                <span class="info-value">{{ $documentLoan->folder_year }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Terceros:</span>
                <span class="info-value">{{ $documentLoan->third_parties }}</span>
            </div>
        </div>

        <div class="location-info">
            <div class="location-title">UBICACIÓN FÍSICA</div>
            <div class="info-row">
                <span class="info-label">Estante:</span>
                <span class="info-value">{{ $documentLoan->shelf_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Bandeja:</span>
                <span class="info-value">{{ $documentLoan->tray }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Caja:</span>
                <span class="info-value">{{ $documentLoan->box_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Orden:</span>
                <span class="info-value">{{ $documentLoan->ord_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Folio:</span>
                <span class="info-value">{{ $documentLoan->folio_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Soporte:</span>
                <span class="info-value">{{ $documentLoan->support }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Conservación:</span>
                <span class="info-value">{{ $documentLoan->main_conservation_medium }} /
                    {{ $documentLoan->preserved_in }}</span>
            </div>
        </div>

        <div class="status">
            @if ($documentLoan->state == 1)
                @if (\Carbon\Carbon::now()->format('Y-m-d') > $documentLoan->return_date)
                    ESTADO DEL PRÉSTAMO: PENDIENTE
                @else
                    ESTADO DEL PRÉSTAMO: PRESTADO
                @endif
            @else
                ESTADO DEL PRÉSTAMO: DEVUELTO
            @endif


        </div>

        <div class="warning">
            IMPORTANTE: El documento {{ $documentLoan->document_reference }} debe ser devuelto en la fecha establecida.
            Responsable del préstamo: {{ $documentLoan->names }} (ID: {{ $documentLoan->identification }})
        </div>

        <div class="totals">
            <div class="total-row">
                <span class="total-label">Usuario Sistema:</span>
                <span>ID: 2</span>
            </div>
            <div class="total-row">
                <span class="total-label">Archivo Central:</span>
                <span>ID: 1</span>
            </div>
            <div class="total-row">
                <span class="total-label">Fecha Creación:</span>
                <span>2025-06-11</span>
            </div>
        </div>

        <div class="dotted-line"></div>

        <div style="text-align: center; font-weight: bold; margin: 10px 0;">
            PRÉSTAMO AUTORIZADO - ARCHIVO CENTRAL
        </div>

        <div class="footer">
            <div class="footer-bold">Sistema: SOLUCIONES INTEGRALES MANA S.A.S</div>
            <div class="footer-bold">Software SGD 1.0</div>
            <div>www.solucionesintegralesmanasas.com</div>
            <div>Nit: 901924358-5</div>
        </div>
    </div>
</body>

</html>
