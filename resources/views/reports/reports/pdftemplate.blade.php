<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportType }}</title>
    <style>
        @page {
            size: A4;
            margin: 1cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        h1 {
            color: #2c3e50;
            font-size: 14pt;
            margin-bottom: 5px;
        }

        h2 {
            color: #34495e;
            font-size: 12pt;
            margin-top: 15px;
            margin-bottom: 5px;
            border-bottom: 1px solid #eee;
            padding-bottom: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 15px;
            page-break-inside: auto;
        }

        th, td {
            border: 0.5pt solid #ddd;
            padding: 4px;
            text-align: left;
            font-size: 8pt;
            word-wrap: break-word;
            max-width: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #2c3e50;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .footer {
            position: running(footer);
            text-align: center;
            font-size: 7pt;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 3px;
        }

        @page {
            @bottom-center {
                content: element(footer);
            }
        }

        .page-number:after {
            content: counter(page);
        }

        .filter-info {
            margin-bottom: 10px;
            font-size: 8pt;
        }

        .filter-info table {
            width: auto;
        }

        .filter-info th, .filter-info td {
            border: none;
            padding: 1px 3px;
        }

        @media print {
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
<div class="header">
    <h1>{{ $reportType }}</h1>
    <p>Generated on: {{ date('F d, Y H:i:s') }}</p>
</div>

<div class="filter-info">
    <h2>Filter Information</h2>
    <table>
        <tr>
            @foreach($filterInfo as $key => $value)
                @if($value)

                    <th>{{ $key }}:</th>
                    <td>{{ $value }}</td>

                @endif
            @endforeach
        </tr>
    </table>
</div>

@if(is_array($data) && count($data) > 0)
    @if(is_array($data[0]))
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
                        <td>{{ is_array($cell) || is_object($cell) ? json_encode($cell) : $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        @foreach($data as $key => $value)
            <h2>{{ $key }}</h2>
            @if(is_array($value) || is_object($value))
                <table>
                    <thead>
                    <tr>
                        <th>Key</th>
                        <th>Value</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($value as $subKey => $subValue)
                        <tr>
                            <td>{{ $subKey }}</td>
                            <td>{{ is_array($subValue) || is_object($subValue) ? json_encode($subValue) : $subValue }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p>{{ $value }}</p>
            @endif
        @endforeach
    @endif
@elseif(is_object($data))
    @foreach($data as $key => $value)
        <h2>{{ $key }}</h2>
        @if(is_array($value) || is_object($value))
            <table>
                <thead>
                <tr>
                    <th>Key</th>
                    <th>Value</th>
                </tr>
                </thead>
                <tbody>
                @foreach($value as $subKey => $subValue)
                    <tr>
                        <td>{{ $subKey }}</td>
                        <td>{{ is_array($subValue) || is_object($subValue) ? json_encode($subValue) : $subValue }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <p>{{ $value }}</p>
        @endif
    @endforeach
@else
    <p>No data available for this report.</p>
@endif

<div class="footer">
    Page <span class="page-number"></span>
</div>
</body>
</html>
