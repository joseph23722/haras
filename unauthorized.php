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
            overflow: hidden;
        }

        /* Efecto de parpadeo para el fondo */
        @keyframes blink {
            0% { opacity: 0.8; }
            50% { opacity: 1; }
            100% { opacity: 0.8; }
        }

        body {
            animation: blink 3s infinite;
        }

        /* Caja de mensaje */
        .message-box {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
            width: 90%;
            max-width: 600px;
            text-align: center;
            transform: translateY(-100px);
            animation: slideIn 1s forwards;
        }

        /* Animación de entrada de la caja */
        @keyframes slideIn {
            0% {
                transform: translateY(-100px);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Título */
        .message-box h1 {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #ffcc00;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }

        /* Subtítulo */
        .message-box p {
            font-size: 1.3rem;
            margin-bottom: 30px;
            line-height: 1.8;
            color: #f8f9fa;
        }

        /* Botón de regreso */
        .btn-back {
            background-color: #ffcc00;
            color: #333;
            border: none;
            padding: 15px 35px;
            font-size: 1.2rem;
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-back:hover {
            background-color: #e6b800;
            transform: scale(1.1);
            box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.3);
        }

        .btn-back:active {
            transform: scale(1);
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Efecto de onda de fondo */
        .wave {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 60px;
            background: rgba(0, 0, 0, 0.3);
            animation: wave 4s infinite;
        }

        @keyframes wave {
            0% {
                transform: translateX(100%);
            }
            50% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(100%);
            }
        }

        /* Contenedor del botón para mejorar la posición */
        .button-container {
            margin-top: 20px;
        }

        /* Sombra sutil para el botón */
        .btn-back {
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

    </style>
</head>
<body>
    <div class="message-box">
        <h1>Acceso Denegado</h1>
        <p>Lo sentimos, no tienes los permisos necesarios para acceder a esta función. Si crees que esto es un error, por favor contacta con el administrador.</p>
        <div class="button-container">
            <button class="btn-back" onclick="window.history.back()">Regresar</button>
        </div>
    </div>

    <div class="wave"></div> <!-- Onda animada para darle más dinamismo a la página -->
</body>
</html>
