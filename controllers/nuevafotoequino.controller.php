<?php
require_once '../models/Nuevafoto.php';

$controller = new Nuevafoto();

header("Content-type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Caso de operación GET
    if (isset($_GET['operation'])) {
        switch ($_GET['operation']) {
            case 'listarFotografias':
                // Verifica si se ha pasado el idEquino como parámetro
                if (isset($_GET['idEquino'])) {
                    $idEquino = $_GET['idEquino'];
                    echo json_encode($controller->ObtenerFotografiasEquino($idEquino)); // Llamada al método para listar las fotos
                } else {
                    echo json_encode(["status" => "error", "message" => "Falta el parámetro idEquino."]);
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
        case 'registrarNuevasFotos':
            // Verifica si los datos necesarios están presentes
            if (isset($requestBody['idEquino']) && isset($requestBody['public_id'])) {
                echo json_encode($controller->registrarNuevasFotos($requestBody));
            } else {
                echo json_encode(["status" => "error", "message" => "Faltan parámetros para registrar la fotografía."]);
            }
            break;
        default:
            echo json_encode(["status" => "error", "message" => "Operación no válida."]);
            break;
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}
