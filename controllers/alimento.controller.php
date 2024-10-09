<?php
require_once '../models/Alimento.php';

$alimento = new Alimento();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $params = $_POST;
        $operation = $params['operation'] ?? '';

        switch ($operation) {
            case 'registrar':
                $result = $alimento->registrarNuevoAlimento($params);
                break;

            case 'entrada':
                $result = $alimento->registrarEntradaAlimento($params);
                break;

            case 'salida':
                $result = $alimento->registrarSalidaAlimento($params);
                break;

            case 'getAllAlimentos':
                $result = $alimento->getAllAlimentos();
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();

            case 'getTipoEquinos':
                $result = $alimento->getTipoEquinos();
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();

            case 'eliminar':
                $idAlimento = $_POST['idAlimento'] ?? null;
                $result = $alimento->eliminarAlimento($idAlimento);
                break;

            case 'notificarStockBajo':
                $minimoStock = $_POST['minimoStock'] ?? 0;
                $result = $alimento->notificarStockBajo($minimoStock);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();

            case 'historial':
                $result = $alimento->obtenerHistorialMovimientos([
                    'tipoMovimiento' => $_POST['tipoMovimiento'] ?? '',
                    'fechaInicio' => $_POST['fechaInicio'] ?? '1900-01-01',
                    'fechaFin' => $_POST['fechaFin'] ?? date('Y-m-d'),
                    'idUsuario' => $_POST['idUsuario'] ?? 0,
                    'limit' => $_POST['limit'] ?? 10,
                    'offset' => $_POST['offset'] ?? 0
                ]);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();

            default:
                throw new Exception('Operación no válida.');
        }

        // Siempre responder en JSON después de cualquier operación
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $result]);
    } elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        parse_str(file_get_contents("php://input"), $_DELETE);
        $idAlimento = $_DELETE['idAlimento'] ?? null;

        if ($idAlimento === null) {
            throw new Exception('ID del alimento no proporcionado para eliminar.');
        }

        $result = $alimento->eliminarAlimento($idAlimento);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $result]);
    } else {
        throw new Exception('Método no permitido.');
    }
} catch (Exception $e) {
    // Asegurarse de que cualquier excepción se maneje correctamente y se devuelva como JSON
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
