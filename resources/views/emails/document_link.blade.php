<!DOCTYPE html>
<html>
<head>
    <title>Document Link</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333333;
        }

        p {
            font-size: 16px;
            line-height: 1.5;
            color: #555555;
        }

        a {
            display: inline-block;
            padding: 10px 15px;
            margin-top: 10px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }

        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Enlace al Documento</h1>
    <p><a href="{{ $documentLink }}">Haz clic aquí para ver el documento</a></p>
</div>
</body>
</html>
