<?php
/** propio */
require_once '../models/Propio.php';

$servicioPropio = new ServicioPropio();

header('Content-Type: application/json');

// Manejo de solicitudes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validación básica para solicitudes POST
    if (empty($data['action'])) {
        echo json_encode(["status" => "error", "message" => "Acción no especificada."]);
        exit;
    }

    $action = $data['action'];

    switch ($action) {
        case 'registrarServicioPropio':
            try {
                // Validar parámetros básicos
                if (empty($data['idEquinoMacho']) && empty($data['idEquinoHembra'])) {
                    echo json_encode(["status" => "error", "message" => "Debe seleccionar un padrillo o una yegua."]);
                    exit;
                }
        
                if (!empty($data['idMedicamento'])) {
                    if (empty($data['unidad']) || empty($data['cantidadAplicada'])) {
                        echo json_encode(["status" => "error", "message" => "Debe especificar unidad y cantidad aplicada si selecciona un medicamento."]);
                        exit;
                    }
        
                    if (empty($data['usoMedicamento'])) {
                        echo json_encode(["status" => "error", "message" => "Debe indicar si el medicamento es para el padrillo o la yegua."]);
                        exit;
                    }
        
                    $idEquino = $data['usoMedicamento'] === 'padrillo' ? $data['idEquinoMacho'] : $data['idEquinoHembra'];
        
                    // Registrar dosis aplicada
                    $resultadoDosis = $servicioPropio->registrarDosisAplicada(
                        $data['idMedicamento'],
                        $idEquino,
                        $data['cantidadAplicada'],
                        $data['unidad']
                    );
        
                    if (!$resultadoDosis) {
                        echo json_encode(["status" => "error", "message" => "Error al registrar dosis para el " . $data['usoMedicamento'] . "."]);
                        exit;
                    }
                }
        
                // Registrar servicio llamando al modelo
                $resultadoServicio = $servicioPropio->registrarServicioPropio($data);
                if ($resultadoServicio['status'] === "error") {
                    echo json_encode(["status" => "error", "message" => $resultadoServicio['message']]);
                    exit;
                }

        
                // Si todo fue exitoso
                echo json_encode(["status" => "success", "message" => "Servicio propio registrado exitosamente."]);
            } catch (PDOException $e) {
                error_log("Error al procesar registro: " . $e->getMessage());
        
                // Extraer mensajes específicos de SIGNAL en el procedimiento almacenado
                if (preg_match('/SQLSTATE\[45000\]:.+?: (.+)/', $e->getMessage(), $matches)) {
                    echo json_encode(["status" => "error", "message" => trim($matches[1])]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Ocurrió un error al registrar el servicio."]);
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
                    empty($data['unidadAplicada']) // Validar también la unidad
                ) {
                    echo json_encode(["status" => "error", "message" => "Faltan parámetros necesarios."]);
                    exit;
                }
        
                // Llamar al método registrarDosisAplicada con los nuevos parámetros
                $result = $servicioPropio->registrarDosisAplicada(
                    $data['idMedicamento'], 
                    $data['idEquino'], 
                    $data['cantidadAplicada'], 
                    $data['unidadAplicada'] // Pasar la unidad aplicada
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
            echo json_encode(["status" => "error", "message" => "Acción POST no válida."]);
            break;
    }

    exit;
}

// Manejo de solicitudes GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['action'])) {
        echo json_encode(["status" => "error", "message" => "Acción no especificada."]);
        exit;
    }

    $action = $_GET['action'];

    switch ($action) {
        case 'listarEquinosPropios':
            if (isset($_GET['tipoEquino'])) {
                $equinos = $servicioPropio->listarEquinosPropios($_GET['tipoEquino']);
                echo json_encode($equinos);
            } else {
                echo json_encode(["status" => "error", "message" => "Parámetro tipoEquino no especificado."]);
            }
            break;

        case 'listarMedicamentos':
            $medicamentos = $servicioPropio->listarMedicamentos();
            echo json_encode($medicamentos);
            break;

        case 'listarUnidadesPorMedicamento':
            if (isset($_GET['idMedicamento'])) {
                $idMedicamento = intval($_GET['idMedicamento']);
                try {
                    $unidades = $servicioPropio->listarUnidadesPorMedicamento($idMedicamento);
                    echo json_encode($unidades);
                } catch (Exception $e) {
                    error_log("Error al listar unidades: " . $e->getMessage());
                    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "ID de medicamento no proporcionado."]);
            }
            break;
            

        case 'listarServiciosPorTipo':
            if (isset($_GET['tipoServicio'])) {
                $servicios = $servicioPropio->listarServiciosPorTipo($_GET['tipoServicio']);
                echo json_encode($servicios);
            } else {
                echo json_encode(["status" => "error", "message" => "Parámetro tipoServicio no especificado."]);
            }
            break;

        case 'obtenerHistorialDosisAplicadas':
            try {
                $historial = $servicioPropio->obtenerHistorialDosisAplicadas();

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
            echo json_encode(["status" => "error", "message" => "Acción GET no válida."]);
            break;
    }

    exit;
}

// Respuesta para métodos no permitidos
echo json_encode(["status" => "error", "message" => "Método de solicitud no permitido."]);
