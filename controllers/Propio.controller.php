<?php
require_once '../models/Propio.php';

$servicioPropio = new ServicioPropio();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    error_log(print_r($data, true)); // Log de los datos que llegan al servidor

    // Validación básica
    if (empty($data['idEquinoMacho']) || empty($data['idEquinoHembra']) || empty($data['fechaServicio']) ||
        empty($data['horaEntrada']) || empty($data['horaSalida'])) {
        echo json_encode(["status" => "error", "message" => "Faltan parámetros necesarios."]);
        exit;
    }

    // Intentar registrar el servicio propio
    try {
        $result = $servicioPropio->registrarServicioPropio($data);
        echo json_encode($result);
    } catch (Exception $e) {
        error_log("Error al registrar servicio propio: " . $e->getMessage());
        echo json_encode(["status" => "error", "message" => "Error al registrar el servicio propio."]);
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['tipoEquino'])) {
        $equinos = $servicioPropio->listarEquinosPropios($_GET['tipoEquino']);
        echo json_encode($equinos);
    } elseif (isset($_GET['listarMedicamentos'])) {
        $medicamentos = $servicioPropio->listarMedicamentos();
        echo json_encode($medicamentos);
    } else {
        echo json_encode(["status" => "error", "message" => "Parámetros no válidos."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método de solicitud no permitido."]);
}
?>