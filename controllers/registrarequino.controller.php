<?php
require_once '../models/Registrarequino.php';

$controller = new Registrarequino();

header("Content-type: application/json; charset=utf-8");

// Verifica si la solicitud es GET o POST
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Caso de operación GET
    if (isset($_GET['operation'])) {
        switch ($_GET['operation']) {
            case 'getAll':
                $estadoMonta = isset($_GET['estadoMonta']) ? $_GET['estadoMonta'] : null;
                echo json_encode($controller->listadoEquinos($estadoMonta));
                break;

            case 'listadoEstadoMonta':
                echo json_encode($controller->listadoEstadoMonta());
                break;

            case 'getHistorial':
                if (isset($_GET['idEquino'])) {
                    echo json_encode($controller->obtenerHistorialEquino($_GET['idEquino']));
                } else {
                    echo json_encode(["status" => "error", "message" => "ID del equino no especificado."]);
                }
                break;

            default:
                echo json_encode(["status" => "error", "message" => "Operación no válida."]);
                break;
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Operación no especificada."]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Caso de operación POST con cuerpo JSON
    $requestBody = json_decode(file_get_contents("php://input"), true);

    // Verifica si la decodificación fue exitosa
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["status" => "error", "message" => "JSON mal formado."]);
        exit;
    }

    // Verifica si la operación está definida
    if (!isset($requestBody['operation'])) {
        echo json_encode(["status" => "error", "message" => "Operación no especificada."]);
        exit;
    }

    switch ($requestBody['operation']) {
        case 'registrarEquino':
            echo json_encode($controller->registrarEquino($requestBody));
            break;

        case 'listarPropietarios':
            echo json_encode($controller->listarPropietarios());
            break;

        case 'listarTipoEquinos':
            echo json_encode($controller->listarTipoEquinos());
            break;

        case 'getAll':
            echo json_encode($controller->listadoEquinos());
            break;

        default:
            echo json_encode(["status" => "error", "message" => "Operación no válida."]);
            break;

        case 'buscarEquinoPorNombre':
            if (isset($requestBody['nombreEquino'])) {
                echo json_encode($controller->buscarEquinoPorNombre($requestBody['nombreEquino']));
            } else {
                echo json_encode(["status" => "error", "message" => "Nombre del equino no especificado."]);
            }
            break;

        case 'buscarEquinosGeneral':
            if (isset($requestBody['nombreEquino'])) {
                echo json_encode($controller->buscarEquinosGenerales($requestBody['nombreEquino']));
            } else {
                echo json_encode(["status" => "error", "message" => "Nombre del equino no especificado."]);
            }
            break;

        case 'buscarNacionalidad':
            if (isset($requestBody['nacionalidad'])) {
                echo json_encode($controller->buscarNacionalidad($requestBody['nacionalidad']));
            } else {
                echo json_encode(["status" => "error", "message" => "Nacionalidad no especificada."]);
            }
            break;
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}
