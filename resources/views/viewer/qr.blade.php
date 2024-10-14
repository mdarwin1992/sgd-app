<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizador de Información de Código QR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .card {
            margin-top: 50px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .card-header {
            background-color: #0056b3;
            border-bottom: none;
            padding: 20px;
        }

        .card-header h4 {
            font-weight: 600;
        }

        .qr-icon {
            font-size: 48px;
            color: #0056b3;
        }

        .info-section {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .info-section h5 {
            color: #0056b3;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }

        .info-item {
            margin-bottom: 15px;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 1.1em;
            color: #212529;
            padding: 8px 12px;
            border-radius: 4px;
        }

        .status-valid {
            color: #28a745;
        }

        .status-invalid {
            color: #dc3545;
        }

        /* Nuevos estilos incorporados */
        .details {
            border: 1px dashed #c3c3c3;
            padding: 15px;
            margin-bottom: 20px;
        }

        .separator {
            border-bottom: 1px dashed #c3c3c3;
            margin: 10px 0;
        }

        .tinfo {
            font-size: 0.7em;
            font-weight: 300;
            color: black;
            font-family: "Arial Narrow", Arial, sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 7px 0;
        }

        #qr {
            padding: 0;
            margin: 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-white">
                    <h4 class="mb-0"><i class="fas fa-qrcode mr-2"></i>Información del Código QR</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-qrcode qr-icon"></i>
                        <h5 id="qr-code" class="mt-2"></h5>
                    </div>
                    <div id="qr-data" class="details">
                        <!-- Los campos se llenarán dinámicamente con JavaScript -->
                    </div>
                </div>
                <div class="card-footer">
                    <div id="result" class="text-center"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        function getAllGetParams(id = 0) {
            const parts = location.pathname.split("/").filter(item => item !== "");
            return parts[id] || null;
        }

        var item = getAllGetParams(4);
        var id = getAllGetParams(3);

        if (id && item) {
            $.ajax({
                url: `/api/dashboard/qr/${id}/${item}`,
                method: 'GET',
                success: function (data) {
                    $('#qr-code').text(`${id}-${item}`);

                    var qrDataHtml = '<h5 class="tinfo"></h5>';
                    for (var key in data) {
                        if (data.hasOwnProperty(key)) {
                            qrDataHtml += `
                                <div class="info-item">
                                    <p class="info-label tinfo">${key}</p>
                                    <p class="info-value ${key.toLowerCase() === 'status' ? (data[key] === 'Válido' ? 'status-valid' : 'status-invalid') : ''}">${data[key]}</p>
                                </div>
                                <div class="separator"></div>
                            `;
                        }
                    }
                    $('#qr-data').html(qrDataHtml);

                    $('#result').html('<div class="alert alert-success">Información recuperada con éxito</div>');
                },
                error: function () {
                    $('#result').html('<div class="alert alert-danger">Error al obtener la información del código QR</div>');
                    $('#qr-data').hide();
                }
            });
        } else {
            $('#result').html('<div class="alert alert-danger">Parámetros de URL incompletos</div>');
            $('#qr-data').hide();
        }
    });
</script>
</body>
</html>
