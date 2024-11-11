<?php
require_once '../models/Herrero.php';

$herrero = new Herrero();
$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json');

// Función para enviar una respuesta JSON al cliente
function sendResponse($status, $message, $data = []) {
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
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
                try {
                    $tiposEquinos = $herrero->getTipoEquinos();
                    if ($tiposEquinos) {
                        sendResponse('success', 'Tipos de equinos obtenidos correctamente.', $tiposEquinos);
                    } else {
                        sendResponse('error', 'No se pudieron obtener los tipos de equinos.');
                    }
                } catch (PDOException $e) {
                    sendResponse('error', 'Error al obtener los tipos de equinos: ' . $e->getMessage());
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
                $params = json_decode(file_get_contents('php://input'), true); // Decodifica JSON
                $params = [
                    'idEquino' => $_POST['idEquino'],
                    'idUsuario' => $_POST['idUsuario'],
                    'fecha' => $_POST['fecha'],
                    'trabajoRealizado' => $_POST['trabajoRealizado'],
                    'herramientasUsadas' => $_POST['herramientasUsadas'],
                    'estadoInicio' => $_POST['estadoInicio'],
                    'estadoFin' => $_POST['estadoFin'],
                    'observaciones' => $_POST['observaciones']
                ];
                $result = $herrero->insertarHistorialHerrero($params);
                sendResponse($result['status'], $result['message']);
                break;

            case 'insertarHerramientaUsada':
                $params = [
                    'idHistorialHerrero' => $_POST['idHistorialHerrero'],
                    'idHerramienta' => $_POST['idHerramienta'],
                    'estadoInicio' => $_POST['estadoInicio'],
                    'estadoFin' => $_POST['estadoFin']
                ];
                $result = $herrero->insertarHerramientaUsada($params);
                sendResponse($result['status'], $result['message']);
                break;

            case 'actualizarEstadoFinalHerramientaUsada':
                $params = [
                    'idHerramientasUsadas' => $_POST['idHerramientasUsadas'],
                    'estadoFin' => $_POST['estadoFin']
                ];
                $result = $herrero->actualizarEstadoFinalHerramientaUsada($params);
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

