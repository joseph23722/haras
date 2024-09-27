<?php
require_once '../models/Alimento.php';

$alimento = new Alimento();

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $operation = $_POST['operation'] ?? '';

        // Capturar todos los parámetros
        $params = [
            'nombreAlimento' => $_POST['nombreAlimento'] ?? null,
            'tipoAlimento' => $_POST['tipoAlimento'] ?? null,
            'cantidad' => $_POST['cantidad'] ?? null,
            'unidadMedida' => $_POST['unidadMedida'] ?? null,
            'costo' => $_POST['costo'] ?? null,
            'lote' => $_POST['lote'] ?? null,
            'fechaCaducidad' => $_POST['fechaCaducidad'] ?? null,
            'idTipoEquino' => $_POST['idTipoEquino'] ?? null,
            'idTipomovimiento' => $_POST['idTipomovimiento'] ?? null,
            'fechaIngreso' => $_POST['fechaIngreso'] ?? null,
            'merma' => $_POST['merma'] ?? 0
        ];

        // Controlar las diferentes operaciones
        switch ($operation) {
            case 'registrar':
                $result = $alimento->registrarNuevoAlimento($params);
                break;

            case 'actualizar_stock':
            case 'movimiento':
                $result = $alimento->actualizarStockAlimento($params);
                break;

            case 'getAllAlimentos':
                $result = $alimento->getAllAlimentos();
                echo json_encode($result);
                exit();

            case 'getAlimentosStockInfo':
                $result = $alimento->getAlimentosStockInfo();
                echo json_encode($result);
                exit();

            case 'getTipoEquinos':
                $result = $alimento->getTipoEquinos();
                echo json_encode($result);
                exit();

            case 'eliminar':
                $idAlimento = $_POST['idAlimento'] ?? null;
                if ($idAlimento === null) {
                    throw new Exception('ID del alimento no proporcionado.');
                }
                $result = $alimento->eliminarAlimento($idAlimento);
                break;

            default:
                throw new Exception('Operación no válida.');
        }

        // Siempre responder en JSON
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        parse_str(file_get_contents("php://input"), $_DELETE);
        $idAlimento = $_DELETE['idAlimento'] ?? null;

        if ($idAlimento === null) {
            throw new Exception('ID del alimento no proporcionado para eliminar.');
        }

        $result = $alimento->eliminarAlimento($idAlimento);
        header('Content-Type: application/json');
        echo json_encode($result);
    }
} catch (Exception $e) {
    // Asegurarse de que cualquier excepción se maneje correctamente y se devuelva como JSON
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
