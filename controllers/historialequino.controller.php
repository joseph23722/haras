<?php
require_once '../models/Historialequino.php';

$controller = new HistorialEquino();

header("Content-type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestBody = json_decode(file_get_contents("php://input"), true);

    // Verifica si la operación está definida
    if (!isset($requestBody['operation'])) {
        echo json_encode(["status" => "error", "message" => "Operación no especificada."]);
        exit;
    }

    switch ($requestBody['operation']) {
        case 'registrarHistorialEquino':
            if (isset($requestBody['idEquino']) && isset($requestBody['descripcion'])) {
                try {
                    // Llamamos al modelo para registrar el historial
                    $response = $controller->registrarHistorialEquino($requestBody);
                    echo json_encode($response);
                } catch (PDOException $e) {
                    // Aquí manejamos los errores lanzados por MySQL
                    if ($e->getCode() == '45000') {
                        // El error es el específico de "Ya existe un historial"
                        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
                    } else {
                        // Otro tipo de error (base de datos o cualquier otro error)
                        echo json_encode(["status" => "error", "message" => "Error al registrar el historial: " . $e->getMessage()]);
                    }
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Faltan parámetros para registrar el historial."]);
            }
            break;

        default:
            echo json_encode(["status" => "error", "message" => "Operación no válida."]);
            break;
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}
