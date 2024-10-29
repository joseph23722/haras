<?php
require_once '../models/Bosta.php';

$controller = new Bosta();
header('Content-Type: application/json; charset=utf-8');

function enviarRespuesta($status, $message)
{
    echo json_encode(["status" => $status, "message" => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['operation'])) {
        switch ($_POST['operation']) {
            case 'registrarBosta':
                // Validar parámetros
                if (empty($_POST['fecha'])) enviarRespuesta("error", "La fecha de registro es obligatoria.");
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['fecha'])) enviarRespuesta("error", "La fecha debe estar en formato AAAA-MM-DD.");
                if (empty($_POST['cantidadsacos'])) enviarRespuesta("error", "La cantidad de sacos es obligatoria.");
                if (!is_numeric($_POST['cantidadsacos']) || $_POST['cantidadsacos'] <= 0) enviarRespuesta("error", "La cantidad de sacos debe ser un número positivo.");
                if (empty($_POST['pesoaprox'])) enviarRespuesta("error", "El peso aproximado es obligatorio.");
                if (!is_numeric($_POST['pesoaprox']) || $_POST['pesoaprox'] <= 0) enviarRespuesta("error", "El peso aproximado debe ser un número positivo.");

                $nuevaBosta = [
                    'fecha' => $_POST['fecha'],
                    'cantidadsacos' => $_POST['cantidadsacos'],
                    'pesoaprox' => $_POST['pesoaprox'],
                ];

                try {
                    $resultado = $controller->RegistroBostas($nuevaBosta);
                    if ($resultado === 1) {
                        enviarRespuesta("success", "Bosta registrada correctamente.");
                    }
                } catch (Exception $e) {
                    // Extraer mensaje de error
                    $mensaje_error = $e->getMessage();
                    if (preg_match('/Ya existe un registro para esta fecha: (.+)/', $mensaje_error, $coincidencias)) {
                        enviarRespuesta("error", $coincidencias[0]);
                    } elseif (preg_match('/La fecha no puede ser mayor a la fecha actual./', $mensaje_error)) {
                        enviarRespuesta("error", "La fecha no puede ser mayor a la fecha actual.");
                    } elseif (preg_match('/No se permite registrar datos los domingos/', $mensaje_error)) {
                        enviarRespuesta("error", "No se permite registrar datos los domingos.");
                    } else {
                        enviarRespuesta("error", "Error inesperado al registrar la bosta: " . $mensaje_error);
                    }
                }
                break;
            default:
                enviarRespuesta("error", "Operación no reconocida.");
                break;
        }
    } else {
        enviarRespuesta("error", "Operación no especificada.");
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['operation']) && $_GET['operation'] === 'obtenerPesos') {
        try {
            $pesos = $controller->ObtenerPesos(date('Y-m-d'));
            echo json_encode(["status" => "success", "data" => $pesos]);
        } catch (Exception $e) {
            enviarRespuesta("error", "Error al obtener los pesos: " . $e->getMessage());
        }
    } else {
        enviarRespuesta("error", "Operación no reconocida para método GET.");
    }
} else {
    enviarRespuesta("error", "Método HTTP no permitido.");
}