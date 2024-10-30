<?php
require_once '../models/Historialme.php';

// Crear una instancia de la clase Historialme
$historialme = new Historialme();

try {
    // Verificar el método de solicitud
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener los parámetros de la solicitud POST
        $params = $_POST;
        $operation = $params['operation'] ?? '';

        switch ($operation) {
            case 'registrarHistorial':
                // Llamada al método para registrar el historial médico
                $result = $historialme->registrarHistorial([
                    'idEquino' => $params['idEquino'] ?? null,
                    'idMedicamento' => $params['idMedicamento'] ?? null,
                    'dosis' => $params['dosis'] ?? null,
                    'cantidad' => $params['cantidad'] ?? null,
                    'frecuenciaAdministracion' => $params['frecuenciaAdministracion'] ?? null,
                    'viaAdministracion' => $params['viaAdministracion'] ?? null,
                    'pesoEquino' => $params['pesoEquino'] ?? null,
                    'fechaInicio' => $params['fechaInicio'] ?? null,
                    'fechaFin' => $params['fechaFin'] ?? null,
                    'observaciones' => $params['observaciones'] ?? null,
                    'reaccionesAdversas' => $params['reaccionesAdversas'] ?? null
                ]);
                
                // Responder con el resultado de la operación
                echo json_encode(['success' => $result]);
                break;

            default:
                echo json_encode(['error' => 'Operación no válida']);
        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Verificar qué tipo de consulta se está realizando
        $operation = $_GET['operation'] ?? '';

        switch ($operation) {
            case 'listarEquinosPorTipo':
                // Llamada al método para listar equinos sin propietario para medicamentos
                $result = $historialme->listarEquinosPorTipo();
                echo json_encode(['data' => $result]);
                break;

            case 'consultarHistorialMedico':
                // Llamada al método para consultar el historial médico de un equino
                $idEquino = $_GET['idEquino'] ?? null;
                if ($idEquino) {
                    $result = $historialme->consultarHistorialMedico($idEquino);
                    echo json_encode(['data' => $result]);
                } else {
                    echo json_encode(['error' => 'ID del equino es requerido']);
                }
                break;

            case 'getAllMedicamentos':
                // Llamada al método para obtener todos los medicamentos
                $result = $historialme->getAllMedicamentos();
                echo json_encode(['data' => $result]);
                break;

            default:
                echo json_encode(['error' => 'Operación no válida']);
        }

    } else {
        echo json_encode(['error' => 'Método de solicitud no permitido']);
    }
} catch (Exception $e) {
    // Capturar y registrar el error
    error_log("Error en el controlador: " . $e->getMessage());
    echo json_encode(['error' => 'Se produjo un error en el servidor']);
}

