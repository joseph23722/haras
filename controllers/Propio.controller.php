<?php
require_once '../models/Propio.php';

$servicioPropio = new ServicioPropio();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Registrar el servicio propio
    $data = json_decode(file_get_contents('php://input'), true);
    $result = $servicioPropio->registrarServicioPropio($data);

    if ($result) {
        echo json_encode(["status" => "success", "message" => "Servicio propio registrado exitosamente."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al registrar el servicio propio."]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Listar equinos, haras o medicamentos según los parámetros recibidos
    if (isset($_GET['tipoEquino'])) {
        $equinos = $servicioPropio->listarEquinosPorTipo($_GET['tipoEquino']);
        echo json_encode($equinos);
    } elseif (isset($_GET['listarHaras'])) {
        $haras = $servicioPropio->listarHaras();
        echo json_encode($haras);
    } elseif (isset($_GET['listarMedicamentos'])) {
        $medicamentos = $servicioPropio->listarMedicamentosConDetalles();
        echo json_encode($medicamentos);
    } else {
        echo json_encode(["status" => "error", "message" => "Parámetros no válidos."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método de solicitud no permitido."]);
}
