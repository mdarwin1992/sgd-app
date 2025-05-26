<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $report_type }} - Reporte</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        h1 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .summary-box {
            background-color: #ecf0f1;
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .summary-item {
            margin-bottom: 10px;
        }

        .summary-label {
            font-weight: bold;
        }

        @page {
            margin: 100px 25px;
        }

        header {
            position: fixed;
            top: -60px;
            left: 0px;
            right: 0px;
            height: 50px;
            text-align: center;
            line-height: 35px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            height: 50px;
            text-align: center;
            line-height: 35px;
        }
    </style>
</head>
<body>
<header>
    {{ $report_type }} - Generado el {{ date('d/m/Y H:i:s') }}
</header>

<footer>
    Página <span class="pagenum"></span>
</footer>

<h1>{{ $report_type }}</h1>

@if(isset($data['total_documentos']))
    <div class="summary-box">
        <div class="summary-item">
            <span class="summary-label">Total de Documentos:</span> {{ $data['total_documentos'] }}
        </div>
        @if(isset($data['documentos_por_estado']))
            <div class="summary-item">
                <span class="summary-label">Documentos por Estado:</span>
                <ul>
                    @foreach($data['documentos_por_estado'] as $estado => $count)
                        <li>{{ $estado }}: {{ $count }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif

@if(isset($headers) && isset($data))
    <table>
        <thead>
        <tr>
            @foreach($headers as $header)
                <th>{{ $header }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($data as $row)
            <tr>
                @foreach($row as $cell)
                    <td>
                        @if(is_array($cell))
                            @foreach($cell as $key => $value)
                                <strong>{{ $key }}:</strong> {{ $value }}<br>
                            @endforeach
                        @else
                            {{ $cell }}
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
</body>
</html>
