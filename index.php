<?php
session_start();

if (isset($_SESSION['login']) && $_SESSION['login']['estado']) {
    header("Location:http://localhost/haras/views");
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
    <title>Haras Rancho Sur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"> <!-- Nueva fuente -->
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <div class="login-container">
        <div class="login-image">
            <h2>Bienvenido a Haras Rancho Sur</h2>
            <div class="bubbles">
                <div class="bubble"></div>
                <div class="bubble"></div>
                <div class="bubble"></div>
                <div class="bubble"></div>
            </div>
        </div>
        <div class="login-form">
            <h3>Iniciar Sesión</h3>
            <form autocomplete="off" id="form-login">
                <div class="form-floating mb-3">
                    <input class="form-control" id="inputEmail" autofocus type="text" placeholder="Nombre de usuario" required />
                    <label for="inputEmail"><i class="fas fa-envelope"></i> Nombre de usuario</label>
                </div>
                <div class="form-floating mb-3">
                    <input class="form-control" id="inputPassword" type="password" placeholder="Contraseña" required />
                    <label for="inputPassword"><i class="fas fa-lock"></i> Contraseña</label>
                </div>

                <!-- Mensaje de error -->
                <div id="loginError" class="text-danger d-none mb-3"></div>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="form-check">
                        <input class="form-check-input" id="inputRememberPassword" type="checkbox" />
                        <label class="form-check-label" for="inputRememberPassword">Recordar Contraseña</label>
                    </div>
                    <a class="small" href="password.html">¿Olvidaste tu contraseña?</a>
                </div>
                <div class="d-grid">
                    <button class="btn-primary" type="submit">Iniciar Sesión</button>
                </div>

            </form>
        </div>
    </div>

    <!-- Mensaje de error -->
    <div id="loginError" class="text-danger d-none mb-3"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="plugins/js/scripts.js"></script>


    <script>
        document.addEventListener("DOMContentLoaded", () => {
            function $(object = null) {
                return document.querySelector(object);
            }

            // Función de ajuste de escala para que el contenedor no se vea afectado por el zoom
            function ajustarEscala() {
                const zoom = window.outerWidth / window.innerWidth;
                const contenedor = $('.login-container');
                contenedor.style.transform = `translate(-50%, -50%) scale(${1 / zoom})`;
            }
            // Llamar a ajustarEscala al cargar la página y al cambiar el tamaño de la ventana
            window.addEventListener('resize', ajustarEscala);
            ajustarEscala();

            async function login() {
                const parametros = new FormData();
                parametros.append("operation", "login");
                parametros.append("correo", $("#inputEmail").value);
                parametros.append("clave", $("#inputPassword").value);

                const response = await fetch(`./controllers/usuario.controller.php`, {
                    method: 'POST',
                    body: parametros
                });
                const data = await response.json();

                if (data.login) {
                    window.location.href = './views/home/';
                } else {
                    $("#loginError").textContent = data.mensaje;
                    $("#loginError").classList.remove("d-none");
                }
            }

            $("#form-login").addEventListener("submit", async (event) => {
                event.preventDefault();
                await login();
            });
        });
    </script>
</body>

</html>