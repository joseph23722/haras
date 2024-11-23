<?php
require_once '../models/Editarequino.php';

$controller = new Editarequino();
header('Content-Type: application/json; charset=utf-8');

try {
    $input = json_decode(file_get_contents("php://input"), true);

    // Validar que el cuerpo sea JSON válido
    if ($input === null) {
        echo json_encode(["status" => "error", "message" => "El cuerpo de la solicitud debe ser JSON válido."]);
        exit;
    }

    // Verificar el método HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(["status" => "error", "message" => "Método HTTP no permitido."]);
        exit;
    }

    // Validar si se especificó una operación
    if (!isset($input['operation']) || empty($input['operation'])) {
        echo json_encode(["status" => "error", "message" => "Operación no especificada."]);
        exit;
    }

    // Manejar operaciones
    switch ($input['operation']) {
        case 'editarEquino':
            // Construir los parámetros para la edición
            $params = array_filter($input, fn($value) => $value !== null && $value !== '', ARRAY_FILTER_USE_BOTH);

            // Registrar los parámetros enviados
            error_log("Parámetros recibidos para edición: " . json_encode($params));

            try {
                $resultado = $controller->editarEquino($params);

                if ($resultado === 1) {
                    echo json_encode([
                        "status" => "success",
                        "message" => "Equino actualizado correctamente."
                    ]);
                } else {
                    echo json_encode([
                        "status" => "error",
                        "message" => "No se pudo actualizar el equino. Verifica los datos enviados."
                    ]);
                }
            } catch (Exception $e) {
                error_log("Error en el modelo: " . $e->getMessage());
                echo json_encode([
                    "status" => "error",
                    "message" => "Error al editar el equino: " . $e->getMessage()
                ]);
            }
            break;

        default:
            echo json_encode([
                "status" => "error",
                "message" => "Operación no válida: " . htmlspecialchars($input['operation'])
            ]);
            break;
    }
} catch (Exception $e) {
    error_log("Error inesperado: " . $e->getMessage());
    echo json_encode([
        "status" => "error",
        "message" => "Error inesperado: " . $e->getMessage()
    ]);
}
