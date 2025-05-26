<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Recibo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .container {
            width: 180px;
        }

        .header {
            text-align: center;
            font-weight: bold;
        }

        .content {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">Recibo de Préstamo</div>
        <div class="content">
            <p>Orden: {{ $documentLoan->order_number }}</p>
            <p>Nombre: {{ $documentLoan->names }}</p>
            <p>Fecha de Préstamo: {{ $documentLoan->registration_date }}</p>
            <p>Fecha de Devolución: {{ $documentLoan->return_date }}</p>
        </div>
    </div>
</body>

</html>
