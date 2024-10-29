<?php
require_once '../models/Bosta.php';

$controller = new Bosta();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['operation'])) {
        switch ($_POST['operation']) {
            case 'registrarBosta':
                $nuevaBosta = [
                    'fecha' => $_POST['fecha'],
                    'cantidadsacos' => $_POST['cantidadsacos'],
                    'pesoaprox' => $_POST['pesoaprox'],
                ];

                try {
                    $resultado = $controller->RegistroBostas($nuevaBosta);

                    if ($resultado === 1) {
                        echo json_encode(["status" => "success", "message" => "Bosta registrada correctamente."]);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Error al registrar la bosta."]);
                    }
                } catch (Exception $e) {
                    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
                }
                break;
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Operación no especificada."]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['operation']) && $_GET['operation'] === 'obtenerPesos') {
        $fecha = date('Y-m-d');
        try {
            $pesos = $controller->ObtenerPesos($fecha);
            echo json_encode(["status" => "success", "data" => $pesos]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método HTTP no permitido."]);
}
