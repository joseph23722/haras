<?php
require_once '../models/Historialme.php';

$historialme = new Historialme();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['operation'])) {
        // Registrar historial médico
        if ($data['operation'] === 'registrar') {
            $params = [
                'idEquino' => $data['idEquino'],
                'fecha' => $data['fecha'],
                'diagnostico' => $data['diagnostico'],
                'tratamiento' => $data['tratamiento'],
                'observaciones' => $data['observaciones'],
                'recomendaciones' => $data['recomendaciones']
            ];

            $result = $historialme->registrarHistorial($params);

            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Historial médico registrado correctamente.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al registrar el historial médico.']);
            }
        } 
        // Listar equinos por tipo
        elseif ($data['operation'] === 'listarEquinosPorTipo') {
            $tipoEquino = $data['tipoEquino'];
            $equinos = $historialme->listarEquinosPorTipo($tipoEquino);
            
            if (!empty($equinos)) {
                echo json_encode($equinos);
            } else {
                // Devuelve un arreglo vacío para evitar errores en la respuesta
                echo json_encode([]);
            }
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Operación no reconocida.']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(['status' => 'error', 'message' => 'Método de solicitud no permitido.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método de solicitud no permitido.']);
}

