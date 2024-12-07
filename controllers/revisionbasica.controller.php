<?php
require_once '../models/RevisionBasica.php';

$controller = new RevisionBasica();

header("Content-type: application/json; charset=utf-8");

// Verifica si la solicitud es GET o POST
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['operation'])) {
        switch ($_GET['operation']) {
            case 'listarYeguasPorPropietario':
                if (isset($_GET['idPropietario'])) {
                    echo json_encode($controller->listarYeguasPorPropietario($_GET['idPropietario']));
                } else {
                    echo json_encode($controller->listarYeguasPorPropietario(null));
                }
                break;

            case 'listarRevisiónBásica':
                echo json_encode($controller->listarRevisiónBásica());
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
        case 'registrarRevisionEquino':
            if (isset($requestBody['idEquino']) && isset($requestBody['tiporevision']) && isset($requestBody['fecharevision']) && isset($requestBody['observaciones'])) {
                echo json_encode($controller->registrarRevisionEquino($requestBody));
            } else {
                echo json_encode(["status" => "error", "message" => "Faltan parámetros para registrar la revisión equina."]);
            }
            break;

        case 'listarYeguasPorPropietario':
            if (isset($requestBody['idPropietario'])) {
                echo json_encode($controller->listarYeguasPorPropietario($requestBody['idPropietario']));
            } else {
                echo json_encode($controller->listarYeguasPorPropietario(null));
            }
            break;

        default:
            echo json_encode(["status" => "error", "message" => "Operación no válida."]);
            break;
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}
