<?php
require_once '../models/Bosta.php';

$controller = new Bosta();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['operation'])) {
        switch ($_GET['operation']) {
            case 'obtenerPesos':
                try {
                    $pesos = $controller->ObtenerPesos(date('Y-m-d'));
                    echo json_encode(["status" => "success", "data" => $pesos]);
                } catch (Exception $e) {
                    echo json_encode(["status" => "error", "message" => "Error al obtener los pesos: " . $e->getMessage()]);
                }
                break;

            case 'listarBostas':
                try {
                    $bostas = $controller->listarBostas();
                    echo json_encode(["status" => "success", "data" => $bostas]);
                } catch (Exception $e) {
                    echo json_encode(["status" => "error", "message" => "Error al listar las bostas: " . $e->getMessage()]);
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
            case 'registrarBosta':
                $nuevaBosta = [
                    'fecha' => $_POST['fecha'],
                    'cantidadsacos' => $_POST['cantidadsacos'],
                    'pesoaprox' => $_POST['pesoaprox'],
                ];

                // Validar parámetros
                if (empty($nuevaBosta['fecha'])) {
                    echo json_encode(["status" => "error", "message" => "La fecha de registro es obligatoria."]);
                    break;
                }
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $nuevaBosta['fecha'])) {
                    echo json_encode(["status" => "error", "message" => "La fecha debe estar en formato AAAA-MM-DD."]);
                    break;
                }
                if (empty($nuevaBosta['cantidadsacos']) || !is_numeric($nuevaBosta['cantidadsacos']) || $nuevaBosta['cantidadsacos'] <= 0) {
                    echo json_encode(["status" => "error", "message" => "La cantidad de sacos debe ser un número positivo."]);
                    break;
                }
                if (empty($nuevaBosta['pesoaprox']) || !is_numeric($nuevaBosta['pesoaprox']) || $nuevaBosta['pesoaprox'] <= 0) {
                    echo json_encode(["status" => "error", "message" => "El peso aproximado debe ser un número positivo."]);
                    break;
                }

                try {
                    $resultado = $controller->RegistroBostas($nuevaBosta);
                    if ($resultado === 1) {
                        echo json_encode(["status" => "success", "message" => "Bosta registrada correctamente."]);
                    }
                } catch (Exception $e) {
                    $mensaje_error = $e->getMessage();
                    if (preg_match('/Ya existe un registro para esta fecha: (.+)/', $mensaje_error, $coincidencias)) {
                        echo json_encode(["status" => "error", "message" => $coincidencias[0]]);
                    } elseif (preg_match('/La fecha no puede ser mayor a la fecha actual./', $mensaje_error)) {
                        echo json_encode(["status" => "error", "message" => "La fecha no puede ser mayor a la fecha actual."]);
                    } elseif (preg_match('/No se permite registrar datos los domingos/', $mensaje_error)) {
                        echo json_encode(["status" => "error", "message" => "No se permite registrar datos los domingos."]);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Error inesperado al registrar la bosta: " . $mensaje_error]);
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