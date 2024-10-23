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
                exit();

            case 'getTipoEquinos':
                $result = $alimento->getTipoEquinos();
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'data' => $result]);
                exit();

            case 'getAllAlimentos':
                $result = $alimento->getAllAlimentos();
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'data' => $result]);
                exit();

            default:
                echo json_encode(['status' => 'error', 'message' => 'Operación no válida para GET.']);
                exit();
        }
    }

    // Si el método es POST, manejarlo aquí
    if ($method === 'POST') {
        // Detectar si el contenido es JSON o formulario
        if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
            $input = file_get_contents('php://input');
            $params = json_decode($input, true); // Decodificar JSON
        } else {
            $params = $_POST; // O usar $_POST si es formulario
        }

        // Verificar si se ha recibido una operación
        $operation = $params['operation'] ?? '';
        if (!$operation) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Operación no definida en la solicitud POST.'
            ]);
            exit();
        }

        // Registrar el valor de la operación para depuración
        error_log("Operación recibida: " . $operation);

        switch ($operation) {
            case 'registrar':
                $result = $alimento->registrarNuevoAlimento($params);
                break;

            case 'entrada':
                $result = $alimento->registrarEntradaAlimento($params);
    
                // Aquí se envía la respuesta una sola vez
                echo json_encode([
                    'status' => $result['status'],
                    'message' => $result['message']
                ]);
                exit();

            case 'salida':
                $result = $alimento->registrarSalidaAlimento($params);
                if ($result['status'] === 'success') {
                    echo json_encode([
                        'status' => 'success',
                        'message' => $result['message']
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => $result['message']
                    ]);
                }
                exit(); 

            // Listar todos los lotes registrados
            case 'listarLotes':
                try {
                    // Obtener los lotes
                    $lotes = $alimento->listarLotes();
                    
                    // Verificar si es éxito
                    if ($lotes['status'] === 'success') {
                        // Envía la respuesta como JSON
                        echo json_encode([
                            'status' => 'success',
                            'message' => 'Lotes obtenidos correctamente.',
                            'data' => $lotes['data']
                        ]);
                    } else {
                        // Si hay error, responde con error en formato JSON
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'No se pudieron obtener los lotes.'
                        ]);
                    }
                } catch (PDOException $e) {
                    // Enviar cualquier error en formato JSON
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error al listar los lotes: ' . $e->getMessage()
                    ]);
                }
            
                // Finalizar el script para asegurar que no haya más salida
                exit();

            
                
            case 'verificarLote':
                $lote = $params['lote'] ?? null;
            
                if (!$lote) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'El lote no puede estar vacío.'
                    ]);
                    exit();
                }
            
                // Llamar al modelo para verificar si el lote existe
                $resultado = $alimento->verificarLote($lote);
            
                // Verificar el resultado devuelto por el modelo
                if ($resultado['status'] === 'error') {
                    echo json_encode([
                        'status' => 'error',
                        'message' => $resultado['message']  // Usar el mensaje devuelto por el modelo
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'success',
                        'message' => $resultado['message']  // Usar el mensaje devuelto por el modelo
                    ]);
                }
                exit();
                
            

            case 'eliminar':
                $idAlimento = $params['idAlimento'] ?? null;
                $result = $alimento->eliminarAlimento($idAlimento);
                break;

            case 'notificarStockBajo':
                $minimoStock = $params['minimoStock'] ?? 0;
                $result = $alimento->notificarStockBajo($minimoStock);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();

            case 'historial':
                try {
                    if (!isset($params['tipoMovimiento']) || empty($params['tipoMovimiento'])) {
                        throw new Exception("El tipo de movimiento ('Entrada' o 'Salida') es obligatorio.");
                    }

                    $result = $alimento->obtenerHistorialMovimientos([
                        'tipoMovimiento' => $params['tipoMovimiento'],
                        'fechaInicio' => $params['fechaInicio'] ?? '1900-01-01',
                        'fechaFin' => $params['fechaFin'] ?? date('Y-m-d'),
                        'idUsuario' => $params['idUsuario'] ?? 0,
                        'limit' => $params['limit'] ?? 10,
                        'offset' => $params['offset'] ?? 0
                    ]);

                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'success', 'data' => $result]);
                } catch (Exception $e) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                }
                exit();

            default:
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Operación no válida para POST.'
                ]);
                exit();
        }

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $result]);
        exit();
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
        exit();
    }

    // Si el método no es ni POST, ni GET, ni DELETE, arrojar error
    else {
        throw new Exception('Método no permitido.');
    }
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit();
}
