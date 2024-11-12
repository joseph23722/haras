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
    header('Content-Type: application/json'); // Asegura que la respuesta es JSON
    echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
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
                    sendResponse('success', 'Historial del equino consultado correctamente.', $historial);
                } else {
                    sendResponse('error', 'No se pudo consultar el historial del equino.');
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

            case 'getTipoEquinos':
                $tiposEquinos = $herrero->getTipoEquinos();
                if ($tiposEquinos) {
                    sendResponse('success', 'Tipos de equinos obtenidos correctamente.', $tiposEquinos);
                } else {
                    sendResponse('error', 'No se pudieron obtener los tipos de equinos.');
                }
                break;

            case 'getEquinosByTipo':
                $idTipoEquino = $_GET['idTipoEquino'] ?? 0;
                $equinos = $herrero->obtenerEquinosPorTipo($idTipoEquino);
                if ($equinos) {
                    sendResponse('success', 'Equinos obtenidos correctamente.', $equinos);
                } else {
                    sendResponse('error', 'No se encontraron equinos para este tipo.');
                }
                break;

            default:
                sendResponse('error', 'Operación no válida para GET.');
        }
    } elseif ($method === 'POST') {
        switch ($operation) {
            case 'insertarHistorialHerrero':
                try {
                    $params = json_decode(file_get_contents('php://input'), true);
            
                    // Log de todos los datos recibidos en el controlador
                    error_log("Datos recibidos en el controlador insertarHistorialHerrero: " . json_encode($params));
            
                    // Verificación de los parámetros obligatorios y log de cada uno
                    $requiredFields = ['idEquino', 'idUsuario', 'fecha', 'trabajoRealizado', 'herramientasUsadas', 'observaciones'];
                    foreach ($requiredFields as $field) {
                        if (!isset($params[$field]) || empty($params[$field])) {
                            error_log("Campo faltante o vacío: $field con valor: " . json_encode($params[$field]));
                            sendResponse('error', 'Datos incompletos para registrar el historial.');
                            return;
                        }
                    }
            
                    // Llamar al método en el modelo para insertar el historial
                    $result = $herrero->insertarHistorialHerrero($params);
                    error_log("Resultado de la inserción en modelo: " . json_encode($result));  // Log del resultado del modelo
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
