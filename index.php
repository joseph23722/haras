<?php
session_start();

// Si el usuario ya inició sesión, redirigir al dashboard
if (isset($_SESSION['login']) && $_SESSION['login']['permitido']){
    header('Location:dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Sistema de gestión de Haras" />
    <meta name="author" content="Haras Inc." />
    <title>Login Haras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"> <!-- Nueva fuente -->
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        body {
            background: url(imagen/caballo2.png);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .login-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            width: 900px;
            max-width: 100%;
            position: relative;
        }

        .login-image {
            background: url('imagen/caballo.png') no-repeat center center; /* Usamos la imagen de fondo */
            background-size: cover; /* Cubrimos todo el contenedor con la imagen */
            padding: 60px;
            color: white;
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-image h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 30px;
            font-weight: bold;
            padding: 10px 20px;
            background: rgba(0, 0, 0, 0.5); /* Fondo semitransparente para mejorar la visibilidad */
            border-radius: 5px;
            color: white;
        }

        .login-image p {
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            margin-left: 10px;
            color: white;
        }

        /* Nubes apiladas verticalmente */
        .clouds {
            position: absolute;
            top: 0;
            bottom: 0;
            right: -67px;
            width: 110px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .cloud {
            background: white;
            border-radius: 90%;
            width: 120px;
            height: 120px;
            margin: 15px 0;
            position: relative;
        }

        .cloud::before, .cloud::after {
            content: '';
            background: white;
            border-radius: 50%;
            position: absolute;
        }

        .cloud::before {
            width: 90px;
            height: 90px;
            top: -45px;
            left: 20px;
        }

        .cloud::after {
            width: 100px;
            height: 100px;
            top: 25px;
            right: -40px;
        }

        .login-form {
            padding: 40px;
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: #f9f9f9;
            border-radius: 15px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
        }

        .login-form h3 {
            font-family: 'Poppins', sans-serif;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-floating label {
            color: #555;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
        }

        .form-floating input {
            border-radius: 25px;
            padding: 20px;
        }

        .form-floating input:focus {
            border-color: #007bff;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 25px;
            padding: 10px 0;
            font-size: 18px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .login-footer {
            margin-top: 20px;
            text-align: center;
        }

        .login-footer a {
            color: #007bff;
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-image">
            <h2>Bienvenido a Haras Rancho Sur</h2>
            <div class="clouds">
                <div class="cloud"></div>
                <div class="cloud"></div>
                <div class="cloud"></div>
                <div class="cloud"></div> <!-- Nubes apiladas verticalmente -->
            </div>
        </div>
        <div class="login-form">
            <h3>Iniciar Sesión</h3>
            <form autocomplete="off" id="form-login">
                <div class="form-floating mb-3">
                    <input class="form-control" id="inputEmail" autofocus type="email" placeholder="correo@example.com" required />
                    <label for="inputEmail"><i class="fas fa-envelope"></i> Correo Electrónico</label>
                </div>
                <div class="form-floating mb-3">
                    <input class="form-control" id="inputPassword" type="password" placeholder="Contraseña" required />
                    <label for="inputPassword"><i class="fas fa-lock"></i> Contraseña</label>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="form-check">
                        <input class="form-check-input" id="inputRememberPassword" type="checkbox" />
                        <label class="form-check-label" for="inputRememberPassword">Recordar Contraseña</label>
                    </div>
                    <a class="small" href="password.html">¿Olvidaste tu contraseña?</a>
                </div>
                <div class="d-grid">
                    <button class="btn btn-primary btn-block" type="submit">Iniciar Sesión</button>
                </div>
                <div class="login-footer small">
                    <a href="register.html">¿No tienes una cuenta? ¡Regístrate aquí!</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelector("#form-login").addEventListener("submit", (event) => {
                event.preventDefault();

                const params = new URLSearchParams();
                params.append("operation", "login");
                params.append("correo", document.querySelector("#inputEmail").value);
                params.append("clave", document.querySelector("#inputPassword").value);

                fetch(`./controllers/usuario.controller.php?${params}`)
                    .then(response => response.json())
                    .then(acceso => {
                        if (!acceso.permitido) {
                            alert(acceso.status);
                        } else {
                            // El login ha sido exitoso; redirigir al dashboard
                            window.location.href = './dashboard.php';
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });

    </script>
</body>
</html>
