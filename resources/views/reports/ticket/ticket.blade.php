<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Solicitud No. {{ $document->reference_code }}</title>
    <style>
        .details {
            border-top: 1px dashed #c3c3c3;
            border-left: 1px dashed #c3c3c3;
            border-right: 1px dashed #c3c3c3;
            border-bottom: 1px dashed #c3c3c3;
        }

        .separator {
            border-bottom: 1px dashed #c3c3c3;
        }

        .tinfo {
            font-size: 0.7em;
            font-weight: 300;
            color: black;
            font-family: "Arial Narrow";
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 7px 0;
        }

        #qr {
            padding-left: 0px;
            padding-right: -20px;
            padding-top: 0px;
            padding-bottom: 0px;

            margin-left: 0px;
            margin-right: 0px;
            margin-top: 0px;
            margin-bottom: 0px;
        }
    </style>
</head>
<body>
<table border="0" width="100%">
    <tr>
        <td width="10%">
            <img src="{{ $qrCode }}" alt="QR Code">
        </td>
        <td width="90%">
            <table border="0" class="details" width="100%" style="text-align: center">
                <tr>
                    <td colspan="4" class="separator tinfo">
                        {{ $entity }}<br>
                        Nit: {{ $nit }}
                    </td>
                </tr>
                <tr>
                    <td class="separator tinfo">Radicado</td>
                    <td class="separator tinfo" colspan="3">{{ $document->reference_code }}</td>
                </tr>
                <tr>
                    <td class="separator tinfo">N° Folios:</td>
                    <td class="separator tinfo" colspan="3">{{ $document->page_count }}</td>
                </tr>
                <tr>
                    <td class="separator tinfo">Procedencia</td>
                    <td class="separator tinfo" colspan="3">{{ $document->sender_name }}</td>
                </tr>
                <tr>
                    <td class="tinfo">Fecha Recibida</td>
                    <td class="tinfo" colspan="4">{{ date("Y-m-d", strtotime($document->received_date)) }}</td>
                </tr>

            </table>
        </td>
    </tr>
</table>


</body>
</html>
