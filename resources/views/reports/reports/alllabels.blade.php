<!-- resources/views/pdfs/archive-records.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Archivo Central</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .page-break {
            page-break-after: always;
        }
        .record-container {
            border: 1px solid #000;
            margin-bottom: 20px;
            padding: 10px;
        }
        .header {
            width: 100%;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .logo {
            width: 15%;
            float: left;
        }
        .logo img {
            width: 100%;
            max-width: 80px;
        }
        .title {
            width: 85%;
            float: right;
            text-align: center;
        }
        .title h1 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }
        .title p {
            margin: 5px 0;
            font-size: 12px;
        }
        .clear {
            clear: both;
        }
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-grid td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 11px;
        }
        .info-grid th {
            border: 1px solid #000;
            background-color: #fff;
            padding: 5px;
            font-size: 11px;
            font-weight: normal;
        }
        .section {
            margin-bottom: 10px;
            border: 1px solid #000;
        }
        .section-title {
            border-bottom: 1px solid #000;
            padding: 5px;
            font-weight: bold;
            background-color: #fff;
        }
        .section-content {
            padding: 5px;
            min-height: 25px;
        }
        .purple-text {
            color: purple;
        }
    </style>
</head>
<body>
@foreach($archives as $archive)
    <div class="record-container">
        <div class="header">
            <div class="logo">
                <img src="{{ public_path('logo.png') }}" alt="Logo Institución">
            </div>
            <div class="title">
                <h1>INSTITUCION EDUCATIVA ANTONIO LENIS</h1>
                <p>SINCELEJO - SUCRE</p>
                <p>NIT: 892200156-5</p>
                <p>Email: lenis@semsincelejo.gov.co</p>
                <h2 class="purple-text">Archivo Central</h2>
            </div>
            <div class="clear"></div>
        </div>

        <table class="info-grid">
            <tr>
                <th>Cod. Documental</th>
                <th>N° Estante</th>
                <th>Bandeja</th>
                <th>N° Caja</th>
                <th>Carpeta N°</th>
                <th>N° Folios</th>
            </tr>
            <tr>
                <td>{{ $archive->system_code }}</td>
                <td>{{ $archive->shelf_number }}</td>
                <td>{{ $archive->tray }}</td>
                <td>{{ $archive->box_number }}</td>
                <td>{{ $archive->ord_number }}</td>
                <td>{{ $archive->folio_number }}</td>
            </tr>
        </table>

        <div class="section">
            <div class="section-title">Nombre de la Oficina</div>
            <div class="section-content">{{ $archive->office->name }}</div>
        </div>

        <div class="section">
            <div class="section-title">Serie Documental</div>
            <div class="section-content">{{ $archive->series->series_code }}</div>
        </div>

        <div class="section">
            <div class="section-title">Referencia Documental</div>
            <div class="section-content">{{ $archive->document_reference }}</div>
        </div>

        <div class="section">
            <div class="section-title">Terceros</div>
            <div class="section-content">{{ $archive->third_parties }}</div>
        </div>

        <div class="section">
            <div class="section-title">Objeto/Observaciones</div>
            <div class="section-content">{{ $archive->object_observations }}</div>
        </div>
    </div>

    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
@endforeach
</body>
</html>
