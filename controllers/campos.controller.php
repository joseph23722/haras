<?php
require_once '../models/RotacionCampos.php'; // Asegúrate de incluir el modelo correcto

$controller = new RotacionCampos();

header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['operation'])) {
        switch ($_GET['operation']) {
            case 'getTiposRotaciones':
                echo json_encode($controller->listarTipoRotaciones());
                break;
            case 'getCampos': // Nueva operación para obtener campos
                echo json_encode($controller->listarCampos());
                break;
            default:
                echo json_encode(["status" => "error", "message" => "Operación no válida."]);
                break;
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Operación no especificada."]);
    }

    if (isset($_POST['operation'])) {
        switch ($_POST['operation']) {
            case 'rotacionCampos':
                $datosRecibidos = [
                    "fechaRotacion" => $campos->limpiarCadena($_POST['fechaRotacion']),
                    "estadoRotacion" => $campos->limpiarCadena($_POST['estadoRotacion']),
                    "direccion" => $campos->limpiarCadena($_POST['detalleRotacion']),
                ];

                // Insertar el personal y obtener el ID generado
                $idRotacion = $campos->rotacionCampos($datosRecibidos);
                echo json_encode(['idRotacion' => $idRotacion]);
                break;
        }
    }
}