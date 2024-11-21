<?php
require_once '../models/Historialequino.php';

$controller = new HistorialEquino();

header("Content-type: application/json; charset=utf-8");

// Verifica si la solicitud es GET o POST
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Caso de operación GET
    if (isset($_GET['operation'])) {
        switch ($_GET['operation']) {
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

        case 'registrarHistorialEquino':
            if (isset($requestBody['idEquino']) && isset($requestBody['descripcion'])) {
                echo json_encode($controller->registrarHistorialEquino($requestBody));
            } else {
                echo json_encode(["status" => "error", "message" => "Faltan parámetros para registrar la campaña."]);
            }
            break;
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}