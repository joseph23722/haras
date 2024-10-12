<?php

// Solo habilitar en desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../models/Admedi.php';

header('Content-Type: application/json'); // Forzar respuesta JSON

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
    if (!isset($_POST['operation'])) {
        sendResponse('error', 'Operación no especificada.');
    }

    $admi = new Admi();
    $operation = $_POST['operation'];

    // Ejecutar la operación basada en el valor de 'operation'
    switch ($operation) {

        // Obtener todos los medicamentos
        case 'getAllMedicamentos':
            $medicamentos = $admi->getAllMedicamentos();
            if ($medicamentos) {
                sendResponse('success', 'Medicamentos obtenidos correctamente.', $medicamentos);
            } else {
                sendResponse('error', 'No se pudieron obtener los medicamentos.');
            }
            break;

        // Registrar nuevo medicamento
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
                'fecha_caducidad' => $_POST['fechaCaducidad'] ?? '',
                'precioUnitario' => floatval($_POST['precioUnitario'] ?? 0)
            ];
            $result = $admi->registrarMedicamento($params);
            if ($result) {
                sendResponse('success', 'Medicamento registrado correctamente.');
            } else {
                sendResponse('error', 'No se pudo registrar el medicamento.');
            }
            break;

        // Registrar entrada de medicamento
        case 'entrada':
            $params = [
                'nombreMedicamento' => $_POST['nombreMedicamento'] ?? '',
                'lote' => $_POST['lote'] ?? '',
                'presentacion' => $_POST['presentacion'] ?? '',
                'dosis' => $_POST['dosis'] ?? '',
                'tipo' => $_POST['tipo'] ?? '',
                'cantidad' => floatval($_POST['cantidad'] ?? 0),
                'stockMinimo' => intval($_POST['stockMinimo'] ?? 0),
                'fechaCaducidad' => $_POST['fechaCaducidad'] ?? '',
                'nuevoPrecio' => floatval($_POST['nuevoPrecio'] ?? 0)
            ];
            $result = $admi->entradaMedicamento($params);
            if ($result) {
                sendResponse('success', 'Entrada de medicamento registrada correctamente.');
            } else {
                sendResponse('error', 'No se pudo registrar la entrada del medicamento.');
            }
            break;

        // Registrar salida de medicamento
        case 'salida':
            $params = [
                'nombreMedicamento' => $_POST['nombreMedicamento'] ?? '',
                'cantidad' => floatval($_POST['cantidad'] ?? 0)
            ];
            if ($params['cantidad'] <= 0) {
                sendResponse('error', 'La cantidad debe ser mayor a 0.');
            }
            $result = $admi->salidaMedicamento($params);
            if ($result) {
                sendResponse('success', 'Salida de medicamento registrada correctamente.');
            } else {
                sendResponse('error', 'No se pudo registrar la salida del medicamento.');
            }
            break;

        // Notificar stock bajo
        case 'notificarStockBajo':
            $medicamentosBajoStock = $admi->notificarStockBajo();
            if ($medicamentosBajoStock) {
                sendResponse('success', 'Medicamentos con stock bajo obtenidos correctamente.', $medicamentosBajoStock);
            } else {
                sendResponse('error', 'No se pudieron obtener las notificaciones de stock bajo.');
            }
            break;

        // Validar presentación y dosis
        case 'validarPresentacionDosis':
            $params = [
                'nombreMedicamento' => $_POST['nombreMedicamento'] ?? '',
                'presentacion' => $_POST['presentacion'] ?? '',
                'dosis' => $_POST['dosis'] ?? '',
                'tipo' => $_POST['tipo'] ?? ''
            ];
            $result = $admi->validarPresentacionDosis($params);
            if ($result) {
                sendResponse('success', 'Validación exitosa.');
            } else {
                sendResponse('error', 'La presentación o dosis no son válidas.');
            }
            break;

        // Agregar nuevo tipo de medicamento
        case 'agregarTipoMedicamento':
            $tipo = $_POST['tipo'] ?? '';
            $result = $admi->agregarTipoMedicamento($tipo);
            if ($result) {
                sendResponse('success', 'Tipo de medicamento agregado correctamente.');
            } else {
                sendResponse('error', 'No se pudo agregar el tipo de medicamento.');
            }
            break;

        // Agregar nueva presentación de medicamento
        case 'agregarPresentacion':
            $presentacion = $_POST['presentacion'] ?? '';
            $result = $admi->agregarPresentacionMedicamento($presentacion);
            if ($result) {
                sendResponse('success', 'Presentación de medicamento agregada correctamente.');
            } else {
                sendResponse('error', 'No se pudo agregar la presentación de medicamento.');
            }
            break;

        // Registrar historial de movimientos
        case 'registrarHistorial':
            $params = [
                'idMedicamento' => intval($_POST['idMedicamento'] ?? 0),
                'accion' => $_POST['accion'] ?? '',
                'tipoMovimiento' => $_POST['tipoMovimiento'] ?? null,
                'cantidad' => intval($_POST['cantidad'] ?? 0)
            ];
            $result = $admi->registrarHistorialMedicamento($params);
            if ($result) {
                sendResponse('success', 'Historial registrado correctamente.');
            } else {
                sendResponse('error', 'No se pudo registrar el historial.');
            }
            break;

        // Listar tipos de medicamentos
        case 'listarTiposMedicamentos':
            $tipos = $admi->listarTiposMedicamentos();
            if ($tipos) {
                sendResponse('success', 'Tipos de medicamentos obtenidos correctamente.', $tipos);
            } else {
                sendResponse('error', 'No se pudieron obtener los tipos de medicamentos.');
            }
            break;

        // Listar presentaciones de medicamentos
        case 'listarPresentacionesMedicamentos':
            $presentaciones = $admi->listarPresentacionesMedicamentos();
            if ($presentaciones) {
                sendResponse('success', 'Presentaciones de medicamentos obtenidas correctamente.', $presentaciones);
            } else {
                sendResponse('error', 'No se pudieron obtener las presentaciones de medicamentos.');
            }
            break;

        // Si la operación no es válida
        default:
            sendResponse('error', 'Operación no válida.');
            break;
    }

} catch (PDOException $e) {
    // Capturar y devolver los errores de MySQL
    sendResponse('error', 'Error en la base de datos: ' . $e->getMessage());
} catch (Exception $e) {
    // Captura cualquier otra excepción y envía un error como JSON
    sendResponse('error', 'Ocurrió un error: ' . $e->getMessage());
}
