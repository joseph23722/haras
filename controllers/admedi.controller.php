<?php

// Solo habilitar en desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../models/Admedi.php';

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
    if (!isset($_POST['operation'])) {
        sendResponse('error', 'Operación no especificada.');
    }

    $admi = new Admi();
    $operation = $_POST['operation'];

    switch ($operation) {

        // Obtener todos los medicamentos
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

            try {
                // Validar combinación antes de registrar
                $validarCombinacion = $admi->validarRegistrarCombinacion([
                    'tipoMedicamento' => $params['tipo'],
                    'presentacionMedicamento' => $params['presentacion'],
                    'dosisMedicamento' => $params['dosis']
                ]);

                if ($validarCombinacion) {
                    $result = $admi->registrarMedicamento($params);

                    if ($result) {
                        // Retornar respuesta en formato JSON
                        echo json_encode(['status' => 'success', 'message' => 'Medicamento registrado correctamente.']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Error: No se pudo registrar el medicamento. Verifique los parámetros.']);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error: La combinación de tipo, presentación y dosis no es válida.']);
                }
            } catch (PDOException $e) {
                echo json_encode(['status' => 'error', 'message' => 'Error al registrar el medicamento: ' . $e->getMessage()]);
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

            try {
                $result = $admi->entradaMedicamento($params);
                if ($result) {
                    sendResponse('success', 'Entrada de medicamento registrada correctamente.');
                } else {
                    sendResponse('error', 'Error: No se pudo registrar la entrada del medicamento. Verifique los datos.');
                }
            } catch (PDOException $e) {
                sendResponse('error', 'Error al registrar la entrada del medicamento: ' . $e->getMessage());
            }
            break;

        // Registrar salida de medicamento
        case 'salida':
            $params = [
                'nombreMedicamento' => $_POST['nombreMedicamento'] ?? '',
                'cantidad' => floatval($_POST['cantidad'] ?? 0)
            ];

            if ($params['cantidad'] <= 0) {
                sendResponse('error', 'Error: La cantidad debe ser mayor a 0.');
            }

            try {
                $result = $admi->salidaMedicamento($params);
                if ($result) {
                    sendResponse('success', 'Salida de medicamento registrada correctamente.');
                } else {
                    sendResponse('error', 'Error: No se pudo registrar la salida del medicamento.');
                }
            } catch (PDOException $e) {
                sendResponse('error', 'Error al registrar la salida del medicamento: ' . $e->getMessage());
            }
            break;

        // Notificar stock bajo
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

        // Validar presentación, tipo y dosis
        case 'validarRegistrarCombinacion':
            $params = [
                'tipoMedicamento' => $_POST['tipoMedicamento'] ?? '',           
                'presentacionMedicamento' => $_POST['presentacionMedicamento'] ?? '', 
                'dosisMedicamento' => $_POST['dosisMedicamento'] ?? ''         
            ];

            try {
                $result = $admi->validarRegistrarCombinacion($params);
                if ($result) {
                    sendResponse('success', 'Validación y registro de combinación exitoso.');
                } else {
                    sendResponse('error', 'Error: La combinación de presentación, tipo o dosis no es válida o ya existe.');
                }
            } catch (PDOException $e) {
                sendResponse('error', 'Error al validar la combinación: ' . $e->getMessage());
            }
            break;

        // Agregar nuevo tipo de medicamento
        case 'agregarTipoMedicamento':
            $tipo = $_POST['tipo'] ?? '';
            try {
                $result = $admi->agregarTipoMedicamento($tipo);
                if ($result) {
                    sendResponse('success', 'Tipo de medicamento agregado correctamente.');
                } else {
                    sendResponse('error', 'Error: No se pudo agregar el tipo de medicamento.');
                }
            } catch (PDOException $e) {
                sendResponse('error', 'Error al agregar el tipo de medicamento: ' . $e->getMessage());
            }
            break;

        // Agregar nueva presentación de medicamento
        case 'agregarPresentacion':
            $presentacion = $_POST['presentacion'] ?? '';
            try {
                $result = $admi->agregarPresentacionMedicamento($presentacion);
                if ($result) {
                    sendResponse('success', 'Presentación de medicamento agregada correctamente.');
                } else {
                    sendResponse('error', 'Error: No se pudo agregar la presentación de medicamento.');
                }
            } catch (PDOException $e) {
                sendResponse('error', 'Error al agregar la presentación del medicamento: ' . $e->getMessage());
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

            try {
                $result = $admi->registrarHistorialMedicamento($params);
                if ($result) {
                    sendResponse('success', 'Historial registrado correctamente.');
                } else {
                    sendResponse('error', 'Error: No se pudo registrar el historial.');
                }
            } catch (PDOException $e) {
                sendResponse('error', 'Error al registrar el historial: ' . $e->getMessage());
            }
            break;

        // Listar tipos de medicamentos
        case 'listarTiposMedicamentos':
            try {
                $tipos = $admi->listarTiposMedicamentos();
                if ($tipos) {
                    sendResponse('success', 'Tipos de medicamentos obtenidos correctamente.', $tipos);
                } else {
                    sendResponse('error', 'Error: No se pudieron obtener los tipos de medicamentos.');
                }
            } catch (PDOException $e) {
                sendResponse('error', 'Error al obtener los tipos de medicamentos: ' . $e->getMessage());
            }
            break;

        // Listar presentaciones de medicamentos
        case 'listarPresentacionesMedicamentos':
            try {
                $presentaciones = $admi->listarPresentacionesMedicamentos();
                if ($presentaciones) {
                    sendResponse('success', 'Presentaciones de medicamentos obtenidas correctamente.', $presentaciones);
                } else {
                    sendResponse('error', 'Error: No se pudieron obtener las presentaciones de medicamentos.');
                }
            } catch (PDOException $e) {
                sendResponse('error', 'Error al obtener las presentaciones de medicamentos: ' . $e->getMessage());
            }
            break;

        // Listar sugerencias de combinaciones de medicamentos (tipo, presentación, dosis)
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

        // Borrar medicamento por ID
        case 'borrarMedicamento':
            $idMedicamento = intval($_POST['idMedicamento'] ?? 0);
            
            if ($idMedicamento > 0) {
                try {
                    $result = $admi->borrarMedicamento($idMedicamento);
                    if ($result) {
                        sendResponse('success', 'Medicamento eliminado correctamente.');
                    } else {
                        sendResponse('error', 'Error: No se pudo eliminar el medicamento.');
                    }
                } catch (PDOException $e) {
                    sendResponse('error', 'Error al eliminar el medicamento: ' . $e->getMessage());
                }
            } else {
                sendResponse('error', 'ID de medicamento inválido.');
            }
            break;


        // Si la operación no es válida
        default:
            sendResponse('error', 'Operación no válida.');
            break;
        
    }

} catch (PDOException $e) {
    sendResponse('error', 'Error en la base de datos: ' . $e->getMessage());
} catch (Exception $e) {
    sendResponse('error', 'Ocurrió un error: ' . $e->getMessage());
}
