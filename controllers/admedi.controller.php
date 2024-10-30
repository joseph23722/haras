<?php

// Solo habilitar en desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../models/Admedi.php';

$admi = new Admi();
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

try {
    // Si el método es GET, manejarlo aquí
    if ($method === 'GET') {
        $operation = $_GET['operation'] ?? '';

        switch ($operation) {
            case 'getAllMedicamentos':
                try {
                    $medicamentos = $admi->getAllMedicamentos();
                    if ($medicamentos) {
                        sendResponse('success', 'Medicamentos obtenidos correctamente.', $medicamentos);
                    } else {
                        sendResponse('error', 'No se pudieron obtener los medicamentos.');
                    }
                } catch (PDOException $e) {
                    sendResponse('error', 'Error al obtener los medicamentos: ' . $e->getMessage());
                }
                break;

            case 'getTipoEquinos':
                try {
                    $tiposEquinos = $admi->getTipoEquinos();
                    if ($tiposEquinos) {
                        sendResponse('success', 'Tipos de equinos obtenidos correctamente.', $tiposEquinos);
                    } else {
                        sendResponse('error', 'No se pudieron obtener los tipos de equinos.');
                    }
                } catch (PDOException $e) {
                    sendResponse('error', 'Error al obtener los tipos de equinos: ' . $e->getMessage());
                }
                break;

            case 'listarTiposMedicamentos':
                try {
                    $tipos = $admi->listarTiposMedicamentos();
                    if ($tipos) {
                        sendResponse('success', 'Tipos de medicamentos obtenidos correctamente.', $tipos);
                    } else {
                        sendResponse('error', 'No se pudieron obtener los tipos de medicamentos.');
                    }
                } catch (PDOException $e) {
                    sendResponse('error', 'Error al obtener los tipos de medicamentos: ' . $e->getMessage());
                }
                break;

            case 'listarPresentacionesMedicamentos':
                try {
                    $presentaciones = $admi->listarPresentacionesMedicamentos();
                    if ($presentaciones) {
                        sendResponse('success', 'Presentaciones de medicamentos obtenidas correctamente.', $presentaciones);
                    } else {
                        sendResponse('error', 'No se pudieron obtener las presentaciones de medicamentos.');
                    }
                } catch (PDOException $e) {
                    sendResponse('error', 'Error al obtener las presentaciones de medicamentos: ' . $e->getMessage());
                }
                break;

            case 'listarSugerenciasMedicamentos':
                try {
                    $sugerencias = $admi->listarSugerenciasMedicamentos();
                    if ($sugerencias) {
                        sendResponse('success', 'Sugerencias obtenidas correctamente.', $sugerencias);
                    } else {
                        sendResponse('error', 'No se pudieron obtener las sugerencias de medicamentos.');
                    }
                } catch (PDOException $e) {
                    sendResponse('error', 'Error al obtener las sugerencias: ' . $e->getMessage());
                }
                break;

            case 'listarLotes':
                try {
                    $lotes = $admi->listarLotesMedicamentos();
                    if ($lotes['status'] === 'success') {
                        sendResponse('success', 'Lotes obtenidos correctamente.', $lotes['data']);
                    } else {
                        sendResponse('error', 'No se pudieron obtener los lotes de medicamentos.');
                    }
                } catch (PDOException $e) {
                    sendResponse('error', 'Error al listar los lotes de medicamentos: ' . $e->getMessage());
                }
                break;

            case 'notificarStockBajo':
                try {
                    $medicamentosBajoStock = $admi->notificarStockBajo();
                    if ($medicamentosBajoStock) {
                        sendResponse('success', 'Medicamentos con stock bajo obtenidos correctamente.', $medicamentosBajoStock);
                    } else {
                        sendResponse('error', 'No se pudieron obtener las notificaciones de stock bajo.');
                    }
                } catch (PDOException $e) {
                    sendResponse('error', 'Error al obtener las notificaciones de stock bajo: ' . $e->getMessage());
                }
                break;

            case 'obtenerHistorialMovimientosMedicamentos':
                $params = [
                    'tipoMovimiento' => $_GET['tipoMovimiento'] ?? 'Entrada',
                    'fechaInicio' => $_GET['fechaInicio'] ?? '1900-01-01',
                    'fechaFin' => $_GET['fechaFin'] ?? date('Y-m-d'),
                    'idUsuario' => intval($_GET['idUsuario'] ?? 0),
                    'limit' => intval($_GET['limit'] ?? 10),
                    'offset' => intval($_GET['offset'] ?? 0)
                ];
                try {
                    $result = $admi->obtenerHistorialMovimientosMedicamentos($params);
                    if ($result['status'] === 'success') {
                        sendResponse('success', 'Historial de movimientos obtenido correctamente.', $result['data']);
                    } else {
                        sendResponse('info', $result['message']);
                    }
                } catch (PDOException $e) {
                    sendResponse('error', 'Error al obtener el historial de movimientos: ' . $e->getMessage());
                }
                break;

            default:
                sendResponse('error', 'Operación no válida para GET.');
        }
    }

    // Si el método es POST, manejarlo aquí
    if ($method === 'POST') {
        $operation = $_POST['operation'] ?? '';

        switch ($operation) {
            case 'registrar':
                $params = [
                    'nombreMedicamento' => $_POST['nombreMedicamento'] ?? '',
                    'descripcion' => $_POST['descripcion'] ?? '',
                    'lote' => $_POST['lote'] ?? '',
                    'presentacion' => $_POST['presentacion'] ?? '',
                    'dosis' => $_POST['dosis'] ?? '',
                    'tipo' => $_POST['tipo'] ?? '',
                    'cantidad_stock' => intval($_POST['cantidad_stock'] ?? 0),
                    'stockMinimo' => intval($_POST['stockMinimo'] ?? 0),
                    'fechaCaducidad' => $_POST['fechaCaducidad'] ?? '',
                    'precioUnitario' => floatval($_POST['precioUnitario'] ?? 0)
                ];

                try {
                    $result = $admi->registrarMedicamento($params);
                    if ($result['status'] === 'success') {
                        sendResponse('success', 'Medicamento registrado correctamente.');
                    } else {
                        sendResponse('error', $result['message']);
                    }
                } catch (PDOException $e) {
                    sendResponse('error', 'Error al registrar el medicamento: ' . $e->getMessage());
                }
                break;

            case 'entrada':
                $params = [
                    'nombreMedicamento' => $_POST['nombreMedicamento'] ?? '',
                    'unidadMedida' => $_POST['unidadMedida'] ?? '',
                    'lote' => $_POST['lote'] ?? '',
                    'cantidad' => floatval($_POST['cantidad'] ?? 0)
                ];

                try {
                    $result = $admi->entradaMedicamento($params);
                    if ($result['status'] === 'success') {
                        sendResponse('success', 'Entrada de medicamento registrada correctamente.');
                    } else {
                        sendResponse('error', $result['message']);
                    }
                } catch (PDOException $e) {
                    sendResponse('error', 'Error al registrar la entrada del medicamento: ' . $e->getMessage());
                }
                break;

            case 'salida':
                $params = [
                    'nombreMedicamento' => $_POST['nombreMedicamento'] ?? '',
                    'unidadMedida' => $_POST['unidadMedida'] ?? '',
                    'cantidad' => floatval($_POST['cantidad'] ?? 0),
                    'idTipoEquino' => intval($_POST['idTipoEquino'] ?? 0),
                    'lote' => $_POST['lote'] ?? ''
                ];

                if ($params['cantidad'] <= 0) {
                    sendResponse('error', 'Error: La cantidad debe ser mayor a 0.');
                }

                try {
                    $result = $admi->salidaMedicamento($params);
                    if ($result['status'] === 'success') {
                        sendResponse('success', 'Salida de medicamento registrada correctamente.');
                    } else {
                        sendResponse('error', $result['message']);
                    }
                } catch (PDOException $e) {
                    sendResponse('error', 'Error al registrar la salida del medicamento: ' . $e->getMessage());
                }
                break;

            case 'agregarTipoMedicamento':
                $tipo = $_POST['tipo'] ?? '';
                try {
                    $result = $admi->agregarTipoMedicamento($tipo);
                    if ($result) {
                        sendResponse('success', 'Tipo de medicamento agregado correctamente.');
                    } else {
                        sendResponse('error', 'No se pudo agregar el tipo de medicamento.');
                    }
                } catch (PDOException $e) {
                    sendResponse('error', 'Error al agregar el tipo de medicamento: ' . $e->getMessage());
                }
                break;

            case 'agregarPresentacion':
                $presentacion = $_POST['presentacion'] ?? '';
                try {
                    $result = $admi->agregarPresentacionMedicamento($presentacion);
                    if ($result) {
                        sendResponse('success', 'Presentación de medicamento agregada correctamente.');
                    } else {
                        sendResponse('error', 'No se pudo agregar la presentación de medicamento.');
                    }
                } catch (PDOException $e) {
                    sendResponse('error', 'Error al agregar la presentación del medicamento: ' . $e->getMessage());
                }
                break;

            case 'validarRegistrarCombinacion':
                try {
                    // Extraer los parámetros recibidos desde la solicitud POST
                    $params = [
                        'tipoMedicamento' => $_POST['tipoMedicamento'] ?? '',
                        'presentacionMedicamento' => $_POST['presentacionMedicamento'] ?? '',
                        'dosisMedicamento' => $_POST['dosisMedicamento'] ?? ''
                    ];
            
                    // Llamar al método para validar y registrar la combinación
                    $resultado = $admi->validarRegistrarCombinacion($params);
            
                    // Enviar la respuesta basada en el resultado
                    if ($resultado['status'] === 'success') {
                        sendResponse('success', $resultado['message'], $resultado['data']);
                    } else {
                        sendResponse('error', $resultado['message']);
                    }
                } catch (PDOException $e) {
                    // Manejo de errores en caso de falla en la base de datos
                    sendResponse('error', 'Error en la validación de la combinación: ' . $e->getMessage());
                }
                break;

            case 'deleteMedicamento':
                // Obtener el ID del medicamento desde el POST
                $idMedicamento = intval($_POST['idMedicamento'] ?? 0);
        
                // Llamar a la función para borrar el medicamento
                $result = $admi->borrarMedicamento($idMedicamento);
        
                // Responder al cliente en formato JSON
                header('Content-Type: application/json');
                echo json_encode($result);
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
