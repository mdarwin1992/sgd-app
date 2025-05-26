<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tabla de Retención Documental</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 5px 0;
        }

        .header h2 {
            font-size: 14px;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th, td {
            border: 1px solid black;
            padding: 4px;
            font-size: 9px;
            vertical-align: middle;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
        }

        .code-column {
            width: 40px;
            text-align: center;
        }

        .retention-column {
            width: 60px;
            text-align: center;
        }

        .disposition-column {
            width: 30px;
            text-align: center;
        }

        .procedure-column {
            width: 300px;
        }

        .footer {
            margin-top: 20px;
            font-size: 10px;
        }

        .conventions {
            margin-top: 10px;
            font-size: 8px;
        }

        .indent {
            padding-left: 20px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>REPUBLICA DE COLOMBIA</h1>
    <h2>{{ $entity->name }}</h2>
    <h2>MUNICIPIO DE </h2>
    <h3>TABLA DE RETENCION DOCUMENTAL</h3>
</div>

@foreach($offices as $index => $officeData)
    <table>
        <tr>
            <td colspan="2">ENTIDAD PRODUCTORA: {{ $entity->name }}</td>
            <td colspan="2">CÓDIGO {{ $entity->nit }}</td>
        </tr>
        <tr>
            <td colspan="2">OFICINA PRODUCTORA: {{ $officeData['office']->name }}</td>
            <td colspan="2">Pág. {{ $index + 1 }} de {{ count($offices) }}</td>
        </tr>
    </table>

    <table>
        <thead>
        <tr>
            <th colspan="3">CÓDIGO</th>
            <th rowspan="2">SERIE Y TIPOS<br>DOCUMENTALES</th>
            <th colspan="2">RETENCION</th>
            <th colspan="5">DISPOSICION FINAL</th>
            <th rowspan="2">PROCEDIMIENTO</th>
        </tr>
        <tr>
            <th>D</th>
            <th>S</th>
            <th>SB</th>
            <th>ARCHIVO<br>GESTIÓN</th>
            <th>ARCHIVO<br>CENTRAL</th>
            <th>CT</th>
            <th>E</th>
            <th>M</th>
            <th>S</th>
            <th>P</th>
        </tr>
        </thead>
        <tbody>
        @foreach($officeData['series'] as $serie)
            <!-- Serie principal -->
            <tr>
                <td class="code-column">{{ $officeData['office']->code }}</td>
                <td class="code-column">{{ $serie->series_code }}</td>
                <td class="code-column">00</td>
                <td colspan="9"><strong>{{ $serie->series_name }}</strong></td>
            </tr>
            <!-- Tipos documentales -->
            @foreach($serie->documentary_types as $type)
                <tr>
                    <td class="code-column"></td>
                    <td class="code-column"></td>
                    <td class="code-column"></td>
                    <td class="indent">{{ $type->document_name }}</td>
                    <td class="retention-column"></td>
                    <td class="retention-column"></td>
                    <td class="disposition-column"></td>
                    <td class="disposition-column"></td>
                    <td class="disposition-column"></td>
                    <td class="disposition-column"></td>
                    <td class="disposition-column"></td>
                    <td class="procedure-column"></td>
                </tr>
            @endforeach
            <!-- Subseries -->
            @foreach($serie->subseries as $subseries)
                <tr>
                    <td class="code-column"></td>
                    <td class="code-column">{{ $serie->series_code }}</td>
                    <td class="code-column">{{ $subseries->subseries_code }}</td>
                    <td>{{ $subseries->subseries_name }}</td>
                    <td class="retention-column">{{ $serie->administrative_retention }}</td>
                    <td class="retention-column">{{ $serie->central_retention }}</td>
                    <td class="disposition-column">{{ $serie->disposition_type == 'CT' ? 'X' : '' }}</td>
                    <td class="disposition-column">{{ $serie->disposition_type == 'E' ? 'X' : '' }}</td>
                    <td class="disposition-column">{{ $serie->disposition_type == 'M' ? 'X' : '' }}</td>
                    <td class="disposition-column">{{ $serie->disposition_type == 'S' ? 'X' : '' }}</td>
                    @foreach($serie->documentary_types as $type)
                        <td class="disposition-column">{{ $type->document_name == 'P' ? 'X' : '' }}</td>
                    @endforeach
                    <td class="procedure-column">{{ $serie->disposal_procedure }}</td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>

    <div class="conventions">
        <p>Convenciones:</p>
        <p>CT= Conservación total &nbsp;&nbsp; E= Eliminación &nbsp;&nbsp; M=Microfilmación &nbsp;&nbsp; S=Selección
            &nbsp;&nbsp; P=Papel &nbsp;&nbsp; EL= Electrónico</p>
    </div>

    <div class="footer">
        <table style="border: none;">
            <tr>
                <td style="border: none; width: 50%;">Jefe de archivo: _________________________</td>
                <td style="border: none; width: 50%;">Fecha: _________________________</td>
            </tr>
        </table>
    </div>

    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
@endforeach
</body>
</html>
