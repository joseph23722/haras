<?php
require_once '../models/Propio.php';

$servicioPropio = new ServicioPropio();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    error_log(print_r($data, true)); // Log de los datos que llegan al servidor

    // Validación básica
    if (
        empty($data['idEquinoMacho']) || empty($data['idEquinoHembra']) || empty($data['fechaServicio'])
    ) {
        echo json_encode(["status" => "error", "message" => "Faltan parámetros necesarios."]);
        exit;
    }

    try {
        $result = $servicioPropio->registrarServicioPropio($data);
        echo json_encode($result);
    } catch (PDOException $e) {
        error_log("Error al registrar servicio propio: " . $e->getMessage());

        // Extraer solo el mensaje específico del error
        $mensaje = $e->getMessage();

        // Verifica si el mensaje contiene "Error:" y extrae solo lo necesario
        if (preg_match('/Error: (.+)/', $mensaje, $matches)) {
            echo json_encode(["status" => "error", "message" => trim($matches[1])]);
        } else {
            echo json_encode(["status" => "error", "message" => "Ocurrió un error al registrar el servicio."]);
        }
    } catch (Exception $e) {
        error_log("Error inesperado: " . $e->getMessage());
        echo json_encode(["status" => "error", "message" => "Error inesperado. Intenta nuevamente."]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['tipoEquino'])) {
        $equinos = $servicioPropio->listarEquinosPropios($_GET['tipoEquino']);
        echo json_encode($equinos);
    } elseif (isset($_GET['listarMedicamentos'])) {
        $medicamentos = $servicioPropio->listarMedicamentos();
        echo json_encode($medicamentos);
    } elseif (isset($_GET['tipoServicio'])) {
        // Listar servicios por tipo (Propio, Mixto o General)
        $tipoServicio = $_GET['tipoServicio'];
    
        $servicios = $servicioPropio->listarServiciosPorTipo($tipoServicio);
        echo json_encode($servicios);
    }
     else {
        echo json_encode(["status" => "error", "message" => "Parámetros no válidos."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método de solicitud no permitido."]);
}
