<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview de PDF Protegido</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #2b2d3e;
            color: #8a8d9f;
        }

        .pdf-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .pdf-viewer {
            flex-grow: 1;
            display: none;
        }

        .navbar {
            background-color: #2b2d3e !important;
        }

        .navbar-brand {
            color: #ffffff !important;
        }

        .btn-outline-light {
            color: #8a8d9f;
            border-color: #8a8d9f;
        }

        .btn-outline-light:hover {
            color: #ffffff;
            background-color: #3a3d52;
        }

        #passwordPrompt {
            background-color: #3a3d52;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 90%;
            margin: 50px auto;
            padding: 20px;
        }

        .form-control {
            background-color: #2b2d3e;
            border-color: #8a8d9f;
            color: #ffffff;
        }

        .form-control:focus {
            background-color: #2b2d3e;
            border-color: #007bff;
            color: #ffffff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="pdf-container">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Preview de PDF Protegido</span>
            <div class="ml-auto">
                <a href="#" id="downloadBtn" class="btn btn-outline-light mr-2" style="display:none;">Descargar PDF</a>
                <button class="btn btn-outline-light" onclick="window.close()">Cerrar</button>
            </div>
        </div>
    </nav>
    <div id="passwordPrompt">
        <h4 class="text-white mb-3 text-center">Este PDF está protegido</h4>
        <div class="form-group">
            <input type="password" class="form-control" id="pdfPassword" placeholder="Ingrese la contraseña">
        </div>
        <button class="btn btn-primary btn-block" onclick="checkPassword()">Acceder</button>
    </div>
    <div class="pdf-viewer">
        <iframe id="pdfViewer" style="width:100%; height:100%; border:none;"></iframe>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
<script>
    var correctPassword = "{{ $id }}";  // Use $id as the password
    function checkPassword() {
        var password = document.getElementById('pdfPassword').value;
        if (password === correctPassword) {
            document.getElementById('passwordPrompt').style.display = 'none';
            document.querySelector('.pdf-viewer').style.display = 'block';
            document.getElementById('pdfViewer').src = "{{ asset('/storage/upload/'.$id.'/respuesta/'.$item) }}";
            document.getElementById('downloadBtn').style.display = 'inline-block';
            document.getElementById('downloadBtn').href = "{{ asset('/storage/upload/'.$id.'/respuesta/'.$item) }}";
        } else {
            alert("Contraseña incorrecta. Intente nuevamente.");
        }
    }
</script>
</body>
</html>
