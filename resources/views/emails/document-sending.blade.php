<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Documento Enviado</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f8f9fa;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f8f9fa;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table class="container" width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 8px;">
                    {{-- HEADER --}}
                    <tr>
                        <td class="header" style="background-color: #f4f4f4; padding: 20px; text-align: center; border-bottom: 1px solid #dee2e6; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                            <h1 style="margin: 0; color: #333333; font-size: 24px;">Nuevo Documento Enviado</h1>
                        </td>
                    </tr>

                    {{-- CONTENT --}}
                    <tr>
                        <td class="content" style="padding: 30px 20px 30px 20px;">
                            <p>Estimado/a receptor/a,</p>
                            <p>Se ha enviado un nuevo documento con la siguiente información:</p>
                            <ul style="padding-left: 20px; margin-bottom: 25px;">
                                <li><strong>Empresa:</strong> {{ $companyName }}</li>
                                <li><strong>Asunto:</strong> {{ $subject }}</li>
                                <li><strong>Remitente:</strong> {{ $sender }}</li>
                                <li><strong>Destinatario:</strong> {{ $recipient }}</li>
                                <li><strong>Número de páginas:</strong> {{ $pageCount }}</li>
                            </ul>

                            {{-- INICIO DEL BOTÓN (Reemplaza el párrafo del enlace) --}}
                            <table border="0" cellspacing="0" cellpadding="0" role="presentation" align="center" style="margin: 20px auto;">
                                <tr>
                                    <td align="center" style="background-color: #0d6efd; border-radius: 5px;" bgcolor="#0d6efd">
                                        <a href="google.com" target="_blank" style="font-size: 16px; font-weight: bold; font-family: Arial, sans-serif; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 5px; display: inline-block;">
                                            Acceder al Documento
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            {{-- FIN DEL BOTÓN --}}

                            <p style="text-align: center; font-size: 14px; color: #6c757d; margin-top: 25px;">Por favor, revise el documento a la brevedad posible.</p>
                            <p>Si tiene alguna pregunta o inquietud, no dude en contactarnos.</p>
                            <p style="margin-top: 30px;">Saludos cordiales,</p>
                            <p style="margin-top: 5px; font-weight: bold;">{{ $companyName }}</p>
                        </td>
                    </tr>

                    {{-- FOOTER --}}
                    <tr>
                        <td class="footer" style="background-color: #f4f4f4; padding: 15px; text-align: center; border-top: 1px solid #dee2e6; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                            <p style="margin: 0; font-size: 12px; color: #888888;">Este es un correo automático, por favor no responda a este mensaje.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
