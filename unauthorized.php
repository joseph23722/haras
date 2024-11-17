<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado</title>
    <style>
        /* Estilo general de la página */
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #fff;
            text-align: center;
        }

        /* Caja de mensaje */
        .message-box {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            width: 80%;
            max-width: 500px;
            text-align: center;
        }

        /* Título */
        .message-box h1 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Subtítulo */
        .message-box p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        /* Botón de regreso */
        .btn-back {
            background-color: #ffcc00;
            color: #333;
            border: none;
            padding: 10px 30px;
            font-size: 1.1rem;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-back:hover {
            background-color: #e6b800;
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <div class="message-box">
        <h1>Acceso Denegado</h1>
        <p>Lo sentimos, no tienes los permisos necesarios para acceder a esta función. Si crees que esto es un error, por favor contacta con el administrador.</p>
        <button class="btn-back" onclick="window.history.back()">Regresar</button>
    </div>
</body>
</html>
