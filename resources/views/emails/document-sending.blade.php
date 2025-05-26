<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Documento Enviado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #f4f4f4;
            padding: 10px;
            text-align: center;
        }

        .content {
            padding: 20px 0;
        }

        .footer {
            background-color: #f4f4f4;
            padding: 10px;
            text-align: center;
            font-size: 0.8em;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Nuevo Documento Enviado</h1>
    </div>
    <div class="content">
        <p>Estimado/a receptor/a,</p>
        <p>Se ha enviado un nuevo documento con la siguiente información:</p>
        <ul>
            <li><strong>Empresa:</strong> {{ $companyName }}</li>
            <li><strong>Asunto:</strong> {{ $subject }}</li>
            <li><strong>Remitente:</strong> {{ $sender }}</li>
            <li><strong>Destinatario:</strong> {{ $recipient }}</li>
            <li><strong>Número de páginas:</strong> {{ $pageCount }}</li>
        </ul>
        <p>Puede acceder al documento en la siguiente ruta: {{ $documentPath }}</p>
        <p>Por favor, revise el documento a la brevedad posible.</p>
        <p>Si tiene alguna pregunta o inquietud, no dude en contactarnos.</p>
        <p>Saludos cordiales,</p>
        <p>{{ $companyName }}</p>
    </div>
    <div class="footer">
        <p>Este es un correo automático, por favor no responda a este mensaje.</p>
    </div>
</div>
</body>
</html>
