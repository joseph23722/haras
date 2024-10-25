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

            case 'getCampoID':
                $idCampo = $_GET['idCampo'] ?? null;

                if ($idCampo) {
                    $resultado = $controller->obtenerCampoID($idCampo);
                    echo json_encode($resultado);
                } else {
                    echo json_encode(["status" => "error", "message" => "ID de campo no proporcionado."]);
                }
                break;

            case 'getTipoSuelo':
                echo json_encode($controller->listarTipoSuelo());
                break;

            case 'getUltimaAccion':
                $idCampo = $_GET['idCampo'] ?? null;

                if ($idCampo) {
                    $resultado = $controller->obtenerUltimaAccion($idCampo);
                    echo json_encode($resultado);
                } else {
                    echo json_encode(["status" => "error", "message" => "ID de campo no proporcionado."]);
                }
                break;

            case 'getRotaciones':
                echo json_encode($controller->listarRotaciones());
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
                    'idTipoSuelo' => $_POST['tipoSuelo'],
                    'estado' => $_POST['estado']
                ];

                $resultado = $controller->registrarCampo($nuevoCampo);

                if ($resultado === -2) {
                    echo json_encode(["status" => "error", "message" => "Error: Ya existe un campo con ese número."]);
                } elseif ($resultado > 0) {
                    echo json_encode(["status" => "success", "message" => "Campo registrado correctamente."]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Error al registrar el campo."]);
                }
                break;

            case 'editarCampo':
                $params = [
                    'idCampo' => $_POST['idCampo'],
                    'numeroCampo' => $_POST['numeroCampo'],
                    'tamanoCampo' => $_POST['tamanoCampo'],
                    'idTipoSuelo' => $_POST['tipoSuelo'],
                    'estado' => $_POST['estado']
                ];

                if (in_array(null, $params, true)) {
                    echo json_encode(["status" => "error", "message" => "Faltan datos para editar el campo."]);
                    break;
                }

                $resultado = $controller->editarCampo($params);
                if ($resultado > 0) {
                    echo json_encode(["status" => "success", "message" => "Campo editado correctamente."]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Error al editar el campo."]);
                }
                break;

            case 'eliminarCampo':
                $idCampo = $_POST['idCampo'] ?? null;

                if ($idCampo) {
                    $resultado = $controller->eliminarCampo($idCampo);
                    if ($resultado > 0) {
                        echo json_encode(["status" => "success", "message" => "Campo eliminado correctamente."]);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Error al eliminar el campo."]);
                    }
                } else {
                    echo json_encode(["status" => "error", "message" => "ID de campo no proporcionado."]);
                }
                break;


            case 'rotacionCampos':
                $datosRecibidos = [
                    "idCampo"       => $_POST['campos'] ?? null,
                    "idTipoRotacion"  => $_POST['tipoRotacion'] ?? null,
                    "fechaRotacion" => $_POST['fechaRotacion'] ?? null,
                    "detalleRotacion" => $_POST['detalleRotacion'] ?? null,
                ];

                // Validar datos recibidos
                if (in_array(null, $datosRecibidos, true)) {
                    echo json_encode(["status" => "error", "message" => "Faltan datos para registrar la rotación."]);
                    break;
                }

                try {
                    $idRotacion = $controller->rotacionCampos($datosRecibidos);

                    if ($idRotacion > 0) {
                        echo json_encode(['status' => 'success', 'idRotacion' => $idRotacion]);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Error al registrar la rotación. ID no válido."]);
                    }
                } catch (Exception $e) {
                    $mensajeError = $e->getMessage();

                    if ($mensajeError === 'Ya existe una rotación del mismo tipo en la misma fecha.') {
                        echo json_encode(["status" => "error", "message" => "No se puede registrar: ya existe una rotación del mismo tipo en esta fecha."]);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Error al registrar la rotación: " . $mensajeError]);
                    }
                }
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