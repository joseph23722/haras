<?php
require_once '../models/Mixto.php';

$servicioMixto = new ServicioMixto();

header('Content-Type: application/json');

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    error_log(print_r($data, true));

    $action = $data['action'] ?? null;

    switch ($action) {
        case 'registrarServicioMixto':
            try {
                // Validaciones iniciales
                if (empty($data['idEquinoMacho']) && empty($data['idEquinoHembra'])) {
                    echo json_encode(["status" => "error", "message" => "Debe seleccionar al menos un equino propio."]);
                    exit;
                }
        
                if (empty($data['idPropietario']) || empty($data['idEquinoExterno']) || empty($data['fechaServicio']) || empty($data['horaEntrada']) || empty($data['horaSalida'])) {
                    echo json_encode(["status" => "error", "message" => "Faltan parámetros necesarios."]);
                    exit;
                }
        
                // Manejar el registro de la dosis si hay un medicamento
                if (!empty($data['idMedicamento'])) {
                    if (empty($data['unidad']) || empty($data['cantidadAplicada'])) {
                        echo json_encode(["status" => "error", "message" => "Debe especificar unidad y cantidad aplicada si selecciona un medicamento."]);
                        exit;
                    }
        
                    if (empty($data['usoMedicamento'])) {
                        echo json_encode(["status" => "error", "message" => "Debe seleccionar si el medicamento es para el Padrillo, Yegua o Equino Externo."]);
                        exit;
                    }
        
                    // Determinar el ID del equino al que se aplica el medicamento
                    $idEquino = null;
                    if ($data['usoMedicamento'] === 'padrillo') {
                        $idEquino = $data['idEquinoMacho'] ?? null;
                    } elseif ($data['usoMedicamento'] === 'yegua') {
                        $idEquino = $data['idEquinoHembra'] ?? null;
                    } elseif ($data['usoMedicamento'] === 'externo') {
                        $idEquino = $data['idEquinoExterno'] ?? null;
                    }
        
                    if (!$idEquino) {
                        echo json_encode(["status" => "error", "message" => "No se pudo determinar el equino al que se aplica el medicamento."]);
                        exit;
                    }
        
                    // Registrar la dosis
                    $resultadoDosis = $servicioMixto->registrarDosisAplicada(
                        $data['idMedicamento'],
                        $idEquino,
                        $data['cantidadAplicada'],
                        $data['unidad'],
                        $data['fechaAplicacion'],
                    );
        
                    if (!$resultadoDosis) {
                        error_log("Error al registrar la dosis aplicada para el equino ID: " . $idEquino);
                        echo json_encode(["status" => "error", "message" => "Error al registrar la dosis para el equino seleccionado."]);
                        exit;
                    }
                }
        
                // Registrar el servicio mixto
                $params = [
                    'idEquinoMacho' => $data['idEquinoMacho'] ?? null,
                    'idEquinoHembra' => $data['idEquinoHembra'] ?? null,
                    'idPropietario' => $data['idPropietario'],
                    'idEquinoExterno' => $data['idEquinoExterno'],
                    'fechaServicio' => $data['fechaServicio'],
                    'horaEntrada' => $data['horaEntrada'],
                    'horaSalida' => $data['horaSalida'],
                    'idMedicamento' => $data['idMedicamento'] ?? null,
                    'detalles' => $data['detalles'] ?? null,
                    'costoServicio' => $data['costoServicio'] ?? null,
                ];
        
                error_log("Intentando registrar servicio mixto con parámetros: " . print_r($params, true));
                $resultadoServicio = $servicioMixto->registrarServicioMixto($params);
        
                // Verificar si el modelo devuelve un error del procedimiento almacenado
                if ($resultadoServicio['status'] === 'error') {
                    echo json_encode(["status" => "error", "message" => $resultadoServicio['message']]);
                    exit;
                }
        
                // Si todo fue exitoso
                echo json_encode(["status" => "success", "message" => "Servicio mixto registrado exitosamente."]);
            } catch (PDOException $e) {
                error_log("Excepción al registrar el servicio mixto: " . $e->getMessage());
        
                // Extraer mensajes específicos de SIGNAL en el procedimiento almacenado
                if (preg_match('/SQLSTATE\[45000\]:.+?: (.+)/', $e->getMessage(), $matches)) {
                    echo json_encode(["status" => "error", "message" => trim($matches[1])]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Ocurrió un error interno. Intente nuevamente."]);
                }
            } catch (Exception $e) {
                error_log("Error inesperado: " . $e->getMessage());
                echo json_encode(["status" => "error", "message" => "Error inesperado. Intente nuevamente."]);
            }
            break;        

        case 'registrarDosisAplicada':
            try {
                // Validar que se reciban todos los parámetros necesarios
                if (
                    empty($data['idMedicamento']) || 
                    empty($data['idEquino']) || 
                    empty($data['cantidadAplicada']) || 
                    empty($data['unidadAplicada']) ||
                    empty($data['fechaAplicacion'])
                ) {
                    echo json_encode(["status" => "error", "message" => "Faltan parámetros necesarios."]);
                    exit;
                }
        
                // Llamar al método registrarDosisAplicada con los nuevos parámetros
                $result = $servicioMixto->registrarDosisAplicada(
                    $data['idMedicamento'], 
                    $data['idEquino'], 
                    $data['cantidadAplicada'], 
                    $data['unidadAplicada'],
                    $data['fechaAplicacion']
                );
        
                if ($result) {
                    echo json_encode(["status" => "success", "message" => "Dosis aplicada registrada correctamente."]);
                } else {
                    echo json_encode(["status" => "error", "message" => "No se pudo registrar la dosis aplicada."]);
                }
            } catch (Exception $e) {
                // Manejar errores y registrar en el log
                error_log("Error al registrar dosis aplicada: " . $e->getMessage());
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
            break;

        default:
            echo json_encode(["status" => "error", "message" => "Acción no válida."]);
            break;
    }
} elseif ($requestMethod === 'GET') {
    $action = $_GET['action'] ?? null;

    switch ($action) {
        case 'listarPropietarios':
            $propietarios = $servicioMixto->listarPropietarios();
            echo json_encode($propietarios);
            break;

        case 'listarEquinosPropios':
            if (!isset($_GET['tipoEquino'])) {
                echo json_encode(["status" => "error", "message" => "Faltan parámetros necesarios."]);
                exit;
            }

            $equinos = $servicioMixto->listarEquinosPropios($_GET['tipoEquino']);
            echo json_encode($equinos);
            break;

        case 'listarMedicamentos':
            $medicamentos = $servicioMixto->listarMedicamentos();
            echo json_encode($medicamentos);
            break;

        case 'listarEquinosExternosPorPropietarioYGenero':
            if (!isset($_GET['idPropietario']) || !isset($_GET['genero'])) {
                echo json_encode(["status" => "error", "message" => "Faltan parámetros necesarios."]);
                exit;
            }
        
            $idPropietario = $_GET['idPropietario'];
            $genero = $_GET['genero']; // 1 para hembra, 2 para macho
            $equinos = $servicioMixto->listarEquinosExternosPorPropietarioYGenero($idPropietario, $genero);
        
            // Convertir el resultado a un array numérico si es necesario
            echo json_encode(array_values($equinos));
            break;
            

        case 'listarUnidadesPorMedicamento':
            if (isset($_GET['idMedicamento'])) {
                $idMedicamento = intval($_GET['idMedicamento']);
                try {
                    $unidades = $servicioMixto->listarUnidadesPorMedicamento($idMedicamento);
                    echo json_encode($unidades);
                } catch (Exception $e) {
                    error_log("Error al listar unidades: " . $e->getMessage());
                    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "ID de medicamento no proporcionado."]);
            }
            break;

        case 'obtenerHistorialDosisAplicadas':
            try {
                $historial = $servicioMixto->obtenerHistorialDosisAplicadas();

                if (!empty($historial)) {
                    echo json_encode(["status" => "success", "data" => $historial]);
                } else {
                    echo json_encode(["status" => "error", "message" => "No se encontraron registros en el historial."]);
                }
            } catch (Exception $e) {
                error_log("Error al obtener historial de dosis aplicadas: " . $e->getMessage());
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
            break;

        default:
            echo json_encode(["status" => "error", "message" => "Acción no válida."]);
            break;
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método de solicitud no permitido."]);
}
