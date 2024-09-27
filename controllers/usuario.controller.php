<?php
session_start();

require_once '../models/Usuario.php';
$Usuario = new Usuario();

try {
    if (isset($_GET['operation'])) {

        switch($_GET['operation']) {
            case 'login':
                $login = [
                    "permitido" => false,
                    "apellidos" => "",
                    "nombres"   => "",
                    "idUsuario" => "",
                    "idRol"     => "",
                    "status"    => ""
                ];

                // Intentamos obtener el usuario de la base de datos
                $row = $Usuario->login(['correo' => $_GET['correo']]);

                if (count($row) == 0) {
                    $login["status"] = "no existe el usuario";
                } else {
                    $claveEncriptada = $row[0]['clave']; // de la BD
                    $claveIngreso = $_GET['clave'];   // del formulario

                    // Verificamos la contraseña
                    if (password_verify($claveIngreso, $claveEncriptada)) {
                        $login["permitido"] = true;
                        $login["apellidos"] = $row[0]["apellidos"];
                        $login["nombres"] = $row[0]["nombres"];
                        $login["idUsuario"] = $row[0]["idUsuario"];
                        $login["idRol"] = $row[0]["idRol"];
                    } else {
                        $login["status"] = "contraseña incorrecta";
                    }
                }

                // Guardar los detalles de la sesión
                $_SESSION['login'] = $login;
                $_SESSION['idUsuario'] = $login["idUsuario"];

                // Devolver respuesta en formato JSON
                echo json_encode($login);
                break;

            case 'destroy':
                session_unset();
                session_destroy();
                header('Location: http://localhost/haras');
                break;

            default:
                // Si no hay una operación válida
                throw new Exception("Operación no válida.");
        }
    }

    if (isset($_POST['operation'])) {
        switch($_POST['operation']) {
            case 'add':
                $datos = [
                    "idPersonal"   => $_POST['idPersonal'],
                    "correo"       => $_POST['correo'],
                    "clave"        => $_POST['clave'],
                    "idRol"        => $_POST['idRol']
                ];

                // Insertar usuario y devolver el ID generado
                $idobtenido = $Usuario->add($datos);
                echo json_encode(["idUsuario" => $idobtenido]);
                break;

            default:
                // Si no hay una operación válida
                throw new Exception("Operación POST no válida.");
        }
    }

} catch (PDOException $e) {
    // Captura cualquier error de la base de datos y devuelve un mensaje en JSON
    echo json_encode([
        "permitido" => false,
        "status" => "Error en la base de datos: " . $e->getMessage()
    ]);
} catch (Exception $e) {
    // Captura cualquier otro error
    echo json_encode([
        "permitido" => false,
        "status" => "Error: " . $e->getMessage()
    ]);
}
