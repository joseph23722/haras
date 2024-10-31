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
            case 'registrarHistorialMedico':
                // Llamada al método para registrar el historial médico
                $result = $historialme->registrarHistorial([
                    'idEquino' => $_POST['idEquino'] ?? null,
                    'idMedicamento' => $_POST['idMedicamento'] ?? null,
                    'dosis' => $_POST['dosis'] ?? null,
                    'frecuenciaAdministracion' => $_POST['frecuenciaAdministracion'] ?? null,
                    'viaAdministracion' => $_POST['viaAdministracion'] ?? null,
                    'pesoEquino' => $_POST['pesoEquino'] ?? null,
                    'fechaInicio' => $_POST['fechaInicio'] ?? null,
                    'fechaFin' => $_POST['fechaFin'] ?? null,
                    'observaciones' => $_POST['observaciones'] ?? null,
                    'reaccionesAdversas' => $_POST['reaccionesAdversas'] ?? null
                ]);
            
                // Enviar la respuesta según el resultado del método
                if ($result['status'] === 'success') {
                    echo json_encode([
                        'status' => 'success',
                        'message' => $result['message']
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => $result['message']
                    ]);
                }
                break;

            case 'deleteMedicamento':
                // Verificar que el ID del medicamento esté en los parámetros POST
                if (isset($_POST['idMedicamento'])) {
                    $idMedicamento = $_POST['idMedicamento'];
                    
                    // Llamar al método para eliminar el medicamento
                    $result = $medicamentoModel->deleteMedicamentoDirect($idMedicamento);
                    
                    // Verificar el resultado y enviar una respuesta adecuada
                    if ($result) {
                        echo json_encode([
                            'status' => 'success',
                            'message' => 'Medicamento eliminado correctamente'
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'No se pudo eliminar el medicamento o el ID no existe'
                        ]);
                    }
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'ID de medicamento no proporcionado'
                    ]);
                }
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
                $result = $historialme->consultarHistorialMedico();
                echo json_encode(['data' => $result]);
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

