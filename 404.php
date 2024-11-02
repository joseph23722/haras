<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada</title>
    <style>
        /* Estilos personalizados */
        body {
            background-color: #f8fbff;
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
            overflow: hidden;
        }
        
        .container {
            max-width: 600px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            border-radius: 10px;
            animation: fadeIn 1s ease;
        }

        h1 {
            font-size: 5em;
            color: #007acc;
            margin: 0;
        }

        p {
            font-size: 1.5em;
            margin-top: 10px;
            color: #666;
        }

        .emoji {
            font-size: 4em;
            margin: 20px 0;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            color: #fff;
            background-color: #007acc;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.2em;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        a:hover {
            background-color: #005f99;
            transform: scale(1.05);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <div class="emoji">😕</div>
        <p>Lo sentimos, la página que estás buscando no existe.</p>
        <!-- Enlace ajustado a la ruta correcta -->
        <a href="/HARAS/views/home/index.php">Volver a la página principal</a>
    </div>
</body>
</html>
