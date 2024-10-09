<?php

// Solo habilitar en desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../models/Admedi.php';

header('Content-Type: application/json'); // Forzar respuesta JSON

// Registrar el inicio del script
error_log("Script iniciado: " . date('Y-m-d H:i:s'));

// Función para enviar una respuesta JSON al cliente
function sendResponse($status, $message, $data = []) {
    error_log("Enviando respuesta al cliente. Status: $status, Mensaje: $message, Data: " . json_encode($data));
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

try {
    error_log("Iniciando validación de la operación solicitada.");
    if (!isset($_POST['operation'])) {
        error_log("Error: Operación no especificada.");
        sendResponse('error', 'Operación no especificada.');
    }

    $admi = new Admi();
    $operation = $_POST['operation'];
    error_log("Operación solicitada: " . $operation);

    // Ejecutar la operación basada en el valor de 'operation'
    switch ($operation) {

        // Obtener todos los medicamentos
        case 'getAllMedicamentos':
            error_log("Intentando obtener todos los medicamentos.");
            $medicamentos = $admi->getAllMedicamentos();
            error_log("Resultado de la función getAllMedicamentos: " . json_encode($medicamentos));

            if ($medicamentos) {
                error_log("Medicamentos obtenidos correctamente. Total de medicamentos: " . count($medicamentos));
                sendResponse('success', 'Medicamentos obtenidos correctamente.', $medicamentos);
            } else {
                error_log("Error: No se pudieron obtener los medicamentos.");
                sendResponse('error', 'No se pudieron obtener los medicamentos.');
            }
            break;

        // Registrar nuevo medicamento
        case 'registrar':
            error_log("Iniciando registro de nuevo medicamento.");
            $params = [
                'nombreMedicamento' => $_POST['nombreMedicamento'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'lote' => $_POST['lote'] ?? '',
                'presentacion' => $_POST['presentacion'] ?? '',
                'dosis' => $_POST['dosis'] ?? '',
                'tipo' => $_POST['tipo'] ?? '',
                'cantidad_stock' => intval($_POST['cantidad_stock'] ?? 0),
                'stockMinimo' => intval($_POST['stockMinimo'] ?? 0),
                'fecha_caducidad' => $_POST['fechaCaducidad'] ?? '',
                'precioUnitario' => floatval($_POST['precioUnitario'] ?? 0)
            ];
            error_log("Parámetros recibidos para registrar medicamento: " . json_encode($params));
            $result = $admi->registrarMedicamento($params);
            error_log("Resultado del registro de medicamento: " . json_encode($result));

            if ($result) {
                error_log("Medicamento registrado correctamente.");
                sendResponse('success', 'Medicamento registrado correctamente.');
            } else {
                error_log("Error: No se pudo registrar el medicamento.");
                sendResponse('error', 'No se pudo registrar el medicamento.');
            }
            break;

        // Registrar entrada de medicamento
        case 'entrada':
            error_log("Iniciando registro de entrada de medicamento.");
            
            // Capturar los parámetros del POST y realizar validaciones básicas
            $params = [
                'nombreMedicamento' => $_POST['nombreMedicamento'] ?? '',
                'lote' => $_POST['lote'] ?? '',
                'presentacion' => $_POST['presentacion'] ?? '',  // Capturar la presentación
                'dosis' => $_POST['dosis'] ?? '',                // Capturar la dosis
                'tipo' => $_POST['tipo'] ?? '',                  // Capturar el tipo de medicamento
                'cantidad' => floatval($_POST['cantidad'] ?? 0),
                'stockMinimo' => intval($_POST['stockMinimo'] ?? 0), // Capturar el stock mínimo
                'fechaCaducidad' => $_POST['fechaCaducidad'] ?? '',
                'nuevoPrecio' => floatval($_POST['nuevoPrecio'] ?? 0)
            ];
            
            // Registrar los parámetros en los logs para depuración
            error_log("Parámetros recibidos para registrar entrada de medicamento: " . json_encode($params));

            // Llamar al método para registrar la entrada de medicamentos
            $result = $admi->entradaMedicamento($params);

            // Registrar el resultado en los logs
            error_log("Resultado del registro de entrada de medicamento: " . json_encode($result));

            if ($result) {
                error_log("Entrada de medicamento registrada correctamente.");
                sendResponse('success', 'Entrada de medicamento registrada correctamente.');
            } else {
                error_log("Error: No se pudo registrar la entrada del medicamento.");
                sendResponse('error', 'No se pudo registrar la entrada del medicamento.');
            }
            break;


        // Registrar salida de medicamento
        case 'salida':
            error_log("Iniciando registro de salida de medicamento.");
            $params = [
                'nombreMedicamento' => $_POST['nombreMedicamento'] ?? '',
                'cantidad' => floatval($_POST['cantidad'] ?? 0)
            ];
            error_log("Parámetros recibidos para registrar salida de medicamento: " . json_encode($params));

            // Añadir un log para asegurarse de que la cantidad es correcta
            if ($params['cantidad'] <= 0) {
                error_log("Error: La cantidad no es válida. Valor: " . $params['cantidad']);
                sendResponse('error', 'La cantidad debe ser mayor a 0.');
            }

            $result = $admi->salidaMedicamento($params);
            error_log("Resultado del registro de salida de medicamento: " . json_encode($result));

            if ($result) {
                error_log("Salida de medicamento registrada correctamente.");
                sendResponse('success', 'Salida de medicamento registrada correctamente.');
            } else {
                error_log("Error: No se pudo registrar la salida del medicamento.");
                sendResponse('error', 'No se pudo registrar la salida del medicamento.');
            }
            break;

        // Notificar stock bajo
        case 'notificarStockBajo':
            error_log("Iniciando obtención de notificaciones de stock bajo.");
            $medicamentosBajoStock = $admi->notificarStockBajo();
            error_log("Resultado de notificaciones de stock bajo: " . json_encode($medicamentosBajoStock));

            if ($medicamentosBajoStock) {
                error_log("Notificaciones de stock bajo obtenidas correctamente.");
                sendResponse('success', 'Medicamentos con stock bajo obtenidos correctamente.', $medicamentosBajoStock);
            } else {
                error_log("Error: No se pudieron obtener las notificaciones de stock bajo.");
                sendResponse('error', 'No se pudieron obtener las notificaciones de stock bajo.');
            }
            break;

        // Validar presentación y dosis
        case 'validarPresentacionDosis':
            error_log("Iniciando validación de presentación y dosis.");
            $params = [
                'nombreMedicamento' => $_POST['nombreMedicamento'] ?? '',
                'presentacion' => $_POST['presentacion'] ?? '',
                'dosis' => $_POST['dosis'] ?? '',
                'tipo' => $_POST['tipo'] ?? ''
            ];
            error_log("Parámetros recibidos para validar presentación y dosis: " . json_encode($params));

            $result = $admi->validarPresentacionDosis($params);
            error_log("Resultado de la validación de presentación y dosis: " . json_encode($result));

            if ($result) {
                error_log("Validación exitosa.");
                sendResponse('success', 'Validación exitosa.');
            } else {
                error_log("Error: La presentación o dosis no son válidas.");
                sendResponse('error', 'La presentación o dosis no son válidas.');
            }
            break;

        // Agregar nuevo tipo de medicamento
        case 'agregarTipoMedicamento':
            error_log("Iniciando proceso para agregar nuevo tipo de medicamento.");
            $tipo = $_POST['tipo'] ?? '';
            error_log("Tipo de medicamento a agregar: " . $tipo);

            $result = $admi->agregarTipoMedicamento($tipo);
            error_log("Resultado de agregar nuevo tipo de medicamento: " . json_encode($result));

            if ($result) {
                error_log("Tipo de medicamento agregado correctamente.");
                sendResponse('success', 'Tipo de medicamento agregado correctamente.');
            } else {
                error_log("Error: No se pudo agregar el tipo de medicamento.");
                sendResponse('error', 'No se pudo agregar el tipo de medicamento.');
            }
            break;

        // Registrar historial de movimientos
        case 'registrarHistorial':
            error_log("Iniciando registro de historial de movimientos.");
            $params = [
                'idMedicamento' => intval($_POST['idMedicamento'] ?? 0),
                'accion' => $_POST['accion'] ?? '',
                'tipoMovimiento' => $_POST['tipoMovimiento'] ?? null,
                'cantidad' => intval($_POST['cantidad'] ?? 0)
            ];
            error_log("Parámetros recibidos para registrar historial: " . json_encode($params));

            $result = $admi->registrarHistorialMedicamento($params);
            error_log("Resultado de registrar historial: " . json_encode($result));

            if ($result) {
                error_log("Historial registrado correctamente.");
                sendResponse('success', 'Historial registrado correctamente.');
            } else {
                error_log("Error: No se pudo registrar el historial.");
                sendResponse('error', 'No se pudo registrar el historial.');
            }
            break;

        // **Nueva operación: Listar tipos de medicamentos**
        case 'listarTiposMedicamentos':
            error_log("Iniciando listado de tipos de medicamentos.");
            $tipos = $admi->listarTiposMedicamentos();
            error_log("Resultado del listado de tipos de medicamentos: " . json_encode($tipos));

            if ($tipos) {
                error_log("Tipos de medicamentos obtenidos correctamente.");
                sendResponse('success', 'Tipos de medicamentos obtenidos correctamente.', $tipos);
            } else {
                error_log("Error: No se pudieron obtener los tipos de medicamentos.");
                sendResponse('error', 'No se pudieron obtener los tipos de medicamentos.');
            }
            break;

        // Si la operación no es válida
        default:
            error_log("Error: Operación no válida.");
            sendResponse('error', 'Operación no válida.');
            break;
    }

} catch (Exception $e) {
    // Captura cualquier excepción y envía un error como JSON
    error_log("Excepción atrapada: " . $e->getMessage());
    sendResponse('error', 'Ocurrió un error: ' . $e->getMessage());
}
