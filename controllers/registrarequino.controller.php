<?php
require_once '../models/Registrarequino.php';

$controller = new Registrarequino();

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

    default:
        echo json_encode(["status" => "error", "message" => "Operación no válida."]);
        break;
}
