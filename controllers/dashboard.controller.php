<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de encabezado y logs
header('Content-Type: application/json');
ini_set("log_errors", 1);
ini_set("error_log", "../logs/error_log.log");

require_once '../models/dashboard.php';

// Función para retornar siempre una respuesta en JSON
function jsonResponse($data)
{
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

try {
    $dashboard = new Dashboard();
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'medicamentos_stock':
            // Aseguramos que el array tenga valores predeterminados
            $data = $dashboard->ObtenerResumenStockMedicamentos() ?: [
                "stock_total" => 0,
                "en_stock" => 0,
                "criticos" => 0
            ];
            jsonResponse($data);
            break;

        case 'alimentos_stock':
            $data = $dashboard->ObtenerResumenStockAlimentos() ?: [
                "stock_total" => 0,
                "cantidad_alimentos" => 0,
                "baja_cantidad" => '',
                "en_stock" => ''
            ];
            jsonResponse($data);
            break;


        case 'total_equinos':
            $data = ['totalEquinos' => $dashboard->ObtenerTotalEquinosRegistrados() ?: 0];
            jsonResponse($data);
            break;

        case 'servicios_semana':
            $data = ['totalServicios' => $dashboard->ObtenerServiciosSemanaActual() ?: 0];
            jsonResponse($data);
            break;

        case 'medicamentos_en_stock':
            $data = ['totalMedicamentos' => $dashboard->ObtenerMedicamentosEnStock() ?: 0];
            jsonResponse($data);
            break;

        case 'alimentos_en_stock':
            $data = ['totalAlimentos' => $dashboard->ObtenerAlimentosEnStock() ?: 0];
            jsonResponse($data);
            break;

        case 'resumen_servicios':
            // Proporcionamos valores predeterminados
            $data = $dashboard->ObtenerResumenServicios() ?: [
                "totalServicios" => 0,
                "porcentajeServiciosPropios" => 0,
                "porcentajeServiciosMixtos" => 0
            ];
            jsonResponse($data);
            break;

        case 'servicios_mensual':
            $meta = $_GET['meta'] ?? 100;
            // Asignamos valores predeterminados en caso de que no haya datos
            $data = $dashboard->ObtenerServiciosRealizadosMensual((int)$meta) ?: [
                "totalServicios" => 0,
                "porcentajeProgreso" => 0,
                "seriesMensual" => []
            ];
            jsonResponse($data);
            break;

        case 'fotografias_equinos':
            $fotografias = $dashboard->ObtenerFotografiasEquinos();
            if ($fotografias) {
                $data = array_map(function ($fotoId) {
                    return [
                        'url' => "https://res.cloudinary.com/dtbhq7drd/image/upload/$fotoId",
                        'fotoId' => $fotoId
                    ];
                }, $fotografias);
            } else {
                $data = ["error" => "No se encontraron fotografías"];
            }
            jsonResponse($data);
            break;

        default:
            jsonResponse(['error' => 'Acción no válida']);
            break;
    }
} catch (Exception $e) {
    // Registrar el error en el log y retornar un JSON con el mensaje de error
    error_log("Error en el controlador dashboard.controller.php: " . $e->getMessage());
    jsonResponse([
        'error' => 'Error en el servidor',
        'message' => $e->getMessage()
    ]);
}
