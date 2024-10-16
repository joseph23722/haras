<?php
require_once '../models/RotacionCampos.php'; // Asegúrate de incluir el modelo correcto

$controller = new RotacionCampos();

header("Content-type: application/json; charset=utf-8");

// Verifica si la solicitud es GET o POST
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Caso de operación GET
    if (isset($_GET['operation'])) {
        switch ($_GET['operation']) {
            case 'getTiposRotaciones':
                echo json_encode($controller->listarTipoRotaciones());
                break;
            default:
                echo json_encode(["status" => "error", "message" => "Operación no válida."]);
                break;
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Operación no especificada."]);
    }
}
