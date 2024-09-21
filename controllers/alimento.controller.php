<?php

require_once '../models/Alimento.php';

$alimento = new Alimento();

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $operation = $_POST['operation'] ?? '';

        // Obtener datos para registrar o actualizar
        $params = [
            'nombreAlimento' => $_POST['nombreAlimento'] ?? null,
            'cantidad' => $_POST['cantidad'] ?? null,
            'costo' => $_POST['costo'] ?? null,
            'idTipoEquino' => $_POST['idTipoEquino'] ?? null,
            'idTipomovimiento' => $_POST['idTipomovimiento'] ?? null,
            'fechaIngreso' => $_POST['fechaIngreso'] ?? null
        ];

        // Operaciones CRUD según la operación solicitada
        if ($operation === 'registrar') {
            $result = $alimento->registrarNuevoAlimento($params);
        } elseif ($operation === 'actualizar_stock' || $operation === 'movimiento') {
            $result = $alimento->actualizarStockAlimento($params);
        } elseif ($operation === 'getAllAlimentos') {
            $result = $alimento->getAllAlimentos();
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        } elseif ($operation === 'getAlimentosStockInfo') {  // Nueva operación para el Dashboard
            $result = $alimento->getAlimentosStockInfo();  // Llamada al nuevo método
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        } elseif ($operation === 'getTipoEquinos') {
            $result = $alimento->getTipoEquinos();
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        } elseif ($operation === 'eliminar') {
            $idAlimento = $_POST['idAlimento'] ?? null;
            if ($idAlimento === null) {
                throw new Exception('ID del alimento no proporcionado.');
            }
            $result = $alimento->eliminarAlimento($idAlimento);
        } else {
            throw new Exception('Operación no válida.');
        }

        // Enviar la respuesta en formato JSON
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        parse_str(file_get_contents("php://input"), $_DELETE);
        $idAlimento = $_DELETE['idAlimento'];

        $result = $alimento->eliminarAlimento($idAlimento);

        // Enviar la respuesta de vuelta al cliente en formato JSON
        header('Content-Type: application/json');
        echo json_encode($result);
    }
} catch (Exception $e) {
    // Capturar errores y enviarlos como respuesta JSON
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
