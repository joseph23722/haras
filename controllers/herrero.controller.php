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
    header('Content-Type: application/json');
    $response = ['status' => $status, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
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

                if ($historial['status'] === 'success') {
                    echo json_encode([
                        "draw" => intval($_GET['draw'] ?? 1),
                        "recordsTotal" => count($historial['data']),
                        "recordsFiltered" => count($historial['data']),
                        "data" => $historial['data']
                    ]);
                } else {
                    sendResponse('error', $historial['message']);
                }
                break;

            case 'listarEquinosPorTipo':
                // Llamada al método para listar equinos por tipo
                $result = $herrero->listarEquinosPorTipo();
                if (!empty($result)) {
                    sendResponse('success', 'Equinos listados correctamente.', $result);
                } else {
                    sendResponse('info', 'No se encontraron equinos para listar.', []);
                }
                break;

            case 'listarTiposTrabajos':
                $result = $herrero->listarTiposTrabajos();
                sendResponse($result['status'], $result['message'], $result['data']);
                break;

            case 'listarHerramientas':
                $result = $herrero->listarHerramientas();
                sendResponse($result['status'], $result['message'], $result['data']);
                break;

            default:
                sendResponse('error', 'Operación no válida para GET.');
        }
    } elseif ($method === 'POST') {
        switch ($operation) {
            case 'insertarHistorialHerrero':
                $params = json_decode(file_get_contents('php://input'), true);
                if (empty($params)) {
                    sendResponse('error', 'Datos JSON inválidos o faltantes.');
                }

                $result = $herrero->insertarHistorialHerrero($params);
                sendResponse($result['status'], $result['message']);
                break;

            case 'agregarTipoTrabajo':
                $params = json_decode(file_get_contents('php://input'), true);
                if (!isset($params['nombreTrabajo']) || empty($params['nombreTrabajo'])) {
                    sendResponse('error', 'El nombre del trabajo es obligatorio.');
                }

                $descripcion = $params['descripcion'] ?? '';
                $result = $herrero->agregarTipoTrabajo($params['nombreTrabajo'], $descripcion);
                sendResponse($result['status'], $result['message']);
                break;

            case 'agregarHerramienta':
                $params = json_decode(file_get_contents('php://input'), true);
                if (!isset($params['nombreHerramienta']) || empty($params['nombreHerramienta'])) {
                    sendResponse('error', 'El nombre de la herramienta es obligatorio.');
                }

                $descripcion = $params['descripcion'] ?? '';
                $result = $herrero->agregarHerramienta($params['nombreHerramienta'], $descripcion);
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
