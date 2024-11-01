<?php
require_once '../models/Alimento.php';

$alimento = new Alimento();

try {
    $method = $_SERVER['REQUEST_METHOD'];

    // Si el método es GET, manejarlo aquí
    if ($method === 'GET') {
        $operation = $_GET['operation'] ?? '';

        switch ($operation) {

            case 'notificarStockBajo':
                // Llamar al método sin parámetros
                $result = $alimento->notificarStockBajo();
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();

                
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

            case 'historial':
                try {
                    $result = $alimento->obtenerHistorialMovimientos([
                        'tipoMovimiento' => $_GET['tipoMovimiento'] ?? '',
                        'fechaInicio' => $_GET['fechaInicio'] ?? '1900-01-01',
                        'fechaFin' => $_GET['fechaFin'] ?? date('Y-m-d'),
                        'idUsuario' => $_GET['idUsuario'] ?? 0,
                        'limit' => $_GET['limit'] ?? 10,
                        'offset' => $_GET['offset'] ?? 0
                    ]);
            
                    // Asegurar que data sea un array
                    $data = $result['data'] ?? [];
                    if (!is_array($data)) {
                        $data = [$data]; // Si no es un array, conviértelo en uno
                    }
            
                    echo json_encode(['status' => 'success', 'data' => $data]);
                } catch (Exception $e) {
                    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                }
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
                 // Limpiar cualquier salida previa antes de enviar el JSON
                ob_clean();
                // Devolver la respuesta en formato JSON para que el frontend pueda interpretarla
                echo json_encode($result);
                exit;

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

            

            
            // Verificar si un lote existe    
            case 'verificarLote':
                $lote = $_POST['lote'] ?? null;
                $unidadMedida = $_POST['unidadMedida'] ?? null;
            
                if (!$lote || !$unidadMedida) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'El lote y la unidad de medida no pueden estar vacíos.'
                    ]);
                    exit();
                }
            
                // Llamar al modelo para verificar si la combinación existe
                $resultado = $alimento->verificarLote($lote, $unidadMedida);
            
                echo json_encode($resultado);
                exit();
            
                
            case 'eliminar':
                $idAlimento = $params['idAlimento'] ?? null;
                $result = $alimento->eliminarAlimento($idAlimento);
                break;


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
