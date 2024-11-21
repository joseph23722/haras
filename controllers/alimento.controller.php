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
                $result = $model->listarTiposYUnidadesAlimentos();
                echo json_encode(['status' => 'success', 'data' => $result]);
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
                $lotes = $alimento->listarLotes();
                if ($lotes['status'] === 'success') {
                    echo json_encode(['status' => 'success', 'message' => 'Lotes obtenidos correctamente.', 'data' => $lotes['data']]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'No se pudieron obtener los lotes.']);
                }
                exit();

            case 'historial':
                $result = $alimento->obtenerHistorialMovimientos([
                    'tipoMovimiento' => $_GET['tipoMovimiento'] ?? 'Entrada',
                    'fechaInicio' => $_GET['fechaInicio'] ?? '1900-01-01',
                    'fechaFin' => $_GET['fechaFin'] ?? date('Y-m-d'),
                    'idUsuario' => $_GET['idUsuario'] ?? 0,
                    'limit' => $_GET['limit'] ?? 10,
                    'offset' => $_GET['offset'] ?? 0
                ]);

                $data = $result['data'] ?? [];
                echo json_encode(['status' => 'success', 'data' => $data]);
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
                // Registrar todos los parámetros recibidos
                error_log("Parámetros recibidos: " . json_encode($_POST));
            
                // Verifica si todos los parámetros necesarios están presentes
                if (
                    isset($_POST['idTipoAlimentoUnidad']) &&
                    isset($_POST['tipoAlimento']) &&
                    isset($_POST['idUnidadMedida']) &&
                    isset($_POST['nombreUnidad'])
                ) {
                    // Recuperar los parámetros
                    $idTipoAlimentoUnidad = $_POST['idTipoAlimentoUnidad'];
                    $tipoAlimento = $_POST['tipoAlimento'];
                    $idUnidadMedida = $_POST['idUnidadMedida'];
                    $nombreUnidad = $_POST['nombreUnidad'];
            
                    // Llama al modelo para realizar la actualización
                    try {
                        $result = $alimento->editarTipoYUnidadEspecifica($idTipoAlimentoUnidad, $tipoAlimento, $idUnidadMedida, $nombreUnidad);
                        echo json_encode($result); // Retorna un JSON válido
                        exit; // Finaliza la ejecución para evitar múltiples respuestas
                    } catch (Exception $e) {
                        error_log("Error al editar Tipo y Unidad: " . $e->getMessage());
                        echo json_encode(['status' => 'error', 'message' => 'Error interno al procesar la solicitud.']);
                        exit;
                    }
                } else {
                    // Si faltan parámetros, registra cuáles faltan
                    $faltantes = [];
                    if (!isset($_POST['idTipoAlimentoUnidad'])) $faltantes[] = 'idTipoAlimentoUnidad';
                    if (!isset($_POST['tipoAlimento'])) $faltantes[] = 'tipoAlimento';
                    if (!isset($_POST['idUnidadMedida'])) $faltantes[] = 'idUnidadMedida';
                    if (!isset($_POST['nombreUnidad'])) $faltantes[] = 'nombreUnidad';
            
                    error_log("Faltan los siguientes parámetros: " . implode(', ', $faltantes));
            
                    // Si faltan parámetros, retorna un mensaje de error
                    echo json_encode(['status' => 'error', 'message' => 'Parámetros incompletos: ' . implode(', ', $faltantes)]);
                    exit;
                }
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
