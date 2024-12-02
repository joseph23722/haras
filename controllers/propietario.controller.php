<?php
require_once '../models/Propietario.php';

$controller = new RegistrarPropietario();

header("Content-type: application/json; charset=utf-8");

// Verifica si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        case 'registrarPropietario':
            // Verificar si el nombre del propietario está especificado
            if (!isset($requestBody['nombreHaras'])) {
                echo json_encode(["status" => "error", "message" => "El nombre del propietario no está especificado."]);
                exit;
            }

            // Registrar el propietario utilizando el modelo
            $resultado = $controller->registrarPropietario($requestBody['nombreHaras']);

            echo json_encode($resultado);
            break;

        default:
            echo json_encode(["status" => "error", "message" => "Operación no válida."]);
            break;
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}
