
<?php

header('Content-Type: application/json');

session_start();

if (!isset($_SESSION['login']) || $_SESSION['login']['estado'] == false) {
    $sesion = [
        "estado"        => false,
        "dashboard"     => "",
        "idUsuario"     => -1,
        "apellidos"     => "",
        "nombres"       => "",
        "correo"        => "",
        "clave"         => "",
        "idRol"         => "",
        "accesos"       => []
    ];
}

require_once '../models/Usuario.php';

$usuario = new Usuario();


if (isset($_GET['operation'])) {
    switch ($_GET['operation']) {
        case 'getAll':
            echo json_encode($usuario->getAll());
            break;

        case 'destroy':
            session_unset();
            session_destroy();
            header('Location: http://localhost/haras');
            exit();

        default:
            throw new Exception("Operación no válida.");
    }
}

if (isset($_POST['operation'])) {
    switch ($_POST['operation']) {
        case 'add':
            // Verificar si todas las variables requeridas están presentes
            if (
                isset($_POST['idPersonal']) &&
                isset($_POST['correo']) &&
                isset($_POST['clave']) &&
                isset($_POST['idRol'])
            ) {
                // Log para verificar los datos antes de procesar
                error_log("ID Personal recibido en el servidor: " . $_POST['idPersonal']);
                error_log("Correo recibido: " . $_POST['correo']);

                // Encriptar la clave
                $claveEncriptada = password_hash($usuario->limpiarCadena($_POST['clave']), PASSWORD_BCRYPT);

                // Preparar los datos para enviar al modelo
                $datosRecibidos = [
                    "idPersonal" => $usuario->limpiarCadena($_POST['idPersonal']),
                    "correo"     => $usuario->limpiarCadena($_POST['correo']),
                    "clave"      => $claveEncriptada,
                    "idRol"      => $usuario->limpiarCadena($_POST['idRol'])
                ];

                // Log para verificar los datos antes de enviarlos al modelo
                error_log("Datos recibidos para registro de usuario: " . json_encode($datosRecibidos));

                // Llamamos al método para agregar el usuario
                $resultado = $usuario->add($datosRecibidos);

                // Log para verificar el resultado devuelto por el modelo
                error_log("Resultado del registro de usuario: " . json_encode($resultado));

                if ($resultado['status'] == 'success') {
                    echo json_encode([
                        "status" => 'success',
                        "idUsuario" => $resultado['idUsuario']
                    ]);
                } else {
                    echo json_encode([
                        "status" => 'error',
                        "message" => $resultado['message']
                    ]);
                }
            } else {
                error_log("Faltan datos requeridos para registrar el usuario. POST: " . json_encode($_POST));
                echo json_encode([
                    "status" => 'error',
                    "message" => 'Faltan datos requeridos para registrar un usuario'
                ]);
            }
            break;
        case 'login':
            $Correo = $usuario->limpiarCadena($_POST['correo']);
            /* echo json_encode($usuario->login(['nomusuario' => $nomUsuario])); */
            $registro = $usuario->login(['correo' => $Correo]);

            // Arreglo para la comunicación para el usuario con la vista
            $resultados = [
                "login"     => false,
                "mensaje"   => ""
            ];

            if (!empty($registro) && isset($registro[0]['correo'], $registro[0]['clave'])) {
                $claveEncriptada = $registro[0]['clave'];
                $claveIngresada = $usuario->limpiarCadena($_POST['clave']);
                if (password_verify($claveIngresada, $claveEncriptada)) {
                    $resultados["login"] = true;
                    $resultados["mensaje"] = "Bienvenido";

                    $sesion["estado"] = true;
                    $sesion["dashboard"] = date('h:i:s d-m-Y');
                    $sesion["idUsuario"] = $registro[0]['idUsuario'];
                    $sesion["apellidos"] = $registro[0]['apellidos'];
                    $sesion["nombres"] = $registro[0]['nombres'];
                    $sesion["correo"] = $registro[0]['correo'];
                    $sesion["clave"] = $registro[0]['clave'];
                    $sesion["idRol"] = $registro[0]['idRol'];

                    // Guardar el nombre completo en una sola variable, si es necesario
                    $sesion["nombreCompleto"] = $registro[0]['nombres'] . ' ' . $registro[0]['apellidos'];

                    //$sesion["accesos"] = $accesosV2[$registro[0]['idRol']]; // Actualización
                    // Añadimos el idUsuario a la sesión global
                    $accesos = $usuario->obtenerPermisos(["idRol" => $registro[0]['idRol']]);
                    $sesion["accesos"] = $accesos;
                    $_SESSION['login'] = $sesion;
                    $_SESSION['idUsuario'] = $registro[0]['idUsuario'];
                } else {
                    $resultados["mensaje"] = "Error en la contraseña";
                    $sesion["estado"] = false;
                    $sesion["apellidos"] = "";
                    $sesion["nombres"] = "";
                }
            } else {
                $resultados["mensaje"] = "No existe el usuario";
                $sesion["estado"] = false;
                $sesion["apellidos"] = "";
                $sesion["nombres"] = "";
            }

            $_SESSION['login'] = $sesion;
            echo json_encode($resultados);
            break;

        case 'actualizarcontrasenia':
            try {
                /* Validando que los parámetros necesarios se encuentren presente */
                if (empty($_POST["correo"]) || empty($_POST["clave"])) {
                    throw new Exception("Faltan parámetros obligatorios (correo o clave).");
                }
                /* Encriptamos la contraseña ingresada por el usuario */
                $claveEncriptada = password_hash($_POST["clave"], PASSWORD_BCRYPT);
                /* Reparamos los datos */
                $datos = [
                    "correo" => $_POST["correo"],
                    "clave" => $claveEncriptada
                ];
                /* Hacemos un llamado al modelo que se actualizará */
                $resultado = $usuario->ActualizarContrasenia($datos);

                if ($resultado) {
                    echo json_encode(["status" => "success", "message" => "Contraseña actualizada correctamente."]);
                } else {
                    throw new Exception("No se pudo actualizar la contraseña.");
                }
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
            break;
    }
}