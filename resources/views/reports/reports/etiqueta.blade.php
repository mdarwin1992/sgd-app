<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $archive->name }} - ARCHIVO CENTRAL</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 10px;
        }

        .record-container {
            border: 1px solid #000;
            padding: 10px;
            margin: auto;
            max-width: 700px;
        }

        .header {
            width: 100%;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .logo {
            width: 15%;
            float: left;
        }

        .logo img {
            width: 100%;
            max-width: 60px;
        }

        .title {
            width: 80%;
            float: right;
            text-align: center;
        }

        .title h1 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
        }

        .title p {
            margin: 3px 0;
            font-size: 10px;
        }

        .clear {
            clear: both;
            padding: 0;
            margin: 0;
        }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .info-grid td {
            border: 1px solid #000;
            padding: 3px;
            font-size: 10px;
        }

        .info-grid th {
            border: 1px solid #000;
            background-color: #fff;
            padding: 3px;
            font-size: 10px;
            font-weight: normal;
        }

        .section {
            margin-bottom: 10px;
            border: 1px solid #000;
        }

        .section-title {
            border-bottom: 1px solid #000;
            padding: 3px;
            font-weight: bold;
            background-color: #fff;
            font-size: 10px;
        }

        .section-content {
            padding: 3px;

            font-size: 10px;
        }

        .purple-text {
            color: purple;
            padding: 0;
            margin: 0;
            padding-left: 70%;
            font-size: 12px;
        }
    </style>
</head>
<body>
<div class="record-container">
    <div class="header">
        <div class="logo">
            <img src="{{ $logo }}" alt="Logo Institución">
        </div>
        <div class="title">
            <h1>{{ $archive->name }}</h1>
            <p>SINCELEJO - SUCRE</p>
            <p>NIT: {{ $archive->nit }} - {{ $archive->verification_digit }}</p>
            <p>Email: {{ $archive->email }}</p>
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
        <tr style="text-align: center">
            <td>{{ $archive->filed }}</td>
            <td>{{ $archive->shelf_number }}</td>
            <td>{{ $archive->tray }}</td>
            <td>{{ $archive->box_number }}</td>
            <td>{{ $archive->ord_number }}</td>
            <td>{{ $archive->folio_number }}</td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">Nombre de la Oficina</div>
        <div class="section-content">{{ $archive->name }}</div>
    </div>

    <div class="section">
        <div class="section-title">Serie Documental</div>
        <div class="section-content">{{ $archive->series_name }}</div>
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
</body>
</html>
