<?php
require_once '../models/Historialme.php';

// Crear una instancia de la clase Historialme
$historialme = new Historialme();

try {
    // Verificar si el contenido es JSON
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $contentType === "application/json") {
        // Leer JSON del cuerpo de la solicitud
        $inputData = json_decode(file_get_contents('php://input'), true);
        error_log("Datos JSON recibidos: " . print_r($inputData, true));

        // Asegurarse de que se capturó correctamente el JSON
        if ($inputData === null) {
            error_log("Error: JSON inválido recibido.");
            echo json_encode(['status' => 'error', 'message' => 'JSON inválido']);
            exit;
        }

        // Obtener la operación desde el JSON
        $operation = $inputData['operation'] ?? '';
        error_log("Operación recibida (JSON): " . $operation);

        switch ($operation) {
            case 'registrarHistorialMedico':
                error_log("Entrando en el case 'registrarHistorialMedico'");
            
                // Decodificar los datos JSON desde el cuerpo de la solicitud
                $inputData = json_decode(file_get_contents('php://input'), true);
                
                if ($inputData === null) {
                    error_log("Error: No se recibieron datos JSON válidos o están mal formateados.");
                    echo json_encode(['status' => 'error', 'message' => 'Datos JSON inválidos o mal formateados']);
                    break;
                }
            
                // Llamada al método para registrar el historial médico con los datos JSON decodificados
                $result = $historialme->registrarHistorial([
                    'idEquino' => $inputData['idEquino'] ?? null,
                    'idMedicamento' => $inputData['idMedicamento'] ?? null,
                    'dosis' => $inputData['dosis'] ?? null,
                    'frecuenciaAdministracion' => $inputData['frecuenciaAdministracion'] ?? null,
                    'viaAdministracion' => $inputData['viaAdministracion'] ?? null,
                    'pesoEquino' => $inputData['pesoEquino'] ?? null,
                    'fechaInicio' => $inputData['fechaInicio'] ?? null,
                    'fechaFin' => $inputData['fechaFin'] ?? null,
                    'observaciones' => $inputData['observaciones'] ?? null,
                    'reaccionesAdversas' => $inputData['reaccionesAdversas'] ?? null,
                    'tipoTratamiento' => $inputData['tipoTratamiento'] ?? null
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
            


            case 'gestionarTratamiento':
                error_log("Entrando en el case 'gestionarTratamiento'");
                
                // Decodificar los datos JSON recibidos desde php://input
                $inputData = json_decode(file_get_contents('php://input'), true);
                
                // Registrar los datos recibidos
                error_log("Datos JSON recibidos en 'gestionarTratamiento': " . print_r($inputData, true));
                
                if ($inputData === null) {
                    error_log("Error: No se recibieron datos JSON válidos o están mal formateados.");
                    echo json_encode(['status' => 'error', 'message' => 'Datos JSON inválidos']);
                    break;
                }
            
                // Obtener idRegistro y accion del JSON decodificado
                $idRegistro = $inputData['idRegistro'] ?? null;
                $accion = $inputData['accion'] ?? null;
            
                error_log("ID de Registro recibido: " . var_export($idRegistro, true));
                error_log("Acción recibida: " . var_export($accion, true));
                
                // Validar los parámetros y continuar con la ejecución
                if ($idRegistro && in_array($accion, ['pausar', 'continuar', 'eliminar'])) {
                    error_log("Parámetros válidos. Llamando a 'gestionarTratamiento' en el modelo.");
                    
                    // Llamar al método en el modelo
                    $result = $historialme->gestionarTratamiento($idRegistro, $accion);
                    
                    error_log("Resultado de gestionarTratamiento en el modelo: " . ($result ? "Éxito" : "Fallo"));
                    
                    echo json_encode([
                        'status' => $result ? 'success' : 'error',
                        'message' => $result
                            ? ($accion === 'pausar' ? 'Tratamiento pausado correctamente' 
                            : ($accion === 'continuar' ? 'Tratamiento continuado correctamente' : 'Tratamiento eliminado correctamente'))
                            : 'No se pudo realizar la acción.'
                    ]);
                } else {
                    // Error en los parámetros recibidos
                    error_log("Error: ID de registro o acción no válidos o no proporcionados.");
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'ID de registro o acción no proporcionados o acción no válida.'
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

