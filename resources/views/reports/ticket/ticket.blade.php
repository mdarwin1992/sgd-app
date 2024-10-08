<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
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
            font-size: 0.6em;
            font-weight: 300;
            color: black;
            font-family: "Arial Narrow";
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 7px 0;
        }
    </style>
</head>
<body>
<table border="0" width="100%" style="text-align: center" class="details">
    <tr>
        <td style="text-align: center" colspan="4" class="separator"><img src="{{ $QR }}" width="50%"></td>
    </tr>
    <tr>
        <td colspan="4" class="separator tinfo">I.E ANTONIO LENIS</td>
    </tr>
    <tr>
        <td class="separator tinfo">Radicado</td>
        <td class="separator tinfo" colspan="3">20241007121</td>
    </tr>
    <tr>
        <td class="separator tinfo">N° Folios:</td>
        <td class="separator tinfo" colspan="3">250</td>
    </tr>
    <tr>
        <td class="separator tinfo">Procedencia</td>
        <td class="separator tinfo" colspan="3">DARWIN MONTES LOPEZ</td>
    </tr>
    <tr>
        <td class="tinfo">Fecha Recibida</td>
        <td class="tinfo" colspan="4">7/10/2024</td>
    </tr>

</table>
</body>
</html>
