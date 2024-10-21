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
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['operation'])) {
        switch ($_POST['operation']) {
            case 'registrarCampo':
                // Captura los datos desde el modal
                $datosRecibidos = [
                    "numeroCampo" => $_POST['numeroCampo'],
                    "tamanoCampo" => $_POST['tamanoCampo'],
                    "tipoSuelo" => $_POST['tipoSuelo'],
                    "estado" => $_POST['estado']
                ];

                // Registrar el campo
                $resultado = $controller->registrarCampos($datosRecibidos);
                
                // Preparar la respuesta
                if ($resultado >= 0) {
                    echo json_encode(['status' => 'success', 'message' => 'Campo registrado correctamente.', 'resultado' => $resultado]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error al registrar el campo.']);
                }
                break;

            case 'rotacionCampos':
                $datosRecibidos = [
                    "fechaRotacion" => $campos->limpiarCadena($_POST['fechaRotacion']),
                    "estadoRotacion" => $campos->limpiarCadena($_POST['estadoRotacion']),
                    "detalleRotacion" => $campos->limpiarCadena($_POST['detalleRotacion']),
                ];

                // Insertar el personal y obtener el ID generado
                $idRotacion = $campos->rotacionCampos($datosRecibidos);
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