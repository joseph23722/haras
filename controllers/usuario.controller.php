
<?php

header('Content-Type: application/json');

session_start();


header('Content-Type: application/json');

/* $accesos = [
    "1"             => ["dashboard", "listadoServicios"],
    "2"             => ["dashboard", "listadoServicios", "usuarios"],
    "3"             => ["dashboard", "registroEquinos", "servicioMixto", "servicioPropio", "usuarios", "inventarioAlimentos", "inventarioMedicamentos", "listadoServicios"],
    "4"             => ["dashboard", "rotacionCampos", "programacionCalendario"],
    "5"             => ["dashboard", "historialMedico"],
    "6"             => ["dashboard", "listadoServicios"]
];

$accesosV2 = [
    1 => [ // Gerente
        ["ruta" => "dashboard", "texto" => "Inicio", "icono" => "fas fa-tachometer-alt"],
        ["ruta" => "listadoServicios", "texto" => "Listado de Servicios", "icono" => "fa-solid fa-list-ol"],
    ],
    2 => [ // Administrador
        ["ruta" => "dashboard", "texto" => "Inicio", "icono" => "fas fa-tachometer-alt"],
        ["ruta" => "listadoServicios", "texto" => "Listado de Servicios", "icono" => "fa-solid fa-list-ol"],
        ["ruta" => "usuarios", "texto" => "Usuarios", "icono" => "fas fa-users"]
    ],
    3 => [ // Supervisor Equino
        ["ruta" => "dashboard", "texto" => "Inicio", "icono" => "fas fa-tachometer-alt"],
        ["ruta" => "registroEquinos", "texto" => "Registro de Equinos", "icono" => "fas fa-clipboard-list"],
        ["ruta" => "servicioMixto", "texto" => "Servicio Mixto", "icono" => "fas fa-exchange-alt"],
        ["ruta" => "servicioPropio", "texto" => "Servicio Propio", "icono" => "fas fa-cog"],
        ["ruta" => "usuarios", "texto" => "Usuarios", "icono" => "fas fa-users"],
        ["ruta" => "inventarioAlimentos", "texto" => "Inventario de Alimentos", "icono" => "fas fa-apple-alt"],
        ["ruta" => "inventarioMedicamentos", "texto" => "Inventario de Medicamentos", "icono" => "fas fa-notes-medical"],
        ["ruta" => "listadoServicios", "texto" => "Listado de Servicios", "icono" => "fa-solid fa-list-ol"]
    ],
    4 => [ // Supervisor Campo
        ["ruta" => "dashboard", "texto" => "Inicio", "icono" => "fas fa-tachometer-alt"],
        ["ruta" => "rotacionCampos", "texto" => "Rotación de Campos", "icono" => "fas fa-solid fa-group-arrows-rotate"],
        ["ruta" => "programacionCalendario", "texto" => "Programación Campos", "icono" => "fas fa-solid fa-group-arrows-rotate"]
    ],
    5 => [ // Médico
        ["ruta" => "dashboard", "texto" => "Inicio", "icono" => "fas fa-tachometer-alt"],
        ["ruta" => "historialMedico", "texto" => "Historial Médico", "icono" => "fas fa-notes-medical"],
    ],
    6 => [ // Herrero
        ["ruta" => "dashboard", "texto" => "Inicio", "icono" => "fas fa-tachometer-alt"],
        ["ruta" => "listadoServicios", "texto" => "Listado de Servicios", "icono" => "fa-solid fa-list-ol"]
    ]
]; */


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
    }
}
