<?php
require_once '../models/Mixto.php';

$servicioMixto = new ServicioMixto();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    error_log(print_r($data, true));

    if (empty($data['idEquinoMacho']) && empty($data['idEquinoHembra'])) {
        echo json_encode(["status" => "error", "message" => "Debe seleccionar al menos un equino propio."]);
        exit;
    }

    if (
        empty($data['idPropietario']) ||
        empty($data['idEquinoExterno']) ||
        empty($data['fechaServicio']) ||
        empty($data['horaEntrada']) ||
        empty($data['horaSalida'])
    ) {
        echo json_encode(["status" => "error", "message" => "Faltan parámetros necesarios."]);
        exit;
    }

    try {
        $params = [
            'idEquinoMacho' => $data['idEquinoMacho'] ?? null,
            'idEquinoHembra' => $data['idEquinoHembra'] ?? null,
            'idPropietario' => $data['idPropietario'],
            'idEquinoExterno' => $data['idEquinoExterno'],
            'fechaServicio' => $data['fechaServicio'],
            'horaEntrada' => $data['horaEntrada'],
            'horaSalida' => $data['horaSalida'],
            'idMedicamento' => $data['idMedicamento'] ?? null,
            'detalles' => $data['detalles'] ?? null
        ];

        error_log("Intentando registrar servicio mixto con parámetros: " . print_r($params, true));

        $result = $servicioMixto->registrarServicioMixto($params);

        echo json_encode($result);
    } catch (Exception $e) {
        error_log("Error al registrar servicio mixto: " . $e->getMessage());
        echo json_encode(["status" => "error", "message" => "Error al registrar el servicio mixto: " . $e->getMessage()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['listarPropietarios'])) {
        $propietarios = $servicioMixto->listarPropietarios();
        echo json_encode($propietarios);
    } elseif (isset($_GET['tipoEquino'])) {
        $equinos = $servicioMixto->listarEquinosPropios($_GET['tipoEquino']);
        echo json_encode($equinos);
    } elseif (isset($_GET['listarMedicamentos'])) {
        $medicamentos = $servicioMixto->listarMedicamentos();
        echo json_encode($medicamentos);
    } // Agregar filtro por género al listar equinos externos
    elseif (isset($_GET['idPropietario']) && isset($_GET['genero'])) {
        $idPropietario = $_GET['idPropietario'];
        $genero = $_GET['genero']; // 1 para hembra, 2 para macho
        $equinos = $servicioMixto->listarEquinosExternosPorPropietarioYGenero($idPropietario, $genero);
        echo json_encode($equinos);
        
    } else {
        echo json_encode(["status" => "error", "message" => "Parámetros no válidos."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método de solicitud no permitido."]);
}

