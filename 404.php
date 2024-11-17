<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada</title>
    <style>
        /* Estilos generales de la página */
        body {
            background-color: #f1f8ff;
            font-family: 'Arial', sans-serif;
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

        /* Contenedor del mensaje */
        .container {
            max-width: 700px;
            padding: 40px 30px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            border-radius: 15px;
            animation: slideIn 1.5s ease-in-out;
        }

        /* Título con efecto de color */
        h1 {
            font-size: 6em;
            color: #ff5c8d;
            margin: 0;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
            animation: textColorChange 3s infinite alternate;
        }

        /* Subtítulo */
        p {
            font-size: 1.5em;
            margin-top: 10px;
            color: #666;
        }

        /* Emoji grande para destacar */
        .emoji {
            font-size: 5em;
            margin: 30px 0;
            animation: bounce 2s infinite;
        }

        /* Estilo del enlace de regreso */
        a {
            display: inline-block;
            margin-top: 30px;
            padding: 15px 35px;
            color: #fff;
            background-color: #00c6ff;
            text-decoration: none;
            border-radius: 30px;
            font-size: 1.3em;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        a:hover {
            background-color: #0099cc;
            transform: scale(1.1);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        a:active {
            background-color: #007bb2;
            transform: scale(1);
        }

        /* Animaciones */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes textColorChange {
            0% {
                color: #ff5c8d;
            }
            50% {
                color: #ffbb00;
            }
            100% {
                color: #32cd32;
            }
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        /* Fondo animado para dar un toque más moderno */
        .background-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://media.giphy.com/media/xT1XGslzS3UoCaydoI/giphy.gif') no-repeat center center fixed;
            background-size: cover;
            filter: blur(10px);
            z-index: -1;
            animation: pulse 4s infinite ease-in-out;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

    </style>
</head>
<body>
    <div class="background-animation"></div>
    <div class="container">
        <h1>404</h1>
        <div class="emoji">😕</div>
        <p>Lo sentimos, la página que estás buscando no existe o ha sido movida.</p>
        <!-- Enlace ajustado a la ruta correcta -->
        <a href="/HARAS/views/home/index.php">Volver a la página principal</a>
    </div>
</body>
</html>
