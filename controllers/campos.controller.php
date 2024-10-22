<?php
require_once '../models/RotacionCampos.php';

$controller = new RotacionCampos();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['operation'])) {
        switch ($_GET['operation']) {
            case 'getTiposRotaciones':
                echo json_encode($controller->listarTipoRotaciones());
                break;
            case 'getCampos':
                $campos = $controller->listarCampos();
                $jsonResponse = json_encode($campos);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    echo json_encode(["status" => "error", "message" => "Error en la codificación JSON: " . json_last_error_msg()]);
                } else {
                    echo $jsonResponse;
                }
                break;
            default:
                echo json_encode(["status" => "error", "message" => "Operación no válida."]);
                break;
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Operación no especificada."]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['operation'])) {
        switch ($_POST['operation']) {
            case 'registrarCampo':
                $nuevoCampo = [
                    'numeroCampo' => $_POST['numeroCampo'],
                    'tamanoCampo' => $_POST['tamanoCampo'],
                    'tipoSuelo' => $_POST['tipoSuelo'],
                    'estado' => $_POST['estado']
                ];

                $resultado = $controller->registrarCampo($nuevoCampo);

                if ($resultado) {
                    echo json_encode(["status" => "success", "message" => "Campo registrado correctamente."]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Error al registrar el campo."]);
                }
                break;

            case 'rotacionCampos':
                $datosRecibidos = [
                    "fechaRotacion" => $controller->limpiarCadena($_POST['fechaRotacion']),
                    "estadoRotacion" => $controller->limpiarCadena($_POST['estadoRotacion']),
                    "detalleRotacion" => $controller->limpiarCadena($_POST['detalleRotacion']),
                ];

                $idRotacion = $controller->rotacionCampos($datosRecibidos);
                echo json_encode(['idRotacion' => $idRotacion]);
                break;

            default:
                echo json_encode(["status" => "error", "message" => "Operación no válida."]);
                break;
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Operación no especificada."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método HTTP no permitido."]);
}