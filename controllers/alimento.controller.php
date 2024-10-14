<?php
require_once '../models/Alimento.php';

$alimento = new Alimento();

try {
    $method = $_SERVER['REQUEST_METHOD'];

    // Si el método es GET, manejarlo aquí
    if ($method === 'GET') {
        $operation = $_GET['operation'] ?? '';

        switch ($operation) {
            case 'getUnidadesMedida':
                $nombreAlimento = $_GET['nombreAlimento'] ?? null;
                if (!$nombreAlimento) {
                    throw new Exception('Nombre del alimento no proporcionado.');
                }

                $result = $alimento->getUnidadesMedida($nombreAlimento);

                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'data' => ['unidadesMedida' => $result]]);
                exit(); // Finaliza la ejecución para evitar respuestas adicionales
        }
    }

    // Si el método es POST, manejarlo aquí
    if ($method === 'POST') {
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

            case 'eliminar':
                $idAlimento = $_POST['idAlimento'] ?? null;
                $result = $alimento->eliminarAlimento($idAlimento);
                break;

            case 'notificarStockBajo':
                $minimoStock = $_POST['minimoStock'] ?? 0;
                $result = $alimento->notificarStockBajo($minimoStock);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit(); // Finaliza la ejecución

            case 'getAllAlimentos':
                $result = $alimento->getAllAlimentos();
                header('Content-Type: application/json');
                echo json_encode($result);
                exit(); // Finaliza la ejecución

            case 'getTipoEquinos':
                $result = $alimento->getTipoEquinos();
                header('Content-Type: application/json');
                echo json_encode($result);
                exit(); // Finaliza la ejecución

            case 'historial':
                try {
                    // Validar que 'tipoMovimiento' esté presente en la solicitud
                    if (!isset($_POST['tipoMovimiento']) || empty($_POST['tipoMovimiento'])) {
                        throw new Exception("El tipo de movimiento ('Entrada' o 'Salida') es obligatorio.");
                    }
            
                    // Llamar al método obtenerHistorialMovimientos con los parámetros correctos
                    $result = $alimento->obtenerHistorialMovimientos([
                        'tipoMovimiento' => $_POST['tipoMovimiento'],  // Se asegura que esté definido
                        'fechaInicio' => $_POST['fechaInicio'] ?? '1900-01-01',
                        'fechaFin' => $_POST['fechaFin'] ?? date('Y-m-d'),
                        'idUsuario' => $_POST['idUsuario'] ?? 0,
                        'limit' => $_POST['limit'] ?? 10,
                        'offset' => $_POST['offset'] ?? 0
                    ]);
            
                    // Devolver el resultado en formato JSON
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'success', 'data' => $result]);
            
                } catch (Exception $e) {
                    // Manejar errores, devolver un mensaje de error en JSON
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                }
                exit(); // Finaliza la ejecución
                

            default:
                throw new Exception('Operación no válida para POST.');
        }

        // Siempre responder en JSON después de cualquier operación POST
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $result]);
        exit(); // Finaliza la ejecución
    }

    // Si el método es DELETE, manejarlo aquí
    if ($method === 'DELETE') {
        parse_str(file_get_contents("php://input"), $_DELETE);
        $idAlimento = $_DELETE['idAlimento'] ?? null;

        if ($idAlimento === null) {
            throw new Exception('ID del alimento no proporcionado para eliminar.');
        }

        $result = $alimento->eliminarAlimento($idAlimento);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $result]);
        exit(); // Finaliza la ejecución
    }

    // Si el método no es ni POST, ni GET, ni DELETE, arrojar error
    else {
        throw new Exception('Método no permitido.');
    }
} catch (Exception $e) {
    // Asegurarse de que cualquier excepción se maneje correctamente y se devuelva como JSON
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit(); // Finaliza la ejecución
}
