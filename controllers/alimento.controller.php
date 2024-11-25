<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../models/Alimento.php';

header('Content-Type: application/json');

// Enviar respuesta JSON


$alimento = new Alimento();

try {
    $method = $_SERVER['REQUEST_METHOD'];

    // Manejar solicitudes GET
    if ($method === 'GET') {
        $operation = $_GET['operation'] ?? '';

        switch ($operation) {

            case 'listarTiposYUnidades':
                try {
                    $result = $alimento->listarSugerenciasAlimentos();
                    if ($result !== false) {
                        echo json_encode(['status' => 'success', 'data' => $result]);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'No se pudieron obtener las sugerencias de tipos y unidades de alimentos.']);
                    }
                } catch (PDOException $e) {
                    echo json_encode(['status' => 'error', 'message' => 'Error al obtener las sugerencias: ' . $e->getMessage()]);
                }
                break;

            case 'obtenerTiposAlimento':
                // Obtener todos los tipos de alimento
                $result = $alimento->obtenerTiposAlimento();
                if (is_array($result) && !isset($result['status'])) {
                    echo json_encode(['status' => 'success', 'data' => $result]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => $result['message'] ?? 'Error al obtener tipos de alimento']);
                }
                exit();
    
            // En el controlador
            case 'obtenerUnidadesPorTipoAlimento':
                // Asegurarse de que recibe un `idTipoAlimento` válido
                $idTipoAlimento = $_GET['idTipoAlimento'] ?? null;
                if (!$idTipoAlimento) {
                    echo json_encode(['status' => 'error', 'message' => 'ID de tipo de alimento no proporcionado.']);
                    exit();
                }

                // Llamar al método para obtener las unidades de medida asociadas
                $result = $alimento->obtenerUnidadesPorTipoAlimento($idTipoAlimento);
                if (is_array($result) && !isset($result['status'])) {
                    echo json_encode(['status' => 'success', 'data' => $result]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => $result['message'] ?? 'Error al obtener unidades de medida']);
                }
                exit();

            case 'getUnidadesMedida':
                $nombreAlimento = $_GET['nombreAlimento'] ?? null;
                if ($nombreAlimento) {
                    // Realiza una consulta para obtener unidades asociadas al alimento específico
                    $result = $alimento->obtenerUnidadesPorAlimento($nombreAlimento);
                    echo json_encode(['status' => 'success', 'data' => $result]);
                }
                exit();
                



            case 'getTipoEquinos':
                $result = $alimento->getEquinosPorCategoria();
       
                header('Content-Type: application/json');
                echo json_encode($result);  // Directamente devolver $result
                exit();
                

            case 'getAllAlimentos':
                $idAlimento = $_GET['idAlimento'] ?? null;
                $result = $alimento->getAllAlimentos($idAlimento);
                header('Content-Type: application/json');
                echo json_encode($result); // Cambiado para devolver el resultado directamente
                exit();
            

            case 'listarLotes':
                $nombreAlimento = isset($_GET['nombreAlimento']) ? $_GET['nombreAlimento'] : null;
                $lotes = $alimento->listarLotes($nombreAlimento);
            
                if ($lotes['status'] === 'success') {
                    echo json_encode(['status' => 'success', 'message' => 'Lotes obtenidos correctamente.', 'data' => $lotes['data']]);
                } else {
                    echo json_encode(['status' => $lotes['status'], 'message' => $lotes['message']]);
                }
                break;
                

            case 'historial':
                // Obtener los parámetros del request
                $result = $alimento->obtenerHistorialMovimientos([
                    'tipoMovimiento' => $_GET['tipoMovimiento'] ?? 'Entrada', // Entrada o Salida
                    'filtroFecha' => $_GET['filtroFecha'] ?? 'todos',        // hoy, ultimaSemana, ultimoMes, todos
                    'idUsuario' => $_GET['idUsuario'] ?? 0,                 // ID del usuario (0 para todos)
                    'limit' => $_GET['limit'] ?? 10,                        // Límite para la paginación
                    'offset' => $_GET['offset'] ?? 0                        // Desplazamiento para la paginación
                ]);
            
                // Preparar la respuesta
                if ($result['status'] === 'success') {
                    echo json_encode(['status' => 'success', 'data' => $result['data']]);
                } elseif ($result['status'] === 'info') {
                    echo json_encode(['status' => 'info', 'message' => $result['message']]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => $result['message']]);
                }
                exit();
                

            default:
                echo json_encode(['status' => 'error', 'message' => 'Operación no válida para GET.']);
                exit();
        }
    }

    // Manejar solicitudes POST
    if ($method === 'POST') {
        $params = $_SERVER['CONTENT_TYPE'] === 'application/json' 
            ? json_decode(file_get_contents('php://input'), true)
            : $_POST;

        $operation = $params['operation'] ?? '';
        if (!$operation) {
            echo json_encode(['status' => 'error', 'message' => 'Operación no definida en la solicitud POST.']);
            exit();
        }

        switch ($operation) {


            case 'editarTipoYUnidad':
                // Configurar encabezado de respuesta como JSON
                header('Content-Type: application/json');
            
                // Leer y decodificar la entrada JSON
                $data = json_decode(file_get_contents('php://input'), true);
            
                $idTipoAlimento = $data['idTipoAlimento'] ?? null;
                $nuevoTipo = $data['tipoAlimento'] ?? '';
                $idUnidadMedida = $data['idUnidadMedida'] ?? null;
                $nuevaUnidad = $data['nombreUnidad'] ?? '';
            
                // Validar si los identificadores están presentes
                if (!$idTipoAlimento || !$idUnidadMedida) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'El ID del tipo de alimento y la unidad de medida son requeridos.'
                    ]);
                    exit; // Terminar ejecución
                }
            
                try {
                    // Llama al modelo para realizar la edición
                    $result = $alimento->editarTipoUnidadAlimento($idTipoAlimento, $nuevoTipo, $idUnidadMedida, $nuevaUnidad);
            
                    if ($result) {
                        echo json_encode([
                            'status' => 'success',
                            'message' => 'Tipo y unidad de alimento actualizados correctamente.'
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'No se pudo actualizar el tipo y unidad de alimento. Verifica los datos.'
                        ]);
                    }
                } catch (Exception $e) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error al actualizar el tipo y unidad de alimento: ' . $e->getMessage()
                    ]);
                }
                exit; // Asegurar que no haya más salidas
                break;
            
            
            case 'agregarTipoUnidadMedidaNuevo':
                // Verificar que se envíen los parámetros necesarios
                if (!isset($params['tipoAlimento'], $params['nombreUnidad'])) {
                    echo json_encode(['status' => 'error', 'message' => 'Faltan datos necesarios para agregar el tipo de alimento y la unidad de medida.']);
                    exit();
                }
    
                // Llamar al método en el modelo
                $result = $alimento->agregarTipoUnidadMedidaNuevo($params['tipoAlimento'], $params['nombreUnidad']);
                echo json_encode($result);
                exit();

                
            case 'registrar':
                if (!isset($params['nombreAlimento'], $params['stockActual'], $params['costo'], $params['stockMinimo'], $params['tipoAlimento'], $params['unidadMedida'], $params['lote'], $params['fechaCaducidad'])) {
                    echo json_encode(['status' => 'error', 'message' => 'Faltan datos necesarios para el registro del alimento.']);
                    exit();
                }
                
                // Asegúrate de que `unidadMedida` corresponde al `idUnidadMedida`
                $params['unidadMedida'] = intval($params['unidadMedida']); // Convertir a entero si no lo es
            
                // Llamar al método para registrar el nuevo alimento
                $result = $alimento->registrarNuevoAlimento($params);
                echo json_encode($result);
                exit();
            

            case 'entrada':
                $result = $alimento->registrarEntradaAlimento($params);
                echo json_encode(['status' => $result['status'], 'message' => $result['message']]);
                exit();

            case 'salida':
                $result = $alimento->registrarSalidaAlimento($params);
                echo json_encode(['status' => $result['status'], 'message' => $result['message']]);
                exit();

            case 'verificarLote':
                $lote = $params['lote'] ?? null;
                $idUnidadMedida = $params['unidadMedida'] ?? null; // Asegúrate de que sea el ID de la unidad de medida, no el nombre
                
                // Validar que los parámetros necesarios están presentes
                if (!$lote || !$idUnidadMedida) {
                    echo json_encode(['status' => 'error', 'message' => 'El lote y la unidad de medida no pueden estar vacíos.']);
                    exit();
                }
            
                // Llamar a la función para verificar el lote
                $resultado = $alimento->verificarLote($lote, $idUnidadMedida);
                echo json_encode($resultado);
                exit();
                
                
                

            case 'eliminar':
                $idAlimento = $params['idAlimento'] ?? null;
                $result = $alimento->eliminarAlimento($idAlimento);
                echo json_encode(['status' => $result['status'], 'message' => $result['message']]);
                exit();

            case 'notificarStockBajo':
                $minimoStock = $params['minimoStock'] ?? 0;
                $result = $alimento->notificarStockBajo();
                
                // Depuración
                error_log(print_r($result, true));  // Registra la respuesta en el log para verificar el contenido
                
                echo json_encode($result);  // Envía la respuesta JSON
                exit();
                

            default:
                echo json_encode(['status' => 'error', 'message' => 'Operación no válida para POST.']);
                exit();
        }
    }

    // Manejar solicitudes DELETE
    if ($method === 'DELETE') {
        parse_str(file_get_contents("php://input"), $_DELETE);
        $idAlimento = $_DELETE['idAlimento'] ?? null;

        if ($idAlimento === null) {
            throw new Exception('ID del alimento no proporcionado para eliminar.');
        }

        $result = $alimento->eliminarAlimento($idAlimento);
        echo json_encode(['status' => $result['status'], 'message' => $result['message']]);
        exit();
    }

    // Método no permitido
    throw new Exception('Método no permitido.');

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit();
}
