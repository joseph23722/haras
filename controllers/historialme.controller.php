<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../models/Historialme.php';

// Crear una instancia de la clase Historialme
$historialme = new Historialme();
$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json');


// Función para enviar respuestas en formato JSON
function sendResponse($status, $message, $data = null) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit(); // Asegura que no se envíe contenido adicional
}


try {
    // Verificar si el contenido es JSON
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $contentType === "application/json") {
        // Leer JSON del cuerpo de la solicitud
        $inputData = json_decode(file_get_contents('php://input'), true);

        // Asegurarse de que se capturó correctamente el JSON
        if ($inputData === null) {
            echo json_encode(['status' => 'error', 'message' => 'JSON inválido']);
            exit;
        }

        // Obtener la operación desde el JSON
        $operation = $inputData['operation'] ?? '';
        switch ($operation) {


            case 'agregarVia':
                // Verificar los parámetros recibidos
                $nombreVia = $_POST['nombreVia'] ?? null;
                $descripcion = $_POST['descripcion'] ?? null;
        
                if ($nombreVia) {
                    // Llamada al método para agregar una nueva vía de administración
                    $result = $model->agregarViaAdministracion($nombreVia, $descripcion);
                    echo json_encode(['status' => $result['status'], 'message' => $result['message']]);
                } else {
                    // Manejo de error si faltan datos
                    echo json_encode(['status' => 'error', 'message' => 'El nombre de la vía es obligatorio.']);
                }
                break;

            
            case 'registrarHistorialMedico':

                // Decodificar los datos JSON desde el cuerpo de la solicitud
                $inputData = json_decode(file_get_contents('php://input'), true);

                if ($inputData === null) {
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

                // Validar los parámetros y continuar con la ejecución
                if ($idRegistro && in_array($accion, ['pausar', 'continuar', 'eliminar'])) {

                    // Llamar al método en el modelo
                    $result = $historialme->gestionarTratamiento($idRegistro, $accion);

                    echo json_encode([
                        'status' => $result ? 'success' : 'error',
                        'message' => $result
                            ? ($accion === 'pausar' ? 'Tratamiento pausado correctamente'
                                : ($accion === 'continuar' ? 'Tratamiento continuado correctamente' : 'Tratamiento eliminado correctamente'))
                            : 'No se pudo realizar la acción.'
                    ]);
                } else {
                    // Error en los parámetros recibidos
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
        $operation = $_GET['operation'] ?? '';

        switch ($operation) {

            case 'listarVias':
                // Llamada al método para listar las vías de administración
                $result = $model->listarViasAdministracion();
                echo json_encode(['status' => 'success', 'data' => $result]);
                break;

            case 'notificarTratamientosVeterinarios':
                try {
                    $result = $historialme->notificarTratamientosVeterinarios();
                    if ($result['status'] === 'success') {
                        sendResponse('success', 'Notificaciones obtenidas correctamente.', $result['data']);
                    } else {
                        sendResponse('error', $result['message']);
                    }
                } catch (Exception $e) {
                    sendResponse('error', 'Error al procesar la solicitud.');
                }
                break;

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
