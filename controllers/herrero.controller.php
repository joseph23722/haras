<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../models/Herrero.php';

$herrero = new Herrero();
$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json');

// Función para enviar una respuesta JSON al cliente
function sendResponse($status, $message, $data = null) {
    // Asegurar encabezado JSON
    header('Content-Type: application/json');

    // Construir la respuesta
    $response = ['status' => $status, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }

    // Imprimir y detener la ejecución
    echo json_encode($response);
    exit();
}



// Obtener la operación
$operation = '';
if ($method === 'GET') {
    $operation = $_GET['operation'] ?? '';
} elseif ($method === 'POST') {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (strpos($contentType, 'application/json') !== false) {
        $data = json_decode(file_get_contents('php://input'), true);
        $operation = $data['operation'] ?? '';
    } else {
        $operation = $_POST['operation'] ?? '';
    }
}

// Procesar las operaciones
try {
    if ($method === 'GET') {
        switch ($operation) {
            

            
            case 'consultarHistorialEquino':
                $idEquino = intval($_GET['idEquino'] ?? 0);
                $historial = $herrero->consultarHistorialEquino($idEquino);
                
                if ($historial) {
                    $response = array(
                        "draw" => intval($_GET['draw'] ?? 1),
                        "recordsTotal" => count($historial),
                        "recordsFiltered" => count($historial),
                        "data" => $historial
                    );
                    echo json_encode($response);
                } else {
                    echo json_encode(array(
                        "draw" => intval($_GET['draw'] ?? 1),
                        "recordsTotal" => 0,
                        "recordsFiltered" => 0,
                        "data" => []
                    ));
                }
                break;
            

            case 'consultarEstadoActualHerramientas':
                $estadoActual = $herrero->consultarEstadoActualHerramientas();
                if ($estadoActual) {
                    sendResponse('success', 'Estado actual de herramientas consultado correctamente.', $estadoActual);
                } else {
                    sendResponse('error', 'No se pudo consultar el estado actual de las herramientas.');
                }
                break;

            case 'listarEquinosPorTipo':
                // Llamada al método para listar equinos sin propietario para medicamentos
                $result = $historialme->listarEquinosPorTipo();
                echo json_encode(['data' => $result]);
                break;


            default:
                sendResponse('error', 'Operación no válida para GET.');
        }
    } elseif ($method === 'POST') {
        switch ($operation) {
            case 'insertarHistorialHerrero':
                try {
                    // Obtener y decodificar los datos JSON del cliente
                    $params = json_decode(file_get_contents('php://input'), true);
                    error_log("Datos recibidos en el controlador insertarHistorialHerrero: " . json_encode($params));
            
                    // Verificar campos obligatorios en la entrada
                    $requiredFields = ['idEquino', 'fecha', 'trabajoRealizado', 'herramientasUsadas', 'observaciones'];
                    foreach ($requiredFields as $field) {
                        if (!isset($params[$field]) || empty($params[$field])) {
                            error_log("Campo faltante o vacío: $field.");
                            sendResponse('error', "El campo '$field' es obligatorio para registrar el historial.");
                            return;
                        }
                    }
            
                    // Llamar al método del modelo para insertar el historial
                    $result = $herrero->insertarHistorialHerrero($params);
                    error_log("Resultado del modelo insertarHistorialHerrero: " . json_encode($result));
            
                    // Enviar la respuesta según el resultado
                    sendResponse($result['status'], $result['message']);
                } catch (Exception $e) {
                    error_log("Excepción en insertarHistorialHerrero: " . $e->getMessage());
                    sendResponse('error', 'Excepción al intentar registrar el historial.');
                }
                break;
            
            
    
            case 'insertarHerramientaUsada':
                $params = [
                    'idHistorialHerrero' => $_POST['idHistorialHerrero'],
                    'idHerramienta' => $_POST['idHerramienta']
                ];
                $result = $herrero->insertarHerramientaUsada($params);
                sendResponse($result['status'], $result['message']);
                break;

            case 'insertarEstadoHerramienta':
                $descripcionEstado = $_POST['descripcionEstado'];
                $result = $herrero->insertarEstadoHerramienta($descripcionEstado);
                sendResponse($result['status'], $result['message']);
                break;

            default:
                sendResponse('error', 'Operación no válida para POST.');
        }
    } else {
        throw new Exception('Método no permitido.');
    }
} catch (Exception $e) {
    sendResponse('error', 'Ocurrió un error: ' . $e->getMessage());
}
