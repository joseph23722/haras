<?php
require_once '../models/Admedi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreMedicamento = $_POST['nombreMedicamento'];
    $cantidad = $_POST['cantidad'];
    $caducidad = $_POST['caducidad'];
    $precioUnitario = $_POST['precioUnitario'];
    $idTipomovimiento = $_POST['idTipomovimiento'];
    $idUsuario = $_POST['idUsuario'];
    $visita = $_POST['visita'];
    $tratamiento = $_POST['tratamiento'];

    // Crear una instancia del modelo Admi
    $admi = new Admi();

    // Llamar al método para registrar el medicamento
    $params = [
        'nombreMedicamento' => $nombreMedicamento,
        'cantidad' => $cantidad,
        'caducidad' => $caducidad,
        'precioUnitario' => $precioUnitario,
        'idTipomovimiento' => $idTipomovimiento,
        'idUsuario' => $idUsuario,
        'visita' => $visita,
        'tratamiento' => $tratamiento
    ];

    $result = $admi->registrarMedicamento($params);

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Medicamento registrado correctamente.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar el medicamento.']);
    }
}
