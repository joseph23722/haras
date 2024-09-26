<?php
require_once '../models/Alimento.php';

$alimento = new Alimento();

try {
    // Manejo de solicitudes POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $operation = $_POST['operation'] ?? '';

        // Obtener datos para registrar o actualizar
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

        // Validar la operación solicitada
        if ($operation === 'registrar') {
            // Registrar un nuevo alimento
            $result = $alimento->registrarNuevoAlimento($params);
        
        } elseif ($operation === 'actualizar_stock' || $operation === 'movimiento') {
            // Actualizar el stock del alimento
            $result = $alimento->actualizarStockAlimento($params);
        
        } elseif ($operation === 'getAllAlimentos') {
            // Obtener todos los alimentos
            $result = $alimento->getAllAlimentos();
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        
        } elseif ($operation === 'getAlimentosStockInfo') {
            // Obtener información de stock para el dashboard
            $result = $alimento->getAlimentosStockInfo();
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        
        } elseif ($operation === 'getTipoEquinos') {
            // Obtener los tipos de equinos
            $result = $alimento->getTipoEquinos();
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        
        } elseif ($operation === 'reporte_inventario') {
            // Generar reporte de inventario
            $dias = $_POST['dias'] ?? 7;
            $result = $alimento->reporteInventario($dias);
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();

        } elseif ($operation === 'verificar_caducidad') {
            // Verificar alimentos caducados
            $result = $alimento->verificarCaducidad();
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();

        } elseif ($operation === 'eliminar') {
            // Eliminar el alimento
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

    // Manejo de solicitudes DELETE
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        parse_str(file_get_contents("php://input"), $_DELETE);
        $idAlimento = $_DELETE['idAlimento'] ?? null;

        if ($idAlimento === null) {
            throw new Exception('ID del alimento no proporcionado para eliminar.');
        }

        // Eliminar el alimento
        $result = $alimento->eliminarAlimento($idAlimento);

        // Enviar la respuesta en formato JSON
        header('Content-Type: application/json');
        echo json_encode($result);
    }
} catch (Exception $e) {
    // Capturar errores y enviarlos como respuesta JSON
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
