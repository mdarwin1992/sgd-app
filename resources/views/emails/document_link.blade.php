<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $companyName }}: Su documento está listo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #0056b3;
            padding: 20px;
            text-align: center;
            color: #ffffff;
        }

        .content {
            padding: 20px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }

        .button {
            display: inline-block;
            background-color: #28a745;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 0.8em;
            color: #6c757d;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>{{ $companyName }}</h1>
    <h2>Su documento está listo</h2>
</div>
<div class="content">
    <p>Estimado/a {{ $recipientName }},</p>
    <p>El documento que transferido está listo para su visualización. Por favor, haga clic en el botón de abajo para
        acceder a su documento:</p>
    <p style="text-align: center;">
        <a href="{{ $documentLink }}" class="button" target="_blank" rel="noopener noreferrer">Ver Documento</a>
    </p>
    <p>Su contraseña de acceso es:</p>
    <p style="text-align: center; font-weight: bold; font-size: 1.2em; background-color: #e9ecef; padding: 10px; border-radius: 5px;">{{ $password }}</p>
    <p><strong>Importante:</strong> Por razones de seguridad, le recomendamos encarecidamente que mantenga esta
        contraseña en un lugar seguro y que no la comparta con terceros.</p>
    <p>Si encuentra alguna dificultad para acceder al documento o si tiene alguna pregunta adicional, no dude en ponerse
        en contacto con nuestro equipo de soporte en <a href="mailto:soporte@{{ $companyName }}.com">soporte@{{
            $companyName }}.com</a>.</p>
    <p>Agradecemos su confianza en {{ $companyName }} y esperamos poder seguir asistiéndole en el futuro.</p>
    <p>Atentamente,<br>El equipo de {{ $companyName }}</p>
</div>
<div class="footer">
    <p>Este es un correo electrónico automático. Por favor, no responda a este mensaje.<br>
        © {{ date('Y') }} {{ $companyName }}. Todos los derechos reservados.</p>
</div>
</body>
</html>
