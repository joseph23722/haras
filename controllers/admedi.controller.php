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
                $result = $admi->getEquinosPorCategoria();
        
                header('Content-Type: application/json');
                echo json_encode($result);  // Directamente devolver $result
                exit();

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
                header('Content-Type: application/json'); // Asegurar el tipo de contenido JSON
                ob_clean(); // Limpiar cualquier salida previa
            
                try {
                    // Verificar si se proporciona el nombre del medicamento para filtrar
                    $nombreMedicamento = $_GET['nombreMedicamento'] ?? null;
            
                    if (!$nombreMedicamento) {
                        echo json_encode(['status' => 'error', 'message' => 'Debe especificar un medicamento.']);
                        exit; // Detener la ejecución
                    }
            
                    // Llamar al método para listar lotes por medicamento
                    $lotes = $admi->listarLotesMedicamentosPorNombre($nombreMedicamento);
            
                    // Enviar la respuesta según el estado del resultado
                    if ($lotes['status'] === 'success') {
                        echo json_encode([
                            'status' => 'success',
                            'message' => 'Lotes obtenidos correctamente.',
                            'data' => $lotes['data']
                        ]);
                        exit; // Detener la ejecución
                    } else {
                        echo json_encode([
                            'status' => 'error',
                            'message' => $lotes['message']
                        ]);
                        exit; // Detener la ejecución
                    }
                } catch (PDOException $e) {
                    // Capturar errores de la base de datos
                    error_log("Error en la base de datos: " . $e->getMessage());
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error en la base de datos: ' . $e->getMessage()
                    ]);
                    exit; // Detener la ejecución
                } catch (Exception $e) {
                    // Capturar errores generales
                    error_log("Error inesperado: " . $e->getMessage());
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error inesperado: ' . $e->getMessage()
                    ]);
                    exit; // Detener la ejecución
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
                    'filtroFecha' => $_GET['filtroFecha'] ?? 'hoy',  // Filtro dinámico (hoy, ultimaSemana, ultimoMes, todos)
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
                    'idEquino' => intval($_POST['idEquino'] ?? 0),  // Cambiar de idTipoEquino a idEquino
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
                

            case 'agregarCombinacionMedicamento':
                // Obtener los datos de tipo, presentación, unidad y dosis desde la solicitud
                $tipo = $_POST['tipo'] ?? '';
                $presentacion = $_POST['presentacion'] ?? '';
                $unidad = $_POST['unidad'] ?? '';
                $dosis = $_POST['dosis'] ?? 0;
            
                try {
                    // Llamar al método en tu clase de administración para agregar la combinación
                    $mensaje = $admi->agregarCombinacionMedicamento($tipo, $presentacion, $unidad, $dosis);
                    
                    // Enviar la respuesta con el mensaje recibido del procedimiento almacenado
                    sendResponse('success', $mensaje);
                } catch (Exception $e) {
                    // Enviar respuesta de error en caso de excepción
                    sendResponse('error', 'Error al agregar la combinación de medicamento: ' . $e->getMessage());
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
                    // Intentar realizar la actualización con los nuevos valores
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

            case 'listarPresentacionesMedicamentos':
                // Asegurarse de que el parámetro idTipo esté presente
                $idTipo = $_POST['idTipo'] ?? null; // Cambia a $_GET si estás usando GET en vez de POST

                if ($idTipo === null) {
                    sendResponse('error', 'El tipo de medicamento (idTipo) es necesario para obtener las presentaciones.');
                    break;
                }

                try {
                    $presentaciones = $admi->listarPresentacionesPorTipo($idTipo);
                    if ($presentaciones) {
                        sendResponse('success', 'Presentaciones de medicamentos obtenidas correctamente.', $presentaciones);
                    } else {
                        sendResponse('error', 'No se pudieron obtener las presentaciones de medicamentos.');
                    }
                } catch (PDOException $e) {
                    sendResponse('error', 'Error al obtener las presentaciones de medicamentos: ' . $e->getMessage());
                }
                break;

                
      
            case 'validarRegistrarCombinacion':
                try {
                    // Extraer los parámetros recibidos desde la solicitud POST
                    $params = [
                        'tipoMedicamento' => $_POST['tipoMedicamento'] ?? '',
                        'presentacionMedicamento' => $_POST['presentacionMedicamento'] ?? '',
                        'dosisMedicamento' => floatval($_POST['dosisMedicamento'] ?? 0), // Convertir a float para coincidir con el tipo DECIMAL en la base de datos
                        'unidadMedida' => $_POST['unidadMedida'] ?? '' // Capturar la unidad de medida
                    ];
            
                    // Llamar al método en el objeto $admi para validar y registrar la combinación
                    $resultado = $admi->validarRegistrarCombinacion($params);
            
                    // Verificar si la respuesta fue exitosa y enviar la respuesta adecuada
                    if ($resultado['status'] === 'success') {
                        sendResponse('success', $resultado['message'], $resultado['data']);
                    } else {
                        sendResponse('error', $resultado['message']);
                    }
                } catch (PDOException $e) {
                    // Capturar y enviar un mensaje de error específico si ocurre una excepción de base de datos
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
