<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Documental</title>
    <style>
        /* Archivo: styles.css */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }

        .header {
            text-align: center;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .logo {
            height: 100px;
            width: auto;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        .login-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #ffffff;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .login-button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="header">
        <!-- Logo del sistema -->
        <img src="{{ asset('assets/images/lgo.png') }}" alt="Logo del Sistema de Gestión Documental" class="logo">

        <!-- Título del sistema -->
        <h1>Sistema de Gestión Documental</h1>

        <!-- Botón de Login -->
        <a href="/login" class="login-button">Iniciar Sesión</a>
    </div>
</body>

</html>
