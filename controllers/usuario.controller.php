<?php
session_start();

require_once '../models/Usuario.php';

header("Content-type: application/json; charset=utf-8");

$accesos = [
    "Gerente"           => ["dashboard", ""],
    "Administrador"     => ["dashboard", "listadoServicios", "usuarios"],
    "Supervisor Equino" => ["dashboard", "registroEquinos", "servicioMixto", "servicioPropio", "usuarios", "inventarioAlimentos", "inventarioMedicamentos", "listadoServicios"],
    "Supervisor Campo"  => ["dashboard", "rotacionCampos"],
    "Médico"            => ["dashboard", "historialMedico"],
    "Herrero"           => ["dashboard", "listadoServicios"]
];

$accesosV2 = [
    
]

$usuario = new Usuario();

try {
    if (isset($_GET['operation'])) {
        switch ($_GET['operation']) {
            case 'getAll':
                echo json_encode($usuario->getAll());
                break;

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
                $row = $usuario->login(['correo' => $_GET['correo']]);

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

            default:
                error_log("Operación no reconocida en el controlador.");
                echo json_encode([
                    "status" => 'error',
                    "message" => 'Operación no reconocida'
                ]);
                break;
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
