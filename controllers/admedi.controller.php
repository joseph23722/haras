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
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Detectar `operation` según el tipo de contenido
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

try {
    // Si el método es GET, manejarlo aquí
    if ($method === 'GET') {
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
                    // Llamar al método para obtener las notificaciones de stock bajo/agotado
                    $medicamentosBajoStock = $admi->notificarStockBajo();
                    
                    if ($medicamentosBajoStock['status'] === 'success') {
                        sendResponse('success', 'Medicamentos con stock bajo obtenidos correctamente.', $medicamentosBajoStock['data']);
                    } else {
                        sendResponse('error', $medicamentosBajoStock['message']);
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
        switch ($operation) {
            case 'registrar':
                $dosisCompleta = trim($_POST['dosis'] . ' ' . ($_POST['unidad'] ?? ''));
                error_log("Dosis completa construida: " . $dosisCompleta);
            
                $params = [
                    'nombreMedicamento' => $_POST['nombreMedicamento'] ?? '',
                    'descripcion' => $_POST['descripcion'] ?? '',
                    'lote' => $_POST['lote'] ?? '',
                    'presentacion' => $_POST['presentacion'] ?? '',
                    'dosisCompleta' => $dosisCompleta,
                    'tipo' => $_POST['tipo'] ?? '',
                    'cantidad_stock' => intval($_POST['cantidad_stock'] ?? 0),
                    'stockMinimo' => intval($_POST['stockMinimo'] ?? 0),
                    'fechaCaducidad' => $_POST['fechaCaducidad'] ?? '',
                    'precioUnitario' => floatval($_POST['precioUnitario'] ?? 0),
                ];
            
                foreach ($params as $key => $value) {
                    error_log("$key => $value");
                }
            
                if (empty($params['dosisCompleta'])) {
                    error_log("Error: La dosis completa está vacía.");
                    sendResponse('error', 'Error: La dosis completa (cantidad y unidad) es obligatoria.');
                    break;
                }
            
                if ($params['cantidad_stock'] < $params['stockMinimo']) {
                    error_log("Error: La cantidad en stock es menor que el stock mínimo.");
                    sendResponse('error', 'Error: La cantidad en stock no puede ser menor que el stock mínimo.');
                    break;
                }
            
                try {
                    $result = $admi->registrarMedicamento($params);
                    error_log("Respuesta de registrarMedicamento:");
                    error_log(print_r($result, true));
            
                    if (isset($result['status']) && $result['status'] === 'success') {
                        sendResponse('success', $result['message']);
                    } else {
                        $message = $result['message'] ?? 'Error desconocido en el registro de medicamentos.';
                        error_log("Error en el registro del medicamento: " . $message);
                        sendResponse('error', $message);
                    }
                } catch (PDOException $e) {
                    error_log("Excepción PDO al registrar el medicamento: " . $e->getMessage());
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
                // Validación: cantidad debe ser mayor que 0
                if ($params['cantidad'] <= 0) {
                    sendResponse('error', 'Error: La cantidad de entrada debe ser mayor a 0.');
                    break;  // Salir del caso si la cantidad no es válida
                }

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
                    'lote' => $_POST['lote'] ?? null,  // Cambiar a NULL si no se proporciona
                    'motivo' => $_POST['motivo'] ?? ''  // Nuevo campo motivo
                ];
            
                // Validación de cantidad y motivo
                if ($params['cantidad'] <= 0) {
                    sendResponse('error', 'Error: La cantidad debe ser mayor a 0.');
                }
                if (empty($params['motivo'])) {
                    sendResponse('error', 'Error: Debe especificar un motivo para la salida del medicamento.');
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

            case 'agregarUnidadMedida':
                $unidad = $_POST['unidad'] ?? '';  // Obtener el valor de la unidad de medida desde el formulario
            
                try {
                    $result = $admi->agregarUnidadMedida($unidad);  // Llama al método en tu clase de administración
            
                    if ($result) {
                        sendResponse('success', 'Unidad de medida agregada correctamente.');
                    } else {
                        sendResponse('error', 'No se pudo agregar la unidad de medida.');
                    }
                } catch (PDOException $e) {
                    sendResponse('error', 'Error al agregar la unidad de medida: ' . $e->getMessage());
                }
                break;

            case 'editarSugerenciaMedicamento':
                $data = json_decode(file_get_contents('php://input'), true);
            
                $idCombinacion = $data['idCombinacion'] ?? null;
                $nuevoTipo = $data['tipo'] ?? '';
                $nuevaPresentacion = $data['presentacion'] ?? '';
                $nuevaUnidad = $data['unidad'] ?? ''; // Asegúrate de que 'unidad' tiene el valor de dosis
            
                // Validar si el idCombinacion está presente
                if (!$idCombinacion) {
                    sendResponse('error', 'El ID de la combinación es requerido para actualizar.');
                    exit;
                }
            
                try {
                    // Intentar realizar la actualización sin validar la existencia previa de la unidad
                    $result = $admi->editarCombinacionCompleta($idCombinacion, $nuevoTipo, $nuevaPresentacion, $nuevaUnidad);
                    if ($result) {
                        sendResponse('success', 'Combinación de medicamento actualizada correctamente.');
                    } else {
                        sendResponse('error', 'No se pudo actualizar la combinación de medicamento. Verifica los datos.');
                    }
                } catch (Exception $e) {
                    sendResponse('error', 'Error al actualizar la combinación de medicamento: ' . $e->getMessage());
                }
                break;
                
                
                
                
                
                
                
                
                

            case 'validarRegistrarCombinacion':
                try {
                    // Extraer los parámetros recibidos desde la solicitud POST
                    $params = [
                        'tipoMedicamento' => $_POST['tipoMedicamento'] ?? '',
                        'presentacionMedicamento' => $_POST['presentacionMedicamento'] ?? '',
                        'dosisMedicamento' => floatval($_POST['dosisMedicamento'] ?? 0), // Cambiado a float para coincidir con DECIMAL
                         'unidadMedida' => $_POST['unidadMedida'] ?? '' // Nueva entrada para la unidad de medida
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
