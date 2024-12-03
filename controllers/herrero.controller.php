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
                // No obtener ni pasar 'idEquino', ya que el procedimiento no acepta parámetros.
                // Llamamos al método que consulta el historial sin ningún parámetro
                $historial = $herrero->consultarHistorialEquino();
            
                // Verificar el estado de la respuesta
                if ($historial['status'] === 'success') {
                    echo json_encode([
                        "draw" => intval($_GET['draw'] ?? 1), // Si se está usando DataTables, para la paginación
                        "recordsTotal" => count($historial['data']), // Total de registros encontrados
                        "recordsFiltered" => count($historial['data']), // Registros filtrados (en este caso es lo mismo que total)
                        "data" => $historial['data'] // Los datos del historial
                    ]);
                } else {
                    // Si hay error en la consulta, devolver un error
                    sendResponse('error', $historial['message']);
                }
                break;
            
            case 'listarTiposYHerramientas':
                try {
                    $tiposYHerramientas = $herrero->listarTiposYHerramientas(); // Llamada al método de la clase `herrero`
                    if ($tiposYHerramientas) {
                        // Log para verificar los datos enviados al cliente
                        error_log("Datos enviados al cliente: " . json_encode($tiposYHerramientas));
                        sendResponse('success', 'Datos listados correctamente (Tipos y Herramientas).', $tiposYHerramientas);
                    } else {
                        sendResponse('error', 'No se pudieron obtener los datos.');
                    }
                } catch (PDOException $e) {
                    sendResponse('error', 'Error al obtener los datos: ' . $e->getMessage());
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
                $status = $result['status'] ?? 'error';
                $message = $result['message'] ?? 'Operación fallida.';
                $data = $result['data'] ?? [];
                sendResponse($status, $message, $data);
                break;
            
            case 'listarHerramientas':
                $result = $herrero->listarHerramientas();
                $status = $result['status'] ?? 'error';
                $message = $result['message'] ?? 'Operación fallida.';
                $data = $result['data'] ?? [];
                sendResponse($status, $message, $data);
                break;
        
            default:
                sendResponse('error', 'Operación no válida para GET.');
        }
    } elseif ($method === 'POST') {
        switch ($operation) {

            case 'editarTipoOHerramienta':
                error_log("Operación: editarTipoOHerramienta iniciada.");
                
                $data = json_decode(file_get_contents('php://input'), true);
            
                // Validar datos recibidos
                $id = $data['id'] ?? null;
                $nombre = $data['nombre'] ?? '';
                $tipo = $data['tipo'] ?? '';
            
                if (!$id || !$nombre || !$tipo) {
                    error_log("Datos faltantes: id=$id, nombre=$nombre, tipo=$tipo");
                    sendResponse('error', 'Todos los campos son requeridos para la actualización.');
                    exit;
                }
            
                try {
                    // Llamar al modelo para realizar la actualización
                    $result = $herrero->editarTipoOHerramienta($id, $nombre, $tipo);
            
                    if ($result) {
                        error_log("Registro actualizado correctamente.");
                        sendResponse('success', 'El registro fue actualizado correctamente.');
                    } else {
                        error_log("No se pudo actualizar el registro: id=$id, nombre=$nombre, tipo=$tipo.");
                        sendResponse('error', 'No se pudo actualizar el registro. Verifica los datos.');
                    }
                } catch (Exception $e) {
                    error_log("Error al procesar la solicitud: " . $e->getMessage());
                    sendResponse('error', 'Error al procesar la solicitud: ' . $e->getMessage());
                }
                break;
            
        
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
                if (!isset($params['nombre']) || empty($params['nombre'])) {
                    sendResponse('error', 'El nombre del trabajo es obligatorio.');
                }
                $result = $herrero->agregarTipoTrabajo($params['nombre']);
                sendResponse($result['status'], $result['message']);
                break;
        
            case 'agregarHerramienta':
                $params = json_decode(file_get_contents('php://input'), true);
                if (!isset($params['nombre']) || empty($params['nombre'])) {
                    sendResponse('error', 'El nombre de la herramienta es obligatorio.');
                }
                $result = $herrero->agregarHerramienta($params['nombre']);
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
